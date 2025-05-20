<?php
function db_connect() {
    $conn = new mysqli("db", "a_group", "kickickic", "chousei");
    if ($conn->connect_error) {
        die("接続失敗: " . $conn->connect_error);
    }
    return $conn;
}

function create_event($name, $desc, $dates) {
    $conn = db_connect();

    // 插入 events
    $stmt = $conn->prepare("INSERT INTO events (event_name, event_description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $desc);
    $stmt->execute();
    $eventId = $stmt->insert_id;
    $stmt->close();

    // 插入 dates（注意字段是 date，类型是 DATE，这里简化成字符串存入）
    $stmt2 = $conn->prepare("INSERT INTO dates (event_id, date) VALUES (?, ?)");

    foreach ($dates as $d) {
        $parsedDate = parse_date_string($d); // 自定义解析函数
        if ($parsedDate) {
            $stmt2->bind_param("is", $eventId, $parsedDate);
            $stmt2->execute();
        }
    }

    $stmt2->close();
    $conn->close();
    return $eventId;
}

// 将 MM/DD hh:mm 转换为 YYYY-MM-DD（仅保留日期部分）
function parse_date_string($input) {
    $input = trim($input);
    $input = preg_replace('/[　\s]+/', ' ', $input); // 把中文空格和多空格合并
    if (preg_match('/(\d{1,2})\/(\d{1,2})/', $input, $matches)) {
        $month = (int)$matches[1];
        $day = (int)$matches[2];
        $year = date("Y");

        // // 如果日期已过，自动加一年（可选）
        // $today = strtotime(date("Y-m-d"));
        // $date = strtotime("$year-$month-$day");
        // if ($date < $today) {
        //     $year++;
        // }

        return sprintf("%04d-%02d-%02d", $year, $month, $day);
    }
    return null;
}


function get_event($eventId) {
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $result;
}

function get_event_dates($eventId) {
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT * FROM dates WHERE event_id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $result;
}


function get_participants($eventId) {
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT * FROM participants WHERE event_id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $result;
}

function get_attendance_matrix($eventId) {
    $conn = db_connect();
    $sql = "SELECT d.date_id, p.participant_id, a.attendance
            FROM dates d
            JOIN attendances a ON d.date_id = a.date_id
            JOIN participants p ON a.participant_id = p.participant_id
            WHERE d.event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    $matrix = [];
    while ($row = $result->fetch_assoc()) {
        $matrix[$row['date_id']][$row['participant_id']] = ['○', '△', '×'][$row['attendance']];
    }

    $stmt->close();
    $conn->close();
    return $matrix;
}

function get_participant_info($participantId) {
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT * FROM participants WHERE participant_id = ?");
    $stmt->bind_param("i", $participantId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $result;
}

function get_participant_attendance($participantId) {
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT date_id, attendance FROM attendances WHERE participant_id = ?");
    $stmt->bind_param("i", $participantId);
    $stmt->execute();
    $result = $stmt->get_result();

    $map = [];
    while ($row = $result->fetch_assoc()) {
        $map[$row['date_id']] = (int)$row['attendance'];
    }

    $stmt->close();
    $conn->close();
    return $map;
}

