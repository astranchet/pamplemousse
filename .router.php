<?php

if (file_exists(__DIR__ . '/web/' . $_SERVER['REQUEST_URI'])) {
	return false; // serve the requested resource as-is.
} else {
	include_once 'web/index.php';
}

