<?php
declare(strict_types=1);
final class AdminController extends Controller {
    public function index(): void { $this->requireEditor(); $m=new Course(Database::connection());$this->render('admin',['lessons'=>$m->lessonsForAdmin(),'courses'=>$m->coursesForAdmin(),'words'=>$m->words(),'token'=>$this->csrf(),'isAdmin'=>($_SESSION['user']['role']==='admin')]); }
    public function createWord(): void {
        $this->requireEditor();$this->verifyCsrf();$d=['lesson_id'=>(int)($_POST['lesson_id']??0),'term'=>trim((string)($_POST['term']??'')),'translation'=>trim((string)($_POST['translation']??'')),'example_sentence'=>trim((string)($_POST['example_sentence']??'')),'pronunciation'=>trim((string)($_POST['pronunciation']??''))];
        if(!$d['lesson_id']||mb_strlen($d['term'])<1||mb_strlen($d['translation'])<1){$_SESSION['notice']='Dars, so‘z va tarjimani to‘ldiring.';}else{try{(new Course(Database::connection()))->addWord($d);$_SESSION['notice']='Yangi so‘z saqlandi.';}catch(PDOException $e){$_SESSION['notice']='Bu so‘z mazkur darsda avvaldan bor.';}}
        $this->redirect('admin');
    }
    public function updateWord(): void { $this->requireEditor();$this->verifyCsrf();$d=['id'=>(int)($_POST['id']??0),'term'=>trim((string)($_POST['term']??'')),'translation'=>trim((string)($_POST['translation']??'')),'example_sentence'=>trim((string)($_POST['example_sentence']??'')),'pronunciation'=>trim((string)($_POST['pronunciation']??''))];if(!$d['id']||!$d['term']||!$d['translation'])$_SESSION['notice']='So‘z va tarjimani to‘ldiring.';else{(new Course(Database::connection()))->updateWord($d);$_SESSION['notice']='So‘z yangilandi.';}$this->redirect('admin'); }
    public function deleteWord(): void { $this->requireAdmin();$this->verifyCsrf();(new Course(Database::connection()))->deleteWord((int)($_POST['id']??0));$_SESSION['notice']='So‘z o‘chirildi.';$this->redirect('admin'); }
    public function createLesson(): void { $this->requireEditor();$this->verifyCsrf();$d=$this->lessonData();if(!$d['course_id']||!$d['title'])$_SESSION['notice']='Kurs va dars nomini kiriting.';else{(new Course(Database::connection()))->addLesson($d);$_SESSION['notice']='Dars yaratildi.';}$this->redirect('admin'); }
    public function updateLesson(): void { $this->requireEditor();$this->verifyCsrf();$d=$this->lessonData();$d['id']=(int)($_POST['id']??0);if(!$d['id']||!$d['course_id']||!$d['title'])$_SESSION['notice']='Kurs va dars nomini kiriting.';else{(new Course(Database::connection()))->updateLesson($d);$_SESSION['notice']='Dars yangilandi.';}$this->redirect('admin'); }
    public function deleteLesson(): void { $this->requireAdmin();$this->verifyCsrf();(new Course(Database::connection()))->deleteLesson((int)($_POST['id']??0));$_SESSION['notice']='Dars o‘chirildi. Unga tegishli so‘zlar ham o‘chirildi.';$this->redirect('admin'); }
    private function lessonData(): array { return ['course_id'=>(int)($_POST['course_id']??0),'title'=>trim((string)($_POST['title']??'')),'unit_no'=>max(1,(int)($_POST['unit_no']??1)),'summary'=>trim((string)($_POST['summary']??''))]; }
}
