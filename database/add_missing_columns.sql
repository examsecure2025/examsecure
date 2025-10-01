-- Check and add missing columns to created_assessments table
-- Each ALTER TABLE statement is wrapped in a check to prevent errors if the column already exists

-- Check and add unique_id if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS unique_id VARCHAR(36) NOT NULL UNIQUE AFTER id;

-- Check and add access_code if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS access_code VARCHAR(6) NOT NULL UNIQUE AFTER unique_id;

-- Check and add year if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS year VARCHAR(4) NOT NULL AFTER title;

-- Check and add course if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS course VARCHAR(100) NOT NULL AFTER year;

-- Check and add sections if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS sections VARCHAR(50) NOT NULL AFTER course;

-- Check and add course_code if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS course_code VARCHAR(20) NOT NULL AFTER sections;

-- Check and add timer if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS timer INT NOT NULL COMMENT 'Duration in minutes' AFTER course_code;

-- Check and add status if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS status ENUM('active', 'closed') NOT NULL DEFAULT 'active' AFTER timer;

-- Check and add school_year if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS school_year VARCHAR(9) NOT NULL COMMENT 'Format: 2024-2025' AFTER status;

-- Check and add schedule if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS schedule DATETIME NOT NULL COMMENT 'When the assessment will start' AFTER school_year;

-- Check and add closing_time if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS closing_time DATETIME NOT NULL COMMENT 'When the assessment will automatically close' AFTER schedule;

-- Check and add shuffle_mcq if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS shuffle_mcq BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Whether to shuffle multiple choice questions' AFTER closing_time;

-- Check and add shuffle_identification if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS shuffle_identification BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Whether to shuffle identification questions' AFTER shuffle_mcq;

-- Check and add shuffle_true_false if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS shuffle_true_false BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Whether to shuffle true/false questions' AFTER shuffle_identification;

-- Check and add ai_check_identification if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS ai_check_identification BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Whether to use AI for checking identification answers' AFTER shuffle_true_false;

-- Check and add owner_id if it doesn't exist
ALTER TABLE created_assessments 
ADD COLUMN IF NOT EXISTS owner_id VARCHAR(36) NOT NULL AFTER created_at;

-- Add foreign key constraint if it doesn't exist
SET @constraint_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'created_assessments'
    AND CONSTRAINT_NAME = 'created_assessments_ibfk_1'
);

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE created_assessments ADD CONSTRAINT created_assessments_ibfk_1 FOREIGN KEY (owner_id) REFERENCES accounts(unique_id) ON DELETE CASCADE',
    'SELECT "Foreign key constraint already exists"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add indexes if they don't exist
CREATE INDEX IF NOT EXISTS idx_access_code ON created_assessments(access_code);
CREATE INDEX IF NOT EXISTS idx_status ON created_assessments(status);
CREATE INDEX IF NOT EXISTS idx_schedule ON created_assessments(schedule);
CREATE INDEX IF NOT EXISTS idx_owner ON created_assessments(owner_id); 