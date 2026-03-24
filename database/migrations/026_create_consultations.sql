-- ============================================================
-- Migration 026: Create consultations table
-- ============================================================

CREATE TABLE IF NOT EXISTS consultations (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid            CHAR(36)        NOT NULL UNIQUE,
    first_name      VARCHAR(100)    NOT NULL,
    last_name       VARCHAR(100)    NOT NULL,
    email           VARCHAR(255)    NOT NULL,
    phone           VARCHAR(30)     DEFAULT NULL,
    consultation_type ENUM('partnership','software_integration','general') NOT NULL DEFAULT 'partnership',
    facility_stage  VARCHAR(50)     DEFAULT NULL,
    planned_location VARCHAR(200)   DEFAULT NULL,
    number_of_courts VARCHAR(20)    DEFAULT NULL,
    software_interest VARCHAR(255)  DEFAULT NULL,
    message         TEXT            DEFAULT NULL,
    status          ENUM('new','contacted','in_progress','closed') NOT NULL DEFAULT 'new',
    notes           TEXT            DEFAULT NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_consultations_status (status),
    INDEX idx_consultations_type (consultation_type),
    INDEX idx_consultations_email (email),
    INDEX idx_consultations_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
