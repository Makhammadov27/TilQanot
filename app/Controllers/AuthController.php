<?php
declare(strict_types=1);
final class AuthController extends Controller {
    public function show(string $mode): void { if(!empty($_SESSION['user']))$this->redirect('home'); $this->render('auth',['mode'=>$mode,'token'=>$this->csrf(),'errors'=>[]]); }
    public function submit(string $mode): void {
        $this->verifyCsrf(); $name=trim((string)($_POST['name']??'')); $email=filter_var(trim((string)($_POST['email']??'')),FILTER_VALIDATE_EMAIL); $password=(string)($_POST['password']??''); $errors=[]; $db=Database::connection();
        if(!$email)$errors[]='Email manzilini to‘g‘ri kiriting.';
        if($mode==='register' && mb_strlen($name)<2)$errors[]='Ismingizni kamida 2 ta harf bilan kiriting.';
        if(mb_strlen($password)<8)$errors[]='Parol kamida 8 belgidan iborat bo‘lsin.';
        if(!$errors && $mode==='register') { try {$s=$db->prepare('INSERT INTO users(name,email,password_hash) VALUES(?,?,?)');$s->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);} catch(PDOException $e){$errors[]='Bu email bilan hisob mavjud.';} }
        if(!$errors) { $s=$db->prepare('SELECT id,name,email,password_hash,role FROM users WHERE email=?');$s->execute([$email]);$u=$s->fetch(); if(!$u || !password_verify($password,$u['password_hash']))$errors[]='Email yoki parol xato.'; else {session_regenerate_id(true);unset($u['password_hash']);$_SESSION['user']=$u;$_SESSION['csrf']=bin2hex(random_bytes(32));$this->redirect('home');} }
        $this->render('auth',['mode'=>$mode,'token'=>$this->csrf(),'errors'=>$errors]);
    }
    public function logout(): void { $this->verifyCsrf(); $_SESSION=[]; if(ini_get('session.use_cookies')){ $p=session_get_cookie_params(); setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']); } session_destroy(); header('Location: index.php'); exit; }
}
