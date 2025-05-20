<?php
require_once("../models/EventModel.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['event_id'] ?? '';
    $name = $_POST['participant_name'] ?? '';
    $comment = $_POST['comment'] ?? '';
    $attendanceData = $_POST['attendance'] ?? [];
    $participantId = $_POST['participant_id'] ?? null;

    if (!$eventId || !$name || empty($attendanceData)) {
        die("必要な情報が不足しています。");
    }

    $conn = db_connect();

    if ($participantId) {
        // ✅ 更新処理
        // 1. 参加者情報更新
        $stmt = $conn->prepare("UPDATE participants SET participant_name = ?, comment = ? WHERE participant_id = ?");
        $stmt->bind_param("ssi", $name, $comment, $participantId);
        $stmt->execute();
        $stmt->close();

        // 2. 出欠情報削除 → 再登録（簡易実装）
        $stmt_del = $conn->prepare("DELETE FROM attendances WHERE participant_id = ?");
        $stmt_del->bind_param("i", $participantId);
        $stmt_del->execute();
        $stmt_del->close();

    } else {
        // ✅ 新規登録処理
        $stmt = $conn->prepare("INSERT INTO participants (event_id, participant_name, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $eventId, $name, $comment);
        $stmt->execute();
        $participantId = $stmt->insert_id;
        $stmt->close();
    }

    // ✅ 出欠情報一括登録
    $stmt2 = $conn->prepare("INSERT INTO attendances (date_id, participant_id, attendance) VALUES (?, ?, ?)");
    foreach ($attendanceData as $dateId => $status) {
        $stmt2->bind_param("iii", $dateId, $participantId, $status);
        $stmt2->execute();
    }
    $stmt2->close();
    $conn->close();

    // ✅ 編集后也回到出欠情報一覧
    header("Location: ../views/attendance_view_creator.php?event_id=" . $eventId);
    exit();
}
