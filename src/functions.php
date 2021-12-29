<?php

if ($config['funziona_nei_canali']) {
    if ($update["channel_post"]) {
        $update["message"] = $update["channel_post"];
        $canale = true;
    }
}

if ($config['funziona_messaggi_modificati']) {
    if ($update["edited_message"]) {
        $update["message"] = $update["edited_message"];
        $editato = true;
        if ($update["edited_message"]["game"]) {
            $game = $update["edited_message"]["game"];
            $game_title = $game["title"];
            $game_description = $game["description"];
            $game_rank = $game["text"];
        }
    }
}

if ($config['funziona_messaggi_modificati_canali']) {
    if ($update["edited_channel_post"]) {
        $update["message"] = $update["edited_channel_post"];
        $editato = true;
        $canale = true;
    }
}
$datemsg = max($update["message"]["date"], $update["message"]["edit_date"] ? $update["message"]["edit_date"] : 0);
$idmsg = $update["message"]["message_id"];
$chatID = $update["message"]["chat"]["id"];
$userID = $update["message"]["from"]["id"];
$msg = $update["message"]["text"];
$username = $update["message"]["from"]["username"];
$nome = $update["message"]["from"]["first_name"];
$cognome = $update["message"]["from"]["last_name"];
if ($chatID<0) {
    $titolo = $update["message"]["chat"]["title"];
    $usernamechat = $update["message"]["chat"]["username"];
}
$entities = $update["message"]["entities"];
$voice = $update["message"]["voice"]["file_id"];
$photo = $update["message"]["photo"][0]["file_id"];
$document = $update["message"]["document"]["file_id"];
$document_name = $update["message"]["document"]["file_name"];
$audio = $update["message"]["audio"]["file_id"];
$sticker = $update["message"]["sticker"]["file_id"];
$gioco = $update["message"]["game"];//[title] o [description]
$inoltrato = false;
if ($update["message"]["forward_from"]["id"]) {
    $inoltrato = true;
    $inoltrato_id = $update["message"]["forward_from"]["id"];
    $inoltrato_nome = $update["message"]["forward_from"]["first_name"];
    $inoltrato_username = $update["message"]["forward_from"]["username"];
    $inoltrato_time = $update["message"]["forward_date"];
}
$risposta = false;
if ($update["message"]["reply_to_message"]["message_id"]) {
    $risposta = true;
    $risposta_idmsg = $update["message"]["reply_to_message"]["message_id"];
    $risposta_userID = $update["message"]["reply_to_message"]["from"]["id"];
    $risposta_date = $update["message"]["reply_to_message"]["date"];
    $risposta_msg = $update["message"]["reply_to_message"]["text"];
    $risposta_entities = $update["message"]["reply_to_message"]["entities"];
}

//tastiere inline
if ($update["callback_query"]) {
    $cbid = $update["callback_query"]["id"];
    $cbdata = $update["callback_query"]["data"];
    $msg = $cbdata;
    $cbmid = $update["callback_query"]["message"]["message_id"];
    $cbmtext = $update["callback_query"]["message"]["text"];
    $chatID = $update["callback_query"]["message"]["chat"]["id"];
    $userID = $update["callback_query"]["from"]["id"];
    $nome = $update["callback_query"]["from"]["first_name"];
    $cognome = $update["callback_query"]["from"]["last_name"];
    $username = $update["callback_query"]["from"]["username"];
    $titolo = $update["callback_query"]["message"]["chat"]["title"];
    $entities = $update["callback_query"]["message"]["entities"];
}

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
$groups = $config["groups"];

$admins = $config["admins"];

