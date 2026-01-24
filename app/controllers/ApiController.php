<?php
// Ubicación: app/controllers/ApiController.php

class ApiController 
{
    public function dni() 
    {
        // 1. Cabecera de seguridad: Le decimos al navegador que devolveremos JSON
        header('Content-Type: application/json');

        // 2. Solo permitimos peticiones POST (Seguridad)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        // 3. Capturar el DNI que envía el JavaScript
        $input = json_decode(file_get_contents('php://input'), true);
        $dni = $input['dni'] ?? '';

        // 4. Validar que tenga 8 dígitos numéricos
        if (strlen($dni) !== 8 || !is_numeric($dni)) {
            echo json_encode(['success' => false, 'message' => 'DNI inválido']);
            exit;
        }

        // 5. Tu Token de apiperu.dev (Seguro en el servidor)
        $token = 'bab6a5397a6adad7ee334fb5c30ebad3b2a81dea1a012dc3b8da82414e95cac1';

        // 6. Preparar la consulta cURL
        $curl = curl_init();
        $params = json_encode(['dni' => $dni]);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://apiperu.dev/api/dni",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYPEER => false, // Evita errores de certificados locales
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ],
        ));

        // 7. Ejecutar y obtener respuesta
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        // 8. Enviar respuesta al JavaScript
        if ($err) {
            echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $err]);
        } else {
            echo $response; // Devolvemos el JSON tal cual nos lo da la API
        }
        exit;
    }
}