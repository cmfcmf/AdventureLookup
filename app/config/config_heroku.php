<?php

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);

$container->setParameter('database_host', $server);
$container->setParameter('database_user', $username);
$container->setParameter('database_password', $password);
$container->setParameter('database_name', $db);
$container->setParameter('elasticsearch_host', getenv('BONSAI_URL'));