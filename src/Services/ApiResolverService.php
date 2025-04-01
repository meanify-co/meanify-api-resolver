<?php

namespace Meanify\ApiResolver\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ApiResolverService
{
    protected $host;
    protected $api_key;
    protected $constant_headers;
    public $METHOD_GET    = 'GET';
    public $METHOD_POST   = 'POST';
    public $METHOD_PUT    = 'PUT';
    public $METHOD_PATCH  = 'PATCH';
    public $METHOD_DELETE = 'DELETE';

    /**
     * @param string|null $host
     * @param string|null $api_key
     */
    public function __construct(?string $host = null, ?string $api_key = null, array $constant_headers = [])
    {
        $this->host             = $host ?? config('meanify-api-resolver.host');
        $this->api_key          = $api_key ?? config('meanify-api-resolver.api_key');
        $this->constant_headers = $constant_headers ?? config('meanify-api-resolver.constant_headers');

        return $this;
    }

    /**
     * @notes Return API Key
     *
     * @return string
     */
    protected function getApiKey()
    {
        return $this->api_key;
    }


    /**
     * @calledBy *
     *
     * @notes Return base path of API endpoints
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->host;
    }

    /**
     * @calledBy *
     *
     * @notes Return token to requests at API
     *
     * @return string
     */
    protected function getAuthorization()
    {
        return 'Bearer '.$this->getApiKey();
    }

    /**
     * @return mixed|\stdClass
     *
     * @throws \Exception
     */
    public function request($method, $uri, $params_for_form_data = [], $params_for_query_string = [], array $options = [])
    {
        try
        {
            $is_multipart = $options['multipart'] ?? false;
            $user_token   = $options['user_token'] ?? null;
            $method       = strtoupper($method);

            //--------------------------------------------------------------------------------------------------------//
            // Format params as queryString
            //--------------------------------------------------------------------------------------------------------//
            $query_string = '';

            foreach ($params_for_query_string as $param => $value)
            {
                if ($query_string == '')
                {
                    $query_string .= '?';
                }
                else
                {
                    $query_string .= '&';
                }

                $query_string .= $param.'='.$value;
            }

            //--------------------------------------------------------------------------------------------------------//
            // Set headers
            //--------------------------------------------------------------------------------------------------------//
            $headers = array_merge($this->constant_headers, [
                'Accept'                    => 'application/json',
                'Authorization'             => $this->getAuthorization(),
                'User-Agent'                => request()->header('User-Agent'),
                'x-mfy-ip-address'          => request()->getClientIp(),
                'x-mfy-user-token'          => $user_token,
            ]);

            //--------------------------------------------------------------------------------------------------------//
            // Instance guzzle client
            //--------------------------------------------------------------------------------------------------------//
            $guzzle = new Client(['verify' => false]);

            if ($method == 'GET')
            {
                $original_result = $guzzle->request($method, $this->getBasePath().$uri.$query_string, [
                    'headers'  => $headers,
                ]);

                $status_code = $original_result->getStatusCode();

                $original_body  = json_decode($original_result->getBody());
            }
            else
            {
                if($is_multipart)
                {
                    $original_result = $guzzle->request($method, $this->getBasePath().$uri.$query_string, [
                        'headers'  => $headers,
                        'multipart' => $params_for_form_data,
                    ]);
                }
                else
                {
                    $original_result = $guzzle->request($method, $this->getBasePath().$uri.$query_string, [
                        'headers'     => $headers,
                        'form_params' => $params_for_form_data,
                    ]);
                }

                $status_code = $original_result->getStatusCode();

                $original_body  = json_decode($original_result->getBody());
            }
        }
        catch (\GuzzleHttp\Exception\RequestException $e)
        {
            abort($e->getCode(), $e->getMessage());
        }

        return $original_body;
    }
}