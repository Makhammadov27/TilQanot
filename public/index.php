<?php
declare(strict_types=1);
ini_set('session.use_strict_mode','1');
session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','path'=>'/']);
session_start();
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; script-src 'self'; img-src 'self' data:; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");

require dirname(__DIR__).'/app/Core/Database.php';
require dirname(__DIR__).'/app/Core/Controller.php';
require dirname(__DIR__).'/app/Models/Course.php';
foreach (glob(dirname(__DIR__).'/app/Controllers/*.php') as $file) require $file;

$route=(string)($_GET['route']??'home');
try {
    switch($route) {
        case 'home': (new HomeController())->index(); break;
        case 'learn': (new HomeController())->learn(); break;
        case 'export': (new HomeController())->export(); break;
        case 'login': case 'register': $auth=new AuthController(); if($_SERVER['REQUEST_METHOD']==='POST')$auth->submit($route);else $auth->show($route); break;
        case 'logout': if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}(new AuthController())->logout();break;
        case 'admin': (new AdminController())->index(); break;
        case 'admin-word-add': if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}(new AdminController())->createWord();break;
        case 'admin-word-update': if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}(new AdminController())->updateWord();break;
        case 'admin-word-delete': if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}(new AdminController())->deleteWord();break;
        case 'admin-lesson-add': if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}(new AdminController())->createLesson();break;
        case 'admin-lesson-update': if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}(new AdminController())->updateLesson();break;
        case 'admin-lesson-delete': if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}(new AdminController())->deleteLesson();break;
        case 'contact': (new ContactController())->show();break;
        case 'contact-send': if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}(new ContactController())->submit();break;
        default: http_response_code(404);(new class extends Controller { public function show():void{$this->render('error',['title'=>'Sahifa topilmadi','message'=>'Bu manzilda sahifa mavjud emas.']);}})->show();
    }
} catch (Throwable $e) {
    error_log((string)$e);
    http_response_code(500);
    if (($_GET['route']??'')==='') echo 'Ma’lumotlar bazasiga ulanib bo‘lmadi. README.md dagi sozlash bosqichlarini bajaring.';
    else echo 'Xatolik yuz berdi. Sozlamalarni tekshiring va qayta urinib ko‘ring.';
}
