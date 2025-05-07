<?php   
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chousei";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1.1 新規イベントの作成
function create_event($event_name, $event_description) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO events (event_name, event_description) VALUES (?, ?)");
    if ($stmt === false) {
        return 1;
    }

    $stmt->bind_param("ss", $event_name, $event_description);

    if ($stmt->execute() === false) {
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}

// 1.2 イベントの取得
function get_event($event_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    if ($stmt === false) {
        return 1;
    }

    $stmt->bind_param("i", $event_id);
    
    if ($stmt->execute() === false) {
        $stmt->close();
        return 1;
    } else {
        $stmt->bind_result($event_id, $event_name, $event_description);
        $stmt->fetch();
        $stmt->close();
        return [
            "event_id" => $event_id,
            "event_name" => $event_name,        
            "event_description" => $event_description
        ];
    }
}

// 2.1 新規候補日の作成
function create_date($event_id, $date) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO dates (event_id, date) VALUES (?, ?)");
    if ($stmt === false) {
        return 1;
    }

    $stmt->bind_param("is", $event_id, $date);

    if ($stmt->execute() === false) {
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}

// 2.2 候補日の取得(複数)
function get_dates($event_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT date_id FROM dates WHERE event_id = ?");
    if ($stmt === false) {
        return 1;
    }

    $stmt->bind_param("i", $event_id);
    
    if ($stmt->execute() === false) {
        $stmt->close();
        return 1;
    } else {
        $stmt->bind_result($date_id);
        $date_ids = [];

        while ($stmt->fetch()) {
            $date_ids[] = $date_id;
        }
        
        $stmt->close();
        return $date_ids;
    }
}

// 2.3 候補日の取得
function get_date($date_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT date FROM dates WHERE date_id = ?");
    if ($stmt === false) {
        return 1;
    }

    $stmt->bind_param("i", $date_id);
    
    if ($stmt->execute() === false) {
        $stmt->close();
        return 1;
    } else {
        $stmt->bind_result($date);
        $stmt->fetch();
        $stmt->close();
        return $date;
    }
}

// 3.1 新規参加情報の作成
function create_attendance($date_id, $participant_id, $attendance) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO attendances (date_id, participant_id, attendance) VALUES (?, ?, ?)");
    if ($stmt === false) {
        return 1;
    }

    $stmt->bind_param("iis", $date_id, $participant_id, $attendance);

    if ($stmt->execute() === false) {
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}

// 4.1 新規参加者の作成
function create_participant($participant_name, $comment) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO participants (participant_name, comment) VALUES (?, ?)");
    if ($stmt === false) {
        return 1;
    }       

    $stmt->bind_param("ss", $participant_name, $comment); 

    if ($stmt->execute() === false) {
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}
?>
