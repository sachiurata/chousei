<!doctype html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>調整くん</title>
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
    </style>
  </head>
  <body>
    <div class="container">
      <div class="row mb-4">
        <h1 class="col-12 text-start border-bottom pb-2 mb-4">調整くん</h1>
      </div>

      <h2 class="mb-2 border-bottom" style="font-weight:bold;">
        <?= htmlspecialchars($eventData['event']['event_name']) ?>
      </h2>
      
      <h2 class="mb-2">イベント詳細説明</h2>
      <small class="mb-3"><?= nl2br(htmlspecialchars($eventData['event']['event_description'])) ?></small><br><br>

      <h2 class="mb-2">日にち候補</h2>
      <small>※各自の出欠状況を変更するには名前のリンクをクリックしてください。</small><br><br>

      <table class="event-table">
        <tr>
          <th>日程</th>
          <th>○</th>
          <th>△</th>
          <th>×</th>
          <?php foreach($eventData['participants'] as $participant): ?>
            <th>
            <!-- 名前をリンクに変更 -->
              <a href="index.php?action=edit_attendance&event_id=<?= $event_id ?>&user_id=<?= $participant['participant_id'] ?>">
                <?= htmlspecialchars($participant['participant_name']) ?>
              </a>
            </th>
          <?php endforeach; ?>
        </tr>

        <?php foreach($eventData['dates'] as $date): ?>
          <tr>
            <td><?= htmlspecialchars($date['date']) ?></td>
            <td>
              <?= count(array_filter($eventData['attendances'], fn($a) => $a['date_id'] === $date['date_id'] && $a['attendance'] === '1')) ?>人
            </td>
            <td>
              <?= count(array_filter($eventData['attendances'], fn($a) => $a['date_id'] === $date['date_id'] && $a['attendance'] === '2')) ?>人
            </td>
            <td>
              <?= count(array_filter($eventData['attendances'], fn($a) => $a['date_id'] === $date['date_id'] && $a['attendance'] === '0')) ?>人
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

        <div class="row mt-4">
          <div class="col-12 text-center">
            <a class="btn btn-outline-dark btn-lg px-5"
               href="index.php?action=attendance&event_id=<?= urlencode($eventData['event']['event_id']) ?>">
              出欠を入力する
            </a>
          </div>
          
        </div>
    </div>
  </body>
</html>
