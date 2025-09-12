CREATE DATABASE IF NOT EXISTS quickauth;

CREATE TABLE `users` (`id` int NOT NULL AUTO_INCREMENT,`name` varchar(255) NOT NULL,`email` varchar(255) NOT NULL,`about` text DEFAULT NULL,`pass` varchar(255) NOT NULL,`remember_token` varchar(64) DEFAULT NULL,`remember_expiry` datetime DEFAULT NULL,PRIMARY KEY (`id`),UNIQUE KEY `email` (`email`),UNIQUE KEY `remember_token` (`remember_token`));