-- Discussion Replies Table
-- Created for MK Scholars E-Learning Platform
-- Allows students and admins to reply to discussion board messages

CREATE TABLE IF NOT EXISTS `DiscussionReplies` (
  `replyId` int(11) NOT NULL AUTO_INCREMENT,
  `discussionId` int(11) NOT NULL COMMENT 'Discussion message this reply belongs to',
  `userId` int(11) NOT NULL COMMENT 'User who replied (student or admin)',
  `replyContent` longtext NOT NULL COMMENT 'Content of the reply',
  `replyDate` date NOT NULL,
  `replyTime` time NOT NULL,
  `createdDate` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`replyId`),
  KEY `idx_discussion_replies` (`discussionId`),
  KEY `idx_user_replies` (`userId`),
  KEY `idx_reply_date` (`replyDate`, `replyTime`),
  FOREIGN KEY (`discussionId`) REFERENCES `DiscussionBoard`(`discussionId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

