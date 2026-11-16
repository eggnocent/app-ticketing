-- Database development UTS Event Ticket.
-- Jalankan sebagai root MySQL: sudo mysql < scripts/create-database.sql
-- Password di bawah hanya untuk lingkungan lokal, jangan dipakai di produksi.

CREATE DATABASE IF NOT EXISTS uts_event_ticket
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'ticket_app'@'localhost' IDENTIFIED BY 'ticket_app';
ALTER USER 'ticket_app'@'localhost' IDENTIFIED BY 'ticket_app';
GRANT ALL PRIVILEGES ON uts_event_ticket.* TO 'ticket_app'@'localhost';

CREATE USER IF NOT EXISTS 'ticket_app'@'127.0.0.1' IDENTIFIED BY 'ticket_app';
ALTER USER 'ticket_app'@'127.0.0.1' IDENTIFIED BY 'ticket_app';
GRANT ALL PRIVILEGES ON uts_event_ticket.* TO 'ticket_app'@'127.0.0.1';

FLUSH PRIVILEGES;
