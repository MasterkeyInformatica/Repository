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
    /**
     * @param   Cache  $cache
     */
    public function setCache(Cache $cache);

    /**
     * @return Cache
     */
    public function getCache() : Cache;

    /**
     * @param   CacheKeyStorage  $keyStorage
     */
    public function setKeyStorage(CacheKeyStorage $keyStorage);

    /**
     * @param   string  $method
     * @param   null|mixed  $args
     */
    public function getCacheKey($method, $args = null);

    /**
     * @return int
     */
    public function getCacheMinutes() : int;

    /**
     * @param   bool  $status
     */
    public function skipCache(bool $status = true);
}