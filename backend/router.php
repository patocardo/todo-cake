<?php
// router.php

$uri = $_SERVER['REQUEST_URI'];

// If the request is for a file or directory that exists, serve it directly
if (file_exists(__DIR__ . '/webroot' . $uri)) {
    return false; // Serve the requested resource as-is.
}

// If the request starts with /api, forward it to CakePHP's index.php
if (strpos($uri, '/api') === 0) {
    include __DIR__ . '/webroot/index.php';
    return true;
}

// For all other requests, serve the Vue app
header('Location: /webroot/js/vue-app/dist/index.html');
return true;