$last_error_message = "No error occurred";
mb_internal_encoding("UTF-8");
function sm($chatID, $text, $rmf = false, $pm = 'pred', $dis = false, $replyto = false, $inline = 'pred')
{
    global $api;
    global $userID;
    global $update;
    global $config;
    global $last_error_message;

    if ($pm=='pred') {
        $pm = $config["formattazione_predefinita"];
    }

    if ($inline=='pred') {
        if ($config["tastiera_predefinita"] == "inline") {
            $inline = true;
        } elseif ($config["tastiera_predefinita"] == "normale") {
            $inline = false;
        }
    }
    if ($rmf == "nascondi") {
        $inline = false;
    }


    $dal = $config["nascondi_anteprima_link"];

    if (!$inline) {
        if ($rmf == 'nascondi') {
            $rm = array('hide_keyboard' => true
);
        } else {
            $rm = array('keyboard' => $rmf,
'resize_keyboard' => true
);
        }
    } else {
        $rm = array('inline_keyboard' => $rmf,
);
    }
    $rm = json_encode($rm);

    $args = array(
'chat_id' => $chatID,
'text' => $text,
'disable_notification' => $dis,
'parse_mode' => $pm
);
    if ($dal) {
        $args['disable_web_page_preview'] = $dal;
    }
    if ($replyto) {
        $args['reply_to_message_id'] = $replyto;
    }
    if ($rmf) {
        $args['reply_markup'] = $rm;
    }
    if ($text) {
        $r = new HttpRequest("post", "https://api.telegram.org/$api/sendmessage", $args);
        $rr = $r->getResponse();
        $ar = json_decode($rr, true);
        $ok = $ar["ok"]; //false
        $e403 = $ar["error_code"];
        if ($ok==false) {
            $last_error_message = $rr;
            return false;
        } elseif ($e403 == "403") {
            $last_error_message = $rr;
            return false;
        } elseif ($e403) {
            $last_error_message = $rr;
            return false;
        } else {
            return $ar['result']['message_id'];
        }
    }
}


function smReturn($chatID, $text, $replyto = false, $dal = false)
{
    global $api;
    global $userID;
    global $update;
    global $config;
    $pm = $config["formattazione_predefinita"];


    //$dal = $config["nascondi_anteprima_link"];
 
    $args = array(
'chat_id' => $chatID,
'text' => $text,
'parse_mode' => $pm
);
    if ($dal) {
        $args['disable_web_page_preview'] = $dal;
    }
    if ($replyto) {
        $args['reply_to_message_id'] = $replyto;
    }
    if ($text) {
        $r = new HttpRequest("post", "https://api.telegram.org/$api/sendmessage", $args);
        $rr = $r->getResponse();
        $ar = json_decode($rr, true);
        $ok = $ar["ok"]; //false
        $e403 = $ar["error_code"];
        if ($e403 == "403") {
            return false;
        } elseif ($e403) {
            return false;
        } else {
            return $ar['result'];
        }
    }
}



function cb_reply($id, $text, $alert = false, $cbmid = false, $ntext = false, $nmenu = false, $npm = "pred")
{
    global $api;
    global $chatID;
    global $config;

    if ($npm == 'pred') {
        $npm = $config["formattazione_predefinita"];
    }



    $args = array(
'callback_query_id' => $id,
'text' => $text,
'show_alert' => $alert

);
    $r = new HttpRequest("get", "https://api.telegram.org/$api/answerCallbackQuery", $args);

    if ($cbmid) {
        if ($nmenu) {
            $rm = array('inline_keyboard' => $nmenu
);
            $rm = json_encode($rm);
        }

        if ($ntext) {
            $args = array(
'chat_id' => $chatID,
'message_id' => $cbmid,
'text' => $ntext,
'parse_mode' => $npm,
);
            if ($nmenu) {
                $args["reply_markup"] = $rm;
            }
            $r = new HttpRequest("post", "https://api.telegram.org/$api/editMessageText", $args);
        }
    }
}


function leave()
{
    global $api;
    global $chatID;
    $args = array(
'chat_id' => $chatID
);
    $r = new HttpRequest("post", "https://api.telegram.org/$api/leaveChat", $args);
    $rr = $r->getResponse();
    $ar = json_decode($rr, true);
    $ok = $ar["ok"]; //false
    $e403 = $ar["error_code"];
    if ($e403 == "403") {
        return false;
    } elseif ($e403) {
        return false;
    } else {
        return true;
    }
}




function memberstatus($chatID, $memberID)
{
    global $api;
    $args = array(
'chat_id' => $chatID,
'user_id' => $memberID
);
    $r = new HttpRequest("get", "https://api.telegram.org/$api/getChatMember", $args);
    $rr = $r->getResponse();
    $ar = json_decode($rr, true);
    $success = $ar["ok"];
    $status = $ar["result"]["status"];
    if ($success == true) {
        return $status;
    } else {
        return false;
    }
}



