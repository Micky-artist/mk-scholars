-- Seed data for MK Scholars database

-- Disable foreign key checks temporarily to avoid reference issues
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data from all tables (be careful with this in production!)
TRUNCATE TABLE `users`;
TRUNCATE TABLE `scholarships`;
TRUNCATE TABLE `Logs`;
TRUNCATE TABLE `posts`;
TRUNCATE TABLE `ApplicationRequests`;
TRUNCATE TABLE `CourseApplications`;
TRUNCATE TABLE `Message`;
TRUNCATE TABLE `AdminAccess`;
TRUNCATE TABLE `subscription`;
TRUNCATE TABLE `countries`;
TRUNCATE TABLE `normUsers`;
TRUNCATE TABLE `PostTags`;
TRUNCATE TABLE `Post_Tag_Mapping`;
TRUNCATE TABLE `UserProfile`;
TRUNCATE TABLE `UserSessions`;
TRUNCATE TABLE `applicationsSurvey`;
TRUNCATE TABLE `Documents`;
TRUNCATE TABLE `youtubeVideos`;
TRUNCATE TABLE `AdminRights`;
TRUNCATE TABLE `Conversation`;
TRUNCATE TABLE `login_logs`;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Insert sample users (passwords are hashed 'password123')
INSERT INTO `users` (`username`, `email`, `password`, `created_at`, `updated_at`) VALUES
('admin', 'admin@mkscholars.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('john_doe', 'john.doe@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('jane_smith', 'jane.smith@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('mike_johnson', 'mike.j@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());

-- Insert sample normUsers (legacy users table)
INSERT INTO `normUsers` (`NoUsername`, `NoEmail`, `NoPhone`, `NoPassword`, `NoCreationDate`, `last_updated`) VALUES
('legacy_user1', 'legacy1@example.com', '+1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('legacy_user2', 'legacy2@example.com', '+1234567891', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());

-- Insert sample countries
INSERT INTO `countries` (`CountryName`, `CountryStatus`, `created_at`, `updated_at`) VALUES
('United States', 1, NOW(), NOW()),
('United Kingdom', 1, NOW(), NOW()),
('Canada', 1, NOW(), NOW()),
('Australia', 1, NOW(), NOW()),
('Germany', 1, NOW(), NOW());

-- Insert sample user profiles
INSERT INTO `UserProfile` (`userId`, `fullName`, `dateOfBirth`, `gender`, `address`, `city`, `countryId`, `postalCode`, `profilePicture`, `bio`, `created_at`, `updated_at`)
SELECT 
    userId,
    CONCAT(UCASE(LEFT(SUBSTRING_INDEX(username, '_', 1), 1)), LOWER(SUBSTRING(SUBSTRING_INDEX(username, '_', 1), 2)), ' ', 
           UCASE(LEFT(SUBSTRING_INDEX(username, '_', -1), 1)), LOWER(SUBSTRING(SUBSTRING_INDEX(username, '_', -1), 2))),
    DATE_SUB(CURDATE(), INTERVAL FLOOR(18 + RAND() * 30) YEAR),
    IF(RAND() > 0.5, 'male', 'female'),
    CONCAT(FLOOR(100 + RAND() * 9000), ' ', 
           ELT(FLOOR(1 + RAND() * 5), 'Main St', 'Oak Ave', 'Pine Rd', 'Maple Dr', 'Cedar Ln'),
           ', Apt ', FLOOR(1 + RAND() * 200)),
    ELT(FLOOR(1 + RAND() * 5), 'New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'),
    FLOOR(1 + RAND() * 5),
    CONCAT(FLOOR(10000 + RAND() * 90000), '-', FLOOR(1000 + RAND() * 9000)),
    CONCAT('avatars/avatar', FLOOR(1 + RAND() * 10), '.jpg'),
    CONCAT('Passionate student interested in ', 
           ELT(FLOOR(1 + RAND() * 5), 'Computer Science', 'Business Administration', 'Medicine', 'Engineering', 'Arts')),
    NOW(),
    NOW()
FROM `users`;

-- Insert sample scholarships
INSERT INTO `scholarships` (`scholarshipTitle`, `scholarshipDetails`, `scholarshipImage`, `scholarshipStatus`, `scholarshipUpdateDate`, `created_at`) VALUES
('Undergraduate STEM Scholarship', 'Scholarship for students pursuing degrees in Science, Technology, Engineering, or Mathematics.', 'stem_scholarship.jpg', 1, NOW(), NOW()),
('Women in Technology Award', 'Award for female students excelling in technology-related fields.', 'women_in_tech.jpg', 1, NOW(), NOW()),
('International Student Grant', 'Financial aid for international students with outstanding academic records.', 'international_grant.jpg', 1, NOW(), NOW()),
('Community Service Scholarship', 'For students who have demonstrated exceptional commitment to community service.', 'community_service.jpg', 1, NOW(), NOW()),
('Graduate Research Fellowship', 'Support for graduate students conducting research in their field of study.', 'grad_research.jpg', 1, NOW(), NOW());

-- Insert sample posts
INSERT INTO `posts` (`projectTitle`, `projectImg1`, `created_at`, `updated_at`) VALUES
('How to Write a Winning Scholarship Essay', 'essay_tips.jpg', NOW(), NOW()),
('Top 10 Scholarships for International Students', 'international_scholarships.jpg', NOW(), NOW()),
('Navigating College Admissions: A Complete Guide', 'admissions_guide.jpg', NOW(), NOW());

-- Insert sample PostTags
INSERT INTO `PostTags` (`tagName`, `tagSlug`, `TagStatus`, `created_at`, `updated_at`) VALUES
('Scholarships', 'scholarships', 1, NOW(), NOW()),
('Admissions', 'admissions', 1, NOW(), NOW()),
('Essay Writing', 'essay-writing', 1, NOW(), NOW()),
('Financial Aid', 'financial-aid', 1, NOW(), NOW()),
('Study Abroad', 'study-abroad', 1, NOW(), NOW());

-- Insert Post_Tag_Mapping
INSERT INTO `Post_Tag_Mapping` (`postId`, `tagId`, `created_at`) VALUES
(1, 3, NOW()),
(1, 1, NOW()),
(2, 1, NOW()),
(2, 5, NOW()),
(3, 2, NOW()),
(3, 4, NOW());

-- Insert sample ApplicationRequests
INSERT INTO `ApplicationRequests` (`UserId`, `ApplicationId`, `RequestDate`, `RequestTime`, `Status`, `Comments`) 
SELECT 
    u.userId,
    s.scholarshipId,
    CURDATE() - INTERVAL FLOOR(RAND() * 30) DAY,
    SEC_TO_TIME(FLOOR(RAND() * 86400)),
    ELT(FLOOR(1 + RAND() * 3), 'pending', 'approved', 'rejected'),
    CASE 
        WHEN RAND() > 0.7 THEN 'Applicant meets all requirements'
        WHEN RAND() > 0.5 THEN 'Additional documents requested'
        ELSE NULL
    END
FROM `users` u
CROSS JOIN `scholarships` s
WHERE RAND() > 0.7
LIMIT 10;

-- Insert sample CourseApplications
INSERT INTO `CourseApplications` (`userId`, `courseId`, `status`, `created_at`, `updated_at`)
SELECT 
    u.userId,
    FLOOR(1 + RAND() * 10),
    ELT(FLOOR(1 + RAND() * 4), 'pending', 'under_review', 'accepted', 'rejected'),
    NOW() - INTERVAL FLOOR(RAND() * 60) DAY,
    NOW() - INTERVAL FLOOR(RAND() * 30) DAY
FROM `users` u
WHERE RAND() > 0.5
LIMIT 5;

-- Insert sample Conversations
INSERT INTO `Conversation` (`UserId`, `AdminId`, `StartDate`, `StartTime`, `EndDate`, `EndTime`, `ConvStatus`, `created_at`, `updated_at`)
SELECT 
    u.userId,
    (SELECT userId FROM `users` WHERE username = 'admin' LIMIT 1),
    CURDATE() - INTERVAL FLOOR(RAND() * 7) DAY,
    SEC_TO_TIME(FLOOR(RAND() * 86400)),
    CASE WHEN RAND() > 0.3 THEN CURDATE() - INTERVAL FLOOR(RAND() * 6) DAY ELSE NULL END,
    CASE WHEN RAND() > 0.3 THEN SEC_TO_TIME(FLOOR(RAND() * 86400)) ELSE NULL END,
    IF(RAND() > 0.3, 1, 0),
    NOW(),
    NOW()
FROM `users` u
WHERE u.username != 'admin'
LIMIT 5;

-- Insert sample Messages
INSERT INTO `Message` (`ConvId`, `UserId`, `AdminId`, `MessageContent`, `SentDate`, `SentTime`, `MessageStatus`, `created_at`, `updated_at`)
SELECT 
    c.convId,
    CASE WHEN RAND() > 0.5 THEN c.UserId ELSE NULL END,
    CASE WHEN RAND() > 0.5 THEN c.AdminId ELSE NULL END,
    ELT(FLOOR(1 + RAND() * 5), 
        'Hello, I have a question about the application process.',
        'When is the deadline for the scholarship?',
        'What documents do I need to submit?',
        'Thank you for your assistance!',
        'Could you provide more information about this program?'),
    c.StartDate,
    ADDTIME(c.StartTime, SEC_TO_TIME(FLOOR(RAND() * 3600))),
    FLOOR(RAND() * 2),
    NOW(),
    NOW()
FROM `Conversation` c
CROSS JOIN (SELECT 1 n UNION SELECT 2 UNION SELECT 3) nums
WHERE nums.n <= FLOOR(1 + RAND() * 5);

-- Insert sample Documents
INSERT INTO `Documents` (`UserId`, `ConvId`, `DocumentName`, `OriginalFileName`, `FilePath`, `FileType`, `FileSize`, `DocumentType`, `Description`, `UploadDate`, `UploadTime`, `Status`, `AdminComments`, `ReviewedBy`, `ReviewedDate`, `created_at`, `updated_at`)
SELECT 
    u.userId,
    (SELECT convId FROM `Conversation` WHERE UserId = u.userId ORDER BY RAND() LIMIT 1),
    CONCAT('Document_', FLOOR(RAND() * 1000), '.pdf'),
    CONCAT('my_', ELT(FLOOR(1 + RAND() * 4), 'transcript', 'cv', 'essay', 'recommendation'), '.pdf'),
    CONCAT('uploads/documents/', UUID(), '.pdf'),
    'application/pdf',
    FLOOR(100000 + RAND() * 9000000),
    ELT(FLOOR(1 + RAND() * 4), 'transcript', 'cv', 'essay', 'recommendation'),
    CONCAT('My ', ELT(FLOOR(1 + RAND() * 4), 'academic transcript', 'CV', 'personal essay', 'letter of recommendation')),
    CURDATE() - INTERVAL FLOOR(RAND() * 30) DAY,
    SEC_TO_TIME(FLOOR(RAND() * 86400)),
    ELT(FLOOR(1 + RAND() * 3), 'pending', 'approved', 'rejected'),
    CASE WHEN RAND() > 0.7 THEN 'Document meets requirements' WHEN RAND() > 0.5 THEN 'Additional information needed' ELSE NULL END,
    CASE WHEN RAND() > 0.5 THEN (SELECT userId FROM `users` WHERE username = 'admin' LIMIT 1) ELSE NULL END,
    CASE WHEN RAND() > 0.5 THEN NOW() - INTERVAL FLOOR(RAND() * 7) DAY ELSE NULL END,
    NOW(),
    NOW()
FROM `users` u
WHERE RAND() > 0.5
LIMIT 8;

-- Insert sample youtubeVideos
INSERT INTO `youtubeVideos` (`videoLink`, `VideoTitle`, `VideoStatus`, `created_at`, `updated_at`) VALUES
('https://www.youtube.com/embed/dQw4w9WgXcQ', 'How to Apply for Scholarships', 1, NOW(), NOW()),
('https://www.youtube.com/embed/9bZkp7q19f0', 'Writing a Winning Personal Statement', 1, NOW(), NOW()),
('https://www.youtube.com/embed/JGwWNGJdvx8', 'Preparing for University Interviews', 1, NOW(), NOW());

-- Insert sample AdminAccess
INSERT INTO `AdminAccess` (`userId`, `accessLevel`, `permissions`, `created_at`, `updated_at`) 
SELECT 
    userId,
    'admin',
    '{"can_manage_users": true, "can_manage_content": true, "can_approve_applications": true}',
    NOW(),
    NOW()
FROM `users`
WHERE username = 'admin';

-- Insert sample AdminRights
INSERT INTO `AdminRights` (`AdminId`, `canManageUsers`, `canManagePosts`, `canManageScholarships`, `canManageSettings`, `canViewReports`, `created_at`, `updated_at`)
SELECT 
    userId,
    1, 1, 1, 1, 1,
    NOW(),
    NOW()
FROM `users`
WHERE username = 'admin';

-- Insert sample login_logs
INSERT INTO `login_logs` (`user_id`, `ip_address`, `user_agent`, `status`, `created_at`)
SELECT 
    u.userId,
    CONCAT('192.168.1.', FLOOR(1 + RAND() * 254)),
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    'success',
    NOW() - INTERVAL FLOOR(RAND() * 30) DAY
FROM `users` u
UNION ALL
SELECT 
    NULL,
    CONCAT('192.168.1.', FLOOR(1 + RAND() * 254)),
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    'failed',
    NOW() - INTERVAL FLOOR(RAND() * 30) DAY
LIMIT 20;

-- Insert sample applicationsSurvey
INSERT INTO `applicationsSurvey` (`FullNames`, `Email`, `Phone`, `CourseId`, `ApplicationContent`, `Comment`, `SubmitDate`, `SubmitTime`, `applicationStatus`, `created_at`, `updated_at`)
SELECT 
    CONCAT(ELT(FLOOR(1 + RAND() * 5), 'John', 'Jane', 'Michael', 'Sarah', 'David'), ' ', 
           ELT(FLOOR(1 + RAND() * 5), 'Smith', 'Johnson', 'Williams', 'Brown', 'Jones')),
    CONCAT(LOWER(SUBSTRING_INDEX(ELT(FLOOR(1 + RAND() * 5), 'john', 'jane', 'michael', 'sarah', 'david'), ' ', -1)), 
           FLOOR(100 + RAND() * 900), '@example.com'),
    CONCAT('+1', FLOOR(200 + RAND() * 800), FLOOR(100 + RAND() * 900), FLOOR(1000 + RAND() * 9000)),
    FLOOR(1 + RAND() * 5),
    'I am interested in applying for this scholarship because ',
    CASE WHEN RAND() > 0.7 THEN 'I need more information about the application process.' ELSE NULL END,
    CURDATE() - INTERVAL FLOOR(RAND() * 60) DAY,
    SEC_TO_TIME(FLOOR(RAND() * 86400)),
    FLOOR(RAND() * 2),
    NOW(),
    NOW()
FROM (SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) nums;

-- Insert sample UserSessions
INSERT INTO `UserSessions` (`sessionId`, `userId`, `ipAddress`, `userAgent`, `lastActivity`, `expiresAt`, `created_at`)
SELECT 
    UUID(),
    u.userId,
    CONCAT('192.168.1.', FLOOR(1 + RAND() * 254)),
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    NOW() - INTERVAL FLOOR(RAND() * 60) MINUTE,
    NOW() + INTERVAL 1 HOUR,
    NOW() - INTERVAL FLOOR(60 + RAND() * 60) MINUTE
FROM `users` u
WHERE RAND() > 0.5;

-- Insert sample Logs
INSERT INTO `Logs` (`userId`, `logMessage`, `logDate`, `logTime`, `logStatus`, `created_at`, `updated_at`)
SELECT 
    u.userId,
    CONCAT('User ', u.username, ' ', 
           ELT(FLOOR(1 + RAND() * 5), 'logged in', 'updated profile', 'applied for scholarship', 'uploaded document', 'viewed dashboard')),
    CURDATE() - INTERVAL FLOOR(RAND() * 30) DAY,
    SEC_TO_TIME(FLOOR(RAND() * 86400)),
    FLOOR(RAND() * 2),
    NOW(),
    NOW()
FROM `users` u
CROSS JOIN (SELECT 1 n UNION SELECT 2 UNION SELECT 3) nums
WHERE RAND() > 0.3
LIMIT 20;

-- Insert sample subscriptions
INSERT INTO `subscription` (`UserId`, `Item`, `SubscriptionStatus`, `subscriptionDate`, `expirationDate`, `created_at`, `updated_at`)
SELECT 
    u.userId,
    ELT(FLOOR(1 + RAND() * 3), 'Premium Membership', 'Scholarship Alerts', 'Newsletter'),
    ELT(FLOOR(1 + RAND() * 3), 'active', 'expired', 'cancelled'),
    CURDATE() - INTERVAL FLOOR(60 + RAND() * 90) DAY,
    CASE 
        WHEN RAND() > 0.5 THEN CURDATE() - INTERVAL FLOOR(30 + RAND() * 60) DAY 
        ELSE CURDATE() + INTERVAL FLOOR(30 + RAND() * 60) DAY 
    END,
    NOW(),
    NOW()
FROM `users` u
WHERE RAND() > 0.5
LIMIT 10;
