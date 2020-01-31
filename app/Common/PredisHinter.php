<?php
/**
 * Created by PhpStorm.
 * User: Louv
 * Date: 2019/8/19
 * Time: 11:05
 */

namespace App\Common;

/**
 * Predis hinter
 *
 * @see \Predis\ClientInterface basedon this interface
 * @see \Predis\Client
 * */
interface PredisHinter
{
    public static function del(array $keys): int;

    public static function dump($key): string;

    public static function exists($key): int;

    public static function expire($key, $seconds): int;

    public static function expireat($key, $timestamp): int;

    public static function keys($pattern): array;

    public static function move($key, $db): int;

    /**
     * @return mixed
     * */
    public static function object($subcommand, $key);

    public static function persist($key): int;

    public static function pexpire($key, $milliseconds): int;

    public static function pexpireat($key, $timestamp): int;

    public static function pttl($key): int;

    public static function randomkey(): string;

    /**
     * @return mixed
     * */
    public static function rename($key, $target);

    public static function renamenx($key, $target): int;

    public static function scan($cursor, array $options = null): array;

    public static function sort($key, array $options = null): array;

    public static function ttl($key): int;

    /**
     * @return mixed
     * */
    public static function type($key);

    public static function append($key, $value): int;

    public static function bitcount($key, $start = null, $end = null): int;

    public static function bitop($operation, $destkey, $key): int;

    public static function bitfield($key, $subcommand, ...$subcommandArg): array;

    public static function decr($key): int;

    public static function decrby($key, $decrement): int;

    public static function get($key): string;

    public static function getbit($key, $offset): int;

    public static function getrange($key, $start, $end): string;

    public static function getset($key, $value): string;

    public static function incr($key): int;

    public static function incrby($key, $increment): int;

    public static function incrbyfloat($key, $increment): string;

    public static function mget(array $keys): array;

    /**
     * @return mixed
     * */
    public static function mset(array $dictionary);

    public static function msetnx(array $dictionary): int;

    /**
     * @return mixed
     * */
    public static function psetex($key, $milliseconds, $value);

