<?php

$url = parse_url(getenv("DATABASE_URL"));
$server = $url["host"];
$port = $url["port"];
$username = $url["user"];
$password = $url["pass"];
$db = ltrim($url['path'],'/');

$container->setParameter('database_driver', 'pdo_pgsql');
$container->setParameter('database_host', $server);
$container->setParameter('database_port', $port);
$container->setParameter('database_user', $username);
$container->setParameter('database_password', $password);
$container->setParameter('database_name', $db);
$container->setParameter('elasticsearch_host', getenv('BONSAI_URL'));