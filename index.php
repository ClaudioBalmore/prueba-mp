<?php

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

require 'vendor/autoload.php';

$resend = Resend::client('re_Qc72Wbin_CFgyECUTvXbPLw4NPXfSaVwF');


MercadoPagoConfig::setAccessToken("APP_USR-7814564292133896-100119-aa29a73b031435648377a718733848d2-2016921642");

$notificacion = file_get_contents("php://input");
$datos = json_decode($notificacion, true);

if (isset($datos['type']) && $datos['type'] == 'payment') {

    $paymentId = $datos['data']['id'];
    $paymentClient = new PaymentClient();

    try {

        $payment = $paymentClient->get($paymentId);
        $filePath = "pagos.txt";
        $file = fopen($filePath, 'a');
        fwrite($file, "NOTIFICACIÓN: " . json_encode($datos, JSON_PRETTY_PRINT) . PHP_EOL);
        fwrite($file, "PAGO: " . json_encode($payment, JSON_PRETTY_PRINT) . PHP_EOL);
        fclose($file);        

        if($payment->status == "approved") {
            //Formatear fecha
            $fechaOriginal = $payment->date_approved;
            $fecha = new DateTime($fechaOriginal);

            $correo = 'Compra realizada con éxito <br>Número de Transacción: '.$payment->id.'<br>Fecha Aprovación: '. $fecha->format('d/m/Y H:i:s') .'<br><br>Datos Comprador<br>Nombre: '.$payment->additional_info->payer->first_name.'<br>Telefono: '.$payment->additional_info->payer->phone->number.'<br>Correo: '.$payment->additional_info->payer->email .'<br>Dirección: '.$payment->additional_info->payer->address->street_name.'<br><br>Detalles de Compra<br>Producto: '.$payment->description .' CLP$'.$payment->transaction_amount.'<br>Envio: CLP$'.$payment->shipping_amount .'<br>Total Pagado: CLP$'.$payment->transaction_amount+$payment->shipping_amount.'<br><br>'.json_encode($payment);

            $result = $resend->emails->send([
                'from' => 'Contacto <onboarding@resend.dev>',
                'to' => ['cnunezcerda@gmail.com'],
                'subject' => 'Notificación de Compra',
                'html' => $correo
            ]);
        }

    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}

http_response_code(200);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba Notificacion</title>
</head>
<body>
    <h1>Json Correcto!!</h1>

</body>
</html>