<?php

namespace Masterkey\Repository\Cache;

/**
 * CacheKeyStorage
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   14/03/2018
 * @package Masterkey\Repository\Cache
 */
class CacheKeyStorage
{
    /**
     * @var string
     */
    protected $storagePath;

    /**
     * @var string
     */
    protected $storageFile = 'repository-cache-keys.json';

    /**
     * @var null|array
     */
    protected $storedKeys = null;

    /**
     * @param   string  $storagePath
     */
    public function __construct(string $storagePath)
    {
        $this->storagePath = $storagePath;

        $this->loadKeys();
    }

    /**
     * @param   string  $group
     * @param   string  $key
     * @return  bool|int
     */
    public function storeKey(string $group, string $key)
    {
        $this->storedKeys[$group] = $this->readKeys($group);

        if ( ! in_array($key, $this->storedKeys[$group]) ) {
            $this->storedKeys[$group][] = $key;
        }

        return $this->writeKeys();
    }

    /**
     * @return  array|null
     */
    private function loadKeys()
    {
        if ( ! is_null($this->storedKeys) && is_array($this->storedKeys) ) {
            return $this->storedKeys;
        }

        $file = $this->getStorageFilePath();

        if ( ! file_exists($file) ) {
            $this->writeKeys();
        }

        $content = file_get_contents($file);

        $this->storedKeys = json_decode($content, true);
    }

    /**
     * @param   string  $group
     * @return  mixed
     */
    public function readKeys(string $group)
    {
        $this->storedKeys[$group] = $this->storedKeys[$group] ?? [];

        return $this->storedKeys[$group];
    }

    /**
     * @return  bool|int
     */
    private function writeKeys() : bool
    {
        $file = $this->getStorageFilePath();

        $keys       = $this->storedKeys ?? [];
        $content    = json_encode($keys, true);

        return file_put_contents($file, $content);
    }

    /**
     * @return  string
     */
    private function getStorageFilePath() : string
    {
        return $this->storagePath . '/' . $this->storageFile;
    }
}