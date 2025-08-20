<?php declare(strict_types=1);

namespace Terah\RestClient;

trait RestClientTrait
{
    protected RestClientInterface $restClient;

    public function setRestClient(RestClientInterface $restClient) : static
    {
        $this->restClient = $restClient;

        return $this;
    }


    public function getRestClient() : RestClientInterface
    {
        return $this->restClient;
    }
}
