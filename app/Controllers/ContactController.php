<?php
declare(strict_types=1);
final class ContactController extends Controller {
    public function show(): void { $this->render('contact',['token'=>$this->csrf()]); }
    public function submit(): void { $this->verifyCsrf();$name=trim((string)($_POST['name']??''));$email=filter_var(trim((string)($_POST['email']??'')),FILTER_VALIDATE_EMAIL);$message=trim((string)($_POST['message']??''));
        if(!$email||mb_strlen($name)<2||mb_strlen($message)<10||mb_strlen($message)>3000){$_SESSION['notice']='Ism, email va xabarni tekshiring (xabar 10–3000 belgi bo‘lsin).';}
        else{$s=Database::connection()->prepare('INSERT INTO contact_messages(user_id,name,email,message) VALUES(?,?,?,?)');$s->execute([$_SESSION['user']['id']??null,$name,$email,$message]);$_SESSION['notice']='Xabaringiz saqlandi. Rahmat!';}
        $this->redirect('contact');
    }
}
