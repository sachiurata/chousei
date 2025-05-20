<!-- ⑦/⑧出欠情報更新画面 -->
<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once("../models/EventModel.php");

$participantId = $_GET['participant_id'] ?? '';
if (!$participantId) {
    die("参加者IDが指定されていません。");
}

$participant = get_participant_info($participantId);
$event = get_event($participant['event_id']);
$dates = get_event_dates($event['event_id']);
$attendanceMap = get_participant_attendance($participantId);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($event['event_name']) ?> - 出欠情報編集</title>
</head>
<body>
  <h1><?= htmlspecialchars($event['event_name']) ?> - 出欠情報編集</h1>
  <form action="../controllers/AttendanceController.php" method="post">
    <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
    <input type="hidden" name="participant_id" value="<?= $participantId ?>">

    <label>お名前：</label><br>
    <input type="text" name="participant_name" required maxlength="100"
           value="<?= htmlspecialchars($participant['participant_name']) ?>"><br><br>

    <label>コメント：</label><br>
    <textarea name="comment" rows="3" cols="40"><?= htmlspecialchars($participant['comment']) ?></textarea><br><br>

    <table border="1">
      <tr>
        <th>日付</th>
        <th>○</th>
        <th>△</th>
        <th>×</th>
      </tr>
      <?php foreach ($dates as $date): 
        $selected = $attendanceMap[$date['date_id']] ?? -1;
      ?>
        <tr>
          <td><?= htmlspecialchars($date['date']) ?></td>
          <td><input type="radio" name="attendance[<?= $date['date_id'] ?>]" value="0" <?= $selected === 0 ? 'checked' : '' ?>></td>
          <td><input type="radio" name="attendance[<?= $date['date_id'] ?>]" value="1" <?= $selected === 1 ? 'checked' : '' ?>></td>
          <td><input type="radio" name="attendance[<?= $date['date_id'] ?>]" value="2" <?= $selected === 2 ? 'checked' : '' ?>></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <br>
    <input type="submit" name="action" value="出欠情報を更新">
  </form>
</body>
</html>
