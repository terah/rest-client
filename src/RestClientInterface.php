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

    /**
     * @param string
     * @return mixed
     */
    public function post(string $entity='');

    /**
     * @param string $entity
     * @return mixed
     */
    public function get(string $entity='');

    /**
     * @param string $entity
     * @return mixed
     */
    public function put(string $entity='');

    /**
     * @param string $entity
     * @return mixed
     */
    public function delete(string $entity='');

    /**
     * @param string $entity
     * @return mixed
     */
    public function sendRequest(string $entity='');

    /**
     * @param string $entity
     * @return mixed
     */
    public function getRawRequest(string $entity='');


    public function getResponse(string $entity='', bool $ignoreErrors=false) : RestResponseInterface;


    public function getPreBuiltResponse(bool $multi=false) : RestResponseInterface;


    public function buildRequest(string $entity='') : RestClientInterface;

    /**
     * @param bool $isMulti
     * @return mixed
     */
    public function curlExec(bool $isMulti=false);


    public function parseResponse(string $response, int $httpStatusCode, string $curlError, int $curlErrorNo) : RestResponseInterface;

    /**
     * @param int $opt
     * @param mixed $val
     * @return RestClientInterface
     */
    public function setCurlOpt(int $opt, $val) : RestClientInterface;


    public function setCurlBasicAuth() : RestClientInterface;


    public function setCurlCookies() : RestClientInterface;


    public function setCurlData() : RestClientInterface;

}