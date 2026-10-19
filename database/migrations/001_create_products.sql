CREATE TABLE IF NOT EXISTS `products` (
    `id` int NOT NULL,
    `name` varchar(255) NOT NULL,
    `description` text,
    `price` decimal(10, 2) NOT NULL,
    `stock` int NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
