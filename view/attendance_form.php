<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>調整くん 出欠入力</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .event-table th, .event-table td {
      border: 1px solid #222;
      padding: 0.3em 0.7em;
      text-align: center;
    }
    .event-table {
      width: 100%;
      margin-bottom: 1em;
      border-collapse: collapse;
    }
    .big-btn {
      font-size: 1.5rem;
      padding: 0.5em 2.5em;
      border:2px solid #222;
      background:#fff;
      border-radius:8px;
      margin: 0 auto;
      display:block;
    }
    .circle-btn {
      border-radius: 50%;
      width: 2em;
      height: 2em;
      border: 1px solid #222;
      margin: 0 0.2em;
      font-size: 1.2em;
      text-align: center;
      line-height: 2em;
      background: #f8f9fa;
      cursor: pointer;
    }
    .circle-btn.selected {
      background: #0d6efd;
      color: #fff;
      border-color: #0d6efd;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row mb-4">
      <h1 class="col-12 text-start border-bottom pb-2 mb-4">調整くん</h1>
    </div>
    <h2 class="mb-2 border-bottom" style="font-weight:bold;"><?= htmlspecialchars($eventData['event']['event_name']) ?></h2>
    <h2 class="mb-2">イベント詳細説明</h2>
    <small class="mb-3"><?= nl2br(htmlspecialchars($eventData['event']['event_description'])) ?></small><br><br>
    <h2 class="mb-2">日にち候補</h2>
    <small>※各自の出欠状況を変更するには名前のリンクをクリックしてください。</small><br><br>
    <!-- ▼▼▼ ここから出欠表 ▼▼▼ -->
    <table class="event-table">
      <tr>
        <th>日程</th>
        <th>○</th>
        <th>△</th>
        <th>×</th>
        <?php foreach($eventData['participants'] as $participant): ?>
          <th><?= htmlspecialchars($participant['participant_name']) ?></th>
        <?php endforeach; ?>
      </tr>
      <?php foreach($eventData['dates'] as $date): ?>
        <tr>
          <td><?= htmlspecialchars($date['date']) ?></td>
          <td>
            <?= count(array_filter($eventData['attendances'], fn($a) => $a['date_id'] == $date['date_id'] && $a['attendance'] === '1')) ?>人
          </td>
          <td>
            <?= count(array_filter($eventData['attendances'], fn($a) => $a['date_id'] == $date['date_id'] && $a['attendance'] === '2')) ?>人
          </td>
          <td>
            <?= count(array_filter($eventData['attendances'], fn($a) => $a['date_id'] == $date['date_id'] && $a['attendance'] === '0')) ?>人
          </td>
          <?php foreach($eventData['participants'] as $participant): ?>
            <td>
              <?php
                $att = '';
                foreach($eventData['attendances'] as $a) {
                  if ($a['date_id'] == $date['date_id'] && $a['participant_id'] == $participant['participant_id']) {
                    if ($a['attendance'] === '1') $att = '○';
                    elseif ($a['attendance'] === '2') $att = '△';
                    elseif ($a['attendance'] === '0') $att = '×';
                  }
                }
                echo $att;
              ?>
            </td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
      <tr>
        <td>コメント</td>
        <td colspan="3"></td>
        <?php foreach($eventData['participants'] as $participant): ?>
          <td><?= htmlspecialchars($participant['comment']) ?></td>
        <?php endforeach; ?>
      </tr>
    </table>
    <!-- ▲▲▲ ここまで出欠表 ▲▲▲ -->

    <h2 class="mb-2">出欠を入力する</h2>
    <!-- ▼ここからフォーム（既存のまま）▼ -->
    <form action="index.php?action=attendance_submit&event_id=<?= urlencode($_GET['event_id']) ?>" method="post">
      <div class="mb-3">
        <label for="user_name" class="form-label">名前 <small>※空文字や「管理者」は使用できません。</small></label>
        <input type="text" class="form-control" name="user_name" id="user_name" value="<?= htmlspecialchars($_POST['user_name'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">日にち候補</label>
        <?php foreach($eventData['dates'] as $date): ?>
          <div class="mb-2">
            <span><?= htmlspecialchars($date['date']) ?></span>
            <input type="hidden" name="dates[]" value="<?= htmlspecialchars($date['date']) ?>">
            <button type="button" class="circle-btn" data-value="○">○</button>
            <button type="button" class="circle-btn" data-value="△">△</button>
            <button type="button" class="circle-btn" data-value="×">×</button>
            <input type="hidden" name="attendance[]" value="">
          </div>
        <?php endforeach; ?>
      </div>
      <div class="mb-3">
        <label for="comment" class="form-label">コメント</label>
        <input type="text" class="form-control" name="comment" id="comment">
      </div>
      <div class="mb-3 text-center">
        <button type="submit" class="btn btn-outline-dark btn-lg px-5">入力する</button>
      </div>
      <?php if (!empty($errors)): ?>
        <div class="text-danger text-center mb-3">
          <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </form>
    <!-- ▲ここまでフォーム▲ -->
  </div>
  <script>
    // 出欠ボタンの選択状態を管理
    document.querySelectorAll('form .mb-2').forEach(function(row, idx) {
      const buttons = row.querySelectorAll('.circle-btn');
      const hidden = row.querySelector('input[name="attendance[]"]');
      buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          buttons.forEach(b => b.classList.remove('selected'));
          btn.classList.add('selected');
          hidden.value = btn.dataset.value;
        });
      });
    });
  </script>
</body>
</html>