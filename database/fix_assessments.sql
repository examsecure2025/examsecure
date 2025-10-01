-- First, check if accounts table exists and has the correct structure
CREATE TABLE IF NOT EXISTS accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unique_id VARCHAR(36) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Drop existing foreign key constraint if it exists
SET @constraint_name = (
    SELECT CONSTRAINT_NAME 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'created_assessments' 
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    AND CONSTRAINT_NAME = 'created_assessments_ibfk_1'
);

SET @sql = IF(@constraint_name IS NOT NULL, 
    CONCAT('ALTER TABLE created_assessments DROP FOREIGN KEY ', @constraint_name),
    'SELECT "No existing constraint found"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Modify created_assessments table to match the data structure
ALTER TABLE created_assessments
    MODIFY COLUMN unique_id VARCHAR(36) NOT NULL,
    MODIFY COLUMN access_code CHAR(6) NOT NULL,
    MODIFY COLUMN title VARCHAR(255) NOT NULL,
    MODIFY COLUMN year VARCHAR(4) NOT NULL,
    MODIFY COLUMN course VARCHAR(100) NOT NULL,
    MODIFY COLUMN sections VARCHAR(50) NOT NULL,
    MODIFY COLUMN course_code VARCHAR(20) NOT NULL,
    MODIFY COLUMN timer INT NOT NULL DEFAULT 0,
    MODIFY COLUMN status ENUM('active', 'closed') NOT NULL DEFAULT 'active',
    MODIFY COLUMN school_year VARCHAR(9) NOT NULL,
    MODIFY COLUMN schedule DATETIME NOT NULL,
    MODIFY COLUMN closing_time DATETIME NOT NULL,
    MODIFY COLUMN shuffle_mcq TINYINT(1) NOT NULL DEFAULT 0,
    MODIFY COLUMN shuffle_identification TINYINT(1) NOT NULL DEFAULT 0,
    MODIFY COLUMN shuffle_true_false TINYINT(1) NOT NULL DEFAULT 0,
    MODIFY COLUMN ai_check_identification TINYINT(1) NOT NULL DEFAULT 0,
    MODIFY COLUMN owner_id VARCHAR(36) NOT NULL;

-- Add back the foreign key constraint
ALTER TABLE created_assessments
ADD CONSTRAINT created_assessments_ibfk_1
FOREIGN KEY (owner_id) REFERENCES accounts(unique_id)
ON DELETE CASCADE;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_access_code ON created_assessments(access_code);
CREATE INDEX IF NOT EXISTS idx_status ON created_assessments(status);
CREATE INDEX IF NOT EXISTS idx_schedule ON created_assessments(schedule);
CREATE INDEX IF NOT EXISTS idx_owner ON created_assessments(owner_id);
CREATE INDEX IF NOT EXISTS idx_course_code ON created_assessments(course_code);
CREATE INDEX IF NOT EXISTS idx_school_year ON created_assessments(school_year); 