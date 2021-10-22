<?php


namespace CoolRunner\Utils\Providers;

use CoolRunner\Utils\Facades\Guzzle;
use CoolRunner\Utils\Facades\LoggingClient;
use CoolRunner\Utils\Facades\LoggingClientFacade;
use CoolRunner\Utils\Models\Logging\ClientLog;
use CoolRunner\Utils\Managers\GuzzleManager;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleClientProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('LoggedClient', function (Application $application, array $parameters = []) {

            $stack = $this->getHandlerStack($parameters);

            $parameters['handler'] = $stack;

            $stack->push(static::getMiddleware($parameters), 'client_logger');

            return new Client($parameters);
        });

        $this->app->singleton(GuzzleManager::class);
        $this->app->bind(
            Client::class,
            static function(Container $app): Client {
                return $app->make(GuzzleManager::class)->client();
            }
        );

        $this->app->alias(Client::class, ClientInterface::class);
        
        Guzzle::extend('default', static function (Container $app, ?array $parameters): Client {
            return app('LoggedClient', $parameters);
        });

    }

    protected function getHandlerStack(array $parameters) : HandlerStack
    {
        return \Arr::get($parameters, 'handler', HandlerStack::create());
    }

    protected function getClockworkEvent(ClientLog $log)
    {
        return clock()->event("Outbound Request: [$log->method] $log->type | $log->service")->name(\Str::random(64))->begin();
    }

    protected function onStatsHandler(ClientLog $log, callable $on_stats = null) : \Closure
    {
        return function (TransferStats $stats) use ($log, $on_stats) {
            if (is_callable($on_stats)) {
                $on_stats($stats);
            }

            $timing = collect($stats->getHandlerStats())->only([
                'total_time',
                'namelookup_time',
                'connect_time',
                'pretransfer_time',
                'starttransfer_time',
                'appconnect_time',
                'redirect_time',
            ])->map(fn($time) => (int)($time * 1000));

            $log->total_time = $timing['total_time'];
            $log->timing     = $timing->except('total_time');
        };
    }

    public static function getMiddleware($parameters = []) {
        return Middleware::tap(function (RequestInterface $request, array &$options) use ($parameters) {

            // Create the initial client_log entry and set the request time
            $log = new ClientLog();
            $log->requested_at = now();

            $user = $parameters[ClientLog::CLIENT_USER] ?? \Auth::user();

            // Get the specified company (using ClientLog::CLIENT_COMPANY_ID request option),
            // or the logged in company (or company provided by agent)
            $log->user()->associate($user);
//                $log->inputLog()->associate(InputLogger::getLog());

            $log->fillFromRequestInterface($request, $options);

            $options = array_merge([
                ClientLog::LOG_ENTRY       => $log,
                ClientLog::CLOCKWORK_ENTRY => $this->getClockworkEvent($log),
                RequestOptions::ON_STATS   => $this->onStatsHandler(
                    $log, $options[RequestOptions::ON_STATS] ?? null
                ),
            ]);
        }, function (RequestInterface $request, array $options, PromiseInterface $promise) {

            $promise->then(function (ResponseInterface $response) use ($options) {
                /** @var ClientLog $log */
                $log = $options[ClientLog::LOG_ENTRY];
                $log->fillFromResponseInterface($response);

                $options[ClientLog::CLOCKWORK_ENTRY]->end();
                $log->save();
            });
        });
    }
}
