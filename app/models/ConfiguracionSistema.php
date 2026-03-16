<?php

class ConfiguracionSistema {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function get() {
        $stmt = $this->pdo->query("SELECT * FROM configuracion_sistema WHERE id_configuracion = 1");
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function actualizar($data) {
        $sql = "UPDATE configuracion_sistema SET 
                    nombre_negocio = :nombre,
                    abreviatura = :abrev,
                    moneda = :moneda,
                    modo_sin_cajero = :modo,
                    meta_puntos_canje = :meta";
                    
        $params = [
            ':nombre' => trim($data['nombre_negocio']),
            ':abrev'  => trim($data['abreviatura']),
            ':moneda' => trim($data['moneda']),
            ':modo'   => (int)($data['modo_sin_cajero'] ?? 0),
            ':meta'   => (int)($data['meta_puntos_canje'] ?? 10)
        ];

        if (!empty($data['logo_path'])) {
            $sql .= ", logo = :logo";
            $params[':logo'] = $data['logo_path'];
        }

        $sql .= " WHERE id_configuracion = 1";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

}
