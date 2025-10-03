-- Performance Optimization Script for Course Management
-- Run this script to improve database performance

-- Add indexes for better query performance
ALTER TABLE `Courses` ADD INDEX `idx_course_status_date` (`courseDisplayStatus`, `courseCreatedDate`);
ALTER TABLE `Courses` ADD INDEX `idx_course_dates` (`courseStartDate`, `courseEndDate`);
ALTER TABLE `Courses` ADD INDEX `idx_course_creator` (`courseCreatedBy`);

-- Add indexes for CoursePricing table
ALTER TABLE `CoursePricing` ADD INDEX `idx_course_pricing_course` (`courseId`);
ALTER TABLE `CoursePricing` ADD INDEX `idx_course_pricing_currency` (`currency`);

-- Add indexes for Currencies table
ALTER TABLE `Currencies` ADD INDEX `idx_currencies_active` (`isActive`);

-- Add indexes for CourseEnrollments table
ALTER TABLE `CourseEnrollments` ADD INDEX `idx_enrollments_course` (`courseId`);
ALTER TABLE `CourseEnrollments` ADD INDEX `idx_enrollments_user` (`userId`);
ALTER TABLE `CourseEnrollments` ADD INDEX `idx_enrollments_status` (`enrollmentStatus`);

-- Add indexes for CourseLessons table
ALTER TABLE `CourseLessons` ADD INDEX `idx_lessons_course` (`courseId`);
ALTER TABLE `CourseLessons` ADD INDEX `idx_lessons_published` (`isPublished`);
ALTER TABLE `CourseLessons` ADD INDEX `idx_lessons_order` (`lessonOrder`);

-- Add indexes for Coupons table
ALTER TABLE `Coupons` ADD INDEX `idx_coupons_code` (`code`);
ALTER TABLE `Coupons` ADD INDEX `idx_coupons_status` (`status`);
ALTER TABLE `Coupons` ADD INDEX `idx_coupons_dates` (`valid_from`, `valid_to`);
ALTER TABLE `Coupons` ADD INDEX `idx_coupons_scope` (`scope_type`, `scope_id`);

-- Add indexes for CouponRedemptions table
ALTER TABLE `CouponRedemptions` ADD INDEX `idx_redemptions_coupon` (`coupon_id`);
ALTER TABLE `CouponRedemptions` ADD INDEX `idx_redemptions_user` (`user_id`);
ALTER TABLE `CouponRedemptions` ADD INDEX `idx_redemptions_date` (`redemption_date`);

-- Optimize table storage
OPTIMIZE TABLE `Courses`;
OPTIMIZE TABLE `CoursePricing`;
OPTIMIZE TABLE `Currencies`;
OPTIMIZE TABLE `CourseEnrollments`;
OPTIMIZE TABLE `CourseLessons`;
OPTIMIZE TABLE `Coupons`;
OPTIMIZE TABLE `CouponRedemptions`;

-- Show table status for verification
SHOW TABLE STATUS WHERE Name IN ('Courses', 'CoursePricing', 'Currencies', 'CourseEnrollments', 'CourseLessons', 'Coupons', 'CouponRedemptions');
