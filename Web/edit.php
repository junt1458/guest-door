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

    if($sql->getLevel() != 2 && $sql->getLevel() != 4) {
        header("Location: ./");
        exit;
    }

    if(empty($_GET['id'])) {
        header("Location: ./user.php");
        exit;
    }

    $ul = $sql->getUserLevel($_GET['id']);

    if($ul == -1) {
        header("Location: ./user.php");
        exit;
    }

    if(!empty($_POST['permission'])) {
        $new_perm = intval($_POST['permission']) - 1;
        if($new_perm < 0) {
            $new_perm = 0;
        }
        if($new_perm > 4) {
            $new_perm = 4;
        }
        $sql->updatePermission($_GET['id'], $new_perm);
        header("Location: ./user.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Guest Door - Edit Permission</title>

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
                <?php if($sql->getLevel() > 2) : ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">ログ</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown01">
                        <a class="dropdown-item" href="./log.php?filter=0">全てのログ</a>
                        <a class="dropdown-item" href="./log.php?filter=1">入退室ログ</a>
                        <a class="dropdown-item" href="./log.php?filter=2">キー管理ログ</a>
                    </div>
                </li>
                <?php endif; ?>
                <?php if($sql->getLevel() != 0) : ?>
                <li class="nav-item btn-nav">
                    <a class="nav-link" href="./user.php">管理</a>
                </li>
                <?php endif; ?>
                <li class="nav-item btn-nav">
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
        <h1>ユーザー: <?php echo $_GET['id']; ?>の権限編集</h1>
        <form method="POST" action="./edit.php?id=<?php echo $_GET['id']; ?>">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="permission" id="permission1" value="1"<?php if($ul == 0) echo " checked"; ?>>
                <label class="form-check-label" for="permission1">
                    レベル0 - キーの使用のみ
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="permission" id="permission2" value="2"<?php if($ul == 1) echo " checked"; ?>>
                <label class="form-check-label" for="permission2">
                    レベル1 - 自分のキーの管理が可能
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="permission" id="permission3" value="3"<?php if($ul == 2) echo " checked"; ?>>
                <label class="form-check-label" for="permission3">
                    レベル2 - ユーザー/キーの管理が可能
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="permission" id="permission4" value="4"<?php if($ul == 3) echo " checked"; ?>>
                <label class="form-check-label" for="permission4">
                    レベル3 - ログ閲覧/自分のキー管理が可能
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="permission" id="permission5" value="5"<?php if($ul == 4) echo " checked"; ?>>
                <label class="form-check-label" for="permission5">
                    レベル4 - ユーザー/キーの管理/ログ閲覧が可能 (フル権限)
                </label>
            </div>
            <button type="submit" class="btn btn-primary">更新</button>
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