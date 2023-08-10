<?php

namespace app\core;

use mysqli;

class Model
{
    const MIN_HASH_LEN = 2;
    const INCREASE_LEN_STEP = 5;
    const LINK_PREFIX_GO = '/go?q=';
    const LINK_PREFIX_STATS = '/stat?key=';
    const ALLOWED_CHARS_FOR_HASH = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

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
     * @param integer $strength
     * @return string
     */
    public function getRandomString(int $strength = self::MIN_HASH_LEN): string
    {
        $charsLib = self::ALLOWED_CHARS_FOR_HASH;
        $inputLength = strlen($charsLib);
        $result = '';

        for ($i = 0; $i < $strength; $i++) {
            $randomChar = $charsLib[mt_rand(0, $inputLength - 1)];
            $result .= $randomChar;
        }

        return $result;
    }

    /**
     * @param int|null $len
     * @param int $step
     * @throws Exception
     * @return string
     */
    public function generateHash(?int $len = null, int $step = 0): string
    {
        if ($len === null) {
            $len = self::MIN_HASH_LEN;
        }

        $result = $this->getRandomString($len);

        if ($this->findByHash($result) !== null) {
            return $this->generateHash(
                $step < self::INCREASE_LEN_STEP ? $len : $len + 1,
                $step < self::INCREASE_LEN_STEP ? $step : 0
            );
        }

        return $result;
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
        return '//' . $_SERVER['SERVER_NAME'] . self::LINK_PREFIX_GO . $hash;
    }

    /**
     * @param string $hash
     * @return string
     */
    public function getStatsLinkFromHash(string $hash): string
    {
        return '//' . $_SERVER['SERVER_NAME'] . self::LINK_PREFIX_STATS . $this->getSecretKeyFromHash($hash);
    }

    /**
     * @param string $url
     * @return array
     */
    public function generateNew(string $url): array
    {
        $hash = $this->generateHash();
        $url = $this->connection->real_escape_string($url);

//        var_dump("insert into `link` ('hash', 'landing', 'counter') values ('{$hash}', '{$url}', 0)");die;
        $result = $this
            ->connection
            ->query("insert into `link` (`hash`, `landing`) values ('{$hash}', '{$url}')");

        return $result ? [
            'hash' => $hash,
            'landing' => $url,
        ] : [];
    }
}