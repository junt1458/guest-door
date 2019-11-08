<?php
    include_once __DIR__ . '/mysql.php';
    session_start();
    $sql = new SQL_Proc();
    if(!$sql->isLogin()) {
        header("Location: ./login.php");
        exit;
    }

    if($sql->getLevel() != 2 && $sql->getLevel() != 4) {
        header("Location: ./");
        exit;
    }

    $failed = false;
    $message = "";
    if(!empty($_POST['user_id']) && !empty($_POST['user_pass']) && !empty($_POST['permission'])) {
        $perm = intval($_POST['permission']) - 1;
        if($perm < 0) {
            $perm = 0;
        }
        if($perm > 4) {
            $perm = 4;
        }
        $res_id = $sql->addUser($_POST['user_id'], $_POST['user_pass'], $perm);
        if($res_id == 0) {
            $failed = true;
            $message = "ユーザーはすでに登録されています。";
        } else if($res_id == -1) {
            header("Location: ./user.php");
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Guest Door - Sign up</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css?a">
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="./">Guest Door</a>
    </nav>

    <main role="main" class="container">

        <form class="form-signin" method="post" action="./add.php">
            <?php if($failed) : ?>
            <div class="alert alert-danger" role="alert"><?php echo $message; ?></div>
            <?php endif; ?>
            <h1 class="h3 mb-3 font-weight-normal">新規ユーザー登録</h1>
            <label for="inputId" class="sr-only">ユーザーID</label>
            <input type="text" id="inputId" name="user_id" a class="form-control" placeholder="ユーザーID" required autofocus>
            <label for="inputPassword" class="sr-only">パスワード</label>
            <input type="password" id="inputPassword" name="user_pass" class="form-control" placeholder="パスワード" required>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="permission" id="permission1" value="1" checked>
                <label class="form-check-label" for="permission1">
                    レベル0 - キーの使用のみ
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="permission" id="permission2" value="2">
                <label class="form-check-label" for="permission2">
                    レベル1 - 自分のキーの管理が可能
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="permission" id="permission3" value="3">
                <label class="form-check-label" for="permission3">
                    レベル2 - ユーザー/キーの管理が可能
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="permission" id="permission4" value="4">
                <label class="form-check-label" for="permission4">
                    レベル3 - ログ閲覧/自分のキー管理が可能
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="permission" id="permission5" value="5">
                <label class="form-check-label" for="permission5">
                    レベル4 - ユーザー/キーの管理/ログ閲覧が可能 (フル権限)
                </label>
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit">登録</button>
        </form>

    </main>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>

</html>