<?php

declare(strict_types=1);

$publicPath = realpath(__DIR__.'/../public') ?: __DIR__.'/../public';
$publicIndex = $publicPath.'/index.php';

$_SERVER['DOCUMENT_ROOT'] = $publicPath;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $publicIndex;

require $publicIndex;
