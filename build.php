<?php
/**
 * @package coolrunner-utils
 * @copyright 2021
 * @internal
 */

use Illuminate\Support\Collection;

require_once 'vendor/autoload.php';

$cfg = require_once __DIR__ . '/src/config/config.php';

$lines = collect("<?php\n");

/**
 * Aliases
 */
$lines->add("namespace {");
collect($cfg['aliases'])->each(function ($class, $alias) use ($lines) {
    $lines->add("  class $alias extends \\$class {}");
});
$lines->add("}\n");

/**
 * Mixins
 */

$export = function ($expression, $return = false) {
    $export   = var_export($expression, true);
    $patterns = [
        "/array \(/"                       => '[',
        "/^([ ]*)\)(,?)$/m"                => '$1]$2',
        "/=>[ ]?\n[ ]+\[/"                 => '=> [',
        "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
    ];

    return preg_replace(array_keys($patterns), array_values($patterns), $export);
};

$mixins = collect($cfg['mixins'])->map(function (array $mixins, string $class) use ($export) {
    $class = new ReflectionClass($class);

    $methods = collect($mixins)->mapWithKeys(function ($mixin) use ($export) {
        $mixin   = new $mixin();
        $methods = (new ReflectionClass($mixin))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        return [
            get_class($mixin) => collect($methods)->mapWithKeys(function (ReflectionMethod $method) use ($export, $mixin) {
                $method->setAccessible(true);
                $name     = $method->getName();
                $function = new ReflectionFunction($method->invoke($mixin));

                $parameters = collect($function->getParameters())->mapWithKeys(function (ReflectionParameter $parameter) use ($export) {
                    $type = (string)$parameter->getType();
                    $name = $parameter->getName();

                    $definition = "$type \$$name";

                    if ($parameter->isOptional()) {
                        $default = $parameter->getDefaultValue();

                        $definition .= ' = ' . preg_replace('/\n|\r/', '', $export($default));
                    }


                    return [$parameter->getName() => trim($definition)];
                });

                $definition = "$name({$parameters->implode(', ')})";
                $parameters = $parameters->keys()->map(fn($p) => "\$$p")->implode(', ');
                $ref        = "/** @var \\{$method->getDeclaringClass()->getName()} \$ref */";
                $call       = "return \$ref->{$name}($parameters)";

                if ($returns = $function->getReturnType()) {
                    $definition .= ' : ' . $returns;;
                }

                return [
                    $name => [
                        'definition' => $definition,
                        'ref'        => $ref,
                        'call'       => $call,
                    ],
                ];
            }),
        ];
    });

    return collect([
        'namespace' => $class->getNamespaceName(),
        'class'     => $class->getShortName(),
        'mixins'    => $methods,
    ]);
});

$mixins->groupBy('namespace')->each(function (Collection $classes, $namespace) use ($lines) {
    $lines->add("namespace $namespace {\n");

    $classes->each(function (Collection $definition) use ($lines) {
        $class = $definition->get('class');
        $lines->add("    class $class {\n");

        $mixins = $definition->get('mixins');

        $mixins->each(function (Collection $methods, $mixin) use ($lines) {
            $methods->each(function (array $definition) use ($lines) {
                [$def, $ref, $call] = array_values($definition);

                $lines->add("        public static function $def {");
                $lines->add("            $ref");
                $lines->add("            $call;");
                $lines->add("        }\n");
            });
        });
        $lines->add("    }\n");
    });
    $lines->add("}\n");
    $classes->each(function (Collection $definition, string $class) use ($lines) {
    });;
});

file_put_contents(__DIR__ . '/_ide_helper.php', $lines->implode("\n"));