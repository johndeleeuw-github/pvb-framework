<?php 

define("ROOT", $_SERVER['DOCUMENT_ROOT'] . "/");

class Load {
    public $database;
    public $format;
    public $page;

    function __construct() {
        $this->database = $this->loadCore("database");
        $this->format = $this->loadCore("format");
        $this->page = $this->loadCore("page");
    }

    public function loadCore($file) {
        if(!empty($file) && file_exists(ROOT . "core/" . $file . ".php")) {
            require_once(ROOT . "core/" . $file . ".php");
            $classname = ucfirst($file);
            return new $classname();
        }
    }
}

$app = new Load();
?>