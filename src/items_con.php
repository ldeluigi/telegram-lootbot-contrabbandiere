<?php
require('_config.php');

$result = mysqli_query($link, "SELECT id, secret_price AS last_contrab FROM items WHERE secret_price > 0");

while ($row = mysqli_fetch_assoc($result))
{
    $rows[] = $row;
}
header('Content-Type: application/json');
echo(json_encode($rows));