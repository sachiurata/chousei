<?php
require_once __DIR__ . '/../model/Event.php';

class EventController {
    public function form() {
        require __DIR__ . '/../view/event_form.php';
    }

    public function create() {
        $event = new Event();
        $result = $event->create($_POST);

        // バリデーションエラーがあればフォームに戻す
        if (isset($result['errors'])) {
            $errors = $result['errors'];
            // エラーと入力値をビューに渡す
            require __DIR__ . '/../view/event_form.php';
            return;
        }

        $event_id = $result['event_id'];
        header("Location: index.php?action=url&event_id=$event_id");
        exit;
    }

    public function url($event_id) {
        $event_url = "index.php?action=detail&event_id=" . urlencode($event_id);
        require __DIR__ . '/../view/event_url.php';
    }

    public function detail($event_id) {
        $event = new Event();
        $eventData = $event->find($event_id); // ['event' => ..., 'dates' => ...]
        require __DIR__ . '/../view/event_detail.php';
    }

    public function attendance($event_id) {
    $event = new Event();
    $eventData = $event->find($event_id);
    require __DIR__ . '/../view/attendance_form.php';
    }

    public function attendance_submit($event_id) {
    $event = new Event();
    $event->saveAttendance($event_id, $_POST);
    header("Location: index.php?action=detail&event_id=" . urlencode($event_id));
    exit;
    }
}