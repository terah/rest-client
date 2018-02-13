<?php declare(strict_types=1);

namespace Terah\RestClient;

class RestClientMulti implements RestClientMultiInterface
{
    /** @var resource */
    protected $curlMultiObject  = null;

    /** @var RestClientInterface[] */
    protected $restClients      = [];

    public function __construct()
    {
        $this->curlMultiObject = curl_multi_init();
    }

    /**
     * @param RestClientInterface $restClient
     * @return RestClientMultiInterface
     */
    public function addClient(RestClientInterface $restClient) : RestClientMultiInterface
    {
        $this->restClients[]    = $restClient;
        curl_multi_add_handle($this->curlMultiObject, $restClient->getHandle());

        return $this;
    }

    /**
     * @return RestResponseInterface[]
     */
    public function execAll()
    {
        do
        {
            $status = curl_multi_exec($this->curlMultiObject, $running);
        }
        while ( $status === CURLM_CALL_MULTI_PERFORM || $running );

        $responses = [];
        foreach ( $this->restClients as $restClient )
        {
            $curlObj        = $restClient->getHandle();
            $responses[]    = $restClient->getPreBuiltResponse(true);
            curl_multi_remove_handle($this->curlMultiObject, $curlObj);
            curl_close($curlObj);
        }

        return $responses;
    }

    public function destroy()
    {
        if ( $this->curlMultiObject )
        {
            curl_multi_close($this->curlMultiObject);
            $this->curlMultiObject = null;
        }
        $this->restClients = [];
    }

    public function __destruct()
    {
        $this->destroy();
    }
}