function remove_emoji($text)
{
    return preg_replace('/[[:^print:]]/', ' ', $text);
}
function restoreEntities($text, $entities = [])
{
    if (is_string($text)) {
        $plus_offset = 0;
        $emoji_offset = -1;
        foreach ($entities as $e) {
            //$emoji_offset = mb_ereg('', $text);
            if ($e['type']=='text_mention') {
                $target = mb_substr($text, $e['offset'] + $plus_offset + $emoji_offset, $e['length']);
                $start_tag = "<a href=\"tg://user?id=".((string) $e['user']['id'])."\">";
                $end_tag = "</a>";
                $text = mb_substr($text, 0, $e['offset'] + $plus_offset + $emoji_offset).$start_tag.$target.$end_tag.mb_substr($text, $e['offset'] + $plus_offset + $e['length'] + $emoji_offset);
                $plus_offset += mb_strlen($start_tag.$end_tag);
                $emoji_offset--;
            } elseif ($e['type']=='bold') {
                $target = mb_substr($text, $e['offset'] + $plus_offset + $emoji_offset, $e['length']);
                $start_tag = "<b>";
                $end_tag = "</b>";
                $text = mb_substr($text, 0, $e['offset'] + $plus_offset + $emoji_offset).$start_tag.$target.$end_tag.mb_substr($text, $e['offset'] + $plus_offset + $e['length'] + $emoji_offset);
                $plus_offset += mb_strlen($start_tag.$end_tag);
                $emoji_offset--;
            }
        }
        return $text;
    } else {
        return false;
    }
}




function editm($chatID, $messID, $testo, $pm = 'pred')
{
    global $api;
    global $userID;
    global $config;
    if ($pm=='pred') {
        $pm = $config["formattazione_predefinita"];
    }


    $args = array(
'chat_id' => $chatID,
'message_id' => $messID,
'text' => $testo,
'parse_mode' => $pm
);
    if ($testo) {
        $r = new HttpRequest("post", "https://api.telegram.org/$api/editMessageText", $args);
        $rr = $r->getResponse();
        $ar = json_decode($rr, true);
        $returned = $ar['ok'];
        if ($returned) {
            return $returned;
        } else {
            return "false";
        }
    }
}


function delm($chatID, $messID)
{
    global $api;
    global $userID;
    $args = array(
'chat_id' => $chatID,
'message_id' => $messID,
);
    $r = new HttpRequest("post", "https://api.telegram.org/$api/deleteMessage", $args);
    $rr = $r->getResponse();
    return $rr['ok'];
}

$modificatore = "OFFERTA LIBERA. Max: ";
function liberaPrezzo($prezzo)
{
    global $modificatore;
    return $modificatore.$prezzo;
}

function isLibero($prezzo)
{
    global $modificatore;
    return strpos($prezzo, $modificatore)!==false;
}

function prezzoFreeAbilitato()
{
    global $link;
	global $userID;
	$p = mysqli_query($link, prepare_for_db("SELECT * FROM c_users WHERE tid=$userID"));
	$n = mysqli_num_rows($p);
    if ($n==1) {
        //for ($i=0; $i<$n; $i++) {
        $b = mysqli_fetch_assoc($p);
        if ($b["free"] == 1)
			return true;
    }
    return false;
}

$modificatore2 = "(Negozio in pvt) ";
function negPvtPrezzo($prezzo)
{
    global $modificatore2;
    return $modificatore2.$prezzo;
}

function isNegPvt($prezzo)
{
    global $modificatore2;
    return strpos($prezzo, $modificatore2)!==false;
}

function negPvtAbilitato()
{
    global $link;
	global $userID;
	$p = mysqli_query($link, prepare_for_db("SELECT * FROM c_users WHERE tid=\"".$userID."\""));
	$n = mysqli_num_rows($p);
    if ($n==1) {
        //for ($i=0; $i<$n; $i++) {
        $b = mysqli_fetch_assoc($p);
        if ($b["pvt"] == 1)
			return true;
    }
    return false;
}

//FUNZIONI DATABASE

