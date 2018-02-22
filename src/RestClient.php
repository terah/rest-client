<?php declare(strict_types=1);

namespace Terah\RestClient;

use function Terah\Assert\Assert;

class RestClient implements RestClientInterface
{
    const FETCH             = 'GET';
    const INSERT            = 'POST';
    const UPDATE            = 'PUT';
    const GET               = 'GET';
    const POST              = 'POST';
    const PUT               = 'PUT';
    const DELETE            = 'DELETE';

    /** @var int $serviceUrl */
    protected $serviceUrl   = '';

    /** @var string $accessToken */
    protected $accessToken  = '';

    /** @var string $authHeader */
    protected $authHeader   = 'X-Auth-Token';

    /** @var string $method */
    protected $method       = 'GET';

    /** @var string $accept */
    protected $accept       = 'json';

    /** @var string $contentType */
    protected $contentType  = 'json';

    /** @var string $version */
    protected $version      = '1.0';

    /** @var array $headers */
    protected $headers      = [];

    /** @var array $data */
    protected $data         = [];

    /** @var bool  */
    protected $verbose      = false;

    /** @var array */
    protected $credentials  = [];

    /** @var resource */
    protected $curlObj      = null;

    /** @var string */
    protected $exceptionType = 'Terah\RestClient\RestException';

    /** @var null Used for error tracing */
    protected $curlUrl      = '';

    /** @var mixed Data to be sent to the service */
    protected $curlData     = null;

    /** @var RestResponse */
    protected $response     = null;

    /** @var bool */
    protected $ignoreErrors = false;

    /** @var array */
    protected $formats      = [
        'json'                  => [['application/json', 'application/x-json'], 'application/json'],
        'xml'                   => [['text/xml', 'application/xml', 'application/x-xml'], 'application/xml'],
        'txt'                   => [ ['text/plain'], 'application/x-www-form-urlencoded'],
        'html'                  => [['text/html', 'application/xhtml+xml'], 'application/x-www-form-urlencoded'],
        'png'                   => [['image/png'], 'image/png'],
        'any'                   => [['*/*'], 'application/x-www-form-urlencoded'],
        'js'                    => [['application/javascript', 'application/x-javascript', 'text/javascript'], 'application/javascript'],
        'css'                   => [['text/css'], 'text/css'],
        'rdf'                   => [['application/rdf+xml'], 'application/rdf+xml'],
        'atom'                  => [['application/atom+xml'], 'application/atom+xml'],
        'rss'                   => [['application/rss+xml'], 'application/rss+xml'],
    ];

    /**
     * @param string $serviceUrl
     * @param string $accessToken
     * @param string $authHeader
     * @param string $username
     * @param string $password
     */
    public function __construct(string $serviceUrl, string $accessToken='', string $authHeader='', string $username='', string $password='')
    {
        Assert($serviceUrl)->notEmpty('The service URL was not specified.')->url('The service URL is not a URL.');
        $this->serviceUrl       = $serviceUrl;//preg_replace('/\/$/', '', $serviceUrl) . '/';
        $this->accessToken      = $accessToken;
        $this->authHeader       = $authHeader ?: $this->authHeader;
        $this->credentials($username, $password);
        $this->reset();
    }

