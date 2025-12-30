<?php

class Router
{
    /**
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
        // 1. Limpieza de la URL
        // Obtenemos solo la ruta, sin parámetros GET (?id=1) y quitamos la BASE_URL
        $path = parse_url($uri, PHP_URL_PATH);

        if (defined('BASE_URL') && BASE_URL !== '' && strpos($path, BASE_URL) === 0) {
            $path = substr($path, strlen(BASE_URL));
        }

        $path = trim($path, '/');

        // 2. Manejo de Alias (Rutas cortas)
        // Convertimos rutas "bonitas" a la estructura Controlador/Metodo real
        if ($path === '' || $path === 'index.php') {
            $path = 'home/index'; // Página de inicio por defecto
        } elseif ($path === 'login') {
            $path = 'auth/login';
        } elseif ($path === 'logout') {
            $path = 'auth/logout';
        } elseif ($path === 'register') {
            $path = 'auth/register';
        }

        // 3. Desglose de la ruta
        $segments = explode('/', $path);

        // a. Obtener Controlador (Primer segmento)
        $controllerBase = array_shift($segments); // Saca el primer elemento
        $controllerName = ucfirst($controllerBase) . 'Controller';

        // b. Obtener Método (Segundo segmento) - Default: index
        $methodName = !empty($segments) ? array_shift($segments) : 'index';

        // c. Obtener Parámetros (Lo que sobra en el array)
        // Ejemplo: en /producto/editar/5, el '5' queda en $params
        $params = $segments;

        // 4. Buscar el archivo del controlador
        $file = APP_PATH . '/controllers/' . $controllerName . '.php';

        if (!file_exists($file)) {
            $this->send404("Controlador no encontrado: $controllerName");
            return;
        }

        // 5. Cargar e Instanciar
        require_once $file;

        if (!class_exists($controllerName)) {
            $this->send404("Clase no definida: $controllerName");
            return;
        }

        $controller = new $controllerName();

        // 6. Ejecutar el método pasando parámetros
        if (method_exists($controller, $methodName)) {
            // Esta función mágica permite pasar el array $params como argumentos individuales ($id, $slug, etc.)
            call_user_func_array([$controller, $methodName], $params);
        } else {
            $this->send404("Método no encontrado: $methodName");
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