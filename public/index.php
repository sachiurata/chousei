<?php
$action = $_GET['action'] ?? 'form';
require_once __DIR__ . '/controller/EventController.php';
$controller = new EventController();

if ($action === 'form') {
    $controller->form();
} elseif ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->create();
} elseif ($action === 'url' && isset($_GET['event_id'])) {
    $controller->url($_GET['event_id']);
} elseif ($action === 'detail' && isset($_GET['event_id'])) {
    $controller->detail($_GET['event_id']);
} elseif ($action === 'attendance' && isset($_GET['event_id'])) {
    $controller->attendance($_GET['event_id']);
} elseif ($action === 'attendance_submit' && isset($_GET['event_id'])) {
    $controller->attendance_submit($_GET['event_id']);
} 
//追加
elseif ($action === 'edit_attendance' && isset($_GET['event_id'], $_GET['user_id'])) {
    $controller->editAttendance($_GET['event_id'], $_GET['user_id']);
} elseif ($action === 'update_attendance' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->updateAttendance($_POST);
}

else {
    http_response_code(404);
    echo "ページが見つかりません";
}