function free()
{
    global $username;
    global $chatID;
    global $idmsg;
	global $link;
    $p = mysqli_query($link, prepare_for_db("SELECT * FROM contrabbandi WHERE nome=\"".$username."\" ORDER BY time DESC"));
    $n = mysqli_num_rows($p);
    if ($n>0) {
        //for ($i=0; $i<$n; $i++) {
        $b = mysqli_fetch_assoc($p);
        $oldchatID = $b['chat_id'];
        $oldmessID = $b['message_id'];
        $item = $b['item'];
        $prezzo = $b['prezzo'];
        $nome = $b['nome'];
        $pc = getPC($item);
        $prenotata = (strpos($b['test'], '*') === 0);
        if (!$prenotata and !isLibero($prezzo)) {
            $prezzo = liberaPrezzo($prezzo);
			mysqli_query($link, prepare_for_db("UPDATE contrabbandi SET prezzo=\"$prezzo\" WHERE test=\"".$b['test']."\""));
            repost(true);
            $m = sm($chatID, "📭 $username, ho reso la tua ultima richiesta ad offerta libera.");
            sleep(5);
            delm($chatID, $m);
            delm($chatID, $idmsg);
        } else {
            $m = sm($chatID, "📬 $username, non è possibile rendere la tua ultima richiesta ad offerta libera.");
            sleep(5);
            delm($chatID, $m);
            delm($chatID, $idmsg);
        }
    } else {
        $m = sm($chatID, "📪 $username, non hai richieste in sospeso da rendere ad offerta libera.");
        sleep(5);
        delm($chatID, $m);
        delm($chatID, $idmsg);
    }
}

function clear()
{
    global $username;
    global $chatID;
    global $idmsg;
	global $link;
    $p = mysqli_query($link, prepare_for_db("SELECT * FROM contrabbandi WHERE nome=\"".$username."\""));
    $n = mysqli_num_rows($p);
    if ($n>0) {
        for ($i=0; $i<$n; $i++) {
            $b = mysqli_fetch_assoc($p);
            mysqli_query($link, prepare_for_db("DELETE FROM contrabbandi WHERE test=\"".$b['test']."\""));
            $oldchatID = $b['chat_id'];
            $oldmessID = $b['message_id'];
            $item = $b['item'];
            $prezzo = $b['prezzo'];
            $nome = $b['nome'];
            $pc = getPC($item);
            $prenotata = (strpos($b['test'], '*') === 0);
            if (($oldchatID!=0) and ($oldmessID!=0)) {
                editm($oldchatID, $oldmessID, "👤 $nome\n🛠 <b>$item</b>\n📦 $pc pc\n💰 $prezzo\n\n".($prenotata ? "🤖✅" : "🤖❌"));
            }
        }
        $m = sm($chatID, "🔨 $username, ho concluso automaticamente tutte le tue richieste aperte.");
        sleep(5);
        delm($chatID, $m);
        delm($chatID, $idmsg);
    } else {
        $m = sm($chatID, "✅ $username, non hai richieste in sospeso.");
        sleep(5);
        delm($chatID, $m);
        delm($chatID, $idmsg);
    }
}

