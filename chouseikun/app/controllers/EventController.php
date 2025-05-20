<?php
require_once("../models/EventModel.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // === 1. イベント作成 ===
    if (!isset($_POST['action'])) {
        $name = $_POST['event_name'] ?? '';
        $desc = $_POST['description'] ?? '';
        $rawDates = $_POST['dates'] ?? '';

        if (trim($name) === '' || trim($rawDates) === '') {
            die("イベント名と候補日が必要です。");
        }

        $dates = array_filter(array_map('trim', explode("\n", $rawDates)));
        $eventId = create_event($name, $desc, $dates);

        // Cookie 設定（30日間）
        setcookie("creator_" . $eventId, "1", time() + 60*60*24*30, "/");

        header("Location: ../views/event_url.php?event_id=" . $eventId);
        exit();
    }

    // === 2. イベント更新 ===
    elseif ($_POST['action'] === 'イベント更新') {
        $eventId = $_POST['event_id'] ?? '';
        $name = $_POST['event_name'] ?? '';
        $desc = $_POST['description'] ?? '';
        $deleteDates = $_POST['delete_dates'] ?? [];
        $newDates = explode("\n", $_POST['new_dates'] ?? '');

        if (!$eventId || !$name) {
            die("イベントIDまたは名前が不正です。");
        }

        $conn = db_connect();

        // (1) イベント名・説明更新
        $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_description = ? WHERE event_id = ?");
        $stmt->bind_param("ssi", $name, $desc, $eventId);
        $stmt->execute();
        $stmt->close();

        // (2) 削除候補日（と関連出欠）削除
        if (!empty($deleteDates)) {
            $in = implode(',', array_map('intval', $deleteDates));
            $conn->query("DELETE FROM attendances WHERE date_id IN ($in)");
            $conn->query("DELETE FROM dates WHERE date_id IN ($in)");
        }

        // (3) 新しい候補日追加
        $stmt2 = $conn->prepare("INSERT INTO dates (event_id, date) VALUES (?, ?)");
        foreach ($newDates as $d) {
            $parsed = parse_date_string($d);
            if ($parsed) {
                $stmt2->bind_param("is", $eventId, $parsed);
                $stmt2->execute();
            }
        }
        $stmt2->close();
        $conn->close();

        header("Location: ../views/attendance_view_creator.php?event_id=" . $eventId);
        exit();
    }

    // === 3. イベント削除 ===
    elseif ($_POST['action'] === 'イベント削除') {
        $eventId = $_POST['event_id'] ?? '';
        if (!$eventId) die("イベントIDが不正です。");

        $conn = db_connect();

        // ON DELETE CASCADE の前提
        $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        header("Location: ../views/delete_confirmed.php");
        exit();
    }
}
