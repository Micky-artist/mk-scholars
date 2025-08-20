-- Create table for writing service applications
CREATE TABLE IF NOT EXISTS `writing_service_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_type` varchar(100) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `document_length` varchar(50) DEFAULT NULL,
  `requirements` text NOT NULL,
  `budget` varchar(50) DEFAULT NULL,
  `application_date` datetime NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `assigned_writer` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `status` (`status`),
  KEY `application_date` (`application_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data (optional)
INSERT INTO `writing_service_applications` (`service_type`, `full_name`, `email`, `phone`, `deadline`, `document_length`, `requirements`, `budget`, `status`) VALUES
('personal_statement', 'John Doe', 'john.doe@example.com', '+1234567890', '2024-12-31', '1-2_pages', 'Need help with university application personal statement for Computer Science program', '100_200', 'pending'),
('resume_cv', 'Jane Smith', 'jane.smith@example.com', '+0987654321', '2024-12-25', '1-2_pages', 'Professional resume for software developer position', '50_100', 'pending');
