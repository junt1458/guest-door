<?php
    include_once __DIR__ . '/mysql.php';
    session_start();
    $sql = new SQL_Proc();
    if(!$sql->isLogin()) {
        header("Location: ./login.php");
        exit;
    }

    if($sql->getLevel() == 0) {
        header("Location: ./");
        exit;
    } else if($sql->getLevel() == 1 || $sql->getLevel() == 3) {
        $_GET['id'] = $_SESSION['user_id'];
    }

    if(empty($_GET['id'])) {
        $_GET['id'] = $_SESSION['user_id'];
    }

    $failed = false;
    $message = "";
    if(!empty($_POST['key_name']) && !empty($_POST['user_pass']) && !empty($_POST['key_type'])) {
        // TODO: キー登録作業の実装
    }
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Guest Door - Add Key</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css?a">
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="./">Guest Door</a>
    </nav>

    <main role="main" class="container">

        <form class="form-signin" method="post" action="./kadd.php?id=<?php echo $_GET['id']; ?>">
            <?php if($failed) : ?>
            <div class="alert alert-danger" role="alert"><?php echo $message; ?></div>
            <?php endif; ?>
            <h1 class="h3 mb-3 font-weight-normal">新規キー登録</h1>
            <label for="inputId" class="sr-only">キー名</label>
            <input type="text" id="inputId" name="key_name" a class="form-control" placeholder="キー名" required autofocus>
            <h1 class="h4 mb-3 font-weight-normal">キータイプ</h1>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="key_type" id="key1" value="1" checked>
                <label class="form-check-label" for="permission1">
                    物理キー
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="key_type" id="key2" value="2">
                <label class="form-check-label" for="permission2">
                    仮想キー
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