-- Création de la base de données
CREATE DATABASE IF NOT EXISTS youdemy;
USE youdemy;

-- Table des utilisateurs
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des catégories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des tags
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des cours
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    content TEXT,
    category_id INT,
    teacher_id INT,
    is_published BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
);
ALTER TABLE courses ADD COLUMN featured TINYINT(1) DEFAULT 0;
-- Table de relation cours-tags (many-to-many)
CREATE TABLE course_tags (
    course_id INT,
    tag_id INT,
    PRIMARY KEY (course_id, tag_id),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Table des inscriptions aux cours
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT,
    student_id INT,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insertion d'un administrateur par défaut
INSERT INTO users (username, email, password, role) 
VALUES ('admin', 'admin@youdemy.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Note: Le mot de passe est 'password', hashé avec bcrypt
-- Inserting into courses table
-- Inserting users into the users table
INSERT INTO users (username, email, password, role, is_active)
VALUES 
('adminUser', 'admin@example.com', 'password123', 'admin', true),
('johnDoe', 'john.doe@example.com', 'password123', 'teacher', true),
('janeSmith', 'jane.smith@example.com', 'password123', 'teacher', true),
('studentOne', 'student.one@example.com', 'password123', 'student', true),
('studentTwo', 'student.two@example.com', 'password123', 'student', true),
('alexGreen', 'alex.green@example.com', 'password123', 'teacher', true),
('markBrown', 'mark.brown@example.com', 'password123', 'student', false);

INSERT INTO categories (id, name) VALUES
(1, 'Web Development'),
(2, 'JavaScript'),
(3, 'Machine Learning'),
(4, 'Database'),
(5, 'Cybersecurity'),
(6, 'Digital Marketing'),
(7, 'Game Development'),
(8, 'Mobile Development');

INSERT INTO courses (title, description, content, category_id, teacher_id, is_published)
VALUES
('Introduction to PHP', 'Learn the basics of PHP, the popular server-side programming language.', 'Content of PHP basics...', 1, 1, true),
('Advanced JavaScript', 'Master JavaScript with advanced topics like closures, promises, and async/await.', 'Content of advanced JavaScript...', 2, 2, true),
('Web Development with Laravel', 'Learn web development using the Laravel PHP framework.', 'Content of Laravel framework...', 1, 3, true),
('React for Beginners', 'Get started with React, the popular JavaScript library for building user interfaces.', 'Content of React basics...', 2, 4, true),
('Machine Learning 101', 'A beginner-friendly introduction to Machine Learning concepts and algorithms.', 'Content of Machine Learning basics...', 3, 5, false),
('Introduction to MySQL', 'Learn how to use MySQL for database management, including queries, joins, and optimization.', 'Content of MySQL basics...', 4, 6, true);
-- Inserting multiple courses into the courses table
INSERT INTO courses (title, description, content, category_id, teacher_id, is_published)
VALUES
('Introduction to PHP', 'Learn the basics of PHP, the popular server-side programming language.', 'Content of PHP basics...', 1, 1, true),
('Advanced JavaScript', 'Master JavaScript with advanced topics like closures, promises, and async/await.', 'Content of advanced JavaScript...', 2, 2, true),
('Web Development with Laravel', 'Learn web development using the Laravel PHP framework.', 'Content of Laravel framework...', 1, 3, true),
('React for Beginners', 'Get started with React, the popular JavaScript library for building user interfaces.', 'Content of React basics...', 2, 4, true),
('Machine Learning 101', 'A beginner-friendly introduction to Machine Learning concepts and algorithms.', 'Content of Machine Learning basics...', 3, 5, false),
('Introduction to MySQL', 'Learn how to use MySQL for database management, including queries, joins, and optimization.', 'Content of MySQL basics...', 4, 6, true),
('Python for Beginners', 'Learn the basics of Python programming for data analysis, web development, and automation.', 'Content of Python basics...', 3, 7, true),
('Mobile App Development with React Native', 'Master mobile app development using React Native for iOS and Android.', 'Content of React Native development...', 5, 8, true),
('Introduction to Cloud Computing', 'Learn the fundamentals of cloud computing and services like AWS, Azure, and Google Cloud.', 'Content of Cloud Computing basics...', 6, 9, true),
('Data Science with R', 'Explore the world of data science and analysis using the R programming language.', 'Content of Data Science with R...', 7, 10, true),
('Building APIs with Node.js', 'Learn how to build scalable RESTful APIs using Node.js and Express.', 'Content of Building APIs with Node.js...', 8, 11, true),
('Android Development with Kotlin', 'Learn Android app development using Kotlin, the modern programming language for Android.', 'Content of Android development with Kotlin...', 9, 12, false),
('Full Stack Web Development with MERN', 'Master full-stack web development using MongoDB, Express, React, and Node.js (MERN).', 'Content of Full Stack MERN development...', 2, 13, true),
('Cybersecurity Essentials', 'Understand the basics of cybersecurity, including network security and ethical hacking.', 'Content of Cybersecurity fundamentals...', 10, 14, true),
('Game Development with Unity', 'Learn how to build 2D and 3D games using the Unity game engine.', 'Content of Unity game development...', 11, 15, true),
('Introduction to SQL', 'Learn how to use SQL to manage relational databases and perform queries.', 'Content of SQL basics...', 4, 16, true),
('Advanced Python for Data Science', 'Take your Python skills to the next level for advanced data science projects and machine learning.', 'Content of Advanced Python for Data Science...', 3, 17, true),
('UI/UX Design Fundamentals', 'Learn the principles of user interface and user experience design for web and mobile apps.', 'Content of UI/UX Design basics...', 12, 18, true),
('Introduction to Docker and Kubernetes', 'Learn the basics of containerization using Docker and Kubernetes for orchestration.', 'Content of Docker and Kubernetes basics...', 6, 19, true),
('Digital Marketing Strategy', 'Learn strategies for digital marketing, including SEO, social media, and content marketing.', 'Content of Digital Marketing...', 13, 20, true),
('Introduction to Blockchain', 'Learn about blockchain technology and its applications in finance, healthcare, and beyond.', 'Content of Blockchain basics...', 14, 21, false),
('Game Development with Unreal Engine', 'Learn how to develop high-performance 3D games using Unreal Engine.', 'Content of Unreal Engine game development...', 11, 22, true);
-- Inserting new users with unique names
INSERT INTO users (username, email, password, role, is_active)
VALUES
('michaelMiller', 'michael.miller@example.com', 'password123', 'teacher', true),
('lisaWhite', 'lisa.white@example.com', 'password123', 'teacher', true),
('davidClark', 'david.clark@example.com', 'password123', 'teacher', true),
('emmaWatson', 'emma.watson@example.com', 'password123', 'teacher', true),
('charlesDavis', 'charles.davis@example.com', 'password123', 'teacher', true),
('karenMartin', 'karen.martin@example.com', 'password123', 'teacher', true),
('jamesWilson', 'james.wilson@example.com', 'password123', 'teacher', true),
('sophieLewis', 'sophie.lewis@example.com', 'password123', 'teacher', true),
('oliverHarris', 'oliver.harris@example.com', 'password123', 'teacher', true),
('lucyWalker', 'lucy.walker@example.com', 'password123', 'teacher', true);
