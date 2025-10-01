-- Remove unnecessary columns from assessment_sessions table
-- This script removes columns that are not needed for the monitoring system

USE exam_secure;

-- Remove the unnecessary columns
ALTER TABLE assessment_sessions 
DROP COLUMN background_count,
DROP COLUMN device_model,
DROP COLUMN platform,
DROP COLUMN app_version,
DROP COLUMN ip_address,
DROP COLUMN user_agent;

-- Note: background_count is being removed because tab_switch_count and background_count
-- are essentially the same thing - both represent the user leaving the exam interface

-- Optional: Update existing APP_BACKGROUNDED events to TAB_SWITCH since they're the same
-- UPDATE cheating_events 
-- SET event_type = 'TAB_SWITCH' 
-- WHERE event_type = 'APP_BACKGROUNDED';

-- Show the updated table structure
DESCRIBE assessment_sessions;
