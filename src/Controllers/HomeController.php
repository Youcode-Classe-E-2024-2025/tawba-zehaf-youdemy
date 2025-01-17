<?php
require_once 'src/Models/Course.php';
class HomeController {
    private $courseModel;

    public function __construct() {
        $this->courseModel = new Course();
    }

    public function index()
    {
        $featuredCourses = $this->courseModel->getFeaturedCourses();
        
        $content = $this->render('home/index.php', [
            'featuredCourses' => $featuredCourses
        ]);

        $this->renderLayout('layouts/main.php', [
            'title' => 'YouDemy - Plateforme d\'apprentissage en ligne',
            'content' => $content
        ]);
    }

    private function render($view, $data = [])
    {
        extract($data);
        ob_start();
        require VIEW_PATH . $view;
        // return ob_get_clean();
    }

    private function renderLayout($layout, $data = [])
    {
        extract($data);
        require VIEW_PATH . $layout;
    }


}