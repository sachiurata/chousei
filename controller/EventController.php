<?php
require_once __DIR__ . '/../model/model.php';

class EventController {
    public function form() {
        require __DIR__ . '/../view/event_form.php';
    }

    //不正の期日かどうかを確認
    function is_valid_date($date_str) {
        $dt = DateTime::createFromFormat('Y-m-d', $date_str);
        return $dt && $dt->format('Y-m-d') === $date_str;
    }

    public function create() {
        global $conn;
    
        $event_name = trim($_POST['event'] ?? '');
        $event_description = trim($_POST['description'] ?? '');
        $candidates = trim($_POST['candidates'] ?? '');
    
        // バリデーション
        $errors = [];
        if (mb_strlen(trim($event_name)) < 1) {$errors[] = "イベント名は必須です。空白のみは無効です。";}
        if (mb_strlen($event_name) > 100) $errors[] = "イベント名は100文字以内で入力してください。";
        if (mb_strlen($event_description) > 300) $errors[] = "説明文は300文字以内で入力してください。";
    
        $candidate_lines = array_filter(array_map('trim', explode("\n", $candidates)), fn($l) => $l !== '');
        if (count($candidate_lines) < 1) $errors[] = "日程候補を1つ以上入力してください。";
        if (count($candidate_lines) > 20) $errors[] = "日程候補は最大20件までです。";
    
        if ($errors) {
            // view に渡してエラー表示を行う
            require __DIR__ . '/../view/event_form.php';
            return;
        }
    
        // イベント登録
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_description) VALUES (?, ?)");
        $stmt->bind_param("ss", $event_name, $event_description);
        $stmt->execute();
        $event_id = $stmt->insert_id;
    
        // 候補日登録
        $stmt_date = $conn->prepare("INSERT INTO dates (event_id, date) VALUES (?, ?)");
        foreach ($candidate_lines as $line) {

            $cleaned = preg_replace('/[^\d\/]/', '', $line); 
            $cleaned = str_replace('/', '-', $cleaned);
        
            $dt = DateTime::createFromFormat('Y-m-d', $cleaned);
            if ($dt && $dt->format('Y-m-d') === $cleaned) {
                $date_sql = $dt->format('Y-m-d');
                $stmt_date->bind_param("is", $event_id, $date_sql);
                $stmt_date->execute();
            } else {
                $errors[] = "不正な日付形式です: $line";
            }
        }

        if (!empty($errors)) {
            require __DIR__ . '/../view/event_form.php';
            return;
        }
        
        
        // Cookie 保存（作成者識別）
        setcookie("event_creator_$event_id", "1", time() + 60*60*24*30, "/");
    
