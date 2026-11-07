
CREATE DATABASE IF NOT EXISTS sportshub CHARACTER SET utf8mb4;
USE sportshub;

CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, email VARCHAR(120) NOT NULL UNIQUE,
  student_id VARCHAR(30), password_hash VARCHAR(255) NOT NULL, role ENUM('student','admin') NOT NULL DEFAULT 'student',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE clubs(id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, sport VARCHAR(40) NOT NULL,
  description TEXT, page_url VARCHAR(255), image VARCHAR(120));
CREATE TABLE memberships(id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, club_id INT NOT NULL,
  status ENUM('pending','approved','rejected') DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(user_id,club_id), FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE, FOREIGN KEY(club_id) REFERENCES clubs(id) ON DELETE CASCADE);
CREATE TABLE events(id INT AUTO_INCREMENT PRIMARY KEY, club_id INT NULL, title VARCHAR(120) NOT NULL,
  type ENUM('training','match','tournament') NOT NULL, event_date DATE NOT NULL, event_time TIME NOT NULL,
  venue VARCHAR(80) NOT NULL, max_participants INT NULL, FOREIGN KEY(club_id) REFERENCES clubs(id) ON DELETE SET NULL);
CREATE TABLE registrations(id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, event_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE(user_id,event_id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE, FOREIGN KEY(event_id) REFERENCES events(id) ON DELETE CASCADE);
CREATE TABLE results(id INT AUTO_INCREMENT PRIMARY KEY, event_id INT NOT NULL, home_team VARCHAR(60) NOT NULL,
  away_team VARCHAR(60) NOT NULL, home_score INT NOT NULL, away_score INT NOT NULL,
  FOREIGN KEY(event_id) REFERENCES events(id) ON DELETE CASCADE);
CREATE TABLE facilities(id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(80) NOT NULL, kind ENUM('facility','equipment') NOT NULL);
CREATE TABLE bookings(id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, facility_id INT NOT NULL, booking_date DATE NOT NULL,
  slot TINYINT NOT NULL, purpose VARCHAR(200) NOT NULL, status ENUM('pending','approved','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(facility_id) REFERENCES facilities(id) ON DELETE CASCADE);
CREATE TABLE attendance(id INT AUTO_INCREMENT PRIMARY KEY, event_id INT NOT NULL, user_id INT NOT NULL, present TINYINT(1) DEFAULT 1,
  UNIQUE(event_id,user_id), FOREIGN KEY(event_id) REFERENCES events(id) ON DELETE CASCADE, FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);


INSERT INTO clubs(name,sport,page_url) VALUES
('Athletics Club','Athletics','https://www.nsbm.ac.lk/club/nsbm-athletics-club/'),
('Badminton Club','Badminton','https://www.nsbm.ac.lk/club/nsbm-badminton-club/'),
('Chess Club','Chess','https://www.nsbm.ac.lk/club/chess-club/'),
('Cricket Club','Cricket','https://www.nsbm.ac.lk/club/cricket-club/'),
('Martial Arts Club','Martial Arts','https://www.nsbm.ac.lk/club/nsbm-martial-arts-club/'),
('Netball Club','Netball','https://www.nsbm.ac.lk/club/nsbm-netball-club/'),
('Rugby Club','Rugby','https://www.nsbm.ac.lk/club/nsbm-rugby-club/'),
('Swimming Club','Swimming','https://www.nsbm.ac.lk/club/swimming-club/'),
('Table Tennis Club','Table Tennis','https://www.nsbm.ac.lk/club/table-tennis-club/'),
('Tennis Club','Tennis','https://www.nsbm.ac.lk/club/tennis-club/'),
('Volleyball Club','Volleyball','https://www.nsbm.ac.lk/club/nsbm-volleyball-club/');
INSERT INTO facilities(name,kind) VALUES
('Recreational Centre','facility'),('Playgrounds','facility'),('Indoor Stadium','facility'),('Swimming Pool','facility'),
('Indoor Gymnasium','facility'),('Outdoor Gymnasium','facility'),('Sports Pavilion','facility'),
('Badminton rackets','equipment'),('Volleyball set','equipment'),('Rugby balls','equipment'),('Cricket kit','equipment');


UPDATE clubs SET image=CONCAT(LOWER(REPLACE(sport,' ','-')),'.jpg');
