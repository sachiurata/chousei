<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once("../models/EventModel.php");

$eventId = $_GET['event_id'] ?? '';
if (!$eventId) {
    die("イベントIDが指定されていません。");
}

$event = get_event($eventId);
$dates = get_event_dates($eventId);
$participants = get_participants($eventId);
$attendanceMatrix = get_attendance_matrix($eventId);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($event['event_name']) ?> - 出欠一覧</title>
  <style>
    table, th, td { border: 1px solid black; border-collapse: collapse; padding: 5px; }
    th { background-color: #eee; }
  </style>
</head>
<body>
  <h1><?= htmlspecialchars($event['event_name']) ?> - 出欠一覧</h1>
  <p><?= nl2br(htmlspecialchars($event['event_description'])) ?></p>

  <table>
  <tr>
    <th>候補日</th>
    <?php foreach ($participants as $p): ?>
      <th>
        <a href="edit_attendance.php?participant_id=<?= $p['participant_id'] ?>">
          <?= htmlspecialchars($p['participant_name']) ?>
        </a>
      </th>
    <?php endforeach; ?>
  </tr>

  <?php foreach ($dates as $date): ?>
    <tr>
      <td><?= htmlspecialchars($date['date']) ?></td>
      <?php foreach ($participants as $p): ?>
        <td><?= $attendanceMatrix[$date['date_id']][$p['participant_id']] ?? '-' ?></td>
      <?php endforeach; ?>
    </tr>
  <?php endforeach; ?>
</table>


  <p><a href="input_attendance.php?event_id=<?= $eventId ?>">出欠情報を入力</a></p>
  <p><a href="edit_event.php?event_id=<?= $eventId ?>">イベントの編集・削除</a></p>

</body>
</html>