function repost($free = false)
{
    global $username;
    global $chatID;
    global $idmsg;
    global $userID;
	global $link;
    $q = mysqli_query($link, prepare_for_db("SELECT * FROM contrabbandi WHERE nome=\"".$username."\" ORDER BY time DESC"));
    $n = mysqli_num_rows($q);
    if ($n) {
        $b = mysqli_fetch_assoc($q);
        $creazione = $b['creation'];
        $nome = $b['nome'];
        $item = $b['item'];
        $prezzo = $b['prezzo'];
        $test = $b['test'];
        $oldchatID = $b['chat_id'];
        $oldmessID = $b['message_id'];
        if ((time()-$creazione)<57600) {
            $giorno = date("z", $creazione);
            $oggi = date("z");
            if ($oggi==$giorno) {
                if (mb_strpos($test, '*')===0) {
                    $m=sm($chatID, "⚠️ $username, la tua ultima richiesta (Oggetto: <b>$item</b>, Prezzo:$prezzo) è già prenotata".(($chatID==$b['chat_id']) ? "" : " in un altro gruppo").". Vai a concluderla.", false, 'pred', false, ($oldchatID==$chatID) ? $oldmessID : false);
                    sleep(5);
                    delm($chatID, $m);
                } else {
                    $time = $b['time'];
                    if ($chatID==$b['chat_id']) {
                        if ((time()-$time)>1800 || $free) {
                            $menu[] = array(
                        array(
                        "text" => "Mi prenoto",
                        "callback_data" => "p_$test")
                        );
                            $menu[] = array(
                        array(
                        "text" => "Concludi richiesta",
                        "callback_data" => "c_$test")
                        );
                            $c = mb_substr($test, 0, 8);
                            $pc = getPC($item);
                            $k = sm($chatID, "👤 <a href=\"tg://user?id=$userID\">$nome</a>\n🛠 <b>$item</b>\n📦 $pc pc\n💰 $prezzo §\n🏷 #".((is_numeric($c)) ? $c."x" : $c), $menu, 'pred', false);
                            mysqli_query($link, prepare_for_db("UPDATE contrabbandi SET time=".((string)time()).", message_id=$k, chat_id=$chatID WHERE test=\"".$test."\""));
                            if (($oldchatID!=0) and ($oldmessID!=0)) {
                                if ($free) {
                                    $d_suc = delm($oldchatID, $oldmessID);
                                }
                                if (!$d_suc) {
                                    editm($oldchatID, $oldmessID, "👤 $nome\n🛠 <b>$item</b>\n📦 $pc pc\n💰 $prezzo §\n\n↩️");
                                }
                            }
                        } else {
                            $m=sm($chatID, "🕒 $username, la tua ultima richiesta risale a meno di mezz'ora fa. (".((string) floor((time()-$time)/60))."m)", false, 'pred', false, $oldmessID);
                            sleep(5);
                            delm($chatID, $m);
                        }
                    } else {
                        if ((time()-$time)>900 || $free) {
                            $menu[] = array(
                        array(
                        "text" => "Mi prenoto",
                        "callback_data" => "p_$test")
                        );
                            $menu[] = array(
                        array(
                        "text" => "Concludi richiesta",
                        "callback_data" => "c_$test")
                        );
                            $c = mb_substr($test, 0, 8);
                            $pc = getPC($item);
                            $k = sm($chatID, "👤 <a href=\"tg://user?id=$userID\">$nome</a>\n🛠 <b>$item</b>\n📦 $pc pc\n💰 $prezzo §\n🏷 #".((is_numeric($c)) ? $c."x" : $c), $menu, 'pred', false);
                            mysqli_query($link, prepare_for_db("UPDATE contrabbandi SET time=".((string)time()).", message_id=$k, chat_id=$chatID WHERE test=\"".$test."\""));
                            if (($oldchatID!=0) and ($oldmessID!=0)) {
                                if ($free) {
                                    $d_suc = delm($oldchatID, $oldmessID);
                                }
                                if (!$d_suc) {
                                    editm($oldchatID, $oldmessID, "👤 $nome\n🛠 <b>$item</b>\n📦 $pc pc\n💰 $prezzo §\n\n➡️👥");
                                }
                            }
                        } else {
                            $m=sm($chatID, "🕤 $username, la tua ultima richiesta in un altro gruppo risale a meno di 15 minuti fa. (".((string) floor((time()-$time)/60))."m)");
                            sleep(5);
                            delm($chatID, $m);
                        }
                    }
                }
            } else {
                $m=sm($chatID, "📆 $username, la tua ultima richiesta non risale ad oggi, inoltrane una nuova. Nel frattempo provvederò ad eliminarla.");
                mysqli_query($link, ("DELETE FROM contrabbandi WHERE test=\"".$b['test']."\""));
                if (($oldchatID!=0) and ($oldmessID!=0)) {
                    editm($oldchatID, $oldmessID, "👴🏻 Questa offerta è obsoleta. (Oggetto: <b>$item</b>, Prezzo:$prezzo, Proprietario: $nome)");
                }
                sleep(5);
                delm($chatID, $m);
            }
        } else {
            $m=sm($chatID, "👴🏻 $username, la tua ultima richiesta è obsoleta.");
            sleep(5);
            delm($chatID, $m);
        }
    } else {
        $m=sm($chatID, "✅ $username, non hai richieste in sospeso.");
        sleep(5);
        delm($chatID, $m);
    }
    delm($chatID, $idmsg);
}

