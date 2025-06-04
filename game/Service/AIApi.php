<?php

declare(strict_types=1);

namespace Game\Service;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
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
            'model' => 'deepseek-chat', // или другая модель, если требуется
            'messages' => [
                ['role' => 'user', 'content' => 'Привет! Как дела?']
            ],
            'temperature' => 0.7,  // опциональные параметры
            'max_tokens' => 1000,
            ...$content
        ];
        try {
            $response = $this->client->post($this->url . "/chat/completions", [
                RequestOptions::JSON => $data,
                RequestOptions::HEADERS => [
                    "Content-Type" => "application/json",
                    "Authorization" => "Bearer {$this->key}"
                ]
            ]);
            $responseData = $response->getBody();
            dd($responseData);
        } catch (Exception $e) {
            dump($e);
            
        } catch (ClientException|RequestException $e) {
            dump($e->getBody());
        }
    }
}