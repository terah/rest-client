<?php declare(strict_types=1);

namespace Terah\RestClient;


interface RestClientInterface
{
    /**
     * @return resource
     */
    public function getHandle();

    /**
     * @param $name
     * @param $value
     *
     * @return RestClientInterface
     */
    public function header(string $name, string $value) : RestClientInterface;

    /**
     * @param array $headers
     *
     * @return RestClientInterface
     */
    public function headers(array $headers) : RestClientInterface;

    /**
     * @param array $data
     *
     * @return RestClientInterface
     */
    public function data(array $data) : RestClientInterface;

    /**
     * @param string $username
     * @param string $password
     * @return RestClientInterface
     */
    public function credentials(string $username, string $password) : RestClientInterface;

    /**
     * @return RestClientInterface
     */
    public function reset() : RestClientInterface;

    /**
     * @param $method
     *
     * @return RestClientInterface
     */
    public function method($method) : RestClientInterface;

    /**
     * @param string $format
     * @param string $contentType
     * @return RestClientInterface
     */
    public function format(string $format, string $contentType='') : RestClientInterface;

    /**
     * @param string $format
     *
     * @return RestClientInterface
     */
    public function accept(string $format) : RestClientInterface;

    /**
     * @param string $format
     *
     * @return RestClientInterface
     */
    public function contentType(string $format) : RestClientInterface;

    /**
     * @param string $version
     *
     * @return RestClientInterface
     */
    public function version(string $version) : RestClientInterface;

    /**
     * @param bool $verbose
     *
     * @return RestClientInterface
     */
    public function verbose(bool $verbose=true) : RestClientInterface;

    /**
     * @param bool $ignore
     * @return RestClientInterface
     */
    public function ignoreErrors(bool $ignore=true) : RestClientInterface;

    /**
     * @param string $exception
     * @return RestClientInterface
     */
    public function exception(string $exception) : RestClientInterface;

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

    /**
     * @param string $entity
     * @param bool $ignoreErrors
     * @return RestResponseInterface
     */
    public function getResponse(string $entity='', bool $ignoreErrors=false) : RestResponseInterface;

    /**
     * @param bool $multi
     * @return RestResponseInterface
     */
    public function getPreBuiltResponse(bool $multi=false) : RestResponseInterface;

    /**
     * @param string $entity
     * @return RestClientInterface
     */
    public function buildRequest(string $entity='') : RestClientInterface;

    /**
     * @param bool $isMulti
     * @return mixed
     */
    public function curlExec(bool $isMulti=false);

    /**
     * @param string $response
     * @param int $httpStatusCode
     * @param string $curlError
     * @param int $curlErrorNo
     * @return RestResponseInterface
     */
    public function parseResponse(string $response, int $httpStatusCode, string $curlError, int $curlErrorNo) : RestResponseInterface;

    /**
     * @param int $opt
     * @param mixed $val
     * @return RestClientInterface
     */
    public function setCurlOpt($opt, $val) : RestClientInterface;

    /**
     * @return RestClientInterface
     */
    public function setCurlBasicAuth() : RestClientInterface;

    /**
     * @return RestClientInterface
     */
    public function setCurlCookies() : RestClientInterface;

    /**
     * @return RestClientInterface
     */
    public function setCurlData() : RestClientInterface;

}