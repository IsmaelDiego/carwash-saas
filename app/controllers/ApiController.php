<?php
// Ubicación: app/controllers/ApiController.php

class ApiController
{
    public function dni()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $dni = $input['dni'] ?? '';

        // 4. Validar que tenga 8 o 11 dígitos numéricos
        $len = strlen($dni);
        if (($len !== 8 && $len !== 11) || !is_numeric($dni)) {
            echo json_encode(['success' => false, 'message' => 'El documento debe tener 8 (DNI) u 11 (RUC) dígitos válidos']);
            exit;
        }

        $token = '14f7052063fd269673be7268b0bb824ea6d804614a792e780b3342d783e9661f';
        $curl = curl_init();

        $endpoint = "https://apiperu.dev/api/dni";
        $params = json_encode(['dni' => $dni]);

        // Si es RUC:
        if ($len === 11) {
            $endpoint = "https://apiperu.dev/api/ruc";
            $params = json_encode(['ruc' => $dni]);
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Fuerza IPv4 para evitar errores de DNS "Could not resolve host" en Windows/XAMPP
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);


        if ($err) {
            echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $err]);
        } else {
            echo $response;
        }
        exit;
    }
}
