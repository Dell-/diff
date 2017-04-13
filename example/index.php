<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

$var1 = "Hello";
$var2 = "Hello";

if (strcmp($var1, $var2) !== 0) {
    echo '$var1 не равно $var2 при регистрозависимом сравнении';
}

echo 'cpm';
