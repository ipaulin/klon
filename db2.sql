
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `users` 
ADD COLUMN `description` TEXT NULL,
ADD COLUMN `followers_count` INT NULL,
ADD COLUMN `followd_count` INT NULL,
ADD COLUMN `display_name` VARCHAR(100) NULL,
ADD COLUMN `nickname` VARCHAR(25) NOT NULL,
ADD COLUMN `profile_image` VARCHAR(40) NULL,
ADD COLUMN `cover_image` VARCHAR(40) NULL;


ALTER TABLE `posts`
ADD FOREIGN KEY fk_post(user_id)
REFERENCES users(id)
ON DELETE RESTRICT
ON UPDATE CASCADE;


CREATE TABLE `user_followers` (
  `user_id` INT(10) unsigned NOT NULL,
  `user_following_id` INT(10) NOT NULL,
  PRIMARY KEY (`user_id`, `user_following_id`),
  UNIQUE KEY `user_following_id_UNIQUE` (`user_following_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
