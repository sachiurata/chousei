<!doctype html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>調整くん</title>
    <style>
      .error-message { color: red; margin-top: 10px; }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="row mb-4">
        <h1 class="col-12 text-start border-bottom pb-2 mb-4">調整くん</h1>
      </div>
      <h2 class="mb-3 border-bottom">イベント新規作成</h2>
      <div class="mb-3">
        <small>イベントが作成されました。以下のURLをメール等を使って皆に知らせてあげよう。</small><br>
        <small>以降、このURLページにて各自の出欠情報を入力してもらいます。</small><br>
      </div>
      <input type="text" id="eventUrlInput" class="event-url-input form-control" readonly value="<?= htmlspecialchars($event_url ?? '#') ?>">
      <div class="row mt-4">
        <div class="col-12 text-center">
          <a id="eventPageBtn" class="btn btn-outline-dark btn-lg px-5" href="<?= htmlspecialchars($event_url ?? '#') ?>">イベントページを表示</a>
        </div>
      </div>
    </div>
  </body>
</html>