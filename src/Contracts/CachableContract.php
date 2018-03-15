<?php

namespace Masterkey\Repository\Contracts;

use Illuminate\Contracts\Cache\Repository as Cache;
use Masterkey\Repository\Cache\CacheKeyStorage;

/**
 * CachableContract
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   14/03/2018
 * @package Masterkey\Repository\Contracts
 */
interface CachableContract
{
    public function setCache(Cache $cache);

    public function getCache() : Cache;

    public function setKeyStorage(CacheKeyStorage $keyStorage);

    public function getCacheKey($method, $args = null);

    public function getCacheMinutes() : int;

    public function skipCache(bool $status = true);
}