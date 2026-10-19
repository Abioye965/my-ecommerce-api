<?php

// Set the content type to JSON
header('Content-Type: application/json');

echo json_encode([
    'status' => 'connected',
    'message' => 'Php is running perfectly'
]);
