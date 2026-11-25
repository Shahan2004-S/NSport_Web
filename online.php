<?php
// Live "students online" data for the admin dashboard and the badge at the top of every admin page.
require_once '../config.php';
header('Content-Type: application/json'); header('Cache-Control: no-store');
if (!is_admin()) { http_response_code(403); echo '{"error":"admin only"}'; exit; }
$list = online_students();
echo json_encode(['count' => count($list), 'students' => $list]);
