<?php

use Meanify\ApiResolver\Services\ApiResolverService;

if (!function_exists('meanify_api_resolver'))
{
    /**
     * @param string|null $host
     * @param string|null $api_key
     * @param array $constant_headers
     * @return ApiResolverService
     */
    function meanify_api_resolver(?string $host = null, ?string $api_key = null, array $constant_headers = []): ApiResolverService
    {
        return new ApiResolverService($host, $api_key, $constant_headers);
    }
}
