-- Assessment Monitoring Table
CREATE TABLE IF NOT EXISTS assessment_monitoring (
    id INT AUTO_INCREMENT PRIMARY KEY,
    monitoring_id VARCHAR(36) NOT NULL UNIQUE,
    assessment_id VARCHAR(36) NOT NULL,
    student_id VARCHAR(36) NOT NULL,
    status ENUM('not_started', 'in_progress', 'completed', 'flagged') NOT NULL DEFAULT 'not_started',
    start_time TIMESTAMP NULL,
    end_time TIMESTAMP NULL,
    warning_count INT NOT NULL DEFAULT 0,
    last_warning_time TIMESTAMP NULL,
    last_activity_time TIMESTAMP NULL,
    screen_leave_count INT NOT NULL DEFAULT 0,
    face_detection_issues INT NOT NULL DEFAULT 0,
    suspicious_activities JSON COMMENT 'Stores details of suspicious activities like app switching, overlays, etc.',
    blockchain_hash VARCHAR(64) COMMENT 'Hash of the assessment attempt for blockchain verification',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES created_assessments(unique_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES accounts(unique_id) ON DELETE CASCADE,
    UNIQUE KEY unique_assessment_student (assessment_id, student_id),
    INDEX idx_status (status),
    INDEX idx_student (student_id),
    INDEX idx_assessment (assessment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Warning Logs Table
CREATE TABLE IF NOT EXISTS warning_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warning_id VARCHAR(36) NOT NULL UNIQUE,
    monitoring_id VARCHAR(36) NOT NULL,
    warning_type ENUM('screen_leave', 'face_detection', 'app_switch', 'overlay_detected', 'multiple_faces', 'other') NOT NULL,
    warning_details JSON COMMENT 'Stores specific details about the warning',
    warning_time TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (monitoring_id) REFERENCES assessment_monitoring(monitoring_id) ON DELETE CASCADE,
    INDEX idx_monitoring (monitoring_id),
    INDEX idx_warning_type (warning_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Logs Table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_id VARCHAR(36) NOT NULL UNIQUE,
    monitoring_id VARCHAR(36) NOT NULL,
    activity_type ENUM('start_assessment', 'answer_question', 'warning_issued', 'status_change', 'complete_assessment') NOT NULL,
    activity_details JSON COMMENT 'Stores specific details about the activity',
    activity_time TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (monitoring_id) REFERENCES assessment_monitoring(monitoring_id) ON DELETE CASCADE,
    INDEX idx_monitoring (monitoring_id),
    INDEX idx_activity_type (activity_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 