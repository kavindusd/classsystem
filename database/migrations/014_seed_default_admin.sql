-- Default admin account
-- Email: admin@classystem.com
-- Password: admin123  ← change this immediately after first login
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
    ('Super Admin', 'admin@classystem.com', '$2y$10$33O6/tsKm01N2pNYvqDt9.ZaW86a.17BCrOjrvQf1VSZNtIYHC.eC', 'admin');

INSERT INTO `admins` (`user_id`, `admin_id`) VALUES
    (LAST_INSERT_ID(), 'ADM000001');
