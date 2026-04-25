<?php
require 'vendor/autoload.php';
echo class_exists('GuzzleHttp\Client') ? 'GuzzleHttp OK' : 'GuzzleHttp NOT found';