    /**
     * @return resource
     */
    public function getHandle()
    {
        return $this->curlObj;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return RestClientInterface
     */
    public function header(string $name, string $value) : RestClientInterface
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param array $headers
     *
     * @return RestClientInterface
     */
    public function headers(array $headers) : RestClientInterface
    {
        foreach ( $headers as $name => $value )
        {
            $this->header($name, $value);
        }

        return $this;
    }

    /**
     * @param array $data
     *
     * @return RestClientInterface
     */
    public function data(array $data) : RestClientInterface
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param string $username
     * @param string $password
     * @return RestClientInterface
     */
    public function credentials(string $username, string $password) : RestClientInterface
    {
        $this->credentials = [$username, $password];

        return $this;
    }

    /**
     * @return RestClientInterface
     */
    public function reset() : RestClientInterface
    {
        $this->data         = [];
        $this->method       = 'GET';
        $this->curlObj      = null;
        $this->response     = null;
        $this->ignoreErrors = false;
        $this->format('json');
        $this->header('X-Api-Version', '1.0');

        return $this;
    }

    /**
     * @param $method
     *
     * @return RestClientInterface
     */
    public function method($method) : RestClientInterface
    {
        $method         = strtoupper($method);
        $this->method   = in_array($method, ['GET', 'POST', 'PUT', 'DELETE']) ? $method : 'GET';

        return $this;
    }

    /**
     * @param string $format
     * @param string $contentType
     * @return RestClientInterface
     */
    public function format(string $format, string $contentType='') : RestClientInterface
    {
        $contentType    = $contentType ?: $format
        ;
        return $this->accept($format)->contentType($contentType);
    }

    /**
     * @param string $format
     *
     * @return RestClientInterface
     */
    public function accept(string $format) : RestClientInterface
    {
        $this->accept = ! array_key_exists($format, $this->formats) ? 'json' : $format;

        return $this;
    }

    /**
     * @param string $format
     *
     * @return RestClientInterface
     */
    public function contentType(string $format) : RestClientInterface
    {
        $this->contentType = ! array_key_exists($format, $this->formats) ? 'json' : $format;
        return $this;
    }

    /**
     * @param string $version
     *
     * @return RestClientInterface
     */
    public function version(string $version) : RestClientInterface
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @param bool $verbose
     *
     * @return RestClientInterface
     */
    public function verbose(bool $verbose=true) : RestClientInterface
    {
        $this->verbose = (bool)$verbose;

        return $this;
    }

    /**
     * @param bool $ignore
     * @return RestClientInterface
     */
    public function ignoreErrors(bool $ignore=true) : RestClientInterface
    {
        $this->ignoreErrors = $ignore;

        return $this;
    }

    /**
     * @param string $exception
     * @return RestClientInterface
     */
    public function exception(string $exception) : RestClientInterface
    {
        $this->exceptionType = $exception;

        return $this;
    }

    /**
     * @param string
     * @return mixed
     */
    public function post(string $entity='')
    {
        return $this->method('post')->sendRequest($entity);
    }

    /**
     * @param string $entity
     * @return mixed
     */
    public function get(string $entity='')
    {
        return $this->method('get')->sendRequest($entity);
    }

    /**
     * @param string $entity
     * @return mixed
     */
    public function put(string $entity='')
    {
        return $this->method('put')->sendRequest($entity);
    }

    /**
     * @param string $entity
     * @return mixed
     */
    public function delete(string $entity='')
    {
        return $this->method('delete')->sendRequest($entity);
    }

    /**
     * @param string $entity
     * @return mixed
     */
    public function sendRequest(string $entity='')
    {
        $result = $this
            ->buildRequest($entity)
            ->curlExec();
        $this->reset();

        return $result;
    }

    /**
     * @param string $entity
     * @return mixed
     */
    public function getRawRequest(string $entity='')
    {
        $this
            ->ignoreErrors(true)
            ->setCurlOpt(CURLINFO_HEADER_OUT, true)
            ->buildRequest($entity)
            ->curlExec();
        $rawRequest = $this->getCurlInfo(CURLINFO_HEADER_OUT);
        $this->reset();

        return $rawRequest;
    }

    /**
     * @param string $entity
     * @return RestResponseInterface
     */
    /**
     * @param string $entity
     * @param bool $ignoreErrors
     * @return RestResponseInterface
     */
    public function getResponse(string $entity='', bool $ignoreErrors=false) : RestResponseInterface
    {
        if ( $ignoreErrors )
        {
            $this
                ->ignoreErrors($ignoreErrors)
                ->setCurlOpt(CURLINFO_HEADER_OUT, true);
        }
        $this
            ->buildRequest($entity)
            ->curlExec();
        $response = $this->response;
        $this->reset();

        return $response;
    }

    /**
     * @param bool $multi
     * @return RestResponseInterface
     */
    public function getPreBuiltResponse(bool $multi=false) : RestResponseInterface
    {
        $this
            ->curlExec($multi);
        $response = $this->response;
        $this->reset();

        return $response;
    }

    /**
     * @param string $entity
     * @return RestClientInterface
     */
    public function buildRequest(string $entity='') : RestClientInterface
    {
        $this
            ->setCurlOpt(CURLOPT_URL, $this->getUrl($entity))
            ->setCurlOpt(CURLOPT_HTTPHEADER, $this->getHeaders())
            ->setCurlOpt(CURLOPT_HEADER, 1)
            ->setCurlOpt(CURLOPT_RETURNTRANSFER, true)
            ->setCurlOpt(CURLOPT_VERBOSE, $this->verbose ? 1 : 0)
            ->setCurlOpt(CURLOPT_SSL_VERIFYHOST, false)
            ->setCurlOpt(CURLOPT_SSL_VERIFYPEER, false)
            ->setCurlOpt(CURLOPT_CUSTOMREQUEST, $this->method)
            ->setCurlOpt(CURLOPT_ENCODING, '')
            ->setCurlBasicAuth()
            ->setCurlCookies()
            ->setCurlData();

        return $this;
    }

    /**
     * @param int $opt
     * @param mixed $val
     * @return RestClientInterface
     */
    public function setCurlOpt($opt, $val) : RestClientInterface
    {
        $this->curlObj = is_null($this->curlObj) ? curl_init() : $this->curlObj;
        curl_setopt($this->curlObj, $opt, $val);
        return $this;
    }

    /**
     * @param int $opt
     * @return mixed
     */
    protected function getCurlInfo($opt)
    {
        return curl_getinfo($this->curlObj, $opt);
    }

    /**
     * @return RestClientInterface
     */
    public function setCurlData() : RestClientInterface
    {
        if ( ! in_array(strtoupper($this->method), ['POST', 'PUT']) )
        {
            return $this;
        }
        if ( $this->contentType === 'json' )
        {
            $this->curlData = ! is_string($this->data) ? json_encode($this->data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) : $this->data;
            return $this->setCurlOpt(CURLOPT_POST, true)->setCurlOpt(CURLOPT_POSTFIELDS, $this->curlData);
        }
        if ( $this->contentType === 'xml' )
        {
            if ( is_array($this->data) )
            {
                $xmlObj = new \SimpleXMLElement("<?xml version=\"1.0\"?><response></response>");
                $this->arrayToXml($this->data, $xmlObj);
                $this->curlData   = $xmlObj->asXML();
            }
            return $this->setCurlOpt(CURLOPT_POST, true)->setCurlOpt(CURLOPT_POSTFIELDS, $this->curlData);
        }
        $this->curlData = is_array($this->data) ? http_build_query($this->data) : $this->data;

        return $this->setCurlOpt(CURLOPT_POST, true)->setCurlOpt(CURLOPT_POSTFIELDS, $this->curlData);
    }

    /**
     * @return RestClientInterface
     */
    public function setCurlBasicAuth() : RestClientInterface
    {
        list($username, $password) = $this->credentials;
        if ( ! empty($username) )
        {
            return $this->setCurlOpt(CURLOPT_USERPWD, "{$username}:{$password}");
        }

        return $this;
    }

    /**
     * @return RestClientInterface
     */
    public function setCurlCookies() : RestClientInterface
    {
        // This allows me to debug api request originating from the phpunit test case
        if ( ! empty($_SERVER['PHP_IDE_CONFIG']) && empty($_SERVER['XDEBUG_CONFIG']) )
        {
            return $this->setCurlOpt(CURLOPT_COOKIE, "XDEBUG_SESSION=PHPSTORM");
        }

        return $this;
    }

    /**
     * @param array $data
     * @param \SimpleXMLElement $xmlObj
     */
    protected function arrayToXml($data, &$xmlObj)
    {
        foreach ( $data as $key => $value )
        {
            if ( ! is_array($value) )
            {
                $xmlObj->addChild("$key", htmlspecialchars("$value"));
                continue;
            }
            if ( ! is_numeric($key) )
            {
                $subnode = $xmlObj->addChild("$key");
                $this->arrayToXml($value, $subnode);
                continue;
            }
            $subnode = $xmlObj->addChild("item$key");
            $this->arrayToXml($value, $subnode);
        }
    }

    /**
     * @param bool $isMulti
     * @return mixed
     */
    public function curlExec(bool $isMulti=false)
    {
        $response           = $isMulti ? curl_multi_getcontent($this->curlObj) : curl_exec($this->curlObj);
        $httpCode           = curl_getinfo($this->curlObj, CURLINFO_HTTP_CODE);
        $curlError          = curl_error($this->curlObj);
        $curlErrNo          = curl_errno($this->curlObj);
        $this->response     = $this->parseResponse((string)$response, $httpCode, $curlError, $curlErrNo);
        if ( ! $this->ignoreErrors && $this->response->isError() )
        {
            $exceptionType      = $this->exceptionType;

            throw new $exceptionType($this->response->getNotification(), $this->response->getHttpStatusCode(), null, $this->response);
        }

        return $this->response->body;
    }

    /**
     * @param string $response
     * @param int $httpStatusCode
     * @param string $curlError
     * @param int $curlErrorNo
     * @return RestResponseInterface
     */
    public function parseResponse(string $response, int $httpStatusCode, string $curlError, int $curlErrorNo) : RestResponseInterface
    {
        list($headers, $body)       = $this->parseHeadersAndBody($response);
        $response                   = [
            'method'                    => $this->method,
            'status'                    => $httpStatusCode,
            'body'                      => $body,
            'headers'                   => $headers,
            'curlError'                 => $curlError,
            'curlErrorNo'               => $curlErrorNo,
        ];
        $contentType                = $this->parseContentType($headers, $this->formats[$this->accept][0][0]);
        switch ( $contentType )
        {
            case 'json':

                $response['body']   = json_decode($response['body'], false);
                break;
            case 'xml':

                $xml                = simplexml_load_string($response['body'], 'SimpleXMLElement', LIBXML_NOCDATA);
                $response['body']   = $this->xml2array($xml);
                break;
        }

        return $this->getResponseObj()->setArray($response);
    }

    /**
     * @param string $responseText
     * @return array
     */
    protected function parseHeadersAndBody(string $responseText) : array
    {
        if ( ! $responseText )
        {
            return [[], ''];
        }
        $responseText               = trim(preg_replace('/^HTTP\/[0-9. ]+Continue/', '', $responseText));
        $parts                      = explode("\r\n\r\n", $responseText, 2);
        list($headerData, $body)    = empty($parts[1]) ? [$parts[0], ''] : $parts;
        unset($responseText);
        $headerData                 = explode("\r\n", $headerData);
        $headers                    = [];
        foreach ( $headerData as $line )
        {
            $line               = trim($line);
            if ( strpos($line, ':') === false )
            {
                $headers[$line]     = $line;
                continue;
            }
            list($name, $text)  = explode(':', trim($line), 2);
            $headers[trim($name)]     = trim($text);
        }

        return [$headers, $body];
    }

    /**
     * @param  array $headers
     * @param  string $defaultContentType
     * @return string  string
     */
    protected function parseContentType(array $headers, string $defaultContentType) : string
    {
        foreach ( $headers as $header => $value )
        {
            if ( strtolower($header) === 'content-type' )
            {
                $defaultContentType = $value;
                break;
            }
        }

        // Split on ";" to remove charset
        if ( strpos($defaultContentType, ";") !== false )
        {
            $defaultContentType = substr($defaultContentType, 0, strpos($defaultContentType, ";"));
        }

        foreach ( $this->formats as $type => $contentTypes )
        {
            foreach ( $contentTypes[0] as $contentType )
            {
                if ( stripos($contentType, $defaultContentType) !== false )
                {
                    return $type;
                }
            }
        }

        return '';
    }


    /**
     * @param $entity
     * @return string
     */
    protected function getUrl(string $entity) : string
    {
        $entity         = preg_replace('/^\//', '', $entity);
        $this->curlUrl  = $this->serviceUrl . $entity;

        if ( $this->method === 'GET' && ! empty($this->data) )
        {
            $this->curlUrl = rtrim($this->curlUrl, '?') . '?' . http_build_query($this->data);
        }

        return $this->curlUrl;
    }

    /**
     * @return array
     */
    protected function getHeaders() : array
    {
        $this->header('Accept', $this->formats[$this->accept][0][0]);
        $this->header('Content-Type', $this->formats[$this->contentType][1]);
        if ( $this->accessToken )
        {
            $this->header($this->authHeader, $this->accessToken);
        }
        $headers = [];
        foreach ( $this->headers as $name => $value )
        {
            if ( $value )
            {
                $headers[$name] = "{$name}:{$value}";
            }
        }

        return $headers;
    }

    /**
     * @return RestResponseInterface
     */
    protected function getResponseObj() : RestResponseInterface
    {
        return new RestResponse();
    }

    /**
     * @param \SimpleXMLElement $xmlObject
     * @param array $out
     * @return array
     */
    protected function xml2array(\SimpleXMLElement $xmlObject, array $out=[]) : array
    {
        foreach( $xmlObject->attributes() as $attr => $val )
        {
            $out['@attributes'][$attr] = (string)$val;
        }
        $hasChildren = false;
        foreach( $xmlObject as $index => $node )
        {
            $hasChildren     = true;
            $out[$index][]  = $this->xml2array($node);
        }
        if ( ! $hasChildren && $val = (string)$xmlObject )
        {
            $out['@value'] = $val;
        }
        foreach ( $out as $key => $vals )
        {
            if ( is_array($vals) && count($vals) === 1 && array_key_exists(0, $vals) )
            {
                $out[$key] = $vals[0];
            }
        }

        return $out;
    }
}

