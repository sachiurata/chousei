<?php
// 例外モードを有効化
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "chousei";

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
        $stmt->bind_result($date_id);
        $date_ids = [];
        while ($stmt->fetch()) {
            $date_ids[] = $date_id;
        }
        $stmt->close();
        return $date_ids;
    }

    // 3.1 新規参加情報の作成
    function create_attendance($date_id, $participant_id, $attendance)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO attendances (date_id, participant_id, attendance) VALUES (?, ?, ?)");
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
        $stmt->bind_result($attendance_id, $date_id, $partiripant_id, $attendance);
        $stmt->fetch();
        $stmt->close();
        return [
            "attendance_id" => $attendance_id,
            "date_id" => $date_id,
            "participant_id" => $participant_id,
            "attendance" => $attendance
        ];
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
        $stmt->close();
        return 0;
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
        $stmt->bind_result($participant_id);
        $participant_ids = [];
        while ($stmt->fetch()) {
            $participant_ids[] = $participant_id;
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
} catch (mysqli_sql_exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>