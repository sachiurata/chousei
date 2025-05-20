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
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($event['event_name']) ?> - 出欠情報入力</title>
</head>

<pre><?php print_r($dates); ?></pre>

<body>
  <h1><?= htmlspecialchars($event['event_name']) ?> - 出欠情報入力</h1>

  <form action="../controllers/AttendanceController.php" method="post">
    <input type="hidden" name="event_id" value="<?= $eventId ?>">

    <label>お名前：</label><br>
    <input type="text" name="participant_name" required maxlength="100"><br><br>

    <label>コメント（任意）：</label><br>
    <textarea name="comment" rows="3" cols="40"></textarea><br><br>

    <table border="1">
      <tr>
        <th>日付</th>
        <th>○</th>
        <th>△</th>
        <th>×</th>
      </tr>
      <?php foreach ($dates as $date): ?>
        <tr>
          <td><?= htmlspecialchars($date['date']) ?></td>
          <td><input type="radio" name="attendance[<?= $date['date_id'] ?>]" value="0" required></td>
          <td><input type="radio" name="attendance[<?= $date['date_id'] ?>]" value="1"></td>
          <td><input type="radio" name="attendance[<?= $date['date_id'] ?>]" value="2"></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <br>
    <input type="submit" value="出欠情報を登録">
  </form>
</body>
</html>
