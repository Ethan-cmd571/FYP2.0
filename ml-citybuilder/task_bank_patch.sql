ALTER TABLE tasks ADD COLUMN difficulty VARCHAR(10) NOT NULL DEFAULT 'easy';
ALTER TABLE crises ADD COLUMN difficulty VARCHAR(10) NOT NULL DEFAULT 'easy';
ALTER TABLE crises ADD COLUMN district VARCHAR(20) NOT NULL DEFAULT 'central';
ALTER TABLE user_state ADD COLUMN happiness_offset INT NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS user_task_completions (
  user_id INT NOT NULL,
  task_id INT NOT NULL,
  completed_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (user_id, task_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- The updated api.php now auto-seeds roughly 50 tasks for each difficulty
-- the first time it runs, so no large manual SQL import is required.
