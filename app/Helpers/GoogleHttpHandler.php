<?php

namespace App\Helpers;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;

/**
 * Custom HTTP Handler untuk Google API
 * Force disable SSL verification di level curl
 */
class GoogleHttpHandler
{
    public static function createHandler()
    {
        // Create curl handler dengan SSL disabled
        $handler = new CurlHandler();
        
        // Wrap dengan custom middleware yang force disable SSL
        $stack = HandlerStack::create($handler);
        
        $stack->push(self::getSSLDisableMiddleware(), 'disable_ssl');
        
        return $stack;
    }
    
    private static function getSSLDisableMiddleware()
    {
        return function (callable $handler) {
            return function ($request, array $options) use ($handler) {
                // Force disable SSL verification
                $options['verify'] = false;
                $options['curl'] = array_merge(
                    $options['curl'] ?? [],
                    [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_MAXREDIRS => 5,
                    ]
                );
                
                return $handler($request, $options);
            };
        };
    }
    
    public static function createClient()
    {
        return new HttpClient([
            'handler' => self::createHandler(),
            'verify' => false,
            'http_errors' => false,
            'timeout' => 60,
            'connect_timeout' => 30,
        ]);
    }
}
