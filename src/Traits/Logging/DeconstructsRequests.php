<?php
namespace CoolRunner\Utils\Traits\Logging;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\HeaderBag;

trait DeconstructsRequests
{
    /** @see json_decode() */
    public function responseJson(?bool $associative = true, int $depth = 512, int $flags = 0)
    {
        return json_decode($this->response_body, $associative, $depth, $flags);
    }

    /** @see json_decode() */
    public function requestJson(?bool $associative = true, int $depth = 512, int $flags = 0)
    {
        return json_decode($this->request_body, $associative, $depth, $flags);
    }

    /** @see simplexml_load_string() */
    public function responseXml(?string $class_name = "SimpleXMLElement", int $options = 0, string $namespace_or_prefix = "", bool $is_prefix = false) : \SimpleXMLElement|null
    {
        return @simplexml_load_string($this->response_body, $class_name, $options, $namespace_or_prefix, $is_prefix) ?: null;
    }

    /** @see simplexml_load_string() */
    public function requestXml(?string $class_name = "SimpleXMLElement", int $options = 0, string $namespace_or_prefix = "", bool $is_prefix = false) : \SimpleXMLElement|null
    {
        return @simplexml_load_string($this->response_body, $class_name, $options, $namespace_or_prefix, $is_prefix) ?: null;
    }

    public function responseBody() : ?string
    {
        return $this->response_body;
    }

    public function requestBody() : ?string
    {
        return $this->request_body;
    }

    public function responseHeaders() : HeaderBag
    {
        return new HeaderBag($this->response_headers);
    }

    public function requestHeaders() : HeaderBag
    {
        return new HeaderBag($this->request_headers);
    }
}
