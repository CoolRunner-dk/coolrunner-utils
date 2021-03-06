<?php

namespace CoolRunner\Utils\Managers;

use GuzzleHttp\Client;
use Illuminate\Support\Manager;
use Psr\Http\Message\UriInterface;

class GuzzleManager extends Manager
{
    /**
     * The registered custom client configs.
     *
     * @var array[]
     */
    protected $registeredConfigs = [];

    /**
     * @param string $baseUri
     * @param array $config
     *
     * @return Client
     */
    public function make(string $baseUri = null, array $config = []) : Client
    {
        if ($baseUri !== null) {
            $config['base_uri'] = $baseUri;
        }

        return $this->createClient($config);
    }

    public function client(?string $identifier = null): Client
    {
        return $this->driver($identifier);
    }

    public function getDefaultDriver(): string
    {
        return $this->config->get('utils.guzzle.default_client');
    }

    public function register(string $identifier, array $config): self
    {
        $this->registeredConfigs[$identifier] = $config;

        return $this;
    }

    protected function createDriver($driver): Client
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        } elseif (isset($this->registeredConfigs[$driver])) {
            return $this->createClient(array_merge(
                $this->registeredConfigs[$driver],
                $this->config->get('utils.guzzle.clients.'.$driver, [])
            ));
        } elseif ($this->config->has('utils.guzzle.clients.'.$driver)) {
            return $this->createClient(
                $this->config->get('utils.guzzle.clients.'.$driver)
            );
        }

        throw new \InvalidArgumentException("Client [$driver] not supported.");
    }

    protected function callCustomCreator($driver): Client
    {
        return $this->customCreators[$driver](
            $this->container,
            $this->prepareConfig($this->config->get('utils.guzzle.clients.'.$driver, []))
        );
    }

    protected function prepareConfig(array $config): array
    {
        return array_merge(
            $this->config->get('utils.guzzle.default_config', []),
            $config
        );
    }

    protected function createClient(array $config): Client
    {
        return new Client($this->prepareConfig($config));
    }
}
