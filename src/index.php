<?php

require 'class-http-request.php';


$content = file_get_contents("php://input");
$update = json_decode($content, true);

var_dump($content);


require '_config.php';

$api = $config['api'];
$idadmin = $config['admin'];
$adminID = $idadmin;
$userbot = $config['userbot'];

require 'functions.php';
require '_comandi.php';
