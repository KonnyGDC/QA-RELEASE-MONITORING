-- QA Release Monitoring — incremental structure updates (safe to re-run)
-- Run AFTER database/schema.sql on every deploy when the app version changes.
-- Uses MariaDB/MySQL compatible patterns; existing rows are preserved.
--
-- Add new ALTER statements below when you change the PHP app schema.
-- Each block should be idempotent (safe if run twice).

SET NAMES utf8mb4;

-- Required lookup values (app works without database/seed.sql)
INSERT INTO release_status (name) VALUES
('Released'),
('For release'),
('Not in release')
ON DUPLICATE KEY UPDATE name = release_status.name;

-- Ticket action history (create, update, delete, import)
CREATE TABLE IF NOT EXISTS ticket_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT NULL,
  change_id VARCHAR(32) NOT NULL,
  action ENUM('created','updated','deleted','imported') NOT NULL,
  details JSON NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_ticket_history_ticket_id (ticket_id),
  KEY idx_ticket_history_change_id (change_id),
  KEY idx_ticket_history_created_at (created_at),
  CONSTRAINT fk_ticket_history_ticket FOREIGN KEY (ticket_id) REFERENCES qa_data(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migrations (version) VALUES ('2026.03.26.4')
ON DUPLICATE KEY UPDATE version = schema_migrations.version;
