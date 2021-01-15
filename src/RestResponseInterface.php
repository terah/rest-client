<?php declare(strict_types=1);

namespace Terah\RestClient;

interface RestResponseInterface
{
    public function setArray(array $data) : RestResponseInterface;


    public function __call(string $name, array $args) : RestResponseInterface;

    /**
     * @param string $name
     * @param mixed $value
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


    public function toArray() : array;


    public function jsonSerialize() : array;


    public function getHttpStatusCode() : int;


    public function isError() : bool;


    public function getNotification() : string;


    /**
     * @return mixed
     */
    public function getBody();

}