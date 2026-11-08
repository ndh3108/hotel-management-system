ALTER TABLE `rooms` MODIFY COLUMN `status` ENUM('Available','Occupied','Cleaning','Maintenance','Hold') DEFAULT 'Available';
ALTER TABLE `rooms` ADD COLUMN `locked_at` DATETIME DEFAULT NULL;
ALTER TABLE `rooms` ADD COLUMN `locked_by_session` VARCHAR(255) DEFAULT NULL;

