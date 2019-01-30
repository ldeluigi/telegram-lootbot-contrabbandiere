<?php

require 'class-http-request.php';


$content = file_get_contents("php://input");
$update = json_decode($content, true);

require 'config.php';
require 'db.php';

$api = $config['api'];
$idadmin = $config['admin'];
$adminID = $idadmin;
$userbot = $config['userbot'];

require 'functions.php';
require '_comandi.php';

$file = "input.json";
$f2 = fopen($file, 'w');
fwrite($f2, $content);
fclose($f2);
