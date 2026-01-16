<?php
$env = parse_ini_file(__DIR__.'/.env');

$DB_HOST = $env['DB_HOST'];
$DB_USER = $env['DB_USER'];
$DB_PASS = $env['DB_PASS'];
$DB_NAME = $env['DB_NAME'];


header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers:content-type");
$conn=mysqli_connect($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
mysqli_set_charset($conn, 'utf8');
?>