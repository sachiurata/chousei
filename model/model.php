<?php
// 例外モードを有効化
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $servername = "db";
    $username = "user";
    $password = "password";
    $dbname = "chousei";//設定を統一
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 1.1 新規イベントの作成
    function create_event($event_name, $event_description)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_description) VALUES (?, ?)");
        $stmt->bind_param("ss", $event_name, $event_description);
        $stmt->execute();
        $event_id = $stmt->insert_id; // 追加したイベントのIDを取得
        $stmt->close();
        return $event_id;
    }

    // 1.2 イベントの取得
    function get_event($event_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $stmt->bind_result($event_id, $event_name, $event_description);
        $stmt->fetch();
        $stmt->close();
        return [
            "event_id" => $event_id,
            "event_name" => $event_name,
            "event_description" => $event_description
        ];
    }

    // 1.3 イベントの更新
    function update_event($event_id, $event_name, $event_description)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_description = ? WHERE event_id = ?");
        $stmt->bind_param("ssi", $event_name, $event_description, $event_id);
        $stmt->execute();
        $stmt->close();
        return 0;
    }

    // 1.4 イベントの削除
    function delete_event($event_id)
    {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $stmt->close();
        return 0;
    }

    // 2.1 新規候補日の作成
    function create_date($event_id, $date)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO dates (event_id, date) VALUES (?, ?)");
        $stmt->bind_param("is", $event_id, $date);
        $stmt->execute();
        $stmt->close();
        return 0;
    }

    // 2.2 候補日の取得
    function get_date($date_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT date FROM dates WHERE date_id = ?");
        $stmt->bind_param("i", $date_id);
        $stmt->execute();
        $stmt->bind_result($date);
        $stmt->fetch();
        $stmt->close();
        return $date;
    }

    // 2.3 候補日IDの取得(複数)
    function get_date_ids($event_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT date_id FROM dates WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $date_ids = [];
    
        while ($row = $result->fetch_assoc()) {
            $date_ids[] = $row['date_id'];
        }
    
        $stmt->close();
        return $date_ids;
    }
    

    // 3.1 新規参加情報の作成
    function create_attendance($date_id, $participant_id, $attendance)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO attendances (date_id, participant_id, attendance) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $date_id, $participant_id, $attendance);
        $stmt->execute();
        $stmt->close();
        return 0;
    }
    

    // 3.2 参加情報の取得
    function get_attendance($date_id, $participant_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM attendances WHERE date_id = ? AND participant_id = ?");
        $stmt->bind_param("ii", $date_id, $participant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
    
        return $row ?: null;
    }

    // 3.3 参加情報の更新
    function update_attendance($attendance_id, $attendance)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE attendances SET attendance = ? WHERE attendance_id = ?");
        $stmt->bind_param("si", $attendance, $attendance_id);
        $stmt->execute() === false;
        $stmt->close();
        return 0;
    }

    // 3.4 参加情報（〇：0 △：1 ×：2）の数の取得
    // ※途中
    function count_atttendance($date_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT COUNT(*) FROM attendances WHERE date_id = ? AND attendance = 2");
        $stmt->bind_param("i", $date_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    // 4.1 新規参加者の作成
    function create_participant($event_id, $participant_name, $comment)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO participants (event_id, participant_name, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $event_id, $participant_name, $comment);
        $stmt->execute();
        $participant_id = $stmt->insert_id;
        $stmt->close();
        return $participant_id;
    }
    

    // 4.2 参加者の取得
    function get_participant($participant_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM participants WHERE participant_id = ?");
        $stmt->bind_param("i", $participant_id);
        $stmt->execute();
        $stmt->bind_result($participant_id, $event_id, $participant_name, $comment);
        $stmt->fetch();
        $stmt->close();
        $participant = [
            "participant_id" => $participant_id,
            "event_id" => $event_id,
            "participant_name" => $participant_name,
            "comment" => $comment
        ];
        return $participant;
    }

    // 4.3 参加者IDの取得（複数）
    function get_participant_ids($event_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT participant_id FROM participants WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $participant_ids = [];
    
        while ($row = $result->fetch_assoc()) {
            $participant_ids[] = $row['participant_id'];
        }
    
        $stmt->close();
        return $participant_ids;
    }
    

    // 4.4 参加者の更新
    function update_participant($participant_id, $participant_name, $comment)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE participants SET participant_name = ?, comment = ? WHERE participant_id = ?");
        $stmt->bind_param("ssi", $participant_name, $comment, $participant_id);
        $stmt->execute();
        $stmt->close();
        return 0;

    }

    // 5.1 全ての参加者の情報を取得
    function get_participant_data($event_id, $participant_id) {
        $participant = get_participant($participant_id);
        $date_ids = get_date_ids($event_id);
    
        $availability = [];
        foreach ($date_ids as $date_id) {
            $att = get_attendance($date_id, $participant_id);
            $availability[$date_id] = $att ? $att['attendance'] : "";  // 存在しない場合NULLを初期値に
        }
    
        $participant['availability'] = $availability;
        return $participant;
    }
    

    // 5.2 出欠情報を一括で更新（存在しない場合は新規作成）
    function update_availability($participant_id, $availability_array) {
        global $conn;

        foreach ($availability_array as $date_id => $status) {
            // 既に出欠情報が存在するか確認
            $stmt = $conn->prepare("SELECT attendance_id FROM attendances WHERE date_id = ? AND participant_id = ?");
            $stmt->bind_param("ii", $date_id, $participant_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $existing = $result->fetch_assoc();
            $stmt->close();

            if ($existing) {
                // 既存の情報を更新
                update_attendance($existing['attendance_id'], $status);
            } else {
                // 出欠情報が存在しない場合は新規作成
                create_attendance($date_id, $participant_id, $status);
            }
    }
}


} catch (mysqli_sql_exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>