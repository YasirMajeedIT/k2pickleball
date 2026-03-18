-- Migration 023: Add tax columns to st_class_attendees
-- Tax amount and rate stored per attendee for audit trail

ALTER TABLE st_class_attendees
    ADD COLUMN tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER gift_amount,
    ADD COLUMN tax_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER tax_amount;
