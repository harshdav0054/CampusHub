-- =========================
-- Create Database
-- =========================
DROP DATABASE IF EXISTS CampusHub;
CREATE DATABASE CampusHub;
USE CampusHub;

-- =========================
-- Students Table
-- =========================
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- =========================
-- College Signup/Login Table
-- =========================
CREATE TABLE IF NOT EXISTS college_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- =========================
-- College Dashboard/Profile Table
-- =========================
CREATE TABLE IF NOT EXISTS college_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_account_id INT NOT NULL,
    college_name VARCHAR(150),
    address VARCHAR(255),
    website VARCHAR(255),
    logo VARCHAR(255),
    course_name VARCHAR(150),
    about TEXT,
    FOREIGN KEY (college_account_id) REFERENCES college_accounts(id) ON DELETE CASCADE
);

-- =========================
-- Admins Table
-- =========================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- =========================
-- Approvals Table
-- =========================
CREATE TABLE IF NOT EXISTS approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_id INT,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (college_id) REFERENCES college_profiles(id) ON DELETE CASCADE
);

-- =========================
-- Contact Messages Table
-- =========================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(150),
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================
-- Insert Default Admin
-- =========================
-- Email: harshdav@gmail.com
-- Password: harshdav0054

INSERT INTO admins (email, password)
VALUES (
  'harshdav@gmail.com',
  'harshdav0054'
);
