<?php

declare(strict_types=1);

namespace Game\Service;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class AIApi
{
    private Client $client;
    private string $key;
    private string $url;

    public function __construct(
        string $key,
        string $url 
    ) {
        $this->key = $key;
        $this->url = trim($url, ' /');
        $this->client = new Client([
            "verify" => false
        ]);
    }

    public function get(array $content = []) : void
    {
        $data = [
            "model" => "deepseek-chat", 
            "messages" => [
                ["role" => "system", "content" => "You are a helpful assistant."],
                ["role" => "user", "content" => "Hello!"]
            ],
            "stream" => false,
            ...$content
        ];
        try {
            $this->client->get($this->url . "/chat/completions", [
                RequestOptions::QUERY => $data,
                RequestOptions::HEADERS => ["Authorization" => "Bearer {$this->key}"]
            ]);
        } catch (Exception $e) {
            dump($e);
        }
    }
}