    /**
     * @return mixed
     * */
    public static function set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null);

    public static function setbit($key, $offset, $value): int;

    public static function setex($key, $seconds, $value): int;

    public static function setnx($key, $value): int;

    public static function setrange($key, $offset, $value): int;

    public static function strlen($key): int;

    public static function hdel($key, array $fields): int;

    public static function hexists($key, $field): int;

    public static function hget($key, $field): string;

    public static function hgetall($key): array;

    public static function hincrby($key, $field, $increment): int;

    public static function hincrbyfloat($key, $field, $increment): string;

    public static function hkeys($key): array;

    public static function hlen($key): int;

    public static function hmget($key, array $fields): array;

    /**
     * @return mixed
     * */
    public static function hmset($key, array $dictionary);

    public static function hscan($key, $cursor, array $options = null): array;

    public static function hset($key, $field, $value): int;

    public static function hsetnx($key, $field, $value): int;

    public static function hvals($key): array;

    public static function hstrlen($key, $field): int;

    public static function blpop(array $keys, $timeout): array;

    public static function brpop(array $keys, $timeout): array;

    public static function brpoplpush($source, $destination, $timeout): array;

    public static function lindex($key, $index): string;

    public static function linsert($key, $whence, $pivot, $value): int;

    public static function llen($key): int;

    public static function lpop($key): string;

    public static function lpush($key, array $values): int;

    public static function lpushx($key, $value): int;

    public static function lrange($key, $start, $stop): array;

    public static function lrem($key, $count, $value): int;

    /**
     * @return mixed
     * */
    public static function lset($key, $index, $value);

    /**
     * @return mixed
     * */
    public static function ltrim($key, $start, $stop);

    public static function rpop($key): string;

    public static function rpoplpush($source, $destination): string;

    public static function rpush($key, array $values): int;

    public static function rpushx($key, $value): int;

    public static function sadd($key, array $members): int;

    public static function scard($key): int;

    public static function sdiff(array $keys): array;

    public static function sdiffstore($destination, array $keys): int;

    public static function sinter(array $keys): array;

    public static function sinterstore($destination, array $keys): int;

    public static function sismember($key, $member): int;

    public static function smembers($key): array;

    public static function smove($source, $destination, $member): int;

    public static function spop($key, $count = null): string;

    public static function srandmember($key, $count = null): string;

    public static function srem($key, $member): int;

    public static function sscan($key, $cursor, array $options = null): array;

    public static function sunion(array $keys): array;

    public static function sunionstore($destination, array $keys): int;

    public static function zadd($key, array $membersAndScoresDictionary): int;

    public static function zcard($key): int;

    public static function zcount($key, $min, $max): string;

    public static function zincrby($key, $increment, $member): string;

    public static function zinterstore($destination, array $keys, array $options = null): int;

    public static function zrange($key, $start, $stop, array $options = null): array;

    public static function zrangebyscore($key, $min, $max, array $options = null): array;

    public static function zrank($key, $member): int;

    public static function zrem($key, $member): int;

    public static function zremrangebyrank($key, $start, $stop): int;

    public static function zremrangebyscore($key, $min, $max): int;

    public static function zrevrange($key, $start, $stop, array $options = null): array;

    public static function zrevrangebyscore($key, $max, $min, array $options = null): array;

    public static function zrevrank($key, $member): int;

    public static function zunionstore($destination, array $keys, array $options = null): int;

    public static function zscore($key, $member): string;

    public static function zscan($key, $cursor, array $options = null): array;

    public static function zrangebylex($key, $start, $stop, array $options = null): array;

    public static function zrevrangebylex($key, $start, $stop, array $options = null): array;

    public static function zremrangebylex($key, $min, $max): int;

    public static function zlexcount($key, $min, $max): int;

    public static function pfadd($key, array $elements): int;

    /**
     * @return mixed
     * */
    public static function pfmerge($destinationKey, array $sourceKeys);

    public static function pfcount(array $keys): int;

    /**
     * @return mixed
     * */
    public static function pubsub($subcommand, $argument);

    public static function publish($channel, $message): int;

    /**
     * @return mixed
     * */
    public static function discard();

    public static function exec(): array;

    /**
     * @return mixed
     * */
    public static function multi();

    /**
     * @return mixed
     * */
    public static function unwatch();

    /**
     * @return mixed
     * */
    public static function watch($key);

    /**
     * @return mixed
     * */
    public static function eval($script, $numkeys, $keyOrArg1 = null, $keyOrArgN = null);

    /**
     * @return mixed
     * */
    public static function evalsha($script, $numkeys, $keyOrArg1 = null, $keyOrArgN = null);

    /**
     * @return mixed
     * */
    public static function script($subcommand, $argument = null);

    /**
     * @return mixed
     * */
    public static function auth($password);

    public static function echo($message): string;

    /**
     * @return mixed
     * */
    public static function ping($message = null);

    /**
     * @return mixed
     * */
    public static function select($database);

    /**
     * @return mixed
     * */
    public static function bgrewriteaof();

    /**
     * @return mixed
     * */
    public static function bgsave();

    /**
     * @return mixed
     * */
    public static function client($subcommand, $argument = null);

    /**
     * @return mixed
     * */
    public static function config($subcommand, $argument = null);

    public static function dbsize(): int;

    /**
     * @return mixed
     * */
    public static function flushall();

    /**
     * @return mixed
     * */
    public static function flushdb();

    public static function info($section = null): array;

    public static function lastsave(): int;

    /**
     * @return mixed
     * */
    public static function save();

    /**
     * @return mixed
     * */
    public static function slaveof($host, $port);

    /**
     * @return mixed
     * */
    public static function slowlog($subcommand, $argument = null);

    public static function time(): array;

    public static function command(): array;

    public static function geoadd($key, $longitude, $latitude, $member): int;

    public static function geohash($key, array $members): array;

    public static function geopos($key, array $members): array;

    public static function geodist($key, $member1, $member2, $unit = null): string;

    public static function georadius($key, $longitude, $latitude, $radius, $unit, array $options = null): array;

    public static function georadiusbymember($key, $member, $radius, $unit, array $options = null): array;
}
