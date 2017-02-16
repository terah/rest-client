<?php declare(strict_types=1);

namespace Terah\RestClient;


interface RestClientMultiInterface
{
    /**
     * @param RestClientInterface $restClient
     * @return RestClientMultiInterface
     */
    public function addClient(RestClientInterface $restClient) : RestClientMultiInterface;
    /**
     * @return RestResponseInterface[]
     */
    public function execAll();

    public function destroy();
}