-- Insert sample feedback data
INSERT INTO feedback (requester_name, service_id, rating, feedback_text, created_at) VALUES
('John Smith', 1, 5, 'Excellent service! The team was very professional and completed the work ahead of schedule.', NOW()),
('Maria Garcia', 1, 4, 'Great experience overall. The staff was friendly and knowledgeable.', NOW() - INTERVAL 1 DAY),
('Robert Johnson', 2, 5, 'Outstanding service quality. Would highly recommend to others.', NOW() - INTERVAL 2 DAY),
('Sarah Williams', 2, 3, 'Service was good but took longer than expected. Staff was helpful though.', NOW() - INTERVAL 3 DAY),
('Michael Brown', 3, 5, 'Amazing work! The team went above and beyond our expectations.', NOW() - INTERVAL 4 DAY),
('Emily Davis', 3, 4, 'Very satisfied with the service. Professional and efficient.', NOW() - INTERVAL 5 DAY),
('David Wilson', 1, 5, 'Best service I have ever received. Will definitely use again!', NOW() - INTERVAL 6 DAY),
('Lisa Anderson', 2, 4, 'Good service, friendly staff. Would use again.', NOW() - INTERVAL 7 DAY),
('James Taylor', 3, 5, 'Exceptional service! The team was very thorough and professional.', NOW() - INTERVAL 8 DAY),
('Jennifer Martinez', 1, 4, 'Great experience. The staff was very helpful and accommodating.', NOW() - INTERVAL 9 DAY); 