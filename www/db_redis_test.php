<?php

try{
	$con =new PDO("mysql:host=mysql;dbname=test","root","123456");
 	echo "MySQL connection is ok...";
}catch(PDOException $e){
  	echo $e->getMessage();
}

echo PHP_EOL;

$redis = new Redis();
$redis->connect('redis',6379);
$redis->set('test','Redis connection is ok...');
echo $redis->get('test');