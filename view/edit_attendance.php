<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>調整くん 出欠編集</title>
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
      display: inline-block;
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

    <h2 class="mb-2 border-bottom"><?= htmlspecialchars($event['event_name']) ?></h2>
    <p><?= nl2br(htmlspecialchars($event['event_description'])) ?></p>

    <!-- 出欠情報一覧表 -->
    <h4 class="mt-4">出欠状況</h4>
    <table class="event-table">
      <tr>
        <th>日程</th>
        <th>○</th>
        <th>△</th>
        <th>×</th>
        <?php
        $participant_ids = get_participant_ids($event['event_id']);
        $participants = [];
        foreach ($participant_ids as $pid) {
          $p = get_participant($pid);
          if ($p) $participants[] = $p;
        }
        foreach ($participants as $p):
        ?>
          <th>
          <!-- 自分の名前は再び選択できません -->
            <?php if ($p['participant_id'] == $participant['participant_id']): ?>
              <?= htmlspecialchars($p['participant_name']) ?>
            <?php else: ?>
              <a href="index.php?action=edit_attendance&event_id=<?= $event['event_id'] ?>&user_id=<?= $p['participant_id'] ?>">
                <?= htmlspecialchars($p['participant_name']) ?>
              </a>
            <?php endif; ?>
          </th>
        <?php endforeach; ?>
      </tr>

      <?php
      $date_ids = get_date_ids($event['event_id']);
      foreach ($date_ids as $date_id):
        $counts = ['1' => 0, '2' => 0, '0' => 0];
        foreach ($participants as $p) {
          $att = get_attendance($date_id, $p['participant_id']);
          if ($att) $counts[$att['attendance']]++;
        }
      ?>
        <tr>
          <td><?= htmlspecialchars(get_date($date_id)) ?></td>
          <td><?= $counts['1'] ?></td>
          <td><?= $counts['2'] ?></td>
          <td><?= $counts['0'] ?></td>
          <?php foreach ($participants as $p): ?>
            <td>
              <?php
                $att = get_attendance($date_id, $p['participant_id']);
                echo $att ? ['×','○','△'][$att['attendance']] : '';
              ?>
            </td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </table>

    <!-- 編集フォーム -->
    <h4 class="mt-4">出欠情報を編集</h4>
    <form method="POST" action="index.php?action=update_attendance">
      <input type="hidden" name="participant_id" value="<?= htmlspecialchars($participant['participant_id']) ?>">
      <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['event_id']) ?>">

      <div class="mb-3">
        <label class="form-label">名前</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($participant['participant_name']) ?>" required>
      </div>

      <div class="mb-4">
        <label class="form-label">日にち候補</label>
        <?php foreach ($participant['availability'] as $date_id => $selected): ?>
          <div class="mb-2 d-flex align-items-center">
            <div style="min-width: 130px; font-weight: 500;">
              <?= htmlspecialchars(get_date($date_id)) ?>
            </div>
            <!-- 値を取得、対応するボタンをハイライト表示 -->
            <?php foreach (['1' => '○', '2' => '△', '0' => '×'] as $val => $label): ?>
              <!-- ↓　===はダメ、型が違う -->
              <label class="circle-btn <?= ($selected == $val) ? 'selected' : '' ?>">
                <input type="radio" name="availability[<?= $date_id ?>]" value="<?= $val ?>" class="d-none"
                  onchange="updateButtonStyle(this)" <?= ($selected === $val) ? 'checked' : '' ?>>
                <?= $label ?>
              </label>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="mb-3">
        <label class="form-label">コメント</label>
        <textarea name="comment" class="form-control" rows="2"><?= htmlspecialchars($participant['comment']) ?></textarea>
      </div>

      <button type="submit" class="big-btn">更新する</button>
</form>

  </div>

  <!-- ボタンをハイライト表示 -->
  <script>
    function updateButtonStyle(radio) {
      const group = radio.closest('div');
      group.querySelectorAll('.circle-btn').forEach(btn => btn.classList.remove('selected'));
      radio.parentElement.classList.add('selected');
    }
  </script>

</body>
</html>
