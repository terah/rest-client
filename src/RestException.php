<?php declare(strict_types=1);

namespace Terah\RestClient;

use Exception;

class RestException extends Exception
{
    protected RestResponse $response;

    public function __construct(string $message, int $code, ?Exception $previous, RestResponse $response)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }


    public function getResponse() : RestResponse
    {
        return $this->response;
    }
}