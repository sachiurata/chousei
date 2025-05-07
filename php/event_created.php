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

    <input type="text" class="form-control url-box mb-4" readonly value="http://localhost:8080/event_view.php?h=c6bf2ffb317a4feba88982af5a7667f9">

    <div class="btn-wrapper">
        <a href="event_view.php?h=c6bf2ffb317a4feba88982af5a7667f9" class="btn btn-primary btn-lg">イベントページを表示</a>
    </div>
</div>

</body>
</html>
