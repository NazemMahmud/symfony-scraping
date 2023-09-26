<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->usePutenv()->bootEnv(dirname(__DIR__) . '/.env.test');
