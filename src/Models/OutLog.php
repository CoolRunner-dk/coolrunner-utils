<?php


namespace CoolRunner\Utils\Models;
use Carbon\Carbon;
use CoolRunner\Utils\Http\Middleware\InputLogger;
use CoolRunner\Utils\Jobs\SaveLog;
use Illuminate\Database\Eloquent\Model;

/**
 * OutLog.
 *
 * @property int $id
 * @property string $service
 * @property string $type
 * @property string $endpoint
 * @property string $app_name
 * @property string|null $request
 * @property string|null $response
 * @property int|null $http_code
 * @property int|null $time
 * @property string|null $phrase
 * @property int|null $customer_id
 * @property int|null in_log_uuid
 * @property string $requested_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */


class OutLog extends Model
{
    protected $table = 'out_logs';


    public function getConnectionName()
    {
        return config('utils.model_connection');
    }


    public static function create(
        $service,
        $type,
        $endpoint = null,
        $request = null,
        $response = null,
        $http_code = null,
        $phrase = null,
        $time = null,
        $requested_at = null,
        $customer_id = null) {

        $log = new static;

        $log->service = $service;
        $log->type = $type;
        $log->endpoint = $endpoint;
        $log->request = $request;
        $log->response = $response;
        $log->http_code = $http_code;
        $log->phrase = $phrase;
        $log->time = $time;
        $log->requested_at = $requested_at;
        $log->customer_id = $customer_id;
        $log->in_log_uuid = InputLogger::getLogId();
        $log->app_name = env('APP_NAME');
        SaveLog::dispatch($log);

            return $log;
        }

}