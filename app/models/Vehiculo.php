<?php


class Vehiculo {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // LISTAR TODOS (Relacionando Cliente y Categoría)
    public function getAll() {
        try {
            $sql = "SELECT 
                        v.id_vehiculo, 
                        v.placa, 
                        v.color, 
                        v.observaciones, 
                        v.fecha_registro,
                        v.id_cliente,
                        v.id_categoria,
                        CONCAT(c.nombres, ' ', c.apellidos) as nombre_propietario,
                        c.dni as dni_propietario,
                        cat.nombre as nombre_categoria
                    FROM vehiculos v
                    INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                    INNER JOIN categorias_vehiculos cat ON v.id_categoria = cat.id_categoria
                    ORDER BY v.id_vehiculo DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // VALIDAR PLACA DUPLICADA
    public function existePlaca($placa, $id_vehiculo = null) {
        $sql = "SELECT COUNT(*) FROM vehiculos WHERE placa = :placa";
        $params = [':placa' => $placa];

        if ($id_vehiculo) {
            $sql .= " AND id_vehiculo != :id";
            $params[':id'] = $id_vehiculo;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    // REGISTRAR
    public function registrar($data) {
        try {
            $sql = "INSERT INTO vehiculos (id_cliente, id_categoria, placa, color, observaciones) 
                    VALUES (:id_cliente, :id_categoria, :placa, :color, :observaciones)";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id_cliente'    => $data['id_cliente'],
                ':id_categoria'  => $data['id_categoria'],
                ':placa'         => strtoupper(trim($data['placa'])),
                ':color'         => $data['color'] ?? null,
                ':observaciones' => $data['observaciones'] ?? null
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // EDITAR
    public function editar($data) {
        try {
            // Nota: Permitimos editar Categoría, Color y Observaciones. 
            // Placa y Dueño suelen ser fijos, pero si necesitas cambiarlos, agrégalos aquí.
            $sql = "UPDATE vehiculos SET 
                        id_categoria = :id_categoria,
                        color = :color,
                        observaciones = :observaciones
                    WHERE id_vehiculo = :id_vehiculo";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id_categoria'  => $data['id_categoria'],
                ':color'         => $data['color'],
                ':observaciones' => $data['observaciones'],
                ':id_vehiculo'   => $data['id_vehiculo']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // ELIMINAR
    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM vehiculos WHERE id_vehiculo = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // HELPER: OBTENER CATEGORÍAS (Para el Select)
    public function getCategorias() {
        $stmt = $this->pdo->query("SELECT * FROM categorias_vehiculos ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}