function search()
{
    global $chatID;
    global $userID;
    global $idmsg;
    global $titolo;
    global $last_error_message;
	global $link;
    if ($chatID == $userID) {
        $chat = -1001123874487;//Vicolo del Contrabbando
        $thischat = "Vicolo del Contrabbando";
    } else {
        $chat = $chatID;
        $thischat = $titolo;
    }
    $limit = time() - 86400;
    $p = mysqli_query($link, prepare_for_db("SELECT * FROM contrabbandi WHERE (creation>$limit) AND (test NOT LIKE \"*%\") AND chat_id=$chat ORDER BY time DESC"));
    $n = mysqli_num_rows($p);
    if ($n>0) {
        $k = 0;
        $oggi = date("z");
        for ($i=0; ($i<$n) and ($i<15); $i++) {
            $b = mysqli_fetch_assoc($p);
            $creazione = $b['creation'];
            $item = $b['item'];
            $prezzo = $b['prezzo'];
            $nome = $b['nome'];
            $test = $b['test'];
            $giorno = date("z", $creazione);
            if ($oggi==$giorno) {
                $c = mb_substr($test, 0, 8);
                $text.="🏷: #".((is_numeric($c)) ? $c."x" : $c)."\n🛠: <b>$item</b>\n📦: ".getPC($item)." pc\n💰: $prezzo §\n👤: $nome\n\n";
                $k++;
            }
        }
        $tosend =  ($k>0)? "Ultime $k offerte di oggi del gruppo\n👥 ".$thischat."\n\n".$text:"Nessuna offerta di oggi disponibile.";
        $char_per_mess = 3500;
        $n_mes = strlen($tosend) / $char_per_mess;
        $test = true;
        for ($i=0; $i<$n_mes and $test; $i++) {
            $test = sm($userID, substr($tosend, $i*$char_per_mess, $char_per_mess));
        }
        if ($userID!=$chatID) {
            if ($test) {
                $m = sm($chatID, "ℹ️ <i>Inviato in privato.</i>");
            } else {
                $m = sm($chatID, "🤖 Per usare questo comando attiva l'<a href=\"https://t.me/$userbot\">Androide del Vicolo</a>", false, 'pred', false, $idcmd, 'pred', true);
            }
            sleep(5);
            delm($chatID, $m);
            delm($chatID, $idmsg);
        }
    } else {
        $m = sm($chatID, "❕ Nessun risultato.");
        sleep(5);
        delm($chatID, $m);
        delm($chatID, $idmsg);
    }
}

function fm($chat_id, $from_chat_id, $message_id, $disable_notification = false)
{
    global $api;
    $args      = array(
        'chat_id' => $chat_id,
        'from_chat_id' => $from_chat_id,
        'message_id' => $message_id,
        'disable_notification' => $disable_notification
    );
    $r = new HttpRequest("post", "https://api.telegram.org/$api/forwardMessage", $args);
    $rr = $r->getResponse();
    return $rr['ok'];
}

function dbreset()
{
	global $link;
    $p = mysqli_query($link, prepare_for_db("SELECT * FROM contrabbandi"));
    $n = mysqli_num_rows($p);
    if ($n>0) {
        for ($i=0; $i<$n; $i++) {
            $b = mysqli_fetch_assoc($p);
            mysqli_query($link, prepare_for_db("DELETE FROM contrabbandi WHERE test=\"".$b['test']."\""));
            $oldchatID = $b['chat_id'];
            $oldmessID = $b['message_id'];
            $item = $b['item'];
            $prezzo = $b['prezzo'];
            $nome = $b['nome'];
            if (($oldchatID!=0) and ($oldmessID!=0)) {
                editm($oldchatID, $oldmessID, "👤 $nome\n🛠 <b>$item</b>\n📦 $pc\n💰 $prezzo\n\n🚽");
            }
            sleep(5);
        }
    }
}


function richiestaAPI($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 0);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
 
    return $result;
}

function getPC($item)
{
    if (mb_strpos($item, " (")!==false) {
        $item = mb_substr($item, 0, mb_strpos($item, " ("));
    }
$dbinfo='contrabbandroide';
$nuovolink = mysqli_connect('localhost', $dbinfo, $dbinfo, $dbinfo, 3307);
    $item_q = mysqli_query($nuovolink, prepare_for_db('SELECT * FROM items WHERE name="'.$item.'"'));
    while ($i = mysqli_fetch_assoc($item_q)) {
        if ($i['name'] == $item) {
            return ((string) $i['craft_pnt']);
        }
    }
    return "n/a (not found)";
}

function tableFormat($item)
{
    $i_tem = preg_replace("/ /", "_", $item);
    $i_tem = preg_replace("/[^\w]/", "", $i_tem);
    return $i_tem;
}

function is_between_times($start = null, $end = null)
{
    if ($start == null) {
        $start = '00:00';
    }
    if ($end == null) {
        $end = '23:59';
    }
    return ($start <= date('H:i') && date('H:i') <= $end);
}
