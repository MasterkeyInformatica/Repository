<?php

namespace Masterkey\Repository\Listeners;

use Illuminate\Cache\Repository as Cache;
use Illuminate\Support\Facades\Config;
use Masterkey\Repository\Cache\CacheKeyStorage;
use Masterkey\Repository\Events\BaseRepositoryEvent;

/**
 * ClearRepositoryCache
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @since   15/03/2018
 * @package Masterkey\Repository\Listeners
 */
class ClearRepositoryCache
{
    protected Cache $cache;

    protected CacheKeyStorage $keyStorage;

    public function __construct(Cache $cache, CacheKeyStorage $keyStorage)
    {
        $this->cache      = $cache;
        $this->keyStorage = $keyStorage;
    }

    public function handle(BaseRepositoryEvent $event)
    {
        $cleanEnabled = Config::get('repository.cache.clean.enabled', true);

        if ($cleanEnabled) {
            $repository = $event->getRepository();
            $action     = $event->getAction();

            if (Config::get("repository.cache.clean.on.{$action}", true)) {
                $keys = $this->keyStorage->readKeys(get_class($repository));

                if (is_array($keys)) {
                    foreach ($keys as $key) {
                        $this->cache->forget($key);
                    }
                }
            }
        }
    }
}