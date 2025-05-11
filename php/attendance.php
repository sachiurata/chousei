<?php
require_once 'db.php';

// ハッシュ取得
$event_hash = $_GET['h'] ?? '';
if (!$event_hash) {
    echo "無効なアクセスです。";
    exit();
}

// イベント取得
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

// 出欠送信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['participant_name'];
    $comment = $_POST['comment'] ?? '';

    // 参加者登録
    $stmt = $conn->prepare("INSERT INTO participants (event_id, participant_name, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $event['event_id'], $name, $comment);
    $stmt->execute();
    $participant_id = $stmt->insert_id;
    $stmt->close();

    // 出欠登録
    $stmt = $conn->prepare("INSERT INTO attendances (date_id, participant_id, attendance) VALUES (?, ?, ?)");
    foreach ($dates as $d) {
        $status = $_POST['attendance'][$d['date_id']] ?? '';
        $stmt->bind_param("iis", $d['date_id'], $participant_id, $status);
        $stmt->execute();
    }
    $stmt->close();

    // ページ再読み込み（リロード対策）
    header("Location: event_view.php?h=" . urlencode($event_hash));
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['event_name']) ?> - 出欠登録 | 調整くん</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 40px; font-family: "Helvetica Neue", sans-serif; }
        .attendance-select { width: 100px; }
        .form-section { margin-top: 30px; }
    </style>
</head>
<body>

<div class="container">
    <h1>調整くん</h1>
    <h3><?= htmlspecialchars($event['event_name']) ?></h3>
    <p><?= nl2br(htmlspecialchars($event['event_description'])) ?></p>

    <hr>

    <form method="POST" action="">
        <div class="form-section">
            <h5>出欠を入力してください</h5>

            <div class="mb-3">
                <label class="form-label">名前</label>
                <input type="text" name="participant_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">コメント（任意）</label>
                <textarea name="comment" class="form-control" rows="2"></textarea>
            </div>

            <h6 class="mt-4">日程ごとの出欠</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>日程</th>
                        <th>出欠</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dates as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('Y年n月j日 (D)', strtotime($d['date']))) ?></td>
                        <td>
                            <select name="attendance[<?= $d['date_id'] ?>]" class="form-select attendance-select" required>
                                <option value="">--選択--</option>
                                <option value="1">◯（参加）</option>
                                <option value="2">△（調整中）</option>
                                <option value="0">×（不参加）</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">送信する</button>
            </div>
        </div>
    </form>
</div>

</body>
</html>
