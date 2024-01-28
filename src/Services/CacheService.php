<?php

namespace App\Services;

use Predis\Client;

class CacheService
{
    public function __construct(
        protected Client $client)
    {}

    /**
     * Set data into redis for list
     * @param string $key
     * @param $value
     * @return void
     */
    public function cacheList(string $key, $value): void
    {
        $this->client->lpush($key, $value);
    }


    /**
     * @param string $key
     * @return array
     */
    public function getList(string $key): array
    {
        return $this->client->lrange($key, 0, -1);
    }

    /**
     * Set data into redis for single value
     * @param string $key
     * @param $value
     * @return void
     */
    public function cacheData(string $key, $value): void
    {
        $this->client->set($key, $value);
    }

    public function cacheValues(array $data) : void
    {
        foreach ($data as $key => $value) {
            $this->cacheData($key, $value);
        }
    }

    /**
     * Set data into redis for single value
     * @param string $key
     * @return mixed
     */
    public function getData( string $key): mixed
    {
        return $this->client->get($key);
    }

    public function deleteList(array $key): void
    {
        $this->client->del($key);
    }
}
