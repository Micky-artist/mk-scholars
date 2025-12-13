-- Submissions Table
-- Created for MK Scholars E-Learning Platform
-- Allows students to submit assignments (text and documents) to facilitators for specific courses

CREATE TABLE IF NOT EXISTS `Submissions` (
  `submissionId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL COMMENT 'Course this submission belongs to',
  `userId` int(11) NOT NULL COMMENT 'Student who submitted',
  `submissionTitle` varchar(255) NOT NULL,
  `submissionContent` longtext DEFAULT NULL COMMENT 'Text content submitted by student',
  `filePath` varchar(500) DEFAULT NULL COMMENT 'Path to uploaded file',
  `fileName` varchar(255) DEFAULT NULL COMMENT 'Original file name',
  `fileType` varchar(50) DEFAULT NULL COMMENT 'File extension/type',
  `fileSize` bigint(20) DEFAULT NULL COMMENT 'File size in bytes',
  `submissionStatus` varchar(20) DEFAULT 'pending' COMMENT 'pending, replied, closed',
  `facilitatorReply` longtext DEFAULT NULL COMMENT 'Reply from facilitator',
  `repliedBy` int(11) DEFAULT NULL COMMENT 'Admin/Facilitator ID who replied',
  `repliedDate` timestamp NULL DEFAULT NULL COMMENT 'Date when facilitator replied',
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updatedDate` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`submissionId`),
  KEY `idx_course_submissions` (`courseId`),
  KEY `idx_user_submissions` (`userId`),
  KEY `idx_submission_status` (`submissionStatus`),
  KEY `idx_created_date` (`createdDate`),
  KEY `idx_replied_by` (`repliedBy`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE,
  FOREIGN KEY (`userId`) REFERENCES `normUsers`(`NoUserId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

