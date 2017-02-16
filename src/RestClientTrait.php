<?php declare(strict_types=1);

namespace Terah\RestClient;

trait RestClientTrait
{
    /** @var RestClient */
    protected $restClient;

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
