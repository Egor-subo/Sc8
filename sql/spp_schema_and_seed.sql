CREATE DATABASE IF NOT EXISTS SPp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE SPp;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(120) NOT NULL,
 phone VARCHAR(12) NOT NULL UNIQUE,
 username VARCHAR(50) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 age TINYINT UNSIGNED DEFAULT NULL,
 class_num TINYINT UNSIGNED DEFAULT NULL,
 role ENUM('student','teacher','admin','director','vice_director') NOT NULL DEFAULT 'student',
 avatar_url VARCHAR(255) DEFAULT NULL,
 about TEXT DEFAULT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE teacher_subject_classes (
 id INT AUTO_INCREMENT PRIMARY KEY,
 teacher_id INT NOT NULL,
 subject_id INT NOT NULL,
 class_num TINYINT UNSIGNED NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
 UNIQUE KEY uq_teacher_subject_class (teacher_id, subject_id, class_num)
);

CREATE TABLE subjects (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL UNIQUE);

CREATE TABLE class_subjects (id INT AUTO_INCREMENT PRIMARY KEY, class_num TINYINT UNSIGNED NOT NULL, subject_id INT NOT NULL, FOREIGN KEY (subject_id) REFERENCES subjects(id), UNIQUE KEY uq_class_subject (class_num, subject_id));

CREATE TABLE posts (id INT AUTO_INCREMENT PRIMARY KEY, author_id INT NOT NULL, title VARCHAR(150) NOT NULL, content TEXT NOT NULL, image_url VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (author_id) REFERENCES users(id));

CREATE TABLE post_likes (post_id INT NOT NULL, user_id INT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(post_id,user_id), FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE);

CREATE TABLE post_comments (id INT AUTO_INCREMENT PRIMARY KEY, post_id INT NOT NULL, user_id INT NOT NULL, comment_text TEXT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE);

CREATE TABLE announcements (id INT AUTO_INCREMENT PRIMARY KEY, author_id INT NOT NULL, title VARCHAR(150) NOT NULL, body TEXT NOT NULL, event_date DATE NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (author_id) REFERENCES users(id));

CREATE TABLE announcement_comments (id INT AUTO_INCREMENT PRIMARY KEY, announcement_id INT NOT NULL, user_id INT NOT NULL, comment_text TEXT NOT NULL, rating TINYINT UNSIGNED DEFAULT 5, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE);

CREATE TABLE assignments (id INT AUTO_INCREMENT PRIMARY KEY, subject_id INT NOT NULL, class_num TINYINT UNSIGNED NOT NULL, teacher_id INT NOT NULL, title VARCHAR(150) NOT NULL, description TEXT NOT NULL, due_date DATE NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (subject_id) REFERENCES subjects(id), FOREIGN KEY (teacher_id) REFERENCES users(id));

CREATE TABLE grades (id INT AUTO_INCREMENT PRIMARY KEY, student_id INT NOT NULL, subject_id INT NOT NULL, teacher_id INT NOT NULL, grade TINYINT UNSIGNED NOT NULL, comment VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE, FOREIGN KEY (subject_id) REFERENCES subjects(id), FOREIGN KEY (teacher_id) REFERENCES users(id));

CREATE TABLE support_tickets (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, message VARCHAR(1500) NOT NULL, answer VARCHAR(1500) DEFAULT NULL, answered_by INT DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, answered_at TIMESTAMP NULL DEFAULT NULL, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, FOREIGN KEY (answered_by) REFERENCES users(id) ON DELETE SET NULL);

INSERT INTO subjects(name) VALUES ('Английский язык');
INSERT INTO subjects(name) VALUES ('Биология');
INSERT INTO subjects(name) VALUES ('География');
INSERT INTO subjects(name) VALUES ('Информатика');
INSERT INTO subjects(name) VALUES ('История');
INSERT INTO subjects(name) VALUES ('Литература');
INSERT INTO subjects(name) VALUES ('Математика');
INSERT INTO subjects(name) VALUES ('Музыка');
INSERT INTO subjects(name) VALUES ('Окружающий мир');
INSERT INTO subjects(name) VALUES ('Русский язык');
INSERT INTO subjects(name) VALUES ('Технология');
INSERT INTO subjects(name) VALUES ('Физика');
INSERT INTO subjects(name) VALUES ('Физкультура');
INSERT INTO subjects(name) VALUES ('Химия');

