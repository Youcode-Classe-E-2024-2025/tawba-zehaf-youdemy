<?php
namespace Youdemy\Controllers;
use Youdemy\Models\Entity\Course;
use Youdemy\Config\Database;
use PDO;
class HomeController {
    private $courseModel;

    public function __construct() {
        $db = new PDO('mysql:host=localhost;dbname=youdemy', 'root', '');
        $this->courseModel = new Course($db);
    }

    // public function index()
    // {
    //     $featuredCourses = $this->courseModel->getFeaturedCourses();
        
    //     $content = $this->render('home/index.php', [
    //         'featuredCourses' => $featuredCourses
    //     ]);

    //     $this->renderLayout('layouts/main.php', [
    //         'title' => 'YouDemy - Plateforme d\'apprentissage en ligne',
    //         'content' => $content
    //     ]);
    // }
    public function index()
{
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT c.*, u.username as teacher_name 
            FROM courses c 
            JOIN users u ON c.teacher_id = u.id 
            ORDER BY c.created_at DESC";
    $courses = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    $this->render('main.php', ['courses' => $courses]);
}


    private function render($view, $data = [])
    {
        extract($data);
        ob_start();
        require VIEW_PATH . '/' . $view; 
        return ob_get_clean();
    }

    private function renderLayout($layout, $data = [])
    {
        extract($data);
        require VIEW_PATH . $layout;
    }


}