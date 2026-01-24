<?php

class Router
{
    /**
     * 3.
     * Punto de entrada principal
     */
    public function run(): void
    {
        $uri = $_SERVER['REQUEST_URI'];
        $this->dispatch($uri);
    }

    /**
     * Lógica de despacho de rutas
     */
    public function dispatch(string $uri): void
    {
        // 1. Limpieza (Igual que antes)
        $path = parse_url($uri, PHP_URL_PATH);
        if (defined('BASE_URL') && BASE_URL !== '' && strpos($path, BASE_URL) === 0) {
            $path = substr($path, strlen(BASE_URL));
        }
        $path = trim($path, '/');

        // 2. Alias (Igual que antes)
        if ($path === '' || $path === 'index.php') $path = 'home/index';
        elseif ($path === 'login') $path = 'auth/login';
        elseif ($path === 'logout') $path = 'auth/logout';

        // 3. Desglose
        $segments = explode('/', $path);

        // --- NUEVA LÓGICA: DETECCIÓN DE CARPETAS ---
        $folder = '';
        
        // Verificamos si el primer segmento es una carpeta real dentro de controllers
        // Ej: Si la URL es "admin/cliente/lista", segments[0] es "admin"
        if (!empty($segments) && is_dir(APP_PATH . '/controllers/' . $segments[0])) {
            $folder = array_shift($segments) . '/'; // Guardamos 'admin/' y lo sacamos del array
        }
        // -------------------------------------------

        // 4. Obtener Controlador
        // Si ya sacamos 'admin', el siguiente segmento es 'cliente'
        // Si la carpeta era admin y no escribieron nada más (midominio.com/admin), mandamos al Dashboard
        if (empty($segments)) {
            $controllerBase = 'Dashboard'; 
        } else {
            $controllerBase = array_shift($segments);
        }

        $controllerName = ucfirst($controllerBase) . 'Controller';

        // 5. Obtener Método
        $methodName = !empty($segments) ? array_shift($segments) : 'index';

        // 6. Parámetros
        $params = $segments;

        // ... (el código anterior de tu dispatch se queda igual hasta el paso 6) ...

        // 7. Buscar el archivo (Concatenamos la $folder)
        $file = APP_PATH . '/controllers/' . $folder . $controllerName . '.php';

        if (!file_exists($file)) {
            $this->send404("Controlador no encontrado en: controllers/$folder$controllerName.php");
            return;
        }

        require_once $file;

        // --- NUEVA LÓGICA DE NAMESPACES ---
        
        // 8. Construir el nombre completo de la clase (Fully Qualified Class Name)
        $fullControllerName = $controllerName; // Asumimos primero que no hay namespace

        if (!empty($folder)) {
            // Convertimos "admin/" en "Admin"
            $subNamespace = ucfirst(trim($folder, '/')); 
            // El resultado será: Controllers\Admin\ClienteController
            $fullControllerName = "Controllers\\" . $subNamespace . "\\" . $controllerName;
        }

        // 9. Verificar si la clase existe (Probamos con Namespace primero)
        if (!class_exists($fullControllerName)) {
            // Fallback: Si no existe con namespace, probamos a la antigua (sin namespace)
            if (!class_exists($controllerName)) {
                $this->send404("Clase no encontrada. Asegúrate de que el namespace coincida con: $fullControllerName");
                return;
            }
            $fullControllerName = $controllerName;
        }

        // 10. Instanciar el Controlador
        $controller = new $fullControllerName();

        if (method_exists($controller, $methodName)) {
            call_user_func_array([$controller, $methodName], $params);
        } else {
            $this->send404("Método $methodName no encontrado en $controllerName");
        }
    }
    /**
     * Función auxiliar para errores 404
     */
    private function send404(string $message = ''): void
    {
        http_response_code(404);
        
        // Si existe una vista personalizada de 404, úsala
        if (defined('VIEW_PATH') && file_exists(VIEW_PATH . '/404.view.php')) {
            require VIEW_PATH . '/404.view.php';
        } else {
            // Fallback simple por si no has creado la vista aún
            echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>";
            echo "<h1>Error 404</h1>";
            echo "<p>Página no encontrada.</p>";
            echo "<p><small>Debug: $message</small></p>";
            echo "<a href='" . BASE_URL . "'>Volver al inicio</a>";
            echo "</div>";
        }
        exit;
    }
}