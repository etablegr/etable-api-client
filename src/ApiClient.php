<?php

namespace Etable;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class ApiClient extends Client
{
    const BASE_URI = 'https://api.e-table.gr';

    const LANGUAGE = 'en';

    const NAME = 'yrizos/etable-api-client';

    const TIMEOUT = 30;

    const VERSION = '0.7';

    private $timeout = self::TIMEOUT;

    public function __construct(array $config = [])
    {
        $token    = isset($config['token']) ? $config['token'] : '';
        $language = isset($config['language']) ? $config['language'] : self::LANGUAGE;
        $timeout  = isset($config['timeout']) && is_numeric($config['timeout']) ? intval($config['timeout']) : self::TIMEOUT;

        unset($config['token'], $config['language'], $config['timeout']);

        $this->setTimeout($timeout);

        if (!isset($config['base_uri'])) {
            $config['base_uri'] = self::BASE_URI;
        }

        $headers                    = isset($config['headers']) && is_array($config['headers']) ? $config['headers'] : [];
        $headers['User-Agent']      = self::getUserAgent();
        $headers['Authorization']   = 'Bearer ' . $token;
        $headers['Accept-Language'] = $language;
        $headers['Accept']          = 'application/json';

        $config['headers'] = $headers;

        parent::__construct($config);
    }

    public static function getArrayResponse(ResponseInterface $response)
    {
        $response = json_decode($response->getBody(), TRUE);
        $response = isset($response['data']) ? $response['data'] : [];

        return $response;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public static function getUserAgent()
    {
        return self::NAME . '/' . self::VERSION . ' (+https://github.com/yrizos/etable-api-client)';
    }

    public function request(
        $method,
        $uri = '',
        array $options = []
    )
    {
        if (!isset($options['timeout'])) {
            $options['timeout'] = $this->getTimeout();
        }

        return parent::request($method, $uri, $options);
    }

    public function setTimeout(int $timeout): ApiClient
    {
        $this->timeout = $timeout;

        return $this;
    }
}
