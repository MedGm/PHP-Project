<?php

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'students';

$dsn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$dsn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


