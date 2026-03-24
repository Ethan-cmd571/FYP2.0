

-- Difficulty + penalty support patch
ALTER TABLE `tasks` ADD COLUMN `difficulty` varchar(10) NOT NULL DEFAULT 'easy';
ALTER TABLE `user_state` ADD COLUMN `happiness_offset` int(11) NOT NULL DEFAULT 0 AFTER `happiness`;

UPDATE `tasks`
SET `difficulty` = CASE
  WHEN `reward_points` >= 70 THEN 'hard'
  WHEN `reward_points` >= 60 THEN 'medium'
  ELSE 'easy'
END;

UPDATE `crises`
SET `difficulty` = CASE
  WHEN `difficulty` >= 3 THEN 3
  WHEN `difficulty` = 2 THEN 2
  ELSE 1
END;

CREATE TABLE IF NOT EXISTS `user_task_completions` (
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
