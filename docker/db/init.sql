-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `mkscholars`;
USE `mkscholars`;

-- Users table (from various references)
CREATE TABLE IF NOT EXISTS `users` (
    `userId` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255) NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`userId`),
    UNIQUE KEY `email` (`email`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Scholarships table (from selectScholarships.php and scholarship-details.php)
CREATE TABLE IF NOT EXISTS `scholarships` (
    `scholarshipId` int(11) NOT NULL AUTO_INCREMENT,
    `scholarshipTitle` varchar(255) NOT NULL,
    `scholarshipDetails` text,
    `scholarshipImage` varchar(255) DEFAULT NULL,
    `scholarshipStatus` tinyint(1) DEFAULT 0 COMMENT '0=unpublished, 1=published',
    `scholarshipUpdateDate` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`scholarshipId`),
    FULLTEXT KEY `scholarship_search` (`scholarshipTitle`, `scholarshipDetails`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Logs table (from actions.php)
CREATE TABLE IF NOT EXISTS `Logs` (
    `logId` int(11) NOT NULL AUTO_INCREMENT,
    `userId` int(11) NOT NULL,
    `logMessage` text NOT NULL,
    `logDate` date NOT NULL,
    `logTime` time NOT NULL,
    `logStatus` tinyint(1) DEFAULT 0 COMMENT '0=unread, 1=read',
    PRIMARY KEY (`logId`),
    KEY `userId` (`userId`),
    CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Posts table (from actions.php)
CREATE TABLE IF NOT EXISTS `posts` (
    `postId` int(11) NOT NULL AUTO_INCREMENT,
    `projectTitle` varchar(255) NOT NULL,
    `projectImg1` varchar(255) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`postId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ApplicationRequests table (from application-requests.php)
CREATE TABLE IF NOT EXISTS `ApplicationRequests` (
    `requestId` int(11) NOT NULL AUTO_INCREMENT,
    `UserId` int(11) NOT NULL,
    `ApplicationId` int(11) NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`requestId`),
    KEY `UserId` (`UserId`),
    KEY `ApplicationId` (`ApplicationId`),
    CONSTRAINT `applicationrequests_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
    CONSTRAINT `applicationrequests_ibfk_2` FOREIGN KEY (`ApplicationId`) REFERENCES `scholarships` (`scholarshipId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CourseApplications table (from course-applications.php)
CREATE TABLE IF NOT EXISTS `CourseApplications` (
    `applicationId` int(11) NOT NULL AUTO_INCREMENT,
    `userId` int(11) NOT NULL,
    `courseId` int(11) DEFAULT NULL,
    `status` varchar(50) DEFAULT 'pending',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`applicationId`),
    KEY `userId` (`userId`),
    CONSTRAINT `courseapplications_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Conversation table (for messaging between users)
CREATE TABLE IF NOT EXISTS `Conversation` (
    `conversationId` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) DEFAULT NULL,
    `created_by` int(11) NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`conversationId`),
    KEY `created_by` (`created_by`),
    CONSTRAINT `conversation_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ConversationParticipants table (for tracking conversation members)
CREATE TABLE IF NOT EXISTS `ConversationParticipants` (
    `participantId` int(11) NOT NULL AUTO_INCREMENT,
    `conversationId` int(11) NOT NULL,
    `userId` int(11) NOT NULL,
    `joined_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`participantId`),
    UNIQUE KEY `unique_participation` (`conversationId`, `userId`),
    KEY `userId` (`userId`),
    CONSTRAINT `conversationparticipants_ibfk_1` FOREIGN KEY (`conversationId`) REFERENCES `Conversation` (`conversationId`) ON DELETE CASCADE,
    CONSTRAINT `conversationparticipants_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Messages table (for storing individual messages in conversations)
CREATE TABLE IF NOT EXISTS `Messages` (
    `messageId` int(11) NOT NULL AUTO_INCREMENT,
    `conversationId` int(11) NOT NULL,
    `senderId` int(11) NOT NULL,
    `message` text NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`messageId`),
    KEY `conversationId` (`conversationId`),
    KEY `senderId` (`senderId`),
    CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversationId`) REFERENCES `Conversation` (`conversationId`) ON DELETE CASCADE,
    CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`senderId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Messages table (from chat.php and related files)
CREATE TABLE IF NOT EXISTS `Message` (
    `MessageId` int(11) NOT NULL AUTO_INCREMENT,
    `ConvId` int(11) NOT NULL,
    `UserId` int(11) DEFAULT NULL,
    `AdminId` int(11) DEFAULT NULL,
    `MessageContent` text NOT NULL,
    `SentDate` date NOT NULL,
    `SentTime` time NOT NULL,
    `MessageStatus` tinyint(1) DEFAULT 0 COMMENT '0=unread, 1=read',
    PRIMARY KEY (`MessageId`),
    KEY `ConvId` (`ConvId`),
    KEY `UserId` (`UserId`),
    KEY `AdminId` (`AdminId`),
    CONSTRAINT `message_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`userId`) ON DELETE SET NULL,
    CONSTRAINT `message_ibfk_2` FOREIGN KEY (`AdminId`) REFERENCES `users` (`userId`) ON DELETE SET NULL,
    CONSTRAINT `message_ibfk_3` FOREIGN KEY (`ConvId`) REFERENCES `Conversation` (`conversationId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin access table (from manageAdminAccess.php)
CREATE TABLE IF NOT EXISTS `AdminAccess` (
    `accessId` int(11) NOT NULL AUTO_INCREMENT,
    `userId` int(11) NOT NULL,
    `accessLevel` varchar(50) NOT NULL DEFAULT 'basic',
    `permissions` text,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`accessId`),
    UNIQUE KEY `userId` (`userId`),
    CONSTRAINT `adminaccess_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Subscription table (from ucat-course.php and list_user_subscriptions.php)
CREATE TABLE IF NOT EXISTS `subscription` (
    `subscriptionId` int(11) NOT NULL AUTO_INCREMENT,
    `UserId` int(11) NOT NULL,
    `Item` varchar(255) NOT NULL,
    `SubscriptionStatus` varchar(50) DEFAULT 'active',
    `subscriptionDate` datetime NOT NULL,
    `expirationDate` datetime DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`subscriptionId`),
    KEY `UserId` (`UserId`),
    CONSTRAINT `subscription_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Countries table (from selectCountries.php)
CREATE TABLE IF NOT EXISTS `countries` (
    `countryId` int(11) NOT NULL AUTO_INCREMENT,
    `CountryName` varchar(100) NOT NULL,
    `CountryStatus` tinyint(1) DEFAULT 1 COMMENT '0=inactive, 1=active',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`countryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Normalized Users table (from CreateUser.php and update_profile.php)
CREATE TABLE IF NOT EXISTS `normUsers` (
    `NoUserId` int(11) NOT NULL AUTO_INCREMENT,
    `NoUsername` varchar(100) NOT NULL,
    `NoEmail` varchar(100) NOT NULL,
    `NoPhone` varchar(50) DEFAULT NULL,
    `NoPassword` varchar(255) NOT NULL,
    `NoCreationDate` timestamp DEFAULT CURRENT_TIMESTAMP,
    `last_updated` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`NoUserId`),
    UNIQUE KEY `NoEmail` (`NoEmail`),
    UNIQUE KEY `NoPhone` (`NoPhone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- PostTags table (from applications.php)
CREATE TABLE IF NOT EXISTS `PostTags` (
    `tagId` int(11) NOT NULL AUTO_INCREMENT,
    `tagName` varchar(100) NOT NULL,
    `tagSlug` varchar(100) DEFAULT NULL,
    `TagStatus` tinyint(1) DEFAULT 1 COMMENT '0=inactive, 1=active',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`tagId`),
    UNIQUE KEY `tagName` (`tagName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Post_Tag_Mapping table (for many-to-many relationship between posts and tags)
CREATE TABLE IF NOT EXISTS `Post_Tag_Mapping` (
    `mappingId` int(11) NOT NULL AUTO_INCREMENT,
    `postId` int(11) NOT NULL,
    `tagId` int(11) NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`mappingId`),
    UNIQUE KEY `post_tag` (`postId`, `tagId`),
    KEY `tagId` (`tagId`),
    CONSTRAINT `posttagmapping_ibfk_1` FOREIGN KEY (`postId`) REFERENCES `posts` (`postId`) ON DELETE CASCADE,
    CONSTRAINT `posttagmapping_ibfk_2` FOREIGN KEY (`tagId`) REFERENCES `PostTags` (`tagId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- UserProfile table (extended user information)
CREATE TABLE IF NOT EXISTS `UserProfile` (
    `profileId` int(11) NOT NULL AUTO_INCREMENT,
    `userId` int(11) NOT NULL,
    `fullName` varchar(255) DEFAULT NULL,
    `dateOfBirth` date DEFAULT NULL,
    `gender` enum('male','female','other') DEFAULT NULL,
    `address` text,
    `city` varchar(100) DEFAULT NULL,
    `countryId` int(11) DEFAULT NULL,
    `postalCode` varchar(20) DEFAULT NULL,
    `profilePicture` varchar(255) DEFAULT NULL,
    `bio` text,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`profileId`),
    UNIQUE KEY `userId` (`userId`),
    KEY `countryId` (`countryId`),
    CONSTRAINT `userprofile_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
    CONSTRAINT `userprofile_ibfk_2` FOREIGN KEY (`countryId`) REFERENCES `countries` (`countryId`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- UserSessions table (for tracking logged-in users)
CREATE TABLE IF NOT EXISTS `UserSessions` (
    `sessionId` varchar(255) NOT NULL,
    `userId` int(11) NOT NULL,
    `ipAddress` varchar(45) DEFAULT NULL,
    `userAgent` text,
    `lastActivity` datetime NOT NULL,
    `expiresAt` datetime NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`sessionId`),
    KEY `userId` (`userId`),
    KEY `expiresAt` (`expiresAt`),
    CONSTRAINT `usersessions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- applicationsSurvey table (from language-coding.php and playground/form2.php)
CREATE TABLE IF NOT EXISTS `applicationsSurvey` (
    `surveyId` int(11) NOT NULL AUTO_INCREMENT,
    `FullNames` varchar(255) DEFAULT NULL,
    `Email` varchar(255) NOT NULL,
    `Phone` varchar(50) DEFAULT NULL,
    `CourseId` int(11) DEFAULT 1,
    `ApplicationContent` text,
    `Comment` text,
    `SubmitDate` date DEFAULT NULL,
    `SubmitTime` time DEFAULT NULL,
    `applicationStatus` tinyint(1) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`surveyId`),
    KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Documents table (from upload_file.php)
CREATE TABLE IF NOT EXISTS `Documents` (
    `documentId` int(11) NOT NULL AUTO_INCREMENT,
    `UserId` int(11) NOT NULL,
    `ConvId` int(11) DEFAULT NULL,
    `DocumentName` varchar(255) NOT NULL,
    `OriginalFileName` varchar(255) NOT NULL,
    `FilePath` varchar(512) NOT NULL,
    `FileType` varchar(100) DEFAULT NULL,
    `FileSize` int(11) DEFAULT NULL,
    `DocumentType` varchar(100) DEFAULT '',
    `Description` text,
    `UploadDate` date DEFAULT NULL,
    `UploadTime` time DEFAULT NULL,
    `Status` enum('pending','approved','rejected') DEFAULT 'pending',
    `AdminComments` text,
    `ReviewedBy` int(11) DEFAULT NULL,
    `ReviewedDate` datetime DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`documentId`),
    KEY `UserId` (`UserId`),
    KEY `ConvId` (`ConvId`),
    KEY `ReviewedBy` (`ReviewedBy`),
    CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- youtubeVideos table (from videoOperations.php)
CREATE TABLE IF NOT EXISTS `youtubeVideos` (
    `videoId` int(11) NOT NULL AUTO_INCREMENT,
    `videoLink` varchar(512) NOT NULL,
    `VideoTitle` varchar(255) DEFAULT NULL,
    `VideoStatus` tinyint(1) DEFAULT 1 COMMENT '0=inactive, 1=active',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`videoId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- AdminRights table (from update_admin_rights.php and manageAdminAccess.php)
CREATE TABLE IF NOT EXISTS `AdminRights` (
    `rightsId` int(11) NOT NULL AUTO_INCREMENT,
    `AdminId` int(11) NOT NULL,
    `canManageUsers` tinyint(1) DEFAULT 0,
    `canManagePosts` tinyint(1) DEFAULT 0,
    `canManageScholarships` tinyint(1) DEFAULT 0,
    `canManageSettings` tinyint(1) DEFAULT 0,
    `canViewReports` tinyint(1) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`rightsId`),
    UNIQUE KEY `AdminId` (`AdminId`),
    CONSTRAINT `adminrights_ibfk_1` FOREIGN KEY (`AdminId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Conversation table (from start_conversation.php and chat functionality)
CREATE TABLE IF NOT EXISTS `Conversation` (
    `convId` int(11) NOT NULL AUTO_INCREMENT,
    `UserId` int(11) NOT NULL,
    `AdminId` int(11) DEFAULT NULL,
    `StartDate` date NOT NULL,
    `StartTime` time NOT NULL,
    `EndDate` date DEFAULT NULL,
    `EndTime` time DEFAULT NULL,
    `ConvStatus` tinyint(1) DEFAULT 0 COMMENT '0=active, 1=closed',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`convId`),
    KEY `UserId` (`UserId`),
    KEY `AdminId` (`AdminId`),
    CONSTRAINT `conversation_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
    CONSTRAINT `conversation_ibfk_2` FOREIGN KEY (`AdminId`) REFERENCES `users` (`userId`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- login_logs table (from login.php - currently commented out)
CREATE TABLE IF NOT EXISTS `login_logs` (
    `logId` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text,
    `status` enum('success','failed') DEFAULT 'failed',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`logId`),
    KEY `user_id` (`user_id`),
    KEY `status` (`status`),
    CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userId`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
