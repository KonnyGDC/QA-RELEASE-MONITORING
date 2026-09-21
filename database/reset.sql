-- DANGER: drops ALL application tables (structure + data)
-- Dev/local only. Re-import schema.sql → migrate.sql → optional seed.sql after reset.
--
-- To keep tables and only clear rows, use database/fresh.sql instead.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS ticket_history;
DROP TABLE IF EXISTS qa_data;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS new_sprint;
DROP TABLE IF EXISTS release_status;
DROP TABLE IF EXISTS schema_migrations;

SET FOREIGN_KEY_CHECKS = 1;
