-- ============================================
-- INITIAL DATA SEEDER
-- ============================================

-- ======= USERS =======
INSERT INTO users (username, email, password_hash, role, created_at)
VALUES
  ('admin', 'admin@test.com',
   '$2y$10$T5uJxPEBGEwBGKyYwF8KMOMi6iqsE.5bDdCN.S2Qz4F7IoWcL8Q2G', 'Admin', NOW()),
  ('manager', 'manager@test.com',
   '$2y$10$T5uJxPEBGEwBGKyYwF8KMOMi6iqsE.5bDdCN.S2Qz4F7IoWcL8Q2G', 'Manager', NOW()),
  ('developer', 'dev@test.com',
   '$2y$10$T5uJxPEBGEwBGKyYwF8KMOMi6iqsE.5bDdCN.S2Qz4F7IoWcL8Q2G', 'User', NOW()),
  ('designer', 'designer@test.com',
   '$2y$10$T5uJxPEBGEwBGKyYwF8KMOMi6iqsE.5bDdCN.S2Qz4F7IoWcL8Q2G', 'User', NOW());

-- ======= CATEGORIES =======
INSERT INTO categories (name, description, color, created_at)
VALUES
  ('Development', 'All coding and feature-related tasks', '#007bff', NOW()),
  ('Design', 'UI/UX and front-end design tasks', '#e83e8c', NOW()),
  ('Testing', 'QA, bug fixes, and testing tasks', '#28a745', NOW()),
  ('Documentation', 'User manuals and technical documentation', '#ffc107', NOW());

-- ======= TASKS =======
INSERT INTO tasks
(title, description, category_id, assigned_to, created_by, priority, status, due_date, created_at)
VALUES
  ('Implement Authentication System',
   'Develop secure user login and registration using PHP sessions and password_hash.',
   1, 3, 1, 'High', 'In Progress', DATE_ADD(CURDATE(), INTERVAL 3 DAY), NOW()),

  ('Design Dashboard UI',
   'Create responsive HTML/CSS dashboard for displaying task metrics.',
   2, 4, 2, 'Medium', 'Pending', DATE_ADD(CURDATE(), INTERVAL 5 DAY), NOW()),

  ('Write Unit Tests for Task Repository',
   'Add PHPUnit test coverage for task creation and update functions.',
   3, 3, 2, 'Low', 'Pending', DATE_ADD(CURDATE(), INTERVAL 10 DAY), NOW()),

  ('Prepare Project Documentation',
   'Write setup guide and database schema description.',
   4, 4, 1, 'Medium', 'Pending', DATE_ADD(CURDATE(), INTERVAL 7 DAY), NOW()),

  ('Fix CSS Bugs on Mobile',
   'Debug layout issues for mobile version of the app.',
   2, 3, 1, 'Critical', 'In Progress', DATE_ADD(CURDATE(), INTERVAL 2 DAY), NOW()),

  ('Deploy to Test Server',
   'Deploy project to internal test environment and verify configuration.',
   1, 4, 1, 'High', 'Pending', DATE_ADD(CURDATE(), INTERVAL 14 DAY), NOW());

-- ======= TASK COMMENTS =======
INSERT INTO task_comments (task_id, user_id, comment, created_at)
VALUES
  (1, 3, 'Started working on login controller today.', NOW()),
  (1, 1, 'Remember to implement CSRF protection.', NOW()),
  (2, 4, 'Initial dashboard layout completed, needs review.', NOW()),
  (3, 3, 'Writing unit tests for TaskRepository::create method.', NOW()),
  (4, 4, 'Will include ER diagram in the documentation.', NOW()),
  (5, 3, 'Fixed navbar responsiveness on mobile view.', NOW()),
  (6, 1, 'Server setup is in progress, expecting completion tomorrow.', NOW());

-- ======= REMEMBER TOKENS (Optional Example) =======
-- (You can leave empty initially; will be auto-generated during login)
