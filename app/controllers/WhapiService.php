<?php

namespace Services;

class WhapiService
{
    private $apiToken = "DYLHAtAKERFlHqaMtNq46BBkNmmq3x1r";
    private $apiUrl   = "https://gate.whapi.cloud/messages/text";
    private $codigoPais = "51";

    public function enviarMensaje($numero, $mensaje)
    {
        $numeroFormateado = $this->limpiarNumero($numero);

        if (strlen($numeroFormateado) < 11) {
            return ['success' => false, 'error' => 'Número inválido'];
        }

        $data = [
            "to"   => $numeroFormateado,
            "body" => $mensaje
        ];

        return $this->ejecutarCurl($data);
    }

    private function limpiarNumero($numero)
    {
        $limpio = preg_replace('/[^0-9]/', '', $numero);
        if (strlen($limpio) === 9) {
            $limpio = $this->codigoPais . $limpio;
        }
        return $limpio;
    }

    private function ejecutarCurl($data)
    {
        $ch = curl_init($this->apiUrl);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->apiToken}",
                "Content-Type: application/json"
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'response' => json_decode($response, true)];
        }

        return ['success' => false, 'error' => $response];
    }
}
