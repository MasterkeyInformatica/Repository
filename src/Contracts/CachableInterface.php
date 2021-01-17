<?php

namespace Masterkey\Repository\Contracts;

use Illuminate\Cache\Repository as Cache;
use Masterkey\Repository\Cache\CacheKeyStorage;

/**
 * CachableContract
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @since   24/04/2018
 * @package Masterkey\Repository\Contracts
 */
interface CachableInterface
{
    public function setCache(Cache $cache);

    public function getCache() : Cache;

    public function setKeyStorage(CacheKeyStorage $keyStorage);

    public function getCacheKey(string $method, $args = null);

    public function getCacheMinutes() : int;

    public function skipCache(bool $status = true);
}