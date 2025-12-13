-- Admin Course Access Table
-- Stores which courses each admin has access to view/manage
-- Only super admins with ManageRights permission can grant/revoke access

CREATE TABLE IF NOT EXISTS `AdminCourseAccess` (
  `accessId` int(11) NOT NULL AUTO_INCREMENT,
  `adminId` int(11) NOT NULL,
  `courseId` int(11) NOT NULL,
  `grantedBy` int(11) DEFAULT NULL COMMENT 'Admin ID who granted this access',
  `grantedDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`accessId`),
  UNIQUE KEY `unique_admin_course` (`adminId`, `courseId`),
  KEY `idx_admin` (`adminId`),
  KEY `idx_course` (`courseId`),
  FOREIGN KEY (`adminId`) REFERENCES `users`(`userId`) ON DELETE CASCADE,
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

