<?php
require_once 'config.php';
header('Content-Type: application/json');
header('Cache-Control: no-store');
echo json_encode(['ok' => (bool)user()]);