INSERT INTO class_subjects(class_num, subject_id) SELECT 1, id FROM subjects WHERE name='Русский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 1, id FROM subjects WHERE name='Математика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 1, id FROM subjects WHERE name='Окружающий мир';
INSERT INTO class_subjects(class_num, subject_id) SELECT 1, id FROM subjects WHERE name='Музыка';
INSERT INTO class_subjects(class_num, subject_id) SELECT 1, id FROM subjects WHERE name='Физкультура';
INSERT INTO class_subjects(class_num, subject_id) SELECT 1, id FROM subjects WHERE name='Технология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 2, id FROM subjects WHERE name='Русский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 2, id FROM subjects WHERE name='Математика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 2, id FROM subjects WHERE name='Окружающий мир';
INSERT INTO class_subjects(class_num, subject_id) SELECT 2, id FROM subjects WHERE name='Музыка';
INSERT INTO class_subjects(class_num, subject_id) SELECT 2, id FROM subjects WHERE name='Физкультура';
INSERT INTO class_subjects(class_num, subject_id) SELECT 2, id FROM subjects WHERE name='Технология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 3, id FROM subjects WHERE name='Русский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 3, id FROM subjects WHERE name='Математика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 3, id FROM subjects WHERE name='Окружающий мир';
INSERT INTO class_subjects(class_num, subject_id) SELECT 3, id FROM subjects WHERE name='Английский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 3, id FROM subjects WHERE name='Музыка';
INSERT INTO class_subjects(class_num, subject_id) SELECT 3, id FROM subjects WHERE name='Физкультура';
INSERT INTO class_subjects(class_num, subject_id) SELECT 3, id FROM subjects WHERE name='Технология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 4, id FROM subjects WHERE name='Русский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 4, id FROM subjects WHERE name='Математика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 4, id FROM subjects WHERE name='Окружающий мир';
INSERT INTO class_subjects(class_num, subject_id) SELECT 4, id FROM subjects WHERE name='Английский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 4, id FROM subjects WHERE name='Музыка';
INSERT INTO class_subjects(class_num, subject_id) SELECT 4, id FROM subjects WHERE name='Физкультура';
INSERT INTO class_subjects(class_num, subject_id) SELECT 4, id FROM subjects WHERE name='Технология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 5, id FROM subjects WHERE name='Русский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 5, id FROM subjects WHERE name='Литература';
INSERT INTO class_subjects(class_num, subject_id) SELECT 5, id FROM subjects WHERE name='Математика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 5, id FROM subjects WHERE name='История';
INSERT INTO class_subjects(class_num, subject_id) SELECT 5, id FROM subjects WHERE name='Биология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 5, id FROM subjects WHERE name='География';
INSERT INTO class_subjects(class_num, subject_id) SELECT 5, id FROM subjects WHERE name='Английский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 5, id FROM subjects WHERE name='Музыка';
INSERT INTO class_subjects(class_num, subject_id) SELECT 5, id FROM subjects WHERE name='Физкультура';
INSERT INTO class_subjects(class_num, subject_id) SELECT 5, id FROM subjects WHERE name='Технология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 6, id FROM subjects WHERE name='Русский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 6, id FROM subjects WHERE name='Литература';
INSERT INTO class_subjects(class_num, subject_id) SELECT 6, id FROM subjects WHERE name='Математика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 6, id FROM subjects WHERE name='История';
INSERT INTO class_subjects(class_num, subject_id) SELECT 6, id FROM subjects WHERE name='Биология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 6, id FROM subjects WHERE name='География';
INSERT INTO class_subjects(class_num, subject_id) SELECT 6, id FROM subjects WHERE name='Английский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 6, id FROM subjects WHERE name='Музыка';
INSERT INTO class_subjects(class_num, subject_id) SELECT 6, id FROM subjects WHERE name='Физкультура';
INSERT INTO class_subjects(class_num, subject_id) SELECT 6, id FROM subjects WHERE name='Технология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 7, id FROM subjects WHERE name='Русский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 7, id FROM subjects WHERE name='Литература';
INSERT INTO class_subjects(class_num, subject_id) SELECT 7, id FROM subjects WHERE name='Математика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 7, id FROM subjects WHERE name='История';
INSERT INTO class_subjects(class_num, subject_id) SELECT 7, id FROM subjects WHERE name='Биология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 7, id FROM subjects WHERE name='География';
INSERT INTO class_subjects(class_num, subject_id) SELECT 7, id FROM subjects WHERE name='Английский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 7, id FROM subjects WHERE name='Музыка';
INSERT INTO class_subjects(class_num, subject_id) SELECT 7, id FROM subjects WHERE name='Физкультура';
INSERT INTO class_subjects(class_num, subject_id) SELECT 7, id FROM subjects WHERE name='Технология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 8, id FROM subjects WHERE name='Русский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 8, id FROM subjects WHERE name='Литература';
INSERT INTO class_subjects(class_num, subject_id) SELECT 8, id FROM subjects WHERE name='Математика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 8, id FROM subjects WHERE name='История';
INSERT INTO class_subjects(class_num, subject_id) SELECT 8, id FROM subjects WHERE name='Биология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 8, id FROM subjects WHERE name='География';
INSERT INTO class_subjects(class_num, subject_id) SELECT 8, id FROM subjects WHERE name='Английский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 8, id FROM subjects WHERE name='Музыка';
INSERT INTO class_subjects(class_num, subject_id) SELECT 8, id FROM subjects WHERE name='Физкультура';
INSERT INTO class_subjects(class_num, subject_id) SELECT 8, id FROM subjects WHERE name='Технология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 9, id FROM subjects WHERE name='Русский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 9, id FROM subjects WHERE name='Литература';
INSERT INTO class_subjects(class_num, subject_id) SELECT 9, id FROM subjects WHERE name='Математика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 9, id FROM subjects WHERE name='История';
INSERT INTO class_subjects(class_num, subject_id) SELECT 9, id FROM subjects WHERE name='Биология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 9, id FROM subjects WHERE name='География';
INSERT INTO class_subjects(class_num, subject_id) SELECT 9, id FROM subjects WHERE name='Английский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 9, id FROM subjects WHERE name='Музыка';
INSERT INTO class_subjects(class_num, subject_id) SELECT 9, id FROM subjects WHERE name='Физкультура';
INSERT INTO class_subjects(class_num, subject_id) SELECT 9, id FROM subjects WHERE name='Технология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 10, id FROM subjects WHERE name='Русский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 10, id FROM subjects WHERE name='Литература';
INSERT INTO class_subjects(class_num, subject_id) SELECT 10, id FROM subjects WHERE name='Математика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 10, id FROM subjects WHERE name='История';
INSERT INTO class_subjects(class_num, subject_id) SELECT 10, id FROM subjects WHERE name='Биология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 10, id FROM subjects WHERE name='География';
INSERT INTO class_subjects(class_num, subject_id) SELECT 10, id FROM subjects WHERE name='Английский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 10, id FROM subjects WHERE name='Музыка';
INSERT INTO class_subjects(class_num, subject_id) SELECT 10, id FROM subjects WHERE name='Физкультура';
INSERT INTO class_subjects(class_num, subject_id) SELECT 10, id FROM subjects WHERE name='Технология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='Русский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='Литература';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='Математика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='История';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='Биология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='География';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='Английский язык';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='Музыка';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='Физкультура';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='Технология';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='Информатика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='Физика';
INSERT INTO class_subjects(class_num, subject_id) SELECT 11, id FROM subjects WHERE name='Химия';

INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES
('Системный администратор','+79990000001','admin','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',35,NULL,'admin','Администратор портала'),
('Директор школы','+79990000002','director','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',48,NULL,'director','Руководство школой'),
('Заместитель директора','+79990000003','vice','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',42,NULL,'vice_director','Контроль учебного процесса'),
('Ученик Тестовый','+79990000111','student1','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',14,8,'student','Тестовый профиль ученика');

INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Английский язык 1','+79991000001','t_1','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Английский язык');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Английский язык 2','+79991000002','t_2','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Английский язык');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Биология 1','+79991000003','t_3','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Биология');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Биология 2','+79991000004','t_4','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Биология');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель География 1','+79991000005','t_5','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: География');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель География 2','+79991000006','t_6','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: География');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Информатика 1','+79991000007','t_7','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Информатика');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Информатика 2','+79991000008','t_8','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Информатика');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель История 1','+79991000009','t_9','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: История');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель История 2','+79991000010','t_10','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: История');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Литература 1','+79991000011','t_11','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Литература');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Литература 2','+79991000012','t_12','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Литература');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Математика 1','+79991000013','t_13','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Математика');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Музыка 1','+79991000014','t_14','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Музыка');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Окружающий мир 1','+79991000015','t_15','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Окружающий мир');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Русский язык 1','+79991000016','t_16','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Русский язык');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Технология 1','+79991000017','t_17','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Технология');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Физика 1','+79991000018','t_18','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Физика');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Физика 2','+79991000019','t_19','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Физика');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Физкультура 1','+79991000020','t_20','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Физкультура');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Физкультура 2','+79991000021','t_21','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Физкультура');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Химия 1','+79991000022','t_22','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Химия');
INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role, about) VALUES ('Учитель Химия 2','+79991000023','t_23','$2y$10$6x4wzFeR3yG6YxV6K9QFf.KlJfqhE0jvTo0Y3y96f9i7s6hC18lK2',30,NULL,'teacher','Преподаватель: Химия');

INSERT INTO posts(author_id,title,content,image_url) VALUES
(2,'Добро пожаловать','Это официальный портал школы SPp. Здесь будут новости, задания и оценки.','https://placehold.co/1200x500'),
(3,'Неделя науки','На этой неделе пройдут открытые уроки и мастер-классы.','https://placehold.co/1000x420');
INSERT INTO announcements(author_id,title,body,event_date) VALUES
(2,'Сдача нормативов','18 мая в 10:00 сдача нормативов для 5 и 6 классов. Быть в спортивной форме.','2026-05-18'),
(3,'Контрольная работа','20 мая состоится контрольная по математике у 8А и 8Б классов.','2026-05-20');
