<?php
require __DIR__ . '/../config/db.php';

// 1. A table that remembers which migrations already ran
$pdo->exec('CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL UNIQUE,
    ran_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ');

// 2. Which one already ran??
$done = $pdo
    ->query('SELECT filename FROM migrations')
    ->fetchAll(PDO::FETCH_COLUMN);

// 3. All migrations files, in order
$files = glob(__DIR__ . '/migrations/*.sql');
sort($files);

// 4. Run only the new ones
foreach ($files as $file) {
    $name = basename($file);
    if (in_array($name, $done)) {
        continue;
    }
    $pdo->exec(file_get_contents($file));
    $stmt = $pdo->prepare('INSERT INTO migrations (filename) VALUES (?)');
    $stmt->execute([$name]);
    echo "Ran: $name\n";
}
echo "Up to date.\n";
