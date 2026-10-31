<?php
declare(strict_types=1);
final class Course {
    public function __construct(private PDO $db) {}
    public function all(?string $search = null): array {
        $sql = "SELECT c.*, COUNT(DISTINCT l.id) lessons_count, COUNT(DISTINCT v.id) words_count FROM courses c LEFT JOIN lessons l ON l.course_id=c.id LEFT JOIN vocabulary v ON v.lesson_id=l.id WHERE c.is_published=1";
        $args=[]; if ($search) { $sql .= ' AND (c.title LIKE ? OR c.description LIKE ? OR c.level LIKE ? OR c.language LIKE ?)'; $q='%'.$search.'%'; $args=[$q,$q,$q,$q]; }
        $sql .= ' GROUP BY c.id ORDER BY c.id'; $s=$this->db->prepare($sql); $s->execute($args); return $s->fetchAll();
    }
    public function lessons(): array { return $this->db->query('SELECT l.*, c.title course_title, COUNT(v.id) words_count FROM lessons l JOIN courses c ON c.id=l.course_id LEFT JOIN vocabulary v ON v.lesson_id=l.id WHERE c.is_published=1 GROUP BY l.id ORDER BY c.id,l.unit_no')->fetchAll(); }
    public function addWord(array $d): void {
        $s=$this->db->prepare('INSERT INTO vocabulary (lesson_id,term,translation,example_sentence,pronunciation) VALUES (?,?,?,?,?)');
        $s->execute([$d['lesson_id'],$d['term'],$d['translation'],$d['example_sentence'] ?? '',$d['pronunciation'] ?? '']);
    }
    public function updateWord(array $d): void { $s=$this->db->prepare('UPDATE vocabulary SET term=?,translation=?,example_sentence=?,pronunciation=? WHERE id=?');$s->execute([$d['term'],$d['translation'],$d['example_sentence']??'',$d['pronunciation']??'',$d['id']]); }
    public function lessonsForAdmin(): array { return $this->db->query('SELECT l.id,l.course_id,l.title,l.unit_no,l.summary,c.title course_title FROM lessons l JOIN courses c ON c.id=l.course_id ORDER BY c.id,l.unit_no')->fetchAll(); }
    public function coursesForAdmin(): array { return $this->db->query('SELECT id,title FROM courses ORDER BY id')->fetchAll(); }
    public function addLesson(array $d): void { $s=$this->db->prepare('INSERT INTO lessons(course_id,title,unit_no,summary) VALUES(?,?,?,?)');$s->execute([$d['course_id'],$d['title'],$d['unit_no'],$d['summary']]); }
    public function updateLesson(array $d): void { $s=$this->db->prepare('UPDATE lessons SET course_id=?,title=?,unit_no=?,summary=? WHERE id=?');$s->execute([$d['course_id'],$d['title'],$d['unit_no'],$d['summary'],$d['id']]); }
    public function deleteLesson(int $id): void { $s=$this->db->prepare('DELETE FROM lessons WHERE id=?');$s->execute([$id]); }
    public function deleteWord(int $id): void { $s=$this->db->prepare('DELETE FROM vocabulary WHERE id=?'); $s->execute([$id]); }
    public function words(): array { return $this->db->query('SELECT v.*,l.title lesson_title,c.title course_title FROM vocabulary v JOIN lessons l ON l.id=v.lesson_id JOIN courses c ON c.id=l.course_id ORDER BY v.id DESC LIMIT 100')->fetchAll(); }
}
