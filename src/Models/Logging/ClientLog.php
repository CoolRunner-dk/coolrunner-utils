<?php
/**
 * @package coolrunner-utils
 * @copyright 2021
 */

namespace CoolRunner\Utils\Models\Logging;

use CoolRunner\Utils\Models\Audit\Audit;
use CoolRunner\Utils\Support\Tools\Bytes;
use CoolRunner\Utils\Traits\Logging\DeconstructsRequests;
use CoolRunner\Utils\Traits\Models\BelongsToAuthModel;
use CoolRunner\Utils\Traits\Models\HasSessionUuid;
use CoolRunner\Utils\Traits\Models\SavesEnvironment;
use CoolRunner\Utils\Traits\Models\SavesModelAsync;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * App\Models\Logging\ClientLog
 *
 * @property int $id
 * @property string|null $type
 * @property string|null $service
 * @property string|null $uri
 * @property string|null $method
 * @property array $query
 * @property array $request_headers
 * @property string|null $request_body
 * @property array $response_headers
 * @property string|null $response_body
 * @property int|null $response_status
 * @property string|null $response_phrase
 * @property int|null $total_time
 * @property array $timing
 * @property string|null $input_log_uuid
 * @property \Illuminate\Support\Carbon|null $requested_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Logging\InputLog|null $inputLog
 * @method static Builder|ClientLog newModelQuery()
 * @method static Builder|ClientLog newQuery()
 * @method static Builder|ClientLog query()
 * @method static Builder|ClientLog whereCreatedAt($value)
 * @method static Builder|ClientLog whereId($value)
 * @method static Builder|ClientLog whereInputLogUuid($value)
 * @method static Builder|ClientLog whereMethod($value)
 * @method static Builder|ClientLog whereQuery($value)
 * @method static Builder|ClientLog whereRequestBody($value)
 * @method static Builder|ClientLog whereRequestHeaders($value)
 * @method static Builder|ClientLog whereRequestedAt($value)
 * @method static Builder|ClientLog whereResponseBody($value)
 * @method static Builder|ClientLog whereResponseHeaders($value)
 * @method static Builder|ClientLog whereResponsePhrase($value)
 * @method static Builder|ClientLog whereResponseStatus($value)
 * @method static Builder|ClientLog whereService($value)
 * @method static Builder|ClientLog whereTiming($value)
 * @method static Builder|ClientLog whereTotalTime($value)
 * @method static Builder|ClientLog whereType($value)
 * @method static Builder|ClientLog whereUpdatedAt($value)
 * @method static Builder|ClientLog whereUri($value)
 * @mixin \Eloquent
 */
class ClientLog extends Model
{
    use DeconstructsRequests;

    use HasSessionUuid;
    use BelongsToAuthModel;

    use SavesEnvironment;
    use SavesModelAsync;


    protected $table = 'client_logs';

    protected $casts = [
        'query'            => 'json',
        'timing'           => 'json',
        'request_headers'  => 'json',
        'response_headers' => 'json',
        'requested_at'     => 'datetime',
    ];

    protected array $allowed_content_types = [
        'application/json',
        'application/x-www-form-urlencoded',
        'text/xml',
        'text/html',
    ];

    /**
     * Maximum characters to log from a request (applies to both input and output
     */
    const CONTENT_MAX = 100000;

    const CLIENT_TYPE       = '___log_type';
    const CLIENT_SERVICE    = '___log_service';

    const LOG_ENTRY       = '___log_entry';
    const CLOCKWORK_ENTRY = '___log_clockwork';
    const CLIENT_USER = '___log_user_id';


//    public function inputLog() : BelongsTo|InputLog
//    {
//        return $this->belongsTo(InputLog::class, 'input_log_uuid', 'uuid');
//    }

    public function fillFromRequestInterface(RequestInterface $request, array $options = [])
    {
        $options = optional($options);

        parse_str($request->getUri()->getQuery(), $query);
        $this->environment     = env('APP_NAME');
        $this->uri             = (string)$request->getUri();
        $this->type            = $options[ClientLog::CLIENT_TYPE] ?: $request->getUri()->getHost();
        $this->service         = $options[ClientLog::CLIENT_SERVICE] ?: ltrim($request->getUri()->getPath(), '/');
        $this->method          = $request->getMethod();
        $this->query           = $query;
        $this->request_headers = $request->getHeaders();
        $this->request_body    = $this->mapBody($request);
    }

    public function fillFromResponseInterface(ResponseInterface $response, array $options = [])
    {
        try {
            $this->response_headers = $response->getHeaders();
            $this->response_body    = $this->mapBody($response);
            $this->response_status  = $response->getStatusCode();
            $this->response_phrase  = $response->getReasonPhrase();
        } catch (\Exception $exception) {
            report($exception);
        }

        $response->getBody()->rewind();
    }

    protected function mapBody(MessageInterface $message) : string
    {
        if ($message instanceof RequestInterface) {
            if ($message->getMethod() === 'GET') {
                return '';
            }
        }

        $content_type = $message->getHeaderLine('Content-Type');

        $body = (clone $message)->getBody()->getContents();

        foreach ($this->allowed_content_types as $allowed_content_type) {
            if (str_contains($content_type, $allowed_content_type)) {
                return substr($body, 0, static::CONTENT_MAX);
            }
        }

        $length = $message->getBody()->getSize();

        if ($length == 0) {
            return '';
        }

        return sprintf('Blocked Content-Type: %s | %s', $content_type, Bytes::reduce($length));
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionName()
    {
        return Config::get('utils.drivers.client_log.connection');
    }

    /**
     * {@inheritdoc}
     */
    public function getTable(): string
    {
        return Config::get('utils.drivers.client_log.table') ?: parent::getTable();
    }

    public function user() : MorphTo {
        return $this->morphTo('user');
    }

    public function audits() : HasMany|Audit {
        return $this->hasMany(Audit::class,'session_uuid','session_uuid');
    }

    public function inputLogs() : HasMany|InputLog {
        return $this->hasMany(InputLog::class,'session_uuid','session_uuid');
    }


}
