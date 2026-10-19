<?php
header('Content-Type: application/json');
require __DIR__ . '/../config/db.php';

echo json_encode([
    'connected' => true,
    'message' => 'Database connection successful',
]);
