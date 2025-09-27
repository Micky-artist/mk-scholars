-- Course Management System Database Tables
-- Created for MK Scholars E-Learning Platform

-- Courses Table
CREATE TABLE IF NOT EXISTS `Courses` (
  `courseId` int(11) NOT NULL AUTO_INCREMENT,
  `courseStartDate` date NOT NULL,
  `courseRegEndDate` date NOT NULL,
  `courseEndDate` date NOT NULL,
  `courseSeats` int(11) NOT NULL DEFAULT 0,
  `coursePhoto` varchar(255) DEFAULT NULL,
  `courseName` varchar(255) NOT NULL,
  `courseShortDescription` text NOT NULL,
  `courseLongDescription` longtext NOT NULL,
  `courseDisplayStatus` int(11) NOT NULL DEFAULT 0 COMMENT '1-open, 2-closed, 0-notactive',
  `coursePaymentCodeName` varchar(100) DEFAULT NULL,
  `coursePricingId` int(11) DEFAULT NULL,
  `courseNotesId` int(11) DEFAULT NULL,
  `courseTagsId` int(11) DEFAULT NULL,
  `courseContent` longtext DEFAULT NULL COMMENT 'JSON content for course structure',
  `courseCreatedBy` int(11) NOT NULL,
  `courseCreatedDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  `courseUpdatedDate` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`courseId`),
  KEY `idx_course_status` (`courseDisplayStatus`),
  KEY `idx_course_dates` (`courseStartDate`, `courseEndDate`),
  KEY `idx_course_creator` (`courseCreatedBy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course Pricing Table
CREATE TABLE IF NOT EXISTS `CoursePricing` (
  `coursePricingId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pricingDescription` text DEFAULT NULL,
  `currency` varchar(3) DEFAULT 'USD',
  `discountAmount` decimal(10,2) DEFAULT 0.00,
  `discountStartDate` date DEFAULT NULL,
  `discountEndDate` date DEFAULT NULL,
  `isFree` tinyint(1) DEFAULT 0,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`coursePricingId`),
  KEY `idx_course_pricing` (`courseId`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Discussion Board Table
CREATE TABLE IF NOT EXISTS `DiscussionBoard` (
  `discussionId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `messageTitle` varchar(255) NOT NULL,
  `messageBody` longtext NOT NULL,
  `messageDate` date NOT NULL,
  `messageTime` time NOT NULL,
  `messageLikes` int(11) DEFAULT 0,
  `messageReport` int(11) DEFAULT 0,
  `isPinned` tinyint(1) DEFAULT 0,
  `parentDiscussionId` int(11) DEFAULT NULL COMMENT 'For replies to discussions',
  `isApproved` tinyint(1) DEFAULT 1,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`discussionId`),
  KEY `idx_course_discussion` (`courseId`),
  KEY `idx_user_discussion` (`userId`),
  KEY `idx_parent_discussion` (`parentDiscussionId`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE,
  FOREIGN KEY (`userId`) REFERENCES `normUsers`(`NoUserId`) ON DELETE CASCADE,
  FOREIGN KEY (`parentDiscussionId`) REFERENCES `DiscussionBoard`(`discussionId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course Files Table
CREATE TABLE IF NOT EXISTS `CourseFiles` (
  `courseMaterialId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL,
  `filePath` varchar(500) NOT NULL,
  `fileName` varchar(255) NOT NULL,
  `fileType` varchar(50) NOT NULL COMMENT 'video, audio, image, document, pdf, etc.',
  `fileSize` bigint(20) DEFAULT NULL,
  `fileDescription` text DEFAULT NULL,
  `uploadDate` date NOT NULL,
  `uploadTime` time NOT NULL,
  `uploadedBy` int(11) NOT NULL,
  `isPublic` tinyint(1) DEFAULT 1,
  `downloadCount` int(11) DEFAULT 0,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`courseMaterialId`),
  KEY `idx_course_files` (`courseId`),
  KEY `idx_file_type` (`fileType`),
  KEY `idx_uploader` (`uploadedBy`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE,
  FOREIGN KEY (`uploadedBy`) REFERENCES `users`(`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course Tags Table
CREATE TABLE IF NOT EXISTS `CourseTags` (
  `courseTagId` int(11) NOT NULL AUTO_INCREMENT,
  `courseTagIcon` varchar(100) DEFAULT NULL,
  `tagDescription` varchar(255) NOT NULL,
  `courseId` int(11) NOT NULL,
  `tagColor` varchar(7) DEFAULT '#007bff',
  `isActive` tinyint(1) DEFAULT 1,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`courseTagId`),
  KEY `idx_course_tags` (`courseId`),
  KEY `idx_tag_active` (`isActive`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course Enrollments Table
CREATE TABLE IF NOT EXISTS `CourseEnrollments` (
  `enrollmentId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `enrollmentDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  `enrollmentStatus` int(11) DEFAULT 1 COMMENT '1-active, 2-completed, 3-dropped, 4-suspended',
  `progressPercentage` decimal(5,2) DEFAULT 0.00,
  `lastAccessedDate` timestamp NULL DEFAULT NULL,
  `completionDate` timestamp NULL DEFAULT NULL,
  `certificateIssued` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`enrollmentId`),
  UNIQUE KEY `unique_enrollment` (`courseId`, `userId`),
  KEY `idx_user_enrollments` (`userId`),
  KEY `idx_course_enrollments` (`courseId`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE,
  FOREIGN KEY (`userId`) REFERENCES `normUsers`(`NoUserId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course Lessons Table
CREATE TABLE IF NOT EXISTS `CourseLessons` (
  `lessonId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL,
  `lessonTitle` varchar(255) NOT NULL,
  `lessonContent` longtext NOT NULL,
  `lessonOrder` int(11) NOT NULL DEFAULT 0,
  `lessonType` varchar(50) DEFAULT 'text' COMMENT 'text, video, audio, quiz, assignment',
  `lessonDuration` int(11) DEFAULT NULL COMMENT 'Duration in minutes',
  `isPublished` tinyint(1) DEFAULT 0,
  `lessonPrerequisites` text DEFAULT NULL,
  `lessonObjectives` text DEFAULT NULL,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updatedDate` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lessonId`),
  KEY `idx_course_lessons` (`courseId`),
  KEY `idx_lesson_order` (`lessonOrder`),
  KEY `idx_lesson_type` (`lessonType`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course Progress Table
CREATE TABLE IF NOT EXISTS `CourseProgress` (
  `progressId` int(11) NOT NULL AUTO_INCREMENT,
  `enrollmentId` int(11) NOT NULL,
  `lessonId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `isCompleted` tinyint(1) DEFAULT 0,
  `completionDate` timestamp NULL DEFAULT NULL,
  `timeSpent` int(11) DEFAULT 0 COMMENT 'Time spent in minutes',
  `lastPosition` int(11) DEFAULT 0 COMMENT 'Last position in video/audio',
  `notes` text DEFAULT NULL,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updatedDate` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`progressId`),
  UNIQUE KEY `unique_progress` (`enrollmentId`, `lessonId`),
  KEY `idx_user_progress` (`userId`),
  KEY `idx_lesson_progress` (`lessonId`),
  FOREIGN KEY (`enrollmentId`) REFERENCES `CourseEnrollments`(`enrollmentId`) ON DELETE CASCADE,
  FOREIGN KEY (`lessonId`) REFERENCES `CourseLessons`(`lessonId`) ON DELETE CASCADE,
  FOREIGN KEY (`userId`) REFERENCES `normUsers`(`NoUserId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample course tags
INSERT INTO `CourseTags` (`courseTagIcon`, `tagDescription`, `courseId`, `tagColor`) VALUES
('fas fa-code', 'Programming', 0, '#007bff'),
('fas fa-language', 'Language Learning', 0, '#28a745'),
('fas fa-graduation-cap', 'Academic', 0, '#ffc107'),
('fas fa-briefcase', 'Professional Development', 0, '#dc3545'),
('fas fa-paint-brush', 'Creative Arts', 0, '#6f42c1'),
('fas fa-chart-line', 'Business', 0, '#17a2b8'),
('fas fa-heart', 'Health & Wellness', 0, '#e83e8c'),
('fas fa-cogs', 'Technology', 0, '#6c757d');