<?php
declare(strict_types=1);
final class HomeController extends Controller {
    public function index(): void {
        $db=Database::connection(); $model=new Course($db); $q=trim((string)($_GET['q'] ?? ''));
        $courses=$model->all($q ?: null); $lessons=$model->lessons(); $user=$_SESSION['user'] ?? null;
        $stats=['words'=>0,'learned'=>0,'streak'=>0];
        if ($user) {
            $s=$db->prepare('SELECT COUNT(*) FROM review_logs WHERE user_id=?'); $s->execute([$user['id']]); $stats['words']=(int)$s->fetchColumn();
            $s=$db->prepare('SELECT COUNT(*) FROM user_vocabulary WHERE user_id=? AND correct_streak>0'); $s->execute([$user['id']]); $stats['learned']=(int)$s->fetchColumn();
            $s=$db->prepare('SELECT COUNT(DISTINCT DATE(reviewed_at)) FROM review_logs WHERE user_id=? AND reviewed_at >= CURDATE() - INTERVAL 6 DAY'); $s->execute([$user['id']]); $stats['streak']=(int)$s->fetchColumn();
        }
        $this->render('home', compact('courses','lessons','q','stats'));
    }
    public function learn(): void {
        $user=$this->requireLogin(); $db=Database::connection();
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $this->verifyCsrf(); $id=(int)($_POST['vocabulary_id'] ?? 0); $result=($_POST['result'] ?? '')==='known'?'known':'again'; $lessonId=(int)($_POST['lesson_id']??0); $cursor=max(0,(int)($_POST['cursor']??0));
            $db->beginTransaction();
            try {
                $s=$db->prepare('INSERT INTO review_logs (user_id,vocabulary_id,result) SELECT ?,id,? FROM vocabulary WHERE id=?'); $s->execute([$user['id'],$result,$id]);
                $s=$db->prepare('SELECT interval_days,correct_streak FROM user_vocabulary WHERE user_id=? AND vocabulary_id=? FOR UPDATE'); $s->execute([$user['id'],$id]); $prior=$s->fetch() ?: ['interval_days'=>0,'correct_streak'=>0];
                if($result==='known') { $streak=(int)$prior['correct_streak']+1; $interval=$streak===1?1:($streak===2?3:min(90,max(4,(int)$prior['interval_days']*2))); $due=(new DateTimeImmutable())->modify('+'.$interval.' days')->format('Y-m-d H:i:s'); }
                else { $streak=0;$interval=0;$due=(new DateTimeImmutable())->modify('+10 minutes')->format('Y-m-d H:i:s'); }
                $s=$db->prepare('INSERT INTO user_vocabulary(user_id,vocabulary_id,next_review_at,interval_days,correct_streak) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE next_review_at=VALUES(next_review_at),interval_days=VALUES(interval_days),correct_streak=VALUES(correct_streak)');
                $s->execute([$user['id'],$id,$due,$interval,$streak]); $db->commit();
            } catch(Throwable $e) { $db->rollBack(); throw $e; }
            $_SESSION['notice']=$result==='known'?'Ajoyib! So‘z o‘rganilganlar qatoriga qo‘shildi.':'Bu so‘z takrorlash uchun navbatga qaytdi.'; header('Location: index.php?route=learn&lesson='.$lessonId); exit;
        }
        $lessonId=(int)($_GET['lesson'] ?? 0);
        $sql='SELECT v.*,l.title lesson_title,c.title course_title FROM vocabulary v JOIN lessons l ON l.id=v.lesson_id JOIN courses c ON c.id=l.course_id LEFT JOIN user_vocabulary uv ON uv.vocabulary_id=v.id AND uv.user_id=? WHERE (uv.vocabulary_id IS NULL OR uv.next_review_at<=NOW())';
        $args=[$user['id']]; if ($lessonId) {$sql.=' AND l.id=?';$args[]=$lessonId;}
        $sql.=' ORDER BY v.id'; $s=$db->prepare($sql); $s->execute($args); $words=$s->fetchAll();
        $cursor=max(0,(int)($_GET['index']??0)); if($words){$cursor=$cursor%count($words);$words=array_merge(array_slice($words,$cursor),array_slice($words,0,$cursor));}
        $allLessons=(new Course($db))->lessons();
        $this->render('learn', ['words'=>$words,'lessonId'=>$lessonId,'cursor'=>$cursor,'allLessons'=>$allLessons,'token'=>$this->csrf()]);
    }
    public function export(): void {
        $u=$this->requireLogin(); $s=Database::connection()->prepare("SELECT v.term,v.translation,v.example_sentence,r.result,r.reviewed_at FROM review_logs r JOIN vocabulary v ON v.id=r.vocabulary_id WHERE r.user_id=? ORDER BY r.reviewed_at DESC"); $s->execute([$u['id']]);
        header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="tilqanot-takrorlash.csv"');
        $out=fopen('php://output','w'); fwrite($out,"\xEF\xBB\xBF"); fputcsv($out,['So‘z','Tarjima','Misol','Natija','Sana']); foreach($s as $row) fputcsv($out,$row); fclose($out); exit;
    }
}
