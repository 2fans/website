CREATE DATABASE it_db;
USE it_db;

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    details TEXT,
    price VARCHAR(100),
    icon VARCHAR(100)
);

INSERT INTO services (name, details, price, icon) VALUES 
('Cloud Setup', 'Full AWS migration and environment setup.', '$500', 'cloud.png'),
('PC Repair', 'Hardware diagnostics and part replacement.', '$50/hr', 'repair.png'),
('Network Security', 'Firewall configuration and threat monitoring.', '$200', 'shield.png');