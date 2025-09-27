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
  `courseShortDescription` text,
  `courseLongDescription` longtext,
  `courseDisplayStatus` int(11) NOT NULL DEFAULT 0 COMMENT '0-notactive, 1-open, 2-closed',
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
  `pricingDescription` text,
  `currency` varchar(3) DEFAULT 'USD',
  `isFree` tinyint(1) DEFAULT 0,
  `discountAmount` decimal(10,2) DEFAULT 0.00,
  `discountEndDate` date DEFAULT NULL,
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
  `uploadDate` date NOT NULL,
  `uploadTime` time NOT NULL,
  `uploadedBy` int(11) NOT NULL,
  `isPublic` tinyint(1) DEFAULT 1,
  `downloadCount` int(11) DEFAULT 0,
  `fileDescription` text,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`courseMaterialId`),
  KEY `idx_course_files` (`courseId`),
  KEY `idx_file_type` (`fileType`),
  KEY `idx_uploaded_by` (`uploadedBy`),
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
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`courseTagId`),
  KEY `idx_course_tags` (`courseId`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course Enrollments Table
CREATE TABLE IF NOT EXISTS `CourseEnrollments` (
  `enrollmentId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `enrollmentDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  `enrollmentStatus` int(11) DEFAULT 1 COMMENT '1-active, 2-completed, 3-dropped',
  `progress` decimal(5,2) DEFAULT 0.00,
  `lastAccessed` timestamp NULL DEFAULT NULL,
  `completionDate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`enrollmentId`),
  UNIQUE KEY `unique_enrollment` (`courseId`, `userId`),
  KEY `idx_course_enrollments` (`courseId`),
  KEY `idx_user_enrollments` (`userId`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE,
  FOREIGN KEY (`userId`) REFERENCES `normUsers`(`NoUserId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course Sections Table (for course content structure)
CREATE TABLE IF NOT EXISTS `CourseSections` (
  `sectionId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL,
  `sectionTitle` varchar(255) NOT NULL,
  `sectionContent` longtext,
  `sectionOrder` int(11) DEFAULT 0,
  `sectionType` varchar(50) DEFAULT 'content' COMMENT 'content, video, quiz, assignment',
  `isPublished` tinyint(1) DEFAULT 0,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updatedDate` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sectionId`),
  KEY `idx_course_sections` (`courseId`),
  KEY `idx_section_order` (`sectionOrder`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course Quizzes Table
CREATE TABLE IF NOT EXISTS `CourseQuizzes` (
  `quizId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL,
  `sectionId` int(11) DEFAULT NULL,
  `quizTitle` varchar(255) NOT NULL,
  `quizDescription` text,
  `quizQuestions` longtext COMMENT 'JSON array of questions',
  `timeLimit` int(11) DEFAULT NULL COMMENT 'Time limit in minutes',
  `maxAttempts` int(11) DEFAULT 1,
  `passingScore` decimal(5,2) DEFAULT 70.00,
  `isPublished` tinyint(1) DEFAULT 0,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`quizId`),
  KEY `idx_course_quizzes` (`courseId`),
  KEY `idx_section_quizzes` (`sectionId`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE,
  FOREIGN KEY (`sectionId`) REFERENCES `CourseSections`(`sectionId`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course Progress Table
CREATE TABLE IF NOT EXISTS `CourseProgress` (
  `progressId` int(11) NOT NULL AUTO_INCREMENT,
  `enrollmentId` int(11) NOT NULL,
  `sectionId` int(11) DEFAULT NULL,
  `quizId` int(11) DEFAULT NULL,
  `progressType` varchar(50) NOT NULL COMMENT 'section, quiz, assignment',
  `isCompleted` tinyint(1) DEFAULT 0,
  `score` decimal(5,2) DEFAULT NULL,
  `timeSpent` int(11) DEFAULT 0 COMMENT 'Time spent in seconds',
  `completedDate` timestamp NULL DEFAULT NULL,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`progressId`),
  KEY `idx_enrollment_progress` (`enrollmentId`),
  KEY `idx_section_progress` (`sectionId`),
  KEY `idx_quiz_progress` (`quizId`),
  FOREIGN KEY (`enrollmentId`) REFERENCES `CourseEnrollments`(`enrollmentId`) ON DELETE CASCADE,
  FOREIGN KEY (`sectionId`) REFERENCES `CourseSections`(`sectionId`) ON DELETE CASCADE,
  FOREIGN KEY (`quizId`) REFERENCES `CourseQuizzes`(`quizId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course Reviews Table
CREATE TABLE IF NOT EXISTS `CourseReviews` (
  `reviewId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (rating >= 1 AND rating <= 5),
  `reviewTitle` varchar(255) DEFAULT NULL,
  `reviewContent` text,
  `isVerified` tinyint(1) DEFAULT 0,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reviewId`),
  UNIQUE KEY `unique_course_review` (`courseId`, `userId`),
  KEY `idx_course_reviews` (`courseId`),
  KEY `idx_user_reviews` (`userId`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE,
  FOREIGN KEY (`userId`) REFERENCES `normUsers`(`NoUserId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Course Notifications Table
CREATE TABLE IF NOT EXISTS `CourseNotifications` (
  `notificationId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL COMMENT 'NULL for all enrolled users',
  `notificationTitle` varchar(255) NOT NULL,
  `notificationContent` text NOT NULL,
  `notificationType` varchar(50) DEFAULT 'general' COMMENT 'general, announcement, reminder, update',
  `isRead` tinyint(1) DEFAULT 0,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notificationId`),
  KEY `idx_course_notifications` (`courseId`),
  KEY `idx_user_notifications` (`userId`),
  FOREIGN KEY (`courseId`) REFERENCES `Courses`(`courseId`) ON DELETE CASCADE,
  FOREIGN KEY (`userId`) REFERENCES `normUsers`(`NoUserId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
