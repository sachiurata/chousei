<?php
require_once __DIR__ . '/../config/db.php';

class Event {
    public function create($data) {
        global $conn;
        $event_name = trim($data['event'] ?? '');
        $event_description = trim($data['description'] ?? '');
        $candidates = trim($data['candidates'] ?? '');

        // バリデーション
        $errors = [];
        if (mb_strlen($event_name) < 1) $errors[] = "イベント名は必須です。";
        if (mb_strlen($event_name) > 100) $errors[] = "イベント名は100文字以内で入力してください。";
        if (mb_strlen($event_description) > 300) $errors[] = "説明文は300文字以内で入力してください。";
        $candidate_lines = array_filter(array_map('trim', explode("\n", $candidates)), fn($l)=>$l!=='');
        if (count($candidate_lines) < 1) $errors[] = "日程候補を1つ以上入力してください。";
        if (count($candidate_lines) > 20) $errors[] = "日程候補は最大20件までです。";

        if ($errors) {
            // コントローラーで処理できるようエラーを返す
            return ['errors' => $errors];
        }

        // イベント登録
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_description) VALUES (?, ?)");
        $stmt->bind_param("ss", $event_name, $event_description);
        $stmt->execute();
        $event_id = $stmt->insert_id;

        // 候補日登録
        $stmt_date = $conn->prepare("INSERT INTO dates (event_id, date) VALUES (?, ?)");
        foreach ($candidate_lines as $line) {
            $date = preg_replace('/[^\d\/]/', '', $line);
            $date = str_replace('/', '-', $date);
            if (strtotime($date)) {
                $date_sql = date('Y-m-d', strtotime($date));
                $stmt_date->bind_param("is", $event_id, $date_sql);
                $stmt_date->execute();
            }
        }

        // Cookie保存（30日）
        setcookie("event_creator_$event_id", "1", time() + 60*60*24*30, "/");
        return ['event_id' => $event_id];
    }

    public function find($event_id) {
        global $conn;
        // イベント情報
        $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $event = $stmt->get_result()->fetch_assoc();

        // 日程情報
        $stmt2 = $conn->prepare("SELECT * FROM dates WHERE event_id = ? ORDER BY date");
        $stmt2->bind_param("i", $event_id);
        $stmt2->execute();
        $dates = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        // 参加者情報
        $stmt3 = $conn->prepare("SELECT * FROM participants WHERE event_id = ?");
        $stmt3->bind_param("i", $event_id);
        $stmt3->execute();
        $participants = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);

        // 出欠情報
        $stmt4 = $conn->prepare("
            SELECT a.*, d.date, p.participant_name, p.comment
            FROM attendances a
            JOIN dates d ON a.date_id = d.date_id
            JOIN participants p ON a.participant_id = p.participant_id
            WHERE d.event_id = ?
        ");
        $stmt4->bind_param("i", $event_id);
        $stmt4->execute();
        $attendances = $stmt4->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'event' => $event,
            'dates' => $dates,
            'participants' => $participants,
            'attendances' => $attendances
        ];
    }

    public function saveAttendance($event_id, $data) {
        global $conn;
        $user_name = trim($data['user_name'] ?? '');
        $comment = trim($data['comment'] ?? '');
        $dates = $data['dates'] ?? [];
        $attendances = $data['attendance'] ?? [];

        // 1. 参加者を登録
        $stmt = $conn->prepare("INSERT INTO participants (event_id, participant_name, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $event_id, $user_name, $comment);
        $stmt->execute();
        $participant_id = $stmt->insert_id;

        // 2. 各日程の出欠を登録
        foreach ($dates as $i => $date_str) {
            // date_idを取得
            $stmt_date = $conn->prepare("SELECT date_id FROM dates WHERE event_id = ? AND date = ?");
            $stmt_date->bind_param("is", $event_id, $date_str);
            $stmt_date->execute();
            $result = $stmt_date->get_result()->fetch_assoc();
            if (!$result) continue;
            $date_id = $result['date_id'];

            // 出欠値をDB用に変換（○:1, △:2, ×:0）
            $att = $attendances[$i] ?? '';
            if ($att === '○') $att_val = '1';
            elseif ($att === '△') $att_val = '2';
            elseif ($att === '×') $att_val = '0';
            else $att_val = '';

            $stmt_att = $conn->prepare("INSERT INTO attendances (date_id, participant_id, attendance) VALUES (?, ?, ?)");
            $stmt_att->bind_param("iis", $date_id, $participant_id, $att_val);
            $stmt_att->execute();
        }
    }
}