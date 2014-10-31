<?php

error_reporting(-1);

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    include __DIR__ . '/../vendor/autoload.php';
} else {
    include __DIR__ . '/../../../autoload.php';
}

