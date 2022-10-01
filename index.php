<?php

try {
    // DB接続
    $pdo = new PDO(
        // ホスト名、データベース名
        'mysql:host=host;dbname=db;charset=utf8;','user','pass');
        // ユーザー名

        // パスワード
        
        // レコード列名をキーとして取得させる
        // [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    

  
   $stmt = $pdo->prepare('SELECT COUNT(*) AS num FROM post');

    $stmt->execute();

    foreach ($stmt as $row) {
        $counter = $row['num'];
    }

    $c = json_encode($counter);

    // 条件指定したSQL文をセット
    // $stmt = $pdo->prepare('SELECT * FROM post WHERE id = :id');
    $stmt = $pdo->prepare('SELECT * FROM post');
    // $stmt = $pdo->prepare('SELECT text FROM post');

    // 「:id」に対して値「1」をセット
    // $stmt->bindValue(':id', $i);

    // SQL実行
    $stmt->execute();


    $userData = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { //結果を配列で取得
        $rtns[] = array(
            'id' => $row['id'],
            'name' => $row['text'],
        );
    }
    $json = json_encode($rtns);

    // 取得したデータを出力
    // foreach ($stmt as $row) {
    //     $rtns[] = array(
    //         'col1' => $row['text'],

    //     );
    // }
    // $row['text'];

    $stmt = $pdo->prepare('INSERT INTO post (text) VALUES(:text)');


    // パラメータ
    if(isset($_POST['commit'])){
        $commit = $_POST['commit'];
       
        
        // 値をセット
        $stmt->bindValue(':text', $commit);
    }

    // 二重投稿防止
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        header("Location:index.php");
    }

    if (!empty($commit)) {
        // SQL実行
        $stmt->execute();
    }
} catch (PDOException $e) {
    // エラー発生
    echo $e->getMessage();
} finally {
    // DBを閉じる
    $pdo = null;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
　　<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/yegor256/tacit@gh-pages/tacit-css.min.css"/>
    <link rel="stylesheet" href="style.css">
    <title>掲示板</title>
</head>

<body>
    <ul id="text" style="list-style: none;"></ul>
    <form action="index.php?" method="post">
        <input id="commit" type="text" type="hidden" name="commit" placeholder="">
        <button id="but" type="submit">送信</button>
    </form>
    <script>
        // const but = document.getElementById('but').addEventListener('click', function() {

        //     // alert('1')
        // });

        const array = <?php echo $json; ?>;
        const c = <?php echo $c; ?>;
        let i = 0
        // console.log(array[0].name);
        const post = document.getElementById('text');
        while (i < c) {
            // 新しいHTML要素を作成
            var new_element = document.createElement('li');
            new_element.textContent = `${array[i].name}`;

            // 指定した要素の中の末尾に挿入
            post.appendChild(new_element);
            i++;
        }

        const placeText = ['個人情報は投稿してはいけません','他人を誹謗、中傷してはいけません','公序良俗に反する発言はしないでください']

        let randomInput = Math.floor(Math.random() * 3)
        console.log(randomInput);

        const place = document.getElementById('commit');
        place.placeholder = placeText[randomInput];
    </script>
</body>
</html>