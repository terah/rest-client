<?php declare(strict_types=1);

namespace Terah\RestClient;

trait RestClientTrait
{
    protected RestClientInterface $restClient;

    /**
     * Sets a cache.
     *
     * @param RestClientInterface $restClient
     * @return $this
     */
    public function setRestClient(RestClientInterface $restClient)
    {
        $this->restClient = $restClient;

        return $this;
    }

    /**
     * Gets a client.
     *
     * @return RestClientInterface
     */
    public function getRestClient() : RestClientInterface
    {
        return $this->restClient;
    }
}
