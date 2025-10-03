-- Coupons schema for referral/discounts

CREATE TABLE IF NOT EXISTS Coupons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(64) NOT NULL UNIQUE,
  description VARCHAR(255) NULL,
  discount_type ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
  discount_value DECIMAL(10,2) NOT NULL DEFAULT 0,
  scope_type ENUM('global','course_pricing','scholarship') NOT NULL DEFAULT 'global',
  scope_id INT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  max_uses INT NULL,
  per_user_limit INT NULL,
  valid_from DATETIME NULL,
  valid_to DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_scope (scope_type, scope_id)
);

CREATE TABLE IF NOT EXISTS CouponRedemptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  coupon_id INT NOT NULL,
  user_id INT NOT NULL,
  transaction_reference VARCHAR(128) NOT NULL,
  amount_charged DECIMAL(12,2) NOT NULL,
  currency VARCHAR(8) NOT NULL,
  status ENUM('success','failed') NOT NULL DEFAULT 'success',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_redemption (coupon_id, user_id, transaction_reference),
  INDEX idx_coupon_user (coupon_id, user_id),
  CONSTRAINT fk_coupon_redemptions_coupon FOREIGN KEY (coupon_id) REFERENCES Coupons(id)
);


