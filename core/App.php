<?php

// ============================================================
// App.php — Router / Dispatcher
// Membaca URL → menentukan Controller & Method yang dipanggil
// ============================================================

class App {

    protected $controllerName = 'Home';
    protected $methodName     = 'index';
    protected $params         = [];

    public function __construct() {
        $url = $this->parseUrl();

        // ── 1. Tentukan Controller ────────────────────────────
        if (isset($url[0]) && !empty($url[0])) {
            $this->controllerName = ucfirst(strtolower($url[0]));
            unset($url[0]);
        }

        $controllerClass = $this->controllerName . 'Controller';
        $controllerFile  = __DIR__ . '/../controllers/' . $controllerClass . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            $this->handle404();
            return;
        }

        $controller = new $controllerClass();

        // ── 2. Tentukan Method ────────────────────────────────
        if (isset($url[1]) && !empty($url[1])) {
            if (method_exists($controller, $url[1])) {
                $this->methodName = $url[1];
            } else {
                $this->handle404();
                return;
            }
            unset($url[1]);
        }

        // ── 3. Kumpulkan Parameter (misal: /admin/edit/5) ─────
        $this->params = $url ? array_values($url) : [];

        // ── 4. Jalankan! ─────────────────────────────────────
        call_user_func_array([$controller, $this->methodName], $this->params);
    }

    // Memecah URL menjadi segmen array
    private function parseUrl() {
        if (isset($_GET['url'])) {
            $clean = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            return explode('/', $clean);
        }
        return [];
    }

    // Tampilkan error 404 sederhana
    private function handle404() {
        http_response_code(404);
        require_once __DIR__ . '/../controllers/HomeController.php';
        $ctrl = new HomeController();
        $ctrl->index();
    }
}
