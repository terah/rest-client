<?php declare(strict_types=1);

namespace Terah\RestClient;

class RestClientMulti
{
    /** @var resource */
    protected $curlMultiObj = null;

    /** @var RestClient[] */
    protected $restClients  = [];

    /** @var resource[] */
    protected $curlObjs     = [];

    public function __construct()
    {
        $this->curlMultiObj = curl_multi_init();
    }

    /**
     * @param RestClient $restClient
     * @return $this
     */
    public function addClient(RestClient $restClient)
    {
        $this->restClients[]    = $restClient;
        curl_multi_add_handle($this->curlMultiObj, $restClient->getHandle());

        return $this;
    }

    /**
     * @return RestResponse[]
     */
    public function execAll()
    {
        do {
            $status = curl_multi_exec($this->curlMultiObj, $running);
        }
        while ( $status === CURLM_CALL_MULTI_PERFORM || $running );

        $responses = [];
        foreach ( $this->restClients as $restClient )
        {
            $curlObj        = $restClient->getHandle();
            $responses[]    = $restClient->getPreBuiltResponse(true);
            curl_multi_remove_handle($this->curlMultiObj, $curlObj);
            curl_close($curlObj);
        }

        return $responses;
    }

    public function destroy()
    {
        if ( $this->curlMultiObj )
        {
            curl_multi_close($this->curlMultiObj);
            $this->curlMultiObj = null;
        }
        $this->restClients = [];
    }

    public function __destruct()
    {
        $this->destroy();
    }
}