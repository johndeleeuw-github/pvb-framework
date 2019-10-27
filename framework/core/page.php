<?php

class Page {

    function __construct() {
        global $app;
        return true;        
    }

    public function load_controller($controller, $action = '') {
        if(empty($controller)) {
            $this->load_view('home');
            return;
        }

        if(!file_exists(ROOT . "controller/" . $controller . ".php")) {
            $this->load_view('page_404');
            return;
        }

        require_once(ROOT . "controller/" . $controller . ".php");
        $load_controller = new $controller($action);
    }

    public function load_model($model) {
        if(file_exists(ROOT . "model/" . $model . ".php")) {
            require_once(ROOT . "model/" . $model . ".php");
            $model_class = ucfirst($model) . 'Model';
            $load_model = new $model_class();
        }
    }

    public function load_view($view) {
        if(file_exists(ROOT . "view/" . $view . ".php")) {
            require_once(ROOT . "view/partials/header.php");
            require_once(ROOT . "view/" . $view . ".php");
            require_once(ROOT . "view/partials/footer.php");
        }
    }

    public function load_plugin($plugin) {
        if(file_exists(ROOT . "public/plugins/" . $plugin . "/" . $plugin . ".php")) {
            require_once(ROOT . "public/plugins/" . $plugin . "/" . $plugin . ".php");
        }
    }

    public function get_url_parameters() {
        $url_parameters = explode('/', $_SERVER['REQUEST_URI']);
        return array_slice($url_parameters, 2);
    }

    public function set_view_data($data) {
        $_SESSION['view_data'] = $data;
    }

    public function get_view_data() {
        return $_SESSION['view_data'];
    }
}

?>