        // URL 通知画面へ遷移
        header("Location: index.php?action=url&event_id=$event_id");
        exit;
    }
    

    public function url($event_id) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $path = "/index.php?action=detail&event_id=" . urlencode($event_id);
    
        $event_url = $protocol . $host . $path;
    
        require __DIR__ . '/../view/event_url.php';
    }
    

    public function detail($event_id) {
        $event = get_event($event_id);
        if (!$event) {
            echo "イベントが見つかりません。";
            exit;
        }

        $date_ids = get_date_ids($event_id);
        $dates = [];
        foreach ($date_ids as $id) {
            $dates[] = ['date_id' => $id, 'date' => get_date($id)];
        }

        $participant_ids = get_participant_ids($event_id);
        $participants = [];
        $attendances = [];

        foreach ($participant_ids as $pid) {
            $p = get_participant($pid);
            if (!$p) continue;
            $participants[] = $p;

            foreach ($date_ids as $did) {
                $a = get_attendance($did, $pid);
                if ($a) {
                    $attendances[] = [
                        'date_id' => $did,
                        'participant_id' => $pid,
                        'attendance' => strval($a['attendance'])
                    ];
                }
            }
        }

        $eventData = [
            'event' => $event,
            'dates' => $dates,
            'participants' => $participants,
            'attendances' => $attendances
        ];

        require __DIR__ . '/../view/event_detail.php';
    }

    public function attendance($event_id) {
        $event = get_event($event_id);
        if (!$event) {
            echo "イベントが見つかりません。";
            exit;
        }

        $date_ids = get_date_ids($event_id);
        $dates = [];
        foreach ($date_ids as $id) {
            $dates[] = ['date_id' => $id, 'date' => get_date($id)];
        }

        $participant_ids = get_participant_ids($event_id);
        $participants = [];
        $attendances = [];

        foreach ($participant_ids as $pid) {
            $p = get_participant($pid);
            if (!$p) continue;
            $participants[] = $p;

            foreach ($date_ids as $did) {
                $a = get_attendance($did, $pid);
                if ($a) {
                    $attendances[] = [
                        'date_id' => $did,
                        'participant_id' => $pid,
                        'attendance' => strval($a['attendance'])
                    ];
                }
            }
        }

        $eventData = [
            'event' => $event,
            'dates' => $dates,
            'participants' => $participants,
            'attendances' => $attendances
        ];

        require __DIR__ . '/../view/attendance_form.php';
    }

    public function attendance_submit($event_id) {
        $participant_name = trim($_POST['user_name'] ?? '');
        $comment = trim($_POST['comment'] ?? '');
        $errors = [];

        // バリデーション
        if (mb_strlen($participant_name) < 1 || $participant_name === '管理者') {
            $errors[] = "参加者名は必須です。";
        }
        if (mb_strlen($participant_name) > 20) {
            $errors[] = "参加者名は20文字以内で入力してください。";
        }
        if (mb_strlen($comment) > 100) {
            $errors[] = "コメントは100文字以内で入力してください。";
        }

        //エラーがある場合
        if (!empty($errors)) {
            $event = get_event($event_id);
            $date_ids = get_date_ids($event_id);
            $dates = [];
            foreach ($date_ids as $id) {
                $dates[] = ['date_id' => $id, 'date' => get_date($id)];
            }

            // 出欠選択の旧入力値
            $attendances = [];
            foreach ($date_ids as $index => $date_id) {
                $value = $_POST['attendance'][$index] ?? '';
                $attendances[] = [
                    'date_id' => $date_id,
                    'attendance' => $value
                ];
            }

            // ビューに渡す変数
            $eventData = [
                'event' => $event,
                'dates' => $dates,
                'participants' => [],
                'attendances' => $attendances
            ];

            $old_input = [
                'user_name' => $participant_name,
                'comment' => $comment,
                'attendance' => $_POST['attendance'] ?? []
            ];

            // エラー表示
            require __DIR__ . '/../view/attendance_form.php';
            return;
        }

        // 正常処理
        $participant_id = create_participant($event_id, $participant_name, $comment);
        $map = ['○' => '1', '△' => '2', '×' => '0'];

        $dates = get_date_ids($event_id);
        foreach ($dates as $index => $date_id) {
            $value = $_POST['attendance'][$index] ?? '';
            if (isset($map[$value])) {
                $att_val = (string)$map[$value];
                create_attendance($date_id, $participant_id, $att_val);
            }
        }

        header("Location: index.php?action=detail&event_id=" . urlencode($event_id));
        exit;
    }

    //出欠情報閲覧の際にデータをGET
    public function editAttendance($event_id, $user_id) {
        $event = get_event($event_id);
        $dates = get_date_ids($event_id);
        $participant = get_participant_data($event_id, $user_id); 

        require __DIR__ . '/../view/edit_attendance.php';
    }

    //出欠情報更新の際にデータをPOST
    public function updateAttendance($postData) {
        $participant_id = $postData['participant_id'];
        $name = $postData['name'];
        $comment = $postData['comment'];
        $availability = $_POST['availability'] ?? [];

        update_participant($participant_id, $name, $comment);
        update_availability($participant_id, $availability);

        header("Location: index.php?action=detail&event_id=" . $postData['event_id']);
        exit;
    }

    

}