<?php
    include_once __DIR__ . '/mysql.php';
    session_start();
    if(empty($_SESSION['user_id']) || empty($_SESSION['user_pass'])) {
        header("Location: ./login.php");
        exit;
    }

    $sql = new SQL_Proc();

    if(!$sql->isLogin()){
        unset($_SESSION['user_id']);
        unset($_SESSION['user_pass']);
        header("Location: ./login.php");
        exit;
    }

    $failed = false;
    $succeeded = false;
    $message = "";
    if(!empty($_POST['user_now']) && !empty($_POST['user_pass']) && !empty($_POST['user_pass2'])) {
        if($_SESSION['user_pass'] !== $_POST['user_now']) { 
            $failed = true;
            $message = "現在のパスワードが異なります！";
        }
        if($_POST['user_pass'] !== $_POST['user_pass2']) {
            $failed = true;
            $message = "新しいパスワードが異なります！";
        }
        if(!$failed) {
            $sql->renewPass($_POST['user_pass']);
            $_SESSION['user_pass'] = $_POST['user_pass'];
            $succeeded = true;
            $message = "パスワードの変更に成功しました。";
        }
    }
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Guest Door - Setting</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css?a">
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="./">Guest Door</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
            aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item btn-nav">
                    <a class="nav-link" href="./">ダッシュボード</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">ログ</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown01">
                        <a class="dropdown-item" href="./log.php?filter=0">全てのログ</a>
                        <a class="dropdown-item" href="./log.php?filter=1">入退室ログ</a>
                        <a class="dropdown-item" href="./log.php?filter=2">キー管理ログ</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown02" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">管理</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown02">
                        <a class="dropdown-item" href="./user.php">ユーザー管理</a>
                        <a class="dropdown-item" href="./key.php">キー管理</a>
                    </div>
                </li>
                <li class="nav-item btn-nav active">
                    <a class="nav-link" href="./setting.php">設定 <span class="sr-only">(current)</span></a>
                </li>
            </ul>
            <ul class="navbar-nav navbar-right">
                <li class="nav-item">
                    <a class="nav-link" href="./logout.php">ログアウト</a>
                </li>
            </ul>
        </div>
    </nav>

    <main role="main" class="container">

    <form class="form-signin" method="post" action="./setting.php">
            <?php if($failed) : ?>
            <div class="alert alert-danger" role="alert"><?php echo $message; ?></div>
            <?php elseif($succeeded) : ?>
            <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
            <?php endif; ?>
            <h1 class="h3 mb-3 font-weight-normal">パスワードの変更</h1>
            <label for="inputNow" class="sr-only">現在のパスワード</label>
            <input type="password" id="inputNow" name="user_now" a class="form-control" placeholder="現在のパスワード" required autofocus>
            <label for="inputPassword" class="sr-only">新しいパスワード</label>
            <input type="password" id="inputPassword" name="user_pass" class="form-control" placeholder="新しいパスワード" required>
            <label for="inputPassword2" class="sr-only">新しいパスワード(確認)</label>
            <input type="password" id="inputPassword2" name="user_pass2" class="form-control" placeholder="新しいパスワード(確認)" required>
            <button class="btn btn-lg btn-primary btn-block" type="submit">パスワードの変更</button>
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