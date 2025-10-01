-- Drop existing foreign key constraint if it exists
SET @constraint_name = (
    SELECT CONSTRAINT_NAME 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = 'exam_secure' 
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

-- Add new foreign key constraint
ALTER TABLE created_assessments
ADD CONSTRAINT created_assessments_ibfk_1
FOREIGN KEY (owner_id) REFERENCES accounts(unique_id)
ON DELETE CASCADE; 