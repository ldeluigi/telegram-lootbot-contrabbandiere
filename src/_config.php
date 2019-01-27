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
"admins" => array(
//elencare qui la lista degli admin abilitati al comando /reset (amministrazione del db del bot)
)

);

$link = mysqli_connect($hostname, $username, $pwd);
mysqli_select_db($link, my_db); // sostituire my_db col database che contiene la tabella `contrabbandi` e `items`

