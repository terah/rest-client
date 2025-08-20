<?php declare(strict_types=1);

namespace Terah\RestClient;

use Closure;
use Terah\Assert\Assert;

class RestClientMulti implements RestClientMultiInterface
{
    /** @var resource */
    protected $curlMultiObject  = null;

    /** @var RestClientInterface[] */
    protected array $restClients = [];

    public function __construct()
    {
        $this->curlMultiObject  = curl_multi_init();
    }


    public function addClient(RestClientInterface $restClient) : RestClientMultiInterface
    {
        $handle                 = $restClient->getHandle();
        $id                     = print_r($handle, true);
        $this->restClients[$id] = $restClient;
        curl_multi_add_handle($this->curlMultiObject, $handle);

        return $this;
    }


    /**
     * @return array|RestResponseInterface[]
     */
    public function execAll() : array
    {
        do
        {
            $status = curl_multi_exec($this->curlMultiObject, $running);
        }
        while ( $status === CURLM_CALL_MULTI_PERFORM || $running );

        $responses              = [];
        foreach ( $this->restClients as $restClient )
        {
            $curlObj                = $restClient->getHandle();
            $responses[]            = $restClient->getPreBuiltResponse(true);
            curl_multi_remove_handle($this->curlMultiObject, $curlObj);
            curl_close($curlObj);
        }

        return $responses;
    }


    public function execAndApplyCallback(Closure $callback) : bool
    {
        do {
            while ( ( $status = curl_multi_exec($this->curlMultiObject, $running) ) == CURLM_CALL_MULTI_PERFORM );
            if ( $status != CURLM_OK )
            {
                break;
            }
            // a request was just completed -- find out which one
            while ( $done = curl_multi_info_read($this->curlMultiObject) )
            {
                $id                     = print_r($done['handle'], true);
                Assert::that($this->restClients)->keyExists($id, "Could not find a rest client for the resource id {$id}");
                $restClient             = $this->restClients[$id];

                $curlObj                = $restClient->getHandle();
                $response               = $restClient->getPreBuiltResponse(true);
                curl_multi_remove_handle($this->curlMultiObject, $done['handle']);
                curl_close($curlObj);
                $callback($response);
            }
        }
        while ($running);

        return true;
    }


    function rolling_curl(array $urls, Closure $callback, ?array $custom_options = null) : bool
    {

        // make sure the rolling window isn't greater than the # of urls
        $rolling_window         = 5;
        $rolling_window         = (sizeof($urls) < $rolling_window) ? sizeof($urls) : $rolling_window;

        $master                 = curl_multi_init();

        // add additional curl options here
        $std_options            = [
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_MAXREDIRS       => 5
        ];
        $options                = ($custom_options) ? ($std_options + $custom_options) : $std_options;

        // start the first batch of requests
        for ( $i = 0; $i < $rolling_window; $i++ )
        {
            $ch                     = curl_init();
            $options[CURLOPT_URL]   = $urls[$i];
            curl_setopt_array($ch,$options);
            curl_multi_add_handle($master, $ch);
        }

        do {
            while ( ( $execrun = curl_multi_exec($master, $running) ) == CURLM_CALL_MULTI_PERFORM );
            if ( $execrun != CURLM_OK )
            {
                break;
            }

            // a request was just completed -- find out which one
            while( $done = curl_multi_info_read($master) )
            {
                $info                   = curl_getinfo($done['handle']);
                if ( $info['http_code'] == 200 )
                {
                    $output                 = curl_multi_getcontent($done['handle']);

                    // request successful.  process output using the callback function.
                    $callback($output);

                    // start a new request (it's important to do this before removing the old one)
                    $ch                     = curl_init();
                    $options[CURLOPT_URL]   = $urls[$i++];  // increment i
                    curl_setopt_array($ch,$options);
                    curl_multi_add_handle($master, $ch);

                    // remove the curl handle that just completed
                    curl_multi_remove_handle($master, $done['handle']);
                }
                else
                {
                // request failed.  add error handling.
                }
            }
        } while ($running);

        curl_multi_close($master);

        return true;
    }


    public function destroy() : void
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