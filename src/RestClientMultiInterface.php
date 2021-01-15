<?php declare(strict_types=1);

namespace Terah\RestClient;


interface RestClientMultiInterface
{
    public function addClient(RestClientInterface $restClient) : RestClientMultiInterface;


    /**
     * @return RestResponseInterface[]
     */
    public function execAll() : array;

    public function destroy();
}