<?php

namespace Goez\RedisDataHelper\Drivers;

/**
 * Class Sets
 * @package Goez\RedisDataHelper\DataDrivers
 */
class SetsDriver extends AbstractDriver
{
    /**
     * @param array $list
     */
    public function addList(array $list)
    {
        $this->client->sadd($this->key, array_map(function ($value) {
            return json_encode((string)$value);
        }, $list));
    }

    /**
     * @param int $count
     * @return array
     */
    public function getList($count = -1)
    {
        if ($count >= 0) {
            $result = $this->client->sscan($this->key, 0, ['count' => $count]);
            $list = isset($result[1]) ? $result[1] : [];
            $list = array_slice($list, 0, $count);
        } else {
            $list = $this->client->smembers($this->key);
        }
        return array_map(function ($item) {
            return json_decode($item, true);
        }, $list);
    }

    /**
     * @return int
     */
    public function count()
    {
        return (int)$this->client->scard($this->key);
    }

    /**
     * @param int $count
     */
    public function pop($count = 1)
    {
        $this->client->spop($this->key, $count);
    }

    /**
     * @param $member
     * @return bool
     */
    public function has($member)
    {
        return (bool)$this->client->sismember($this->key, json_encode($member));
    }

    /**
     * @param $member
     * @return int
     */
    public function remove($member)
    {
        return $this->client->srem($this->key, json_encode($member));
    }
}
