<?php

namespace Tests\Drivers;

use Goez\RedisDataHelper\Drivers\SortedSetsDriver;
use PHPUnit_Framework_TestCase as TestCase;
use Tests\InitTestRedisClient;

class SortedSetsDriverTest extends TestCase
{
    use InitTestRedisClient;

    protected $keyPrefix = 'testing:';

    /**
     * @test
     */
    public function it_should_add_list_with_given_key()
    {
        $key = $this->assembleKey('example');

        $list = [
            '111111' => 4,
            '222222' => 2,
            '333333' => 6,
        ];
        $expected = [
            '"222222"',
            '"111111"',
            '"333333"',
        ];

        $driver = new SortedSetsDriver($this->testRedisClient);
        $driver->key($key)->addList($list);

        $actual = $this->testRedisClient->zcard($key);
        $this->assertEquals(3, $actual);

        $actual = $this->testRedisClient->zrange($key, 0, -1);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_scored_list_with_given_key()
    {
        $key = $this->assembleKey('example');

        $list = [
            '"111111"' => 4,
            '"222222"' => 2,
            '"333333"' => 6,
        ];
        $expected = [
            '222222' => 2,
            '111111' => 4,
            '333333' => 6,
        ];

        $this->testRedisClient->zadd($key, $list);

        $driver = new SortedSetsDriver($this->testRedisClient);
        $actual = $driver->key($key)->getList();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_partial_list_with_given_key()
    {
        $key = $this->assembleKey('example');

        $list = [
            '"111111"' => 4,
            '"222222"' => 2,
            '"333333"' => 6,
        ];
        $expected = [
            '111111' => 4,
            '333333' => 6,
        ];

        $this->testRedisClient->zadd($key, $list);

        $driver = new SortedSetsDriver($this->testRedisClient);
        $actual = $driver->key($key)->getList(2);

        $this->assertEquals($expected, $actual);
    }
}
