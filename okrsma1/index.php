<?php
// XAMPP/localhost entry point for the Laravel application.
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->handleRequest(Illuminate\Http\Request::capture());
