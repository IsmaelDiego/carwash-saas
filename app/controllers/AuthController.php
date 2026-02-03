<?php

class AuthController
{
    /**
     * Maneja el inicio de sesión (Soporta Email o DNI)
     */
    public function login(): void
    {
        // 1. Si ya está logueado, al Home
        if (isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        // 2. Proceso de Login (POST desde JS)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // El frontend puede enviar el campo como 'email' o 'identifier'
            // Asumimos que el input del usuario llega aquí
            $identifier = $data['email'] ?? $data['identifier'] ?? ''; 
            $password   = $data['password'] ?? '';

            if (empty($identifier) || empty($password)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Usuario y contraseña obligatorios']);
                return;
            }

            $userModel = new User($pdo);
            $user = null;

            // ---------------------------------------------------------
            // LÓGICA HÍBRIDA: ¿Es Email o DNI?
            // ---------------------------------------------------------
            if (strpos($identifier, '@') !== false) {
                // Tiene arroba -> Es Admin usando Email
                $user = $userModel->findByEmail($identifier);
            } else {
                // No tiene arroba -> Es Empleado usando DNI
                $user = $userModel->findByDni($identifier);
            }

            // ---------------------------------------------------------
            // VERIFICACIÓN DE PASSWORD (V3.2)
            // ---------------------------------------------------------
            // Nota: La columna en BD es 'password_hash'
            if ($user && password_verify($password, $user['password_hash'])) {
                
                session_regenerate_id(true);

                // Guardamos datos usando las columnas de V3.2
                $_SESSION['user'] = [
                    'id'       => $user['id_usuario'], // id_usuario
                    'name'     => $user['nombres'],    // nombres
                    'dni'      => $user['dni'],        // dni
                    'role'     => $user['id_rol'],     // id_rol (1, 2 o 3)
                    'rolename' => $user['rol_nombre'], // Viene del JOIN
                    'avatar'   => $user['avatar_url'] ?? 'default.png'
                ];

                echo json_encode([
                    'success'  => true,
                    'redirect' => BASE_URL . '/home'
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas o usuario inactivo']);
            }
            return;
        }

        // 3. Mostrar vista
        require VIEW_PATH . '/auth/login.view.php';
    }

    /**
     * Registro de usuarios (Adaptado a DNI y Nombres)
     */
    public function register(): void
    {
        if (isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // Validar campos requeridos para V3.2
            // 'nombres', 'dni', 'password' son obligatorios. Email es opcional para operarios.
            if (empty($data['nombres']) || empty($data['dni']) || empty($data['password'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'DNI, Nombre y Contraseña son obligatorios']);
                return;
            }

            $userModel = new User($pdo);

            // Verificar si el DNI ya existe (Primary Unique)
            if ($userModel->findByDni($data['dni'])) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Este DNI ya está registrado']);
                return;
            }

            // Preparar datos para el Modelo
            // Por defecto, si se registran desde fuera, les damos rol 3 (Operario) o lo que definas.
            $newUser = [
                'id_rol'   => 3, // Default: Operario
                'dni'      => $data['dni'],
                'nombres'  => $data['nombres'],
                'email'    => $data['email'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'password' => $data['password']
            ];

            try {
                if ($userModel->create($newUser)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Usuario registrado correctamente',
                        'redirect' => BASE_URL . '/login'
                    ]);
                } else {
                    throw new Exception("No se pudo crear el registro");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            return;
        }

        require VIEW_PATH . '/auth/register.view.php';
    }

    /**
     * Cerrar sesión (Sin cambios mayores, solo limpieza)
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        // Cargar vista de despedida
        require VIEW_PATH . '/auth/sign-out.view.php';
        exit;
    }
}