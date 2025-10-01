-- Multiple Choice Questions Table
CREATE TABLE IF NOT EXISTS multiple_choice_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id VARCHAR(36) NOT NULL UNIQUE,
    assessment_id VARCHAR(36) NOT NULL,
    question_text TEXT NOT NULL,
    option_a TEXT NOT NULL,
    option_b TEXT NOT NULL,
    option_c TEXT NOT NULL,
    option_d TEXT NOT NULL,
    option_e TEXT NOT NULL,
    correct_answer CHAR(1) NOT NULL CHECK (correct_answer IN ('A', 'B', 'C', 'D', 'E')),
    points INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES created_assessments(unique_id) ON DELETE CASCADE,
    INDEX idx_assessment (assessment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Identification Questions Table
CREATE TABLE IF NOT EXISTS identification_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id VARCHAR(36) NOT NULL UNIQUE,
    assessment_id VARCHAR(36) NOT NULL,
    question_text TEXT NOT NULL,
    correct_answer TEXT NOT NULL,
    points INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES created_assessments(unique_id) ON DELETE CASCADE,
    INDEX idx_assessment (assessment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- True or False Questions Table
CREATE TABLE IF NOT EXISTS true_false_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id VARCHAR(36) NOT NULL UNIQUE,
    assessment_id VARCHAR(36) NOT NULL,
    question_text TEXT NOT NULL,
    correct_answer BOOLEAN NOT NULL,
    points INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES created_assessments(unique_id) ON DELETE CASCADE,
    INDEX idx_assessment (assessment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Student Responses Table
CREATE TABLE IF NOT EXISTS student_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    response_id VARCHAR(36) NOT NULL UNIQUE,
    assessment_id VARCHAR(36) NOT NULL,
    student_id VARCHAR(36) NOT NULL,
    question_id VARCHAR(36) NOT NULL,
    question_type ENUM('multiple_choice', 'identification', 'true_false') NOT NULL,
    student_answer TEXT NOT NULL,
    is_correct BOOLEAN NOT NULL,
    points_earned INT NOT NULL DEFAULT 0,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES created_assessments(unique_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES accounts(unique_id) ON DELETE CASCADE,
    INDEX idx_assessment_student (assessment_id, student_id),
    INDEX idx_question (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assessment Results Table
CREATE TABLE IF NOT EXISTS assessment_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    result_id VARCHAR(36) NOT NULL UNIQUE,
    assessment_id VARCHAR(36) NOT NULL,
    student_id VARCHAR(36) NOT NULL,
    total_questions INT NOT NULL,
    correct_answers INT NOT NULL,
    total_points INT NOT NULL,
    score_percentage DECIMAL(5,2) NOT NULL,
    started_at TIMESTAMP NOT NULL,
    completed_at TIMESTAMP NOT NULL,
    status ENUM('in_progress', 'completed', 'abandoned') NOT NULL DEFAULT 'in_progress',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES created_assessments(unique_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES accounts(unique_id) ON DELETE CASCADE,
    UNIQUE KEY unique_assessment_student (assessment_id, student_id),
    INDEX idx_student (student_id),
    INDEX idx_assessment (assessment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 