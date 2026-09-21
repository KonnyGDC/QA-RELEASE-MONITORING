-- QA Release Monitoring — demo seed (safe to re-run)
-- Tables: release_status, new_sprint, user, qa_data
-- Run AFTER database/schema.sql and database/migrate.sql

SET NAMES utf8mb4;

INSERT INTO release_status (name) VALUES
('Released'),
('For release'),
('Not in release')
ON DUPLICATE KEY UPDATE name = release_status.name;

INSERT INTO new_sprint (name) VALUES ('Sprint 10')
ON DUPLICATE KEY UPDATE name = new_sprint.name;

INSERT INTO `user` (name, role) VALUES
('Andrew C. Baldonado', 'Developer'),
('Ayn Mathew A. Astrera', 'Developer'),
('Bryan E. Berza', 'Developer'),
('Cene Vincent A. Soriano', 'Developer'),
('Charm Dominic N. Perez', 'Developer'),
('Earl Rhayan D. Padua', 'Developer'),
('Francis Alfonso Bandelaria', 'Developer'),
('Jameson T. Carigao', 'Developer'),
('John Ernie E. Angeles', 'Developer'),
('John Kylle Eries A. Carreon', 'Developer'),
('Karl Ghian C. Mercado', 'Developer'),
('Nario S. Albos', 'Developer'),
('Raven B. Bautista', 'Developer'),
('Ric M. Gregorio', 'Developer'),
('Rick Rosell O. David', 'Developer'),
('Rona Jean B. Castro', 'Developer'),
('Vincent C. Baylon', 'Developer'),
('Francis Alfonso Bandelaria', 'QA'),
('Mark Anthony A. Udarbe', 'QA'),
('Mryk Howell D. David', 'QA')
ON DUPLICATE KEY UPDATE name = `user`.name;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1122', 'BOS ATTI - Purchasing - Add CATEGORY 2 (AC) on the Approval Apps', (SELECT id FROM `user` WHERE name = 'Earl Rhayan D. Padua' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2026-04-25 10:20:00', (SELECT id FROM release_status WHERE name = 'Not in release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1268', '[BUG] BOS BPI - Sales Exporting report per Range - Incorrect alignment of report outputs', (SELECT id FROM `user` WHERE name = 'Vincent C. Baylon' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-06-22 10:38:00', (SELECT id FROM release_status WHERE name = 'Not in release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1358', 'BOS RPI - Purchasing - [RUR] Quantity Received - Did Not Return In PO', (SELECT id FROM `user` WHERE name = 'Andrew C. Baldonado' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-07-09 17:08:00', (SELECT id FROM release_status WHERE name = 'Not in release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1364', '[BUG] [DPC - Banking] - Incoming Payments - Customer - SI Did not update upon posting or cancellation', (SELECT id FROM `user` WHERE name = 'Nario S. Albos' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-07-15 12:50:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1377', 'BOS PLUS / BMOI - AP Invoice Status Remains Closed After Payment Cancellation', (SELECT id FROM `user` WHERE name = 'Nario S. Albos' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-07-16 19:40:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1411', 'BOS RPI : RFP Uploading (Payment Only) - Duplicate Profit Center', (SELECT id FROM `user` WHERE name = 'Francis Alfonso Bandelaria' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-07-30 10:50:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1437', 'BOS BGFI - CTGI - Collection - No Cost Center Captured in Upload Collection', (SELECT id FROM `user` WHERE name = 'Francis Alfonso Bandelaria' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-08-06 10:35:00', (SELECT id FROM release_status WHERE name = 'Not in release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1442', 'BOS - CTGI / BPI - PURCHASING BOOK REPORT - TO CORRECT EXPORTED REPORT (CTGI/BOS-DIR-20092-PURCHASES BOOK) FOR ACCURATE SUMMARY', (SELECT id FROM `user` WHERE name = 'Cene Vincent A. Soriano' AND role = 'Developer' LIMIT 1), NULL, 'Review', 'Leader Code Review', 'Emergency', '2020-08-11 10:50:00', (SELECT id FROM release_status WHERE name = 'Not in release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1443', '[BUG] BOS BPI - Sales - Item Master Uploading - PR Quantity Incorrect', (SELECT id FROM `user` WHERE name = 'Ayn Mathew A. Astrera' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'UAT', 'For UAT', 'Emergency', '2020-08-11 18:25:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1447', '[BUG] BLMS FM - Issue In Verification - Truck Return and Re-weigh Verification Module', (SELECT id FROM `user` WHERE name = 'Bryan E. Berza' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Planning', 'Pending - For RCA', 'Emergency', '2020-08-12 14:37:00', (SELECT id FROM release_status WHERE name = 'Not in release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1449', '[BUG] BOS - CTGI - Sales Receivable - Create Repeating Upload - WRONG AMOUNT CAPTURED IN THE POSTED AR TRANSACTION due to a truncated number', (SELECT id FROM `user` WHERE name = 'Cene Vincent A. Soriano' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-08-12 17:14:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1463', '231 - BPI / SLMS (DPC) - Distribution - Not Creating Master Data for TR', (SELECT id FROM `user` WHERE name = 'Charm Dominic N. Perez' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mryk Howell D. David' AND role = 'QA' LIMIT 1), 'UAT', 'For UAT', 'Emergency', '2020-08-18 10:04:00', (SELECT id FROM release_status WHERE name = 'Not in release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1470', 'BOS [BUG] Sales - CR Copy From Error from SO', (SELECT id FROM `user` WHERE name = 'Ayn Mathew A. Astrera' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-08-25 10:08:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1471', 'BOS BPI: Sales - Invoice - Allow user to edit', (SELECT id FROM `user` WHERE name = 'Ayn Mathew A. Astrera' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'UAT', 'For UAT', 'Emergency', '2020-08-25 10:20:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1473', 'BOS-BPI [BUG] AP INVOICE - VAT amount goes over to non-taxable PO item', (SELECT id FROM `user` WHERE name = 'Andrew C. Baldonado' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-08-27 09:45:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1480', 'BOS-RPI [BUG] ROU - SYSTEM BENCHMARK RATE', (SELECT id FROM `user` WHERE name = 'Earl Rhayan D. Padua' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Open', 'Emergency', '2020-09-01 11:18:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1486', 'BOS - RPI - PNI Report - Unable to export to excel', (SELECT id FROM `user` WHERE name = 'Earl Rhayan D. Padua' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Open', 'Emergency', '2020-09-03 10:00:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1487', 'BOS RPI (DPC) - Sales Tracking List Error Unknown Column', (SELECT id FROM `user` WHERE name = 'Ayn Mathew A. Astrera' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-09-03 10:04:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1488', 'BOS RPI (Sales) - Urgent - Wrong Quantity captured in the UM Totaling Print Layout', (SELECT id FROM `user` WHERE name = 'Francis Alfonso Bandelaria' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-09-03 11:28:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1490', 'BOS RPI (Sales) ROU - Printing error on DR - Can not print', (SELECT id FROM `user` WHERE name = 'Andrew C. Baldonado' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-09-04 18:25:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1497', 'BOS (BUG) - Multiple document issue for Cancelled GPO', (SELECT id FROM `user` WHERE name = 'Andrew C. Baldonado' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-09-07 14:14:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1511', '[BUG] BOS BPI - PG Feeding Stock Transfer Confirmation - Change status', (SELECT id FROM `user` WHERE name = 'Earl Rhayan D. Padua' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-09-08 10:14:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1011', '[WMS] Live (LIVE) - Express Delivery SO Uploading', (SELECT id FROM `user` WHERE name = 'Ayn Mathew A. Astrera' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Emergency', '2020-09-08 18:00:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-584', '(ENHANCEMENT) - BOS CTGI - Business Center (BC) Disbursement Fund Processing', (SELECT id FROM `user` WHERE name = 'John Kylle Eries A. Carreon' AND role = 'Developer' LIMIT 1), NULL, 'Release', 'Release Associated', 'Normal', '2020-02-18 11:11:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-540', '(ENHANCEMENT) - BOS CTGI - Business Center (BC) Disbursement Fund Processing', (SELECT id FROM `user` WHERE name = 'Bryan E. Berza' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-02-28 16:30:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-848', 'WMS Planning Serving Report: Enhance the Existing Filter of the Report', (SELECT id FROM `user` WHERE name = 'Bryan E. Berza' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-03-04 11:11:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-896', 'WMS: ENDING INVENTORY [Intransit Report] - Request for Customer Name Filter in the Summary', (SELECT id FROM `user` WHERE name = 'Karl Ghian C. Mercado' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-03-23 14:01:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-910', 'EBT BSFI : Document Tracer - RFP Monitoring Report Enhancement', (SELECT id FROM `user` WHERE name = 'Ayn Mathew A. Astrera' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Francis Alfonso Bandelaria' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-03-25 14:08:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-947', 'CTGI - RFP Report (both surface & central system)', (SELECT id FROM `user` WHERE name = 'Rona Jean B. Castro' AND role = 'Developer' LIMIT 1), NULL, 'UAT', 'For UAT', 'Normal', '2020-03-04 14:50:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-950', '[WMS] Warehouse - Truck Monitoring: Outbound Logistics Queuing System', (SELECT id FROM `user` WHERE name = 'Jameson T. Carigao' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-03-06 15:24:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1139', 'WMS: Production Report - Unscan for Freezing Summary Report', (SELECT id FROM `user` WHERE name = 'Karl Ghian C. Mercado' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-04-27 15:01:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1282', 'BOS DPLUG - Sales - Include Intercompany Sales into TR reports', (SELECT id FROM `user` WHERE name = 'Ayn Mathew A. Astrera' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Implementation', 'Dev Rework', 'Normal', '2020-06-28 13:01:00', (SELECT id FROM release_status WHERE name = 'Not in release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1353', 'WMS SERVING REPORT PDO: Sort per Distribution Channel (Serving Report)', (SELECT id FROM `user` WHERE name = 'Bryan E. Berza' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-07-08 08:30:00', (SELECT id FROM release_status WHERE name = 'Not in release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1383', 'BOS [BUG] WMS WRU - Process Request Quantity with 2 LMS TR with 1 WMS SO', (SELECT id FROM `user` WHERE name = 'Charm Dominic N. Perez' AND role = 'Developer' LIMIT 1), NULL, 'Implementation', 'Development In Progress', 'Normal', '2020-07-28 11:21:00', (SELECT id FROM release_status WHERE name = 'Not in release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1435', 'WMS - Sales Materials Uploading', (SELECT id FROM `user` WHERE name = 'Ayn Mathew A. Astrera' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Normal', '2020-08-07 14:05:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1438', '[WMS] REPORTS - Logistics Report : Live Trucker Billing - Automation of Live Hauling Billing (generation)', (SELECT id FROM `user` WHERE name = 'John Ernie E. Angeles' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-08-10 14:11:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1451', 'BSFI EBT: Sales - Add Delivery Confirmation Function', (SELECT id FROM `user` WHERE name = 'Ayn Mathew A. Astrera' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-08-10 10:50:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1461', '[SBX] WMS: Customer - Automate Branch Selection of Customer Master Mass Upload', (SELECT id FROM `user` WHERE name = 'John Kylle Eries A. Carreon' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-08-18 08:30:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1466', 'WMS : SSRS : Report : Delivery Report Additional Column', (SELECT id FROM `user` WHERE name = 'Ric M. Gregorio' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-08-19 17:00:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1479', '[WMS] Live : Live Trucker Rate Uploading', (SELECT id FROM `user` WHERE name = 'John Ernie E. Angeles' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-08-28 15:28:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1482', 'BOS CTGI - Banking: Update Sorting of Generated TXT/CSV Files by PV Number (in Corporate Check Printing)', (SELECT id FROM `user` WHERE name = 'Rona Jean B. Castro' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-09-02 10:18:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1484', '[WMS]: Warehouse - Truck Monitoring - Add of Comments and Replies in Queuing Management', (SELECT id FROM `user` WHERE name = 'Jameson T. Carigao' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-09-02 11:15:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1485', '[WMS]: DISPATCHING - DP OUTBOUND LOGISTICS QUEUING VIEW ENHANCEMENT IN SUPPORT TO THE EXISTING TICKET (CH-950[WMS] Warehouse - Truck Monitoring: Outbound Logistics Queuing System)', (SELECT id FROM `user` WHERE name = 'Jameson T. Carigao' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Mark Anthony A. Udarbe' AND role = 'QA' LIMIT 1), 'Release', 'Release Associated', 'Normal', '2020-09-03 10:04:00', (SELECT id FROM release_status WHERE name = 'For release' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1510', 'BOS - BPI Stock Receipt', (SELECT id FROM `user` WHERE name = 'Rick Rosell O. David' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Francis Alfonso Bandelaria' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Normal', '2020-09-10 10:41:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1512', 'BOS - STS Stock Confirmation - Require Attachment for Feed Mill Warehouses', (SELECT id FROM `user` WHERE name = 'Rick Rosell O. David' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Francis Alfonso Bandelaria' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Normal', '2020-09-10 13:41:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1454', 'BOS BPI - Purchasing - PO Prooflist Report- Additional filter & column STATUS', (SELECT id FROM `user` WHERE name = 'Raven B. Bautista' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Francis Alfonso Bandelaria' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Standard', '2020-08-14 10:45:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1458', 'BOS CTGI - Purchasing - PO Prooflist Report- Additional filter & column STATUS', (SELECT id FROM `user` WHERE name = 'Raven B. Bautista' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Francis Alfonso Bandelaria' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Standard', '2020-08-14 10:45:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1460', 'BOS BPI - Purchasing - PO Prooflist Report- Additional filter & column STATUS', (SELECT id FROM `user` WHERE name = 'Raven B. Bautista' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Francis Alfonso Bandelaria' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Standard', '2026-08-14 10:44:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1462', 'BOS CTGI - Purchasing - PO Prooflist Report- Additional filter & column STATUS', (SELECT id FROM `user` WHERE name = 'Raven B. Bautista' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Francis Alfonso Bandelaria' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Standard', '2026-08-14 10:45:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1476', 'BOS BPI - Purchasing - PO Prooflist Report- Additional filter & column STATUS', (SELECT id FROM `user` WHERE name = 'Raven B. Bautista' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Francis Alfonso Bandelaria' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Standard', '2026-08-27 10:08:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;

INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), 'CH-1477', 'BOS BPI Financial Report - Add-on REMARKS AND SUPPLIER NAME in VP Summary L.C. - New', (SELECT id FROM `user` WHERE name = 'Rona Jean B. Castro' AND role = 'Developer' LIMIT 1), (SELECT id FROM `user` WHERE name = 'Francis Alfonso Bandelaria' AND role = 'QA' LIMIT 1), 'Close', 'Completed', 'Standard', '2026-08-27 10:28:00', (SELECT id FROM release_status WHERE name = 'Released' LIMIT 1)
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;
