-- NSBM SportsHub: update v4 (campus student list + "students online").
-- Do this ONLY if the site shows "The database needs one small update".
-- (Normally the site adds these by itself the first time it is opened.)
--
-- 1. In phpMyAdmin click YOUR database in the left list (do not run CREATE DATABASE or USE).
-- 2. Open the SQL tab, paste everything below and press Go.
-- If you see "Duplicate column name 'last_seen'", that part was already done: run only the second command.

ALTER TABLE users ADD COLUMN last_seen DATETIME NULL;

CREATE TABLE IF NOT EXISTS student_roster(id INT AUTO_INCREMENT PRIMARY KEY, student_id VARCHAR(30) NOT NULL UNIQUE,
  full_name VARCHAR(100) NOT NULL, email VARCHAR(120) NOT NULL UNIQUE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
