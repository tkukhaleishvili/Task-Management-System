-- ============================================
-- TASKS TABLE
-- ============================================
DROP TABLE IF EXISTS tasks;

CREATE TABLE tasks (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  category_id INT UNSIGNED NULL,
  assigned_to INT UNSIGNED NULL,
  created_by INT UNSIGNED NOT NULL,
  priority ENUM('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
  status ENUM('Pending','In Progress','Completed') NOT NULL DEFAULT 'Pending',
  due_date DATE NULL,
  is_deleted TINYINT(1) NOT NULL DEFAULT 0, 
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL, 

  -- Foreign Keys
  CONSTRAINT fk_tasks_category FOREIGN KEY (category_id)
    REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE,

  CONSTRAINT fk_tasks_assigned FOREIGN KEY (assigned_to)
    REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,

  CONSTRAINT fk_tasks_creator FOREIGN KEY (created_by)
    REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,

  -- Indexes
  INDEX idx_tasks_status (status),
  INDEX idx_tasks_priority (priority),
  INDEX idx_tasks_due_date (due_date),
  INDEX idx_tasks_assigned_to (assigned_to),
  INDEX idx_tasks_is_deleted (is_deleted),
  FULLTEXT KEY ft_tasks_title_desc (title, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
