<?php
/**
 * Helper para envío de correos electrónicos
 */

function sendRecoveryEmail($toEmail, $userName, $pin) {
    $subject = "🔒 Código de Recuperación - " . $userName;
    $from = "ismaeldiego@gmail.com";
    
    // Diseño Premium del Correo
    $message = "
    <html>
    <head>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 0; }
            .container { width: 100%; max-width: 600px; margin: 20px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
            .header { background: #696cff; padding: 30px; text-align: center; color: #fff; }
            .header h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
            .content { padding: 40px; text-align: center; }
            .pin-code { font-size: 48px; font-weight: bold; color: #696cff; letter-spacing: 10px; margin: 25px 0; padding: 15px; background: #f0f1ff; border-radius: 8px; display: inline-block; }
            .footer { background: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #9ca3af; }
            .btn { background: #696cff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>RECUPERACIÓN DE ACCESO</h1>
            </div>
            <div class='content'>
                <p>Hola <strong>$userName</strong>,</p>
                <p>Has solicitado restablecer tu contraseña. Utiliza el siguiente código de verificación para continuar con el proceso:</p>
                <div class='pin-code'>$pin</div>
                <p>Este código expirará en 15 minutos por tu seguridad.</p>
                <p>Si no solicitaste este cambio, puedes ignorar este correo de forma segura.</p>
            </div>
            <div class='footer'>
                © " . date('Y') . " Sistema Carwash - Todos los derechos reservados.<br>
                Enviado desde: $from
            </div>
        </div>
    </body>
    </html>
    ";

    // Cabeceras para correo HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: <$from>" . "\r\n";

    // Nota: En XAMPP/Windows, mail() requiere configuración en php.ini
    // Si se cuenta con PHPMailer, se recomienda integrarlo aquí.
    return @mail($toEmail, $subject, $message, $headers);
}
