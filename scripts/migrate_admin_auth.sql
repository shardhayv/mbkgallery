USE maithili_gallery;

-- Add security columns to admin_users table
ALTER TABLE admin_users 
ADD COLUMN IF NOT EXISTS last_login timestamp NULL DEFAULT NULL,
ADD COLUMN IF NOT EXISTS failed_attempts int(11) DEFAULT 0,
ADD COLUMN IF NOT EXISTS locked_until timestamp NULL DEFAULT NULL,
ADD COLUMN IF NOT EXISTS updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();

-- Update default admin password with proper hash
UPDATE admin_users SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username = 'admin';

SELECT 'Migration completed successfully!' as message;