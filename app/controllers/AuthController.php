<?php

class AuthController
{
    /**
     * Maneja el inicio de sesión
     */
    public function login(): void
    {
        // 1. Si el usuario ya está logueado, lo mandamos directo al Home (Router decidirá dashboard)
        if (isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        // 2. Si la petición es POST, asumimos que viene del JavaScript (fetch)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json'); // Respuesta siempre en JSON

            // Leer el cuerpo de la petición (JSON raw)
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            // Validar campos vacíos
            if (empty($email) || empty($password)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Email y contraseña son obligatorios']);
                return;
            }

            $userModel = new User($pdo);
            
            // Buscar usuario (El modelo debe filtrar por active = 1)
            $user = $userModel->findByEmail($email);

            // Verificar contraseña
            if ($user && password_verify($password, $user['password'])) {
                
                // Seguridad: Regenerar ID de sesión para evitar Session Fixation
                session_regenerate_id(true);

                // Guardar datos en sesión
                $_SESSION['user'] = [
                    'id'   => $user['id'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'email' => $user['email']
                ];

                // Responder éxito a JS
                echo json_encode([
                    'success'  => true,
                    'redirect' => BASE_URL . '/home'
                ]);
            } else {
                // Responder error a JS
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Credenciales inválidas o cuenta inactiva']);
            }
            return; // Importante: Salir para no renderizar HTML
        }

        // 3. Si es GET, simplemente mostramos el formulario HTML
        require VIEW_PATH . '/auth/login.view.php';
    }

    /**
     * Maneja el registro de usuarios
     */
    public function register(): void
    {
        // Si ya está logueado, no debería ver el registro (opcional)
        if (isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            // Leer JSON
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // Validaciones básicas
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                return;
            }

            $userModel = new User($pdo);

            // Verificar si el correo ya existe
            // Nota: findByEmail suele filtrar activos, idealmente deberías tener un método checkEmailExists
            // pero para este caso práctico, si encuentra uno, bloqueamos.
            if ($userModel->findByEmail($data['email'])) {
                http_response_code(409); // Conflict
                echo json_encode(['success' => false, 'message' => 'El correo electrónico ya está registrado']);
                return;
            }

            // Intentar crear el usuario
            try {
                if ($userModel->create($data)) {
                    echo json_encode([
                        'success' => true,
                        'redirect' => BASE_URL . '/login' // JS redirigirá aquí
                    ]);
                } else {
                    throw new Exception("Error al guardar en base de datos");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
            }
            return;
        }

        // Mostrar la vista de registro
        require VIEW_PATH . '/auth/register.view.php';
    }

    /**
     * Cerrar sesión
     */
    public function logout(): void
    {
        // 1. Vaciar array de sesión
        $_SESSION = [];

        // 2. Invalidar la cookie de sesión (Borrado profundo)
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // 3. Destruir la sesión en el servidor
        session_destroy();

        // ---------------------------------------------------------
        // CAMBIO AQUÍ: En lugar de redirigir, cargamos la vista.
        // ---------------------------------------------------------
        // La sesión ya está destruida en este punto, así que es seguro.
        
        require VIEW_PATH . '/auth/sign-out.view.php';
        
        // No necesitamos exit aquí porque es el final del script, 
        // pero no está de más ponerlo.
        exit;
    }
}