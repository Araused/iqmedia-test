<?php

namespace app\core;

use mysqli;

class Model
{
    const SALT = 'PHP_Rulez!';

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
    public function generateHash()
    {
        return '';
    }

    /**
     * @param string $hash
     * @return array|null
     */
    public function findByHash(string $hash)
    {
        return $this
            ->connection
            ->query("select * from link where hash = '{$hash}'")
            ->fetch_assoc();
    }

    /**
     * @param string $hash
     * @return string
     */
    public function getSecretKeyFromHash(string $hash)
    {
        return '';
    }

    /**
     * @param string $key
     * @return string
     */
    public function getHashFromSecretKey(string $key)
    {
        return '';
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function updateCounterByHash(string $hash)
    {
        return $this
            ->connection
            ->query("update link set counter = counter + 1 where hash = '{$hash}'");
    }

    /**
     * @param string $url
     * @return array
     */
    public function generateNew(string $url)
    {
        return [];
    }
}