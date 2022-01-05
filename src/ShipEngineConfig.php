<?php

namespace BluefynInternational\ShipEngine;

use BluefynInternational\ShipEngine\Util\Assert;
use DateInterval;
use Exception;
use Illuminate\Contracts\Support\Arrayable;

final class ShipEngineConfig implements \JsonSerializable, Arrayable
{
    public string $apiKey;

    public string $baseUrl;

    public int $pageSize;

    public int $retries;

    public bool $asObject = false;

    public DateInterval $timeout;

    /**
     * @param array $config [apiKey:string, baseUrl:string, pageSize:int, retries:int, timeout:DateInterval|string]
     *
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        $assert = new Assert();
        $api_key = $config['apiKey'] ?? config('shipengine.credentials.key');
        $assert->isApiKeyValid($api_key);
        $this->apiKey = $api_key;

        $retries = $config['retries'] ?? config('shipengine.retries', 1);
        $assert->isRetriesValid($retries);
        $this->retries = $retries;

        $timeout_value = $config['timeout'] ?? new DateInterval(config('shipengine.timeout', 'PT10S'));
        if (is_string($timeout_value)) {
            $timeout_value = new DateInterval($timeout_value);
        }
        $assert->isTimeoutValid($timeout_value);
        $this->timeout = $timeout_value;

        $this->asObject = boolval($config['asObject'] ?? config('shipengine.response.as_object', false));

        $this->baseUrl = $config['baseUrl'] ?? self::getBaseUri();
        $this->pageSize = $config['pageSize'] ?? config('shipengine.response.page_size', 50);
    }

    /**
     * Merge in method level config into the global config used by the **ShipEngine** object.
     *
     * @param array|ShipEngineConfig|null $newConfig
     *
     * @return $this
     * @throws Exception
     */
    public function merge(array|ShipEngineConfig|null $newConfig = null): ShipEngineConfig
    {
        if ($newConfig instanceof ShipEngineConfig) {
            $newConfig = $newConfig->toArray();
        }

        if (empty($newConfig)) {
            return $this;
        }

        $config = array_merge($this->toArray(), $newConfig);

        return new ShipEngineConfig($config);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize() : array
    {
        return [
            'apiKey' => $this->apiKey,
            'baseUrl' => $this->baseUrl,
            'pageSize' => $this->pageSize,
            'retries' => $this->retries,
            'timeout' => $this->timeout->s,
            'asObject' => $this->asObject,
        ];
    }

    public static function getBaseUri() : string
    {
        return config('shipengine.endpoint.base', 'https://api.shipengine.com/');
    }

    public function toArray()
    {
        return [
            'apiKey' => $this->apiKey,
            'baseUrl' => $this->baseUrl,
            'pageSize' => $this->pageSize,
            'retries' => $this->retries,
            'timeout' => $this->timeout,
            'asObject' => $this->asObject,
        ];
    }
}
