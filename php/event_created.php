<?php
$event_hash = $_GET['h'] ?? '';
$url = "http://localhost:8080/event_view.php?h=" . urlencode($event_hash);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>イベント作成完了 - 調整くん</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 40px;
            font-family: "Helvetica Neue", sans-serif;
        }
        .title {
            font-size: 2rem;
            font-weight: bold;
        }
        .url-box {
            font-family: monospace;
        }
        .btn-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="mb-4">調整くん</h1>
    <hr>
    <h4>イベント新規作成</h4>
    <p>
        イベントが作成されました。以下のURLをメール等で使って皆に知らせてあげよう。<br>
        以降、このURLページにて各自の出欠情報を入力してもらいます。
    </p>

    <input type="text" class="form-control url-box mb-4" readonly value="<?= htmlspecialchars($url) ?>">

    <div class="btn-wrapper">
        <a href="<?= htmlspecialchars($url) ?>" class="btn btn-primary btn-lg">イベントページを表示</a>
    </div>
</div>

</body>
</html>
