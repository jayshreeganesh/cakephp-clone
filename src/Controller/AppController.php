<?php
namespace App\Controller;

use CakeCore\Request;
use CakeCore\Flash;
use CakeCore\Router;
use CakeCore\TableRegistry;
use CakeCore\Table;

class AppController {
    public Request $request;
    public Flash $Flash;
    protected array $viewVars = [];
    protected string $layout = 'default';
    protected ?string $view = null;
    protected string $controllerName = '';
    protected string $actionName = '';
    protected bool $rendered = false;

    public function __construct() {
        $this->request = new Request();
        $this->Flash = new Flash();
    }

    public function set($key, $value = null) {
        if (is_array($key)) {
            $this->viewVars = array_merge($this->viewVars, $key);
        } else {
            $this->viewVars[$key] = $value;
        }
    }

    public function fetchTable(string $alias): Table {
        return TableRegistry::get($alias);
    }

    public function __get(string $alias) {
        return $this->fetchTable($alias);
    }

    public function redirect($url) {
        $target = Router::url($url);
        header('Location: ' . $target);
        exit;
    }

    public function setControllerName(string $name) { $this->controllerName = $name; }
    public function setActionName(string $name) { $this->actionName = $name; }

    public function render(?string $view = null, ?string $layout = null) {
        if ($this->rendered) return;
        $this->rendered = true;

        $viewName = $view ?: $this->actionName;
        $layoutName = $layout ?: $this->layout;

        $viewFile = ROOT . "/src/View/{$this->controllerName}/{$viewName}.php";
        $layoutFile = ROOT . "/src/View/layout/{$layoutName}.php";

        extract($this->viewVars);
        $this->Flash = $this->Flash;

        ob_start();
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<p>View file not found: {$viewFile}</p>";
        }
        $content = ob_get_clean();

        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content;
        }
    }
}
