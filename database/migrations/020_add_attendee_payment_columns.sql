-- Migration 020: Add payment/booking columns to st_class_attendees
-- Supports: Card payments, manual payments, discounts, credit codes, gift certificates, refunds, cancellations

ALTER TABLE st_class_attendees
    ADD COLUMN payment_method VARCHAR(30) DEFAULT 'manual' AFTER quote_amount,
    ADD COLUMN payment_id BIGINT UNSIGNED NULL AFTER payment_method,
    ADD COLUMN square_payment_id VARCHAR(100) NULL AFTER payment_id,
    ADD COLUMN payment_status VARCHAR(30) DEFAULT 'pending' AFTER square_payment_id,
    ADD COLUMN discount_code VARCHAR(50) NULL AFTER payment_status,
    ADD COLUMN discount_amount DECIMAL(10,2) DEFAULT 0.00 AFTER discount_code,
    ADD COLUMN credit_code_id BIGINT UNSIGNED NULL AFTER discount_amount,
    ADD COLUMN credit_amount DECIMAL(10,2) DEFAULT 0.00 AFTER credit_code_id,
    ADD COLUMN gift_certificate_id BIGINT UNSIGNED NULL AFTER credit_amount,
    ADD COLUMN gift_amount DECIMAL(10,2) DEFAULT 0.00 AFTER gift_certificate_id,
    ADD COLUMN refunded_amount DECIMAL(10,2) DEFAULT 0.00 AFTER gift_amount,
    ADD COLUMN cancelled_at DATETIME NULL AFTER refunded_amount,
    ADD COLUMN cancelled_reason TEXT NULL AFTER cancelled_at;

-- Index for payment lookups
ALTER TABLE st_class_attendees ADD INDEX idx_sca_payment_id (payment_id);
ALTER TABLE st_class_attendees ADD INDEX idx_sca_square_payment_id (square_payment_id);
ALTER TABLE st_class_attendees ADD INDEX idx_sca_credit_code_id (credit_code_id);
ALTER TABLE st_class_attendees ADD INDEX idx_sca_gift_certificate_id (gift_certificate_id);
