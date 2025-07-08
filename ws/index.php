<?php
require 'vendor/autoload.php';
require 'db.php';
require 'routes/route.php';

Flight::route('GET /', function() {
    header('Content-Type: text/html');
    readfile(__DIR__ . '/../index.html');
});

Flight::start();
 