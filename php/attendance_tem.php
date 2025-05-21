<?php
// 仮のデータ（本来はDBから取得）
$event_name = "ソフト開発部お花見";
$event_description = "ソフト開発部でのお花見を3月30日までにご記入下さい！";
$date_options = ["3/23(土)13:00", "3/24(日)13:00", "3/25(月)13:00", "3/26(火)13:00"];
$participants = [
    ["name" => "田中", "availability" => ["◯", "△", "×", "◯"]],
    ["name" => "山田", "availability" => ["◯", "◯", "×", "△"]],
    ["name" => "佐藤", "availability" => ["△", "◯", "◯", "◯"]],
];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>出欠入力 - 調整くん</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 40px;
            font-family: "Helvetica Neue", sans-serif;
        }
        table th, table td {
            text-align: center;
        }
        .form-section {
            margin-top: 40px;
        }
        .form-radio label {
            margin-right: 10px;
        }
        .submit-btn {
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">調整くん</h1>
    <hr>

    <div class="mb-4">
        <h5>ソフト開発部お花見</h5>
        <p><?= htmlspecialchars($event_description) ?></p>
    </div>

    <h6>日にち候補</h6>
    <p class="text-muted">※ 各自の出欠を確認するには名前のリンクをクリックして下さい。</p>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>名前</th>
                <?php foreach ($date_options as $date): ?>
                    <th><?= htmlspecialchars($date) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($participants as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p["name"]) ?></td>
                    <?php foreach ($p["availability"] as $a): ?>
                        <td><?= htmlspecialchars($a) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="form-section">
        <h6>出欠を入力する</h6>
        <form method="POST" action="submit_availability.php">
            <div class="mb-3">
                <label class="form-label">名前（変更できません）</label>
                <input type="text" class="form-control" name="name" value="佐藤" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">日にち選択</label>
                <?php foreach ($date_options as $i => $date): ?>
                    <div class="mb-2">
                        <strong><?= htmlspecialchars($date) ?></strong><br>
                        <div class="form-radio">
                            <label><input type="radio" name="availability[<?= $i ?>]" value="◯" required> ◯</label>
                            <label><input type="radio" name="availability[<?= $i ?>]" value="△"> △</label>
                            <label><input type="radio" name="availability[<?= $i ?>]" value="×"> ×</label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">コメント</label>
                <input type="text" class="form-control" name="comment" placeholder="※ 連絡等があれば">
            </div>

            <button type="submit" class="btn btn-primary submit-btn">入力する</button>
        </form>
    </div>
</div>
</body>
</html>
