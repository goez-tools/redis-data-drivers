<?php

namespace Tests\Drivers;

use Goez\RedisDataHelper\Drivers\MultiDriver;
use PHPUnit_Framework_TestCase as TestCase;
use Tests\InitTestRedisClient;

class MultiDriverTest extends TestCase
{
    use InitTestRedisClient;

    /**
     * @var string
     */
    protected $keyPrefix = 'testing:';

    /**
     * @test
     */
    public function it_should_delete_nothing()
    {
        $driver = new MultiDriver($this->testRedisClient);
        $count = $driver->key('nothing')->delete();
        $this->assertEquals(0, $count);
    }

    /**
     * @test
     */
    public function it_should_delete_keys()
    {
        $keyPattern = $this->assembleKey('*');
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $result = $this->testRedisClient->mget($this->testRedisClient->keys($keyPattern));
        $this->assertCount(3, $result);

        $driver = new MultiDriver($this->testRedisClient);
        $count = $driver->key($keyPattern)->delete();
        $this->assertEquals(3, $count);
    }

    /**
     * @test
     */
    public function it_should_get_multiple_value_with_key_pattern()
    {
        $keyPattern = $this->assembleKey('*');
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $expected = [1, 2, 3,];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $actual = $driver->key($keyPattern)->get();
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_multiple_value_with_key_array()
    {
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $expected = [1, 2, 3,];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($keys)->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_an_empty_array()
    {
        $expected = [];
        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_an_empty_array_with_a_not_existing_key()
    {
        $expected = [];
        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key('example:*')->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_null_array()
    {
        $expected = [null, null, null,];
        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key([
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ])->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_keys_with_wildcard_string()
    {
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $expected = [
            'testing:abc',
            'testing:def',
            'testing:ghi',
        ];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key('testing:*')->keys();

        sort($expected);
        sort($result);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_dictionary_with_key_array()
    {
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $expected = [
            'testing:abc' => 1,
            'testing:def' => 2,
            'testing:ghi' => 3,
        ];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($keys)->withKey()->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_dictionary_with_wildcard_string()
    {
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $expected = [
            'testing:abc' => 1,
            'testing:def' => 2,
            'testing:ghi' => 3,
        ];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key('testing:*')->withKey()->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_keys_count()
    {
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $expected = 3;

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($keys)->count();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_zero()
    {
        $keys = [];
        $expected = 0;

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($keys)->count();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_one()
    {
        $key = $this->assembleKey('example');
        $expected = 1;

        $this->testRedisClient->set($key, 1);

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($key)->count();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_find_keys_by_scan()
    {
        $keyPattern = $this->assembleKey('*');
        $key1 = $this->assembleKey('abc');
        $key2 = $this->assembleKey('def');
        $key3 = $this->assembleKey('ghi');
        $keys = [
            $key1,
            $key2,
            $key3,
        ];
        $expected = $keys;
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $cursor = '0';
        $actual = [];
        do {
            list($cursor, $results) = $driver->key($keyPattern)->scan($cursor, 1);
            foreach ($results as $key) {
                $actual[] = $key;
            }
        } while ($cursor !== '0');
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_scan_all_keys()
    {
        $keyPattern = $this->assembleKey('*');
        $key1 = $this->assembleKey('abc');
        $key2 = $this->assembleKey('def');
        $key3 = $this->assembleKey('ghi');
        $keys = [
            $key1,
            $key2,
            $key3,
        ];
        $expected = $keys;
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $actual = $driver->key($keyPattern)->scanAll(1);
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_scan_nothing_if_pattern_not_found()
    {
        $keyPattern = $this->assembleKey('http');
        $key1 = $this->assembleKey('www');
        $key2 = $this->assembleKey('com');
        $keys = [
            $key1,
            $key2,
        ];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $expected = [];
        $driver = new MultiDriver($this->testRedisClient);
        $actual = $driver->key($keyPattern)->scanAll(1);
        $this->assertEquals($expected, $actual);
    }
}
