<?php


namespace App\Helpers;


use App\Exceptions\Fetch\RequestFailed;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Trait Fetchable
 * @package App\Helpers
 */
trait Fetchable
{
    /**
     * @var string
     */
    protected string $root;
    /**
     * @var ClientInterface
     */
    protected ClientInterface $client;
    /**
     * @var bool
     */
    protected bool $secure = true;
    /**
     * @var int
     */
    protected int $port = 443;

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @return string
     */
    public function getSchema(): string
    {
        return $this->secure ? 'https://' : 'http://';
    }

    /**
     * @param string $endpoint
     * @param string $method
     * @param array $headers
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function fetch(string $endpoint, string $method = 'GET', array $headers = []): ResponseInterface
    {
        return $this->client->request($method, $this->constructUrl($this->getSchema(), $this->root .':'. $this->port, $endpoint), [
            'headers' => $headers,
        ]);
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function getJsonBodyArray(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param ...$parts
     * @return string
     */
    protected function constructUrl(...$parts): string
    {
        return implode('/', array_map(function ($part) {
            return preg_replace(["/^\//", "/\/$/"], '', $part);
        }, $parts));
    }
}
