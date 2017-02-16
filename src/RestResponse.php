<?php declare(strict_types=1);

namespace Terah\RestClient;

use function Terah\Assert\Assert;

/**
 * Class RestResponse
 *
 * @package Terah\RestClient
 * @property string method
 * @property int status
 * @property string body
 * @property array headers
 * @property string curlError
 * @property int curlErrorNo
 */
class RestResponse implements \JsonSerializable, RestResponseInterface
{
    /**
     * @var array
     */
    protected $_meta_data   = [
        'method'                => '',
        'status'                => 200,
        'body'                  => null,
        'headers'               => [],
        'curlError'             => '',
        'curlErrorNo'           => null,
    ];

    /**
     * @param array $data
     */
    public function __construct(array $data=[])
    {
        $this->setArray($data);
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->_meta_data['body'];
    }

    /**
     * @param array $data
     * @return RestResponseInterface
     */
    public function setArray(array $data) : RestResponseInterface
    {
        foreach ( $data as $name => $value )
        {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param array $args
     * @return RestResponseInterface
     */
    public function __call(string $name, array $args) : RestResponseInterface
    {
        return $this->set($name, $args[0]);
    }

    /**
     * @param string $name
     * @param $value
     * @return RestResponseInterface
     */
    public function set(string $name, $value) : RestResponseInterface
    {
        // We don't want this throwing exceptions in invalid keys
        // as this maybe used inside the RestException class and
        // exceptions inside exceptions is a bit to hard to grok
        if ( ! array_key_exists($name, $this->_meta_data) )
        {
            return $this;
        }
        // Don't set it if it's not the same type
        if ( ! is_null($this->_meta_data[$name]) && gettype($this->_meta_data[$name]) !== gettype($value) )
        {
            return $this;
        }
        $this->_meta_data[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name)
    {
        Assert($this->_meta_data)->keyExists($name, "Invalid property ({$name}) sent to response meta");

        return $this->_meta_data[$name];
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return $this->_meta_data;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->_meta_data;
    }


    /**
     * @return int
     */
    public function getHttpStatusCode() : int
    {
        return $this->_meta_data['status'];
    }

    /**
     * @return bool
     */
    public function isError() : bool
    {
        return $this->curlErrorNo || $this->status > 300;
    }

    /**
     * @return string
     */
    public function getNotification() : string
    {
        $httpMessages = preg_grep('/^HTTP/', array_keys($this->_meta_data['headers']));
        if ( empty($httpMessages) )
        {
            return (string)$this->status;
        }

        return $httpMessages[0];
    }
}