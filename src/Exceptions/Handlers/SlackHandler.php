<?php
namespace CoolRunner\Utils\Exceptions\Handlers;

use App\Exceptions\Handler;
use App\Http\Middleware\InputLogger;
use App\Models\Company;
use Illuminate\Filesystem\FilesystemAdapter;
use Monolog\Logger;

class SlackHandler extends \Monolog\Handler\SlackHandler
{
    protected array $icons = [
        'DEBUG'     => ':information_source:',
        'INFO'      => ':information_source:',
        'NOTICE'    => ':exclamation:',
        'WARNING'   => ':exclamation:',
        'ERROR'     => ':boom:',
        'CRITICAL'  => ':boom:',
        'ALERT'     => ':boom:',
        'EMERGENCY' => ':boom:',
    ];

    protected array $pre_wrap = [
        'context.file',
        'context.input log',
    ];


    public function __construct(
        string $webhookUrl,
        ?string $channel = null,
        ?string $username = null,
        bool $useAttachment = true,
        ?string $iconEmoji = null,
        bool $useShortAttachment = false,
        bool $includeContextAndExtra = true,
        $level = Logger::CRITICAL,
        bool $bubble = true,
        array $excludeFields = []
    ) {
        parent::__construct($webhookUrl, $channel, $username, $useAttachment, $iconEmoji,$level, $useShortAttachment, $includeContextAndExtra, $bubble, $excludeFields);
    }


    public function addContext(array $context) : array
    {
//        if ($company = Company::current()) {
//            $context += [
//                'company' => "#$company->id | $company->name",
//            ];
//        }
//
//        if ($log = InputLogger::getLog()) {
//            $context += [
//                'input log' => $log->uuid,
//            ];
//        }

        if ($user = \Auth::user()) {
            $identifier = $user->email ?: null;
            $context += [
                class_basename($user) => $identifier ? "#$user->id | $identifier" : "$user->id",
            ];

        }

        return $context;
    }

    protected function write(array $record) : void
    {
        $this->getSlackRecord()->setUserIcon(\Arr::get($this->icons, \Arr::get($record, 'level_name')));


        $handler = app(Handler::class);
        if ($handler instanceof Handler) {
            $context = $this->addContext(Arr::get($record, 'context', []));

            $record['context'] = $context;
        }

        // If the record contains an exception then store it externally and replace it with a URL for the file
        if (Arr::has($record, 'context.exception')) {
            $exception = \Arr::get($record, 'context.exception');
            \Arr::forget($record, 'context.exception');
            if ($exception instanceof Throwable) {
                \Arr::set($record, 'context.type', get_class($exception));
                \Arr::set($record, 'context.file', "{$exception->getFile()}:{$exception->getLine()}");
                \Arr::set($record, 'context.exception', $this->handleException($exception));
            }
        }

        // If the record doesn't have a file reference (like with regular Log::whatever calls),
        // then locate where the call originated from in the stack trace
        if (!Arr::has($record, 'context.file')) {
            $vendor_path = base_path('vendor');

            $entry = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10))->filter(function ($entry) use ($vendor_path) {
                return Arr::get($entry, 'class') !== static::class && !\Str::startsWith(Arr::get($entry, 'file'), $vendor_path);
            })->first();

            if ($entry && Arr::has($entry, ['file', 'line'])) {
                \Arr::set($record, 'context.file', implode(':', \Arr::only($entry, ['file', 'line'])));
            }
        }

        // Wrap some keys with ` (backwards tick) for readability
        foreach ($this->pre_wrap as $key) {
            if (\Arr::has($record, $key)) {
                \Arr::set($record, $key, sprintf('`%s`', Arr::get($record, $key)));
            }
        }

        parent::write($record);
    }

    public function handleException(\Throwable $exception) : string
    {
        // Convert the exception to JSON and store it remotely
        $exception = [
            'message'   => $exception->getMessage(),
            'code'      => $exception->getCode(),
            'exception' => get_class($exception),
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
            'trace'     => collect($exception->getTrace())->map(function ($trace) {
                return \Arr::except($trace, ['args']);
            })->all(),
        ];

        $path = sprintf("route-runner/%s/%s.json", now()->format('Y-m-d'), md5(random_bytes(16)));

        $this->getDisk()->put($path, json_encode($exception));

        // Return the url for the file
        return $this->getDisk()->url($path);
    }

    public function getDisk() : FilesystemAdapter
    {
        return \Storage::disk('errors');
    }
}
