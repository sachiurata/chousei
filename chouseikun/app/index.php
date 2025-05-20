<?php
// エラー表示
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>調整くん - イベント作成</title>
  <link rel="stylesheet" href="static/css/style.css">
</head>
<body>
  <h1>調整くん - イベント作成</h1>

  <form action="controllers/EventController.php" method="post">
    <label>イベント名（必須）：</label><br>
    <input type="text" name="event_name" required maxlength="100"><br><br>

    <label>説明文（任意）：</label><br>
    <textarea name="description" rows="4" cols="50" maxlength="300"></textarea><br><br>

    <label>候補日（1行ずつ）：</label><br>
    <textarea name="dates" rows="6" cols="50" required></textarea><br><br>

    <input type="submit" value="イベント作成">
  </form>
</body>
</html>
