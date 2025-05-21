<?php
require_once 'db.php';

// URLパラメータからハッシュ取得
$event_hash = $_GET['h'] ?? '';

// ハッシュが無ければエラー表示
if (!$event_hash) {
    echo "無効なアクセスです。";
    exit();
}

// イベント情報取得
$stmt = $conn->prepare("SELECT event_id, event_name, event_description FROM events WHERE event_hash = ?");
$stmt->bind_param("s", $event_hash);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    echo "イベントが見つかりませんでした。";
    exit();
}

// 日程取得
$stmt = $conn->prepare("SELECT date_id, date FROM dates WHERE event_id = ? ORDER BY date ASC");
$stmt->bind_param("i", $event['event_id']);
$stmt->execute();
$dates_result = $stmt->get_result();
$dates = $dates_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['event_name']) ?> - 調整くん</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 40px;
            font-family: "Helvetica Neue", sans-serif;
        }
        .date-list {
            margin-top: 20px;
        }
        .date-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="mb-4">調整くん</h1>
    <h3><?= htmlspecialchars($event['event_name']) ?></h3>
    <p><?= nl2br(htmlspecialchars($event['event_description'])) ?></p>

    <h5 class="mt-4">候補日程</h5>
    <div class="date-list">
        <?php foreach ($dates as $d): ?>
            <div class="date-item">
                <?= htmlspecialchars(date('Y年n月j日 (D)', strtotime($d['date']))) ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
