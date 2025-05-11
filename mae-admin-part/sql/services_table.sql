CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample services
INSERT INTO services (name, description) VALUES
('Document Processing', 'Assistance with document preparation and processing'),
('Legal Consultation', 'Legal advice and consultation services'),
('Community Support', 'Community outreach and support programs');