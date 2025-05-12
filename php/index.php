<?php

require_once 'db.php';

$event_name = $_POST['event_name'] ?? '';
$event_description = $_POST['event_description'] ?? '';
$date_str = $_POST['date'] ?? ''; // ← name="date" に合わせて修正
$date_lines = array_filter(array_map('trim', explode("\n", $date_str)));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // トランザクション開始
        $conn->beginTransaction();

        // イベント挿入
        $event_hash = md5(uniqid($event_name, true));
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_description, event_hash) VALUES (:name, :desc, :hash)");
        $stmt->bindValue(':name', $event_name);
        $stmt->bindValue(':desc', $event_description);
        $stmt->bindValue(':hash', $event_hash);
        $stmt->execute();
        $event_id = $conn->lastInsertId();

        // 日程挿入
        $stmt = $conn->prepare("INSERT INTO dates (event_id, date) VALUES (:event_id, :date)");
        foreach ($date_lines as $line) {
            $date = date('Y-m-d', strtotime($line));
            $stmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
            $stmt->bindValue(':date', $date);
            $stmt->execute();
        }

        // コミット
        $conn->commit();

        // 完了ページにリダイレクト
        header("Location: complete.php?h=$event_hash");
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        die("データベースエラー: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>調整くん - 簡単スケジュール調整、出欠管理ツール</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 40px;
            font-family: "Helvetica Neue", sans-serif;
        }
        .example {
            font-size: 0.9em;
            color: gray;
        }
        .calendar-img {
            max-width: 100%;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .submit-btn {
            margin-top: 30px;
            font-size: 20px;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <h1 class="mb-4">調整くん</h1>
        <hr>
        <form method="POST" action="">
            <div class="row mb-4">
                <!-- 左側カラム -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Step1）イベント名</label>
                        <div class="example mb-1">※ 忘年会、打ち合わせ など</div>
                        <input type="text" class="form-control" name="event_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">メモ（任意）</label>
                        <div class="example mb-1">※ 飲み会の日程調整しましょう！</div>
                        <textarea class="form-control" name="event_description" rows="5"></textarea>
                    </div>
                </div>

                <!-- 右側カラム -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Step2）日程候補</label>
                        <div class="example mb-1">
                            ※ 候補日程／日時を入力してください<br>
                            候補の区切りは改行で判断されます。
                        </div>
                        <textarea class="form-control" name="date" rows="7" placeholder="例：&#10;8/7(月) 20:00〜&#10;8/8(火) 20:00〜&#10;8/9(水) 21:00〜" required></textarea>
                    </div>
                    <div class="example mb-2">
                        ↓ 日付をクリックすると日程に日時が入ります（※仮）
                    </div>
                    <img src="calendar_placeholder.png" alt="カレンダー" class="calendar-img">
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary submit-btn">出欠表をつくる</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>