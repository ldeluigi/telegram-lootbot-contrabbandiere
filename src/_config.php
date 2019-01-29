<?php

$config = array(

"formattazione_predefinita" => "HTML",
     //o "Markdown" o "" per nulla
"formattazione_messaggi_globali" => "HTML",
"nascondi_anteprima_link" => true,
"tastiera_predefinita" => "inline",
"funziona_nei_canali" => false,
"funziona_messaggi_modificati" => false,
"funziona_messaggi_modificati_canali" => false,

// Da settare obbligatoriamente
"api" => "botXXXXXXXXX",  // chiave del bot presa da botfather con "bot" davanti
"admin" => "1234567890",  // Telegram ID dell'admin (creatore)
"userbot" => "userbot",   // username del bot
"groups" => array(
//elencare qui gli id delle chat in cui il bot è abilitato
),
// true significa che verrà disabilitato il controllo sul gruppo
"allow_all_groups" => false,
"admins" => array(
//elencare qui la lista degli admin abilitati al comando /reset (amministrazione del db del bot)
),

// impostazioni per db
"db_hostname" => "localhost",
"db_username" => "root",
"db_password" => "",
"db_name" => "mydb",

// inserire qui il token per le api di lootbot
"lootbot_api_token" => "yourtoken"

);

$link = mysqli_connect($config["db_hostname"], $config["db_username"], $config["db_password"]);
if ($link != FALSE) {
     mysqli_select_db($link, $config["db_name"]); 
}

