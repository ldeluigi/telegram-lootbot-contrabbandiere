# telegram-lootbot-contrabbandiere

## Requisiti
Le query sul db usano purtroppo mysql che è stato deprecato dal PHP dalla versione 7.
Inoltre l'SQL è valido e testato solo su un dbms MySQL.

## Setup
Una volta che i sorgenti compilano è necessario impostare a mano il webhook da telegram.
Un vettore dei config è contenuto dentro il file \_config.php, mentre altre impostazioni vanno settate per update_items.php
Inoltre sarebbe opportuno eseguire periodicamente lo script update_items.php per mantenere la tabella sugli oggetti aggiornata

## Deploy
Se tutto funziona è possibile usare il bot da Telegram

## Il progetto
Questo progetto dipende da un altro bot pertanto la sua vita è segnata da quella di Loot Bot.
Accetto le PR che ritengo migliorino il codice in qualsiasi modo, in particolare sarebbe davvero utile un porting per PHP 7.
