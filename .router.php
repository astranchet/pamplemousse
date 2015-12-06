<?php

$requestUri = $_SERVER['REQUEST_URI'];
$filePath = __DIR__ . '/web/' . $requestUri;

if (file_exists(rawurldecode($filePath))) {
	return false; // serve the requested resource as-is.
} else {
	include_once 'web/index.php';
}

