<?php


$redis = new RedisArray([

    '127.0.0.1:6379',
    '127.0.0.1:6380'

],['auth'=>'randomredis','connection_timeout'=>3000]);
$redis->auth('randomredis');

echo phpversion('redis') . "\n";

var_dump($redis->ping());
var_dump($redis->echo('test'));
var_dump($redis->_hosts());

$redis = new Redis();
$redis->connect('127.0.0.1', 6379, 30);
$redis->auth('randomredis');
var_dump($redis->ping());
$redis = new Redis();
$redis->connect('127.0.0.1', 6380, 30);
$redis->auth('randomredis');
var_dump($redis->ping());
