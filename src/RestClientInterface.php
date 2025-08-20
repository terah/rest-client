<?php declare(strict_types=1);

namespace Terah\RestClient;


interface RestClientInterface
{
    /**
     * @return resource
     */
    public function getHandle();


    public function header(string $name, string $value) : RestClientInterface;


    public function headers(array $headers) : RestClientInterface;


    public function data(array $data) : RestClientInterface;


    public function dataObject($data) : RestClientInterface;


    public function credentials(string $username, string $password) : RestClientInterface;


    public function reset() : RestClientInterface;


    public function method(string $method) : RestClientInterface;


    public function format(string $format, string $contentType='') : RestClientInterface;


    public function accept(string $format) : RestClientInterface;


    public function contentType(string $format) : RestClientInterface;


    public function version(string $version) : RestClientInterface;


    public function verbose(bool $verbose=true) : RestClientInterface;


    public function ignoreErrors(bool $ignore=true) : RestClientInterface;


    public function exception(string $exception) : RestClientInterface;


    public function responseType(string $responseType) : RestClientInterface;


    public function post(string $entity='') : mixed;

  
    public function get(string $entity='') : mixed;

    
    public function put(string $entity='') : mixed;

  
    public function delete(string $entity='') : mixed;


    public function sendRequest(string $entity='') : mixed;


    public function getRawRequest(string $entity='') : mixed;


    public function getResponse(string $entity='', bool $ignoreErrors=false) : RestResponseInterface;


    public function getPreBuiltResponse(bool $multi=false) : RestResponseInterface;


    public function buildRequest(string $entity='') : RestClientInterface;


    public function curlExec(bool $isMulti=false) : mixed;


    public function parseResponse(string $response, int $httpStatusCode, string $curlError, int $curlErrorNo) : RestResponseInterface;


    public function setCurlOpt(int $opt, mixed $val) : RestClientInterface;


    public function setCurlBasicAuth() : RestClientInterface;


    public function setCurlCookies() : RestClientInterface;


    public function setCurlData() : RestClientInterface;

}