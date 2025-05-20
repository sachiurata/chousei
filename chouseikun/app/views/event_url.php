<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$eventId = $_GET['event_id'] ?? '';
if (!$eventId) {
    die("イベントIDが指定されていません。");
}

$url = "http://localhost:8080/views/attendance_view_creator.php?event_id=" . $eventId;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>イベントURL通知</title>
</head>
<body>
  <h1>イベントが作成されました！</h1>
  <p>以下のURLを参加者に共有してください：</p>
  <p><a href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($url) ?></a></p>
</body>
</html>
