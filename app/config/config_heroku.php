<?php

$url = parse_url(getenv("JAWSDB_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = ltrim($url['path'],'/');

$container->setParameter('database_host', $server);
$container->setParameter('database_user', $username);
$container->setParameter('database_password', $password);
$container->setParameter('database_name', $db);
$container->setParameter('elasticsearch_host', getenv('BONSAI_URL'));