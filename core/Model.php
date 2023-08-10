<?php

namespace app\core;

use mysqli;

class Model
{
    const MIN_HASH_LEN = 2;
    const LINK_PREFIX = '/go?q=';

    protected mysqli $connection;

    /**
     * @param array $dbConfig
     */
    public function __construct(array $dbConfig)
    {
        if (array_diff(['host', 'user', 'password', 'database'], array_keys($dbConfig))) {
            throw new Exception('Неверный формат конфигурации');
        }

        $this->connection = new mysqli(
            $dbConfig["host"],
            $dbConfig["user"],
            $dbConfig["password"],
            $dbConfig["database"]
        );
    }

    /**
     * @throws Exception
     * @return string
     */
    public function generateHash(): string
    {
        return '';
    }

    /**
     * @param string $hash
     * @return array|null
     */
    public function findByHash(string $hash): ?array
    {
        return $this
            ->connection
            ->query("select * from link where hash = '{$hash}'")
            ->fetch_assoc();
    }

    /**
     * @param string $key
     * @return array|null
     */
    public function findBySecretKey(string $key): ?array
    {
        $hash = $this->getHashFromSecretKey($key);
        return $this->findByHash($hash);
    }

    /**
     * @param string $hash
     * @return string
     */
    public function getSecretKeyFromHash(string $hash): string
    {
        return base64_encode($hash);
    }

    /**
     * @param string $key
     * @return string
     */
    public function getHashFromSecretKey(string $key): string
    {
        return base64_decode($key);
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function updateCounterByHash(string $hash): bool
    {
        return $this
            ->connection
            ->query("update link set counter = counter + 1 where hash = '{$hash}'");
    }

    /**
     * @param string $hash
     * @return string
     */
    public function getShortlinkFromHash(string $hash): string
    {
        return '//' . $_SERVER['SERVER_NAME'] . self::LINK_PREFIX . $hash;
    }

    /**
     * @param string $url
     * @return array
     */
    public function generateNew(string $url): array
    {
        return [];
    }
}