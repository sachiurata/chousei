<?php
require_once 'db_connect.php'; // DB接続設定
require_once 'functions.php';  // あなたの関数群をここに記述 or 読み込む

$event_name = $_POST['title'] ?? '';
$event_description = $_POST['description'] ?? '';
$dates_text = $_POST['dates'] ?? '';

if (create_event($event_name, $event_description) === 0) {
    // 直前に挿入されたイベントIDを取得（PDO想定）
    global $pdo;
    $event_id = $pdo->lastInsertId();

    // 候補日を1行ずつ登録
    $dates = explode("\n", $dates_text);
    foreach ($dates as $date) {
        $trimmed_date = trim($date);
        if ($trimmed_date !== '') {
            create_date($event_id, $trimmed_date);
        }
    }

    echo "イベントと候補日が登録されました！";
} else {
    echo "イベント登録に失敗しました。";
}
?>