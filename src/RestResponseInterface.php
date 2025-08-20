<?php declare(strict_types=1);

namespace Terah\RestClient;

interface RestResponseInterface
{
    public function setArray(array $data) : RestResponseInterface;


    public function __call(string $name, array $args) : RestResponseInterface;


    public function set(string $name, mixed $value) : RestResponseInterface;


    public function __get(string $name) : mixed;

 
    public function get(string $name) : mixed;


    public function toArray() : array;


    public function jsonSerialize() : array;


    public function getHttpStatusCode() : int;


    public function isError() : bool;


    public function getNotification() : string;

    
    public function getBody() : mixed;

}