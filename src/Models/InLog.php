<?php


namespace CoolRunner\Utils\Models;
use CoolRunner\Utils\Jobs\SaveInLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * OutLog.
 *
 * @property int $id
 * @property string $uuid
 * @property string $prefix
 * @property string $method
 * @property string $headers
 * @property string $app_name
 * @property string|null $request
 * @property string|null $response
 * @property string|null $route
 * @property string|null $route_name
 * @property string|null $request_content_type
 * @property int|null $response_code
 * @property int|null $time
 * @property int|null $auth_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */

class InLog extends Model
{
    protected $table = 'in_logs';

    protected $casts = ['headers' => 'json'];

    protected $fillable = ['*'];

    protected $appends = [
        'jsonResponse',
        'xmlResponse',
        'jsonRequest',
        'xmlRequest',
    ];

    protected $blocked_content_types = [
        'text/html; charset=UTF-8',
        'application/pdf',
        'image/jpg',
        'image/jpeg',
        'image/png',
        'image/bmp',
        'image/svg',
        'image/tiff',
    ];

    public function getConnectionName()
    {
        return config('utils.model_connection');
    }

    /*
     * for job to not create new.
     */

    public function mergeInputs($log, $log2){
        $properties = array_merge($log->toArray(), $log2->toArray());
        foreach ($this->appends as $append){
            unset($properties[$append]);
        }

        foreach ($properties as $key => $value){
            $this->setAttribute($key, $value);
        }
    }

    public static function create(Request $request)
    {
        $route = \Route::getRoutes()->match($request);

        $log = new static();

        $log->method = $request->method();
        $log->prefix = $route->getPrefix();

        $log->route      = $request->getRequestUri();
        $log->route_name = $route->getName();

        $log->headers              = $request->headers->all();
        $log->request_content_type = static::getContentType($request);
        $log->request              = static::filterRequest($request);
        $log->uuid                 = Str::uuid();
        $log->created_at           = now();
        $log->app_name = env('APP_NAME');
        SaveInLog::dispatch($log);

        return $log;
    }

    public static function getContentType(Request $request)
    {
        $content_Type = $request->header('Content-Type') ?: null;

        if(Str::contains($request->header('content-type'), 'multipart/form-data')) {
            $content_Type = 'multipart/form-data';
        }

        return $content_Type;
    }
    public static function filterRequest(Request $request)
    {
        if ($request->header('Content-Type') === 'application/json') {
            $content = json_decode($request->getContent(), true);
            $content = is_array($content) ? $content : [$content];
            $content = strip_keys(['password', 'password_confirmation', 'img_base64'], $content);

            $content = json_encode($content);

            if (Str::length($content) > 250000) {
                $content = substr($content, 0, 100000); // Limit message size for SQS queue
            }

            return $content;
        }

        if ($request->header('Content-Type') === 'text/xml' ||
            $request->header('Content-Type') === 'application/xml') {
            try {
                $content = json_decode(json_encode(new \SimpleXMLElement($request->getContent())), true);
                $content = is_array($content) ? $content : [$content];
                $content = strip_tags(['password', 'password_confirmation', 'img_base64'], $content);

                return json_encode($content);
            } catch (\Throwable $exception) {
            }
        }

        if (Str::contains($request->header('content-type'), 'multipart/form-data')) {
            return json_encode([
                'input' => $request->input(),
                'files' => collect($request->files->all())->map(function (UploadedFile $file) {
                    return [
                        'name'     => $file->getClientOriginalName(),
                        'mime'     => $file->getMimeType(),
                        'size_raw' => $file->getSize(),
                    ];
                }),
            ]);
        }

        return $request->getContent();
    }
    /**
     * @param Response $response
     * @param int      $time
     * @param bool     $async
     *
     * @return $this
     * @author Morten K. Harders 🐢 <mh@coolrunner.dk>
     */
    public function setResponse($response, $time, $async = true)
    {
        $this->response              = $response->getContent();
        $this->response_code         = $response->getStatusCode();
        $this->response_content_type = $response->headers->get('Content-Type');

        if (in_array($content_type = $response->headers->get('Content-Type'), $this->blocked_content_types)) {
            $this->response = sprintf("Content blocked: $content_type | %s bytes", mb_strlen($this->response));
        }

        $this->auth_id = Auth::id()?: null;
        $this->time    = $time;

        SaveInLog::dispatch($this);

        return $this;
    }

    public function getJsonResponseAttribute()
    {
        return json_decode($this->response, true);
    }

    public function getXmlResponseAttribute()
    {
        try {
            return new \SimpleXMLElement($this->response);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    public function getJsonRequestAttribute()
    {
        return json_decode($this->request, true);
    }

    public function getXmlRequestAttribute()
    {
        try {
            return new \SimpleXMLElement($this->request);
        } catch (\Throwable $exception) {
            return null;
        }
    }



}