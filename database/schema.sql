CREATE DATABASE IF NOT EXISTS tilqanot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tilqanot;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','operator','student') NOT NULL DEFAULT 'student',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE courses (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  language VARCHAR(60) NOT NULL DEFAULT 'English',
  level VARCHAR(40) NOT NULL,
  description TEXT NOT NULL,
  accent VARCHAR(24) NOT NULL DEFAULT '#6c62ef',
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE lessons (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  course_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(150) NOT NULL,
  unit_no SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  summary TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_lessons_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE vocabulary (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lesson_id BIGINT UNSIGNED NOT NULL,
  term VARCHAR(120) NOT NULL,
  translation VARCHAR(160) NOT NULL,
  example_sentence VARCHAR(255) NOT NULL DEFAULT '',
  pronunciation VARCHAR(120) NOT NULL DEFAULT '',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_vocabulary_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
  UNIQUE KEY uq_lesson_term (lesson_id, term)
) ENGINE=InnoDB;

CREATE TABLE review_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  vocabulary_id BIGINT UNSIGNED NOT NULL,
  result ENUM('known','again') NOT NULL,
  reviewed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_reviews_vocab FOREIGN KEY (vocabulary_id) REFERENCES vocabulary(id) ON DELETE CASCADE,
  KEY idx_review_user_date (user_id, reviewed_at)
) ENGINE=InnoDB;

CREATE TABLE user_vocabulary (
  user_id BIGINT UNSIGNED NOT NULL,
  vocabulary_id BIGINT UNSIGNED NOT NULL,
  next_review_at DATETIME NOT NULL,
  interval_days SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  correct_streak SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, vocabulary_id),
  KEY idx_due_reviews (user_id, next_review_at),
  CONSTRAINT fk_user_vocab_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_user_vocab_word FOREIGN KEY (vocabulary_id) REFERENCES vocabulary(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_messages_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Create a first administrator by registering in the app, then promote that account:
-- UPDATE users SET role='admin' WHERE email='your@email.uz';

INSERT INTO courses (title, language, level, description, accent) VALUES
('English Foundations', 'English', 'A1 · Boshlang‘ich', 'Kundalik suhbatlar uchun eng kerakli ibora va so‘zlar.', '#7067f0'),
('Travel English', 'English', 'A2 · Elementar', 'Sayohat, aeroport va mehmonxonada erkin muloqot qiling.', '#23a890'),
('Business English', 'English', 'B1 · O‘rta', 'Ish uchrashuvlari va yozishmalar uchun amaliy lug‘at.', '#f29d52'),
('Spanish Basics', 'Español', 'A1 · Boshlang‘ich', 'Ispan tilidagi ilk tanishuv va kundalik iboralar.', '#e97868'),
('Deutsch A1', 'Deutsch', 'A1 · Boshlang‘ich', 'Nemis tilida salomlashuv va sodda savol-javoblar.', '#4f8bc9');

INSERT INTO lessons (course_id,title,unit_no,summary) VALUES
(1,'Salomlashuv va tanishuv',1,'Tanishish, hol-ahvol so‘rash va xayrlashish.'),
(1,'Kundalik odatlar',2,'Kun tartibi va odatlarni sodda gaplarda aytish.'),
(2,'Aeroportda',1,'Ro‘yxatdan o‘tish va yo‘l so‘rash.'),
(3,'Ish uchrashuvi',1,'Uchrashuvni boshlash va fikr bildirish.'),
(4,'Primeras palabras',1,'Ispan tilidagi salomlashuv va tanishuv.'),
(5,'Hallo und willkommen',1,'Nemis tilida salomlashish va o‘zingizni tanishtirish.');

INSERT INTO vocabulary (lesson_id,term,translation,example_sentence,pronunciation) VALUES
(1,'Hello','Salom','Hello, my name is Sam.','he-LOU'),(1,'Nice to meet you','Tanishganimdan xursandman','Nice to meet you, Amina.','nays tu miit yu'),(1,'How are you?','Qalaysiz?','How are you today?','hau ar yu'),(1,'I am from Uzbekistan','Men O‘zbekistondanman','I am from Uzbekistan.','ay em from uzbekistan'),(1,'See you later','Keyinroq ko‘rishamiz','See you later, Ali!','sii yu leyter'),
(2,'Wake up','Uyg‘onmoq','I wake up at seven.','weyk ap'),(2,'Usually','Odatda','I usually walk to work.','yujuali'),(2,'Have breakfast','Nonushta qilmoq','We have breakfast at home.','hev brekfast'),
(3,'Boarding pass','Samolyotga chiqish taloni','Here is my boarding pass.','bording pas'),(3,'Departure','Jo‘nab ketish','The departure is at noon.','diparcher'),(3,'Gate','Chiqish darvozasi','Which gate is it?','geyt'),
(4,'Agenda','Kun tartibi','Let us review the agenda.','ajenda'),(4,'Deadline','Oxirgi muddat','The deadline is Friday.','dedlayn'),(4,'Follow up','Keyin davom ettirmoq','I will follow up by email.','folo ap'),
(5,'Hola','Salom','Hola, me llamo Ana.','ola'),(5,'Gracias','Rahmat','Muchas gracias.','grasias'),(5,'Por favor','Iltimos','Un café, por favor.','por favor'),
(6,'Hallo','Salom','Hallo, ich bin Amir.','halo'),(6,'Danke','Rahmat','Danke schön.','danke'),(6,'Bitte','Iltimos / marhamat','Ein Wasser, bitte.','bite');
