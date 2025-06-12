<!doctype html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>調整くん</title>
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <style>
      .error-message { color: red; margin-top: 10px; }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="row mb-4">
        <h1 class="col-12 text-start border-bottom pb-2 mb-4">調整くん</h1>
      </div>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $err): ?>
            <div><?= htmlspecialchars($err) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form action="index.php?action=create" method="post">
        <div class="row">
          <div class="col-md-4">
            <!-- 1カラム目: イベント名・メモ -->
            <h2>Step1) イベント名</h2>
            <small>※忘年会、打ち合わせなど</small>
            <input type="text" class="form-control my-2" name="event" id="event" maxlength="100" required>
            <h2>メモ（任意）</h2>
            <small>※飲み会の日時調整しましょう！</small>
            <textarea class="form-control my-2" name="description" id="description" maxlength="300" rows="6"></textarea>
          </div>
          <div class="col-md-4">
            <!-- 2カラム目: 日程候補 -->
            <h2>Step2) 日程候補</h2>
            <small>※候補日程/日時を入力してください</small><br>
            <small>　候補の区切りは改行で判断されます。</small><br>
            <small>　</small><br>
            <small>例：</small><br>
            <small>　2025/05/17</small><br>
            <small>　2025/05/18</small><br>
            <small>　2025/05/19</small><br>
            <textarea class="form-control my-2" name="candidates" id="candidates" rows="8"></textarea>
          </div>
          <div class="col-md-4">
            <!-- 3カラム目: カレンダー -->
            <h2>カレンダー</h2>
            <div id="datepicker"></div>
          </div>
          <div class="row mt-4">
            <div class="col-12 text-center">
              <button id="create-event" type="submit" class="btn btn-outline-dark btn-lg px-5">出欠表をつくる</button>
              <div id="errMsg" class="error-message"></div>
            </div>          
          </div>
        </div>
      </form>
    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script>
    <!-- jQuery Core & UI -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css" crossorigin="anonymous">
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js" integrity="sha256-sw0iNNXmOJbQhYFuC9OF2kOlD5KQKe1y5lfBn4C9Sjg=" crossorigin="anonymous"></script>

    <script>
      $( function() {
        $( "#datepicker" ).datepicker( {
          firstDay: 1,
          minDate: 0,
          // maxDate: "+2m",
          dateFormat: "yyyy/mm/dd",
          onSelect: function(dateText, inst) {
          // YYYY/MM/DD形式
          const date = $(this).datepicker('getDate');
          const yyyy = date.getFullYear();
          const mm = ('0' + (date.getMonth() + 1)).slice(-2);
          const dd = ('0' + date.getDate()).slice(-2);
          const line = `${yyyy}/${mm}/${dd}`;
          const area = document.getElementById("candidates");
          let lines = area.value.split('\n').filter(l => l.trim().length > 0);
          if (lines.length >= 20) return; // 最大20行
          area.value += (area.value && !area.value.endsWith('\n') ? "\n" : "") + line;
          }
        });
      });

        // バリデーションとイベント作成
        document.getElementById("create-event").onclick = function(ev) {
        ev.preventDefault();
        const errMsg = document.getElementById("errMsg");
        errMsg.textContent = "";

        // 1. イベント名
        // const eventName = document.getElementById("event").value;
        // if (eventName.length < 1) {
        //   errMsg.textContent = "イベント名は必須です。";
        // return;
        // }
        // if (eventName.length > 100) {
        //   errMsg.textContent = "イベント名は100文字以内で入力してください。";
        // return;
        // }

        // 2. 説明文（任意・300文字まで）
        const description = document.getElementById("description").value;
        if (description.length > 300) {
          errMsg.textContent = "説明文は300文字以内で入力してください。";
        return;
        }

         // 3. 日程候補
        const rawCandidates = document.getElementById("candidates").value;
        let lines = rawCandidates.split('\n')
        .map(l => l.replace(/^[\s\u3000]+|[\s\u3000]+$/g, ""))
        .filter(l => l.length > 0)
        .map(l => l.length > 50 ? l.slice(0, 50) : l);
        if (lines.length < 1) {
          errMsg.textContent = "日程候補を1つ以上入力してください。";
        return;
        }
        if (lines.length > 20) {
          errMsg.textContent = "日程候補は最大20件までです。";
        return;
        }

        // バリデーションOKならフォーム送信
        ev.target.form.submit();
      };

    </script>
  </body>
</html>