<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/setup.html#checking-symfony-application-configuration-and-setup
// for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
if (!getenv('HEROKU')) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../app/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);

// https://devcenter.heroku.com/articles/deploying-symfony3#trusting-the-heroku-router
$request = Request::createFromGlobals();
Request::setTrustedProxies(
    // trust *all* requests
    ['127.0.0.1', $request->server->get('REMOTE_ADDR')],

    // only trust X-Forwarded-Port/-Proto, not -Host
    Request::HEADER_X_FORWARDED_AWS_ELB
);

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
