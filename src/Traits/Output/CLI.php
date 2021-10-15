<?php
/**
 * @package coolrunner-utils
 * @copyright 2021
 */

namespace CoolRunner\Utils\Traits\Output;

use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Traits\ForwardsCalls;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @mixin Command
 */
trait CLI
{
    use ForwardsCalls;

    protected ?Command $cli = null;

    public function cli() : Command
    {
        if ($this instanceof Command) {
            return $this;
        }

        if (!$this->cli) {
            global $argv;

            $output = new Command();
            $input  = new ArgvInput($argv);

            $output->setInput($input);
            $output->setOutput(new OutputStyle($input, new ConsoleOutput()));

            $this->cli = $output;
        }

        return $this->cli;
    }

    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->cli(), $name)) {
            return $this->forwardCallTo($this->cli(), $name, $arguments);
        }
    }
}