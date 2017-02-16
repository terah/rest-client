<?php declare(strict_types=1);

namespace Terah\RestClient;

interface RestResponseInterface
{
    public function setArray(array $data) : RestResponseInterface;
    /**
     * @param string $name
     * @param array $args
     * @return RestResponseInterface
     */
    public function __call(string $name, array $args) : RestResponseInterface;
    /**
     * @param string $name
     * @param $value
     * @return RestResponseInterface
     */
    public function set(string $name, $value) : RestResponseInterface;
    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name);
    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name);
    /**
     * @return array
     */
    public function toArray() : array;
    /**
     * @return array
     */
    public function jsonSerialize();
    /**
     * @return int
     */
    public function getHttpStatusCode() : int;
    /**
     * @return bool
     */
    public function isError() : bool;
    /**
     * @return string
     */
    public function getNotification() : string;

    /**
     * @return mixed
     */
    public function getBody();

}