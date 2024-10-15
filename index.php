<?php

use MercadoPago\MercadoPagoConfig;

require 'vendor/autoload.php';

MercadoPagoConfig::setAccessToken("APP_USR-7814564292133896-100119-aa29a73b031435648377a718733848d2-2016921642");

$notificacion = file_get_contents("php://input");

$datos = json_decode($notificacion, true);

$filePath = "pagos.txt";
$file = fopen($filePath, 'a');
fwrite($file, "NOTIFICACIÓN: " . $datos . PHP_EOL);
fclose($file);

http_response_code(200);