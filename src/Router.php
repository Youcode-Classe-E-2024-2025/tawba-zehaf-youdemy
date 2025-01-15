<?php

class Router {

    // Directory paths for actions (controllers) and views
    private $actionDirectory;
    private $viewDirectory;

    // Default view and 404 (not found) view
    private $defaultView;
    private $notFound;

    /**
     * Constructor for the Router class.
     *
     * @param string $dir Path to the directory where view files are stored.
     * @param string $default Default view to render when no view is specified.
     * @param string $notFound View to render when a specified view is not found.
     * @param string $actionDir Path to the directory where controller (action) files are stored.
     */
    public function __construct($dir = __DIR__ . '/../Views', $default = 'home', $notFound = '404', $actionDir = __DIR__ . '/../Controllers') {
        $this->viewDirectory = rtrim($dir, '/') . '/';
        $this->defaultView = $default;
        $this->notFound = $notFound;
        $this->actionDirectory = rtrim($actionDir, '/') . '/';
    }

    /**
     * Render the view file based on the `view` parameter in the URL.
     *
     * - If no `view` parameter is provided, the default view is loaded.
     * - If the specified view file does not exist, the 404 view is rendered.
     *
     * @return void
     */
    public function view(): void {
        // Retrieve the view name from the URL or use the default view
        $view = $_GET['view'] ?? $this->defaultView;

        // Sanitize and format the view name
        $view = ucfirst(strtolower(basename($view)));

        // Construct the full path to the view file
        $viewFile = $this->viewDirectory . $view . '_view.php';

        // Check if the view file exists and include it, otherwise include the 404 view
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            require_once $this->viewDirectory . $this->notFound . '_view.php';
        }
    }

    /**
     * Execute the controller action based on the `action` parameter in the URL.
     *
     * - The `action` parameter should follow the format `Controller_method`.
     * - The corresponding controller file and method will be called if they exist.
     *
     * @return mixed The return value of the controller method, if any.
     */
    public function action() {
        // Check if the `action` parameter is set in the URL
        if (!isset($_GET['action'])) {
            return;
        }

        // Retrieve and sanitize the action name
        $action = basename($_GET['action']);
        $action = explode('_', $action);

        // Extract the controller class name and method name
        $className = $action[0] . 'Controller';
        $actionFile = $this->actionDirectory . $action[0] . 'Controller.php';

        // Check if the controller file and method exist
        if (class_exists($className, true)) {
            require_once $actionFile;
            if (method_exists($className, $action[1])) {
                // Instantiate the controller and call the method
                $controller = new $className();
                return call_user_func([$controller, $action[1]]);
            }
        }
    }
}

