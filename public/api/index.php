<?php
header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'data'    => [
        'name'    => 'mydev.papdatabase API',
        'version' => '1.0.0',
    ],
]);
