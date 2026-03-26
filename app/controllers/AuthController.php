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
            
            if ($user) {
                if (password_verify($password, $user['password_hash'])) {
                    // VERIFICACIÓN DE ESTADO (Requerido por el USER)
                    if ($user['estado'] == 0) {
                        http_response_code(403); // Prohibido
                        echo json_encode([
                            'success' => false, 
                            'is_inactive' => true,
                            'message' => 'Tu cuenta está INACTIVA. Contacta con el administrador.'
                        ]);
                        return;
                    }

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
                        'message'  => '¡Acceso Concedido! Bienvenido/a.',
                        'redirect' => BASE_URL . '/home'
                    ]);
                } else {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'message' => 'La contraseña es inválida.']);
                }
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'El correo/DNI ingresado no existe.']);
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
                'id_rol'   => $data['id_rol'], // Default: Operario
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
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        // Cargar vista de despedida
        require VIEW_PATH . '/auth/sign-out.view.php';
        exit;
    }

    /**
     * Solicitar recuperación de contraseña (Notificación al Admin)
     */
    public function solicitarRecuperacion(): void
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        global $pdo;
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $identifier = $data['identifier'] ?? '';

        if (empty($identifier)) {
            echo json_encode(['success' => false, 'message' => 'Ingresa tu DNI o Correo']);
            return;
        }

        $userModel = new User($pdo);
        $user = null;

        if (strpos($identifier, '@') !== false) {
            $user = $userModel->findByEmail($identifier);
        } else {
            $user = $userModel->findByDni($identifier);
        }

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'El usuario no existe en el sistema']);
            return;
        }

        // --- LÓGICA DE CORREO (Link/PIN) ---
        // SOLO PARA ADMINISTRADORES (ID_ROL = 1)
        if ($user['id_rol'] == 1) {
            if (!empty($user['email'])) {
                try {
                    $pin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                    // Guardar PIN
                    $stmt = $pdo->prepare("INSERT INTO password_reset_tokens (id_usuario, token, expires_at) VALUES (:u, :t, :e)");
                    $stmt->execute([':u' => $user['id_usuario'], ':t' => $pin, ':e' => $expiry]);

                    // Enviar Correo (Usamos @ para evitar que warnings de PHP rompan el JSON en local)
                    if (sendRecoveryEmail($user['email'], $user['nombres'], $pin)) {
                        ob_clean();
                        echo json_encode([
                            'success' => true, 
                            'has_email' => true,
                            'message' => 'Código de 6 dígitos enviado a ' . substr($user['email'], 0, 3) . '***@***'
                        ]);
                    } else {
                        // MODO SIMULACIÓN PARA DESARROLLO (XAMPP)
                        if (APP_ENV === 'development') {
                            ob_clean();
                            echo json_encode([
                                'success' => true, 
                                'has_email' => true,
                                'message' => '[MODO TEST] Servidor de correo no configurado. Tu PIN es: ' . $pin
                            ]);
                        } else {
                            throw new Exception("El servidor de correo no está disponible momentáneamente.");
                        }
                    }
                } catch (Exception $e) {
                    ob_clean();
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Error: ' . $e->getMessage() . ' Contacta con soporte técnico.'
                    ]);
                }
            } else {
                ob_clean();
                echo json_encode([
                    'success' => false, 
                    'message' => 'Tu cuenta de Administrador no tiene un correo vinculado para recibir el PIN.'
                ]);
            }
            return;
        }

        // --- MÉTODO PARA OTROS ROLES (Cajeros, Operarios) ---
        try {
            // Verificar si ya tiene una solicitud pendiente
            $stmt = $pdo->prepare("SELECT id_notificacion FROM notificaciones_recuperacion WHERE id_usuario = :id AND estado = 'PENDIENTE'");
            $stmt->execute([':id' => $user['id_usuario']]);
            if ($stmt->fetch()) {
                ob_clean();
                echo json_encode(['success' => true, 'has_email' => false, 'message' => 'Ya existe una solicitud pendiente. El administrador pronto reiniciará tu acceso.']);
                return;
            }

            $stmt = $pdo->prepare("INSERT INTO notificaciones_recuperacion (id_usuario) VALUES (:id)");
            $stmt->execute([':id' => $user['id_usuario']]);

            ob_clean();
            echo json_encode([
                'success' => true, 
                'has_email' => false,
                'message' => 'Solicitud enviada. Por seguridad, el administrador debe autorizar el cambio de tu contraseña.'
            ]);
        } catch (Exception $e) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Error al procesar notificación: ' . $e->getMessage()]);
        }
    }

    /**
     * Verificar PIN de recuperación
     */
    public function verificarPin(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $identifier = $data['identifier'] ?? '';
        $pin = $data['pin'] ?? '';

        if (empty($pin) || empty($identifier)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        // Buscar al usuario
        $userModel = new User($pdo);
        if (strpos($identifier, '@') !== false) $user = $userModel->findByEmail($identifier);
        else $user = $userModel->findByDni($identifier);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Usuario inválido']);
            return;
        }

        $stmt = $pdo->prepare("SELECT id FROM password_reset_tokens WHERE id_usuario = :u AND token = :t AND used = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([':u' => $user['id_usuario'], ':t' => $pin]);
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => true, 'message' => 'PIN verificado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'PIN incorrecto o expirado']);
        }
    }

    /**
     * Restablecer contraseña con PIN
     */
    public function restablecerConPin(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $identifier = $data['identifier'] ?? '';
        $pin = $data['pin'] ?? '';
        $new_password = $data['password'] ?? '';

        if (empty($pin) || empty($identifier) || empty($new_password)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        if (strlen($new_password) < 6) {
            echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
            return;
        }

        // Buscar al usuario
        $userModel = new User($pdo);
        if (strpos($identifier, '@') !== false) $user = $userModel->findByEmail($identifier);
        else $user = $userModel->findByDni($identifier);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Usuario inválido']);
            return;
        }

        // Verificar PIN nuevamente antes de cambiar
        $stmt = $pdo->prepare("SELECT id FROM password_reset_tokens WHERE id_usuario = :u AND token = :t AND used = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([':u' => $user['id_usuario'], ':t' => $pin]);
        $token = $stmt->fetch();

        if ($token) {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            $pdo->beginTransaction();
            try {
                // 1. Actualizar Password
                $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = :h WHERE id_usuario = :u");
                $stmt->execute([':h' => $hash, ':u' => $user['id_usuario']]);

                // 2. Marcar PIN como usado
                $stmt = $pdo->prepare("UPDATE password_reset_tokens SET used = 1 WHERE id = :id");
                $stmt->execute([':id' => $token['id']]);

                $pdo->commit();
                echo json_encode(['success' => true, 'message' => '¡Contraseña actualizada con éxito! Ahora puedes iniciar sesión.']);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Error al actualizar contraseña']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Sesión de recuperación expirada']);
        }
    }

    /**
     * Verificar la contraseña del usuario actual (Seguridad Extra)
     */
    public function verifyPassword(): void
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Sesión no iniciada']);
            return;
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $password = $data['password'] ?? '';

        if (empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Contraseña requerida']);
            return;
        }

        global $pdo;
        $userModel = new User($pdo);
        
        // El usuario ya está logueado, usamos su ID de sesión
        $userId = $_SESSION['user']['id'];
        
        // Necesitamos un método para buscar por ID o simplemente usar findByDni/Email
        // Buscamos directamente en la BD por ID para estar seguros
        $stmt = $pdo->prepare("SELECT password_hash FROM usuarios WHERE id_usuario = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
        }
    }
}
