USE sportshub;

CREATE TABLE IF NOT EXISTS news(id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(160) NOT NULL, sport VARCHAR(40) NULL,
  summary VARCHAR(300) NOT NULL, body TEXT NOT NULL, image VARCHAR(120), author_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(author_id) REFERENCES users(id) ON DELETE SET NULL);
CREATE TABLE IF NOT EXISTS banners(id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(120) NOT NULL, subtitle VARCHAR(220),
  image VARCHAR(120) NOT NULL, link VARCHAR(255), sort_order INT NOT NULL DEFAULT 0, active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);

INSERT INTO banners(title,subtitle,image,link,sort_order) VALUES
('Sports at NSBM Green University','Join one of eleven official sports clubs, book facilities and follow every result.','sample-1.jpg','clubs.php',1),
('Life on the Green Campus','Train, compete and make friends. Admin can change these banners any time.','sample-2.jpg','fixtures.php',2);
INSERT INTO news(title,sport,summary,body,image) VALUES
('Welcome to SportsHub News','Cricket','Sample story: the sports office will post match reports and announcements here.','This is a sample news story so the home page is not empty.\n\nLog in as admin, open Admin > News, and edit or delete this story, then add your own news with a photo.','sample-cricket.jpg'),
('Swimming club training schedule','Swimming','Sample story: new early-morning practice slots are open for club members.','Sample text. Replace it with the real schedule from the Admin > News page.','sample-swimming.jpg'),
('Inter-faculty badminton tournament','Badminton','Sample story: registrations for the badminton tournament will open soon.','Sample text. Replace it with real details from the Admin > News page.','sample-badminton.jpg');
