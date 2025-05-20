<!-- ⑨イベント更新・削除画面 -->
<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once("../models/EventModel.php");

$eventId = $_GET['event_id'] ?? '';
if (!$eventId) die("イベントIDが指定されていません。");

$event = get_event($eventId);
$dates = get_event_dates($eventId);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>イベント編集</title>
</head>
<body>
  <h1>イベント編集</h1>

  <form action="../controllers/EventController.php" method="post">
    <input type="hidden" name="event_id" value="<?= $eventId ?>">

    <label>イベント名：</label><br>
    <input type="text" name="event_name" value="<?= htmlspecialchars($event['event_name']) ?>" required><br><br>

    <label>説明文：</label><br>
    <textarea name="description" rows="4" cols="50"><?= htmlspecialchars($event['event_description']) ?></textarea><br><br>

    <h3>削除する候補日：</h3>
    <?php foreach ($dates as $date): ?>
      <input type="checkbox" name="delete_dates[]" value="<?= $date['date_id'] ?>">
      <?= htmlspecialchars($date['date']) ?><br>
    <?php endforeach; ?>

    <h3>新たに追加する候補日（1行ずつ）：</h3>
    <textarea name="new_dates" rows="4" cols="50"></textarea><br>

    <input type="submit" name="action" value="イベント更新"><br><br>
    <input type="submit" name="action" value="イベント削除" onclick="return confirm('本当に削除しますか？');">
  </form>
</body>
</html>
