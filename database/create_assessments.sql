-- Drop the table if it exists
DROP TABLE IF EXISTS created_assessments;

-- Create the created_assessments table
CREATE TABLE created_assessments (
    unique_id VARCHAR(36) PRIMARY KEY,
    access_code CHAR(6) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    year VARCHAR(4) NOT NULL,
    course VARCHAR(100) NOT NULL,
    sections VARCHAR(50) NOT NULL,
    course_code VARCHAR(20) NOT NULL,
    timer INT NOT NULL DEFAULT 0,
    status ENUM('active', 'closed') NOT NULL DEFAULT 'active',
    school_year VARCHAR(9) NOT NULL,
    schedule DATETIME NOT NULL,
    closing_time DATETIME NOT NULL,
    shuffle_mcq TINYINT(1) NOT NULL DEFAULT 0,
    shuffle_identification TINYINT(1) NOT NULL DEFAULT 0,
    shuffle_true_false TINYINT(1) NOT NULL DEFAULT 0,
    ai_check_identification TINYINT(1) NOT NULL DEFAULT 0,
    owner_id VARCHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES accounts(unique_id) ON DELETE CASCADE,
    INDEX idx_access_code (access_code),
    INDEX idx_status (status),
    INDEX idx_schedule (schedule),
    INDEX idx_owner (owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for better query performance
CREATE INDEX idx_course_code ON created_assessments(course_code);
CREATE INDEX idx_school_year ON created_assessments(school_year);
CREATE INDEX idx_created_at ON created_assessments(created_at); 