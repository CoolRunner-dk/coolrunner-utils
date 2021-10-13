<?php


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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\MessageInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * App\Models\Logging\InputLog
 *
 * @property int $id
 * @property string $uuid
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
 * @property string|null $route_uri
 * @property string|null $route_name
 * @property array $route_parameters
 * @property string|null $route_action
 * @property \Illuminate\Support\Carbon|null $requested_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Logging\ClientLog[] $clientLogs
 * @method static Builder|\App\Models\Logging\InputLog newModelQuery()
 * @method static Builder|InputLog newQuery()
 * @method static Builder|InputLog query()
 * @method static Builder|InputLog whereCreatedAt($value)
 * @method static Builder|InputLog whereId($value)
 * @method static Builder|InputLog whereMethod($value)
 * @method static Builder|InputLog whereQuery($value)
 * @method static Builder|InputLog whereRequestBody($value)
 * @method static Builder|InputLog whereRequestHeaders($value)
 * @method static Builder|InputLog whereRequestedAt($value)
 * @method static Builder|InputLog whereResponseBody($value)
 * @method static Builder|InputLog whereResponseHeaders($value)
 * @method static Builder|InputLog whereResponsePhrase($value)
 * @method static Builder|InputLog whereResponseStatus($value)
 * @method static Builder|InputLog whereRouteAction($value)
 * @method static Builder|InputLog whereRouteName($value)
 * @method static Builder|InputLog whereRouteParameters($value)
 * @method static Builder|InputLog whereRouteUri($value)
 * @method static Builder|InputLog whereTotalTime($value)
 * @method static Builder|InputLog whereUpdatedAt($value)
 * @method static Builder|InputLog whereUri($value)
 * @method static Builder|InputLog whereUuid($value)
 * @mixin \Eloquent
 */
class InputLog extends Model
{
    use DeconstructsRequests;
    use HasSessionUuid;
    use BelongsToAuthModel;
    use SavesEnvironment;
    use SavesModelAsync;


    const CONTENT_MAX = 100000;

    protected $start_time;

    protected $casts = [
        'query'            => 'json',
        'timing'           => 'json',
        'request_headers'  => 'json',
        'response_headers' => 'json',
        'route_parameters' => 'json',
        'requested_at'     => 'datetime',
    ];

    protected $allowed_content_types = [
        'application/json',
        'text/xml',
    ];

    public function fillFromRequest(Request $request) : static
    {
        $this->uri             = $request->getUri();
        $this->method          = $request->method();
        $this->query           = $request->query->all();
        $this->request_headers = $request->headers->all();
        $this->request_body    = $this->mapBody($request->getContent(), $request->headers);

        return $this;
    }

    public function fillFromResponse(Response $response) : static
    {
        $this->response_headers = $response->headers->all();
        $this->response_body    = $this->mapBody($response->getContent(), $response->headers);
        $this->response_status  = $response->getStatusCode();
        $this->response_phrase  = Response::$statusTexts[$response->getStatusCode()] ?? 'Unknown';
        $this->total_time       = (int)((microtime(true) - LARAVEL_START) * 1000);

        return $this;
    }

    public function fillFromRoute(Route $route) : static
    {
        $this->route_name       = $route->getName();
        $this->route_uri        = $route->uri();
        $this->route_parameters = $route->parameters();

        $action = $route->getAction('uses');
        if (is_string($action)) {
            $this->route_action = $action;
        } elseif ($action instanceof \Closure) {
            $this->setActionFromClosure($action);
        }

        return $this;
    }

    protected function setActionFromClosure(\Closure $closure)
    {
        $rff = new \ReflectionFunction($closure);

        $file  = \Str::replaceFirst(base_path(), '', $rff->getFileName());
        $lines = implode(':', [$rff->getStartLine(), $rff->getEndLine()]);

        $this->route_action = "$file:$lines";
    }

    protected function mapBody($body, HeaderBag $headers) : string
    {
        $content_type = $headers->get('Content-Type');

        foreach ($this->allowed_content_types as $allowed_content_type) {
            if (str_contains($content_type, $allowed_content_type)) {
                return substr($body, 0, static::CONTENT_MAX);
            }
        }

        $length = $headers->get('Content-Length') ?: mb_strlen($body);

        return sprintf('Blocked Content-Type: %s | %s', $content_type, Bytes::reduce($length));
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionName()
    {
        return Config::get('utils.drivers.input_log.connection');
    }

    /**
     * {@inheritdoc}
     */
    public function getTable(): string
    {
        return Config::get('utils.drivers.input_log.table') ?: parent::getTable();
    }

    public function user() {
        return $this->morphTo('user');
    }

    public function audits() : HasMany|Audit {
        return $this->hasMany(Audit::class,'session_uuid','session_uuid');
    }

    public function clientLogs() : HasMany|ClientLog {
        return $this->hasMany(ClientLog::class,'session_uuid','session_uuid');
    }
}
