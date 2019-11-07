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

    $user_list = $sql->getUserList();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Guest Door - User Management</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css?a">
</head>

<body class="block-ovr">
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
                <li class="nav-item btn-nav active">
                    <a class="nav-link" href="./user.php">管理</a>
                </li>
                <li class="nav-item btn-nav">
                    <a class="nav-link" href="./setting.php">設定</a>
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
        <div
            class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-3 border-bottom fit-space">
            <h1 class="h2">ユーザー/キー管理</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <a href="./add.php" class="btn btn-sm btn-outline-secondary">ユーザー作成</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ユーザー名</th>
                        <th>権限の管理</th>
                        <th>キーの管理</th>
                        <th>ユーザーの削除</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i = 0; $i < count($user_list); $i++) : ?>
                    <tr>
                        <td><?php echo ($i + 1); ?></td>
                        <td><?php echo $user_list[$i]; ?></td>
                        <td><a href="./edit.php?id=<?php $user_list[$i]; ?>" class="btn btn-sm btn-primary">編集</a></td>
                        <td><a href="./key.php?id=<?php $user_list[$i]; ?>" class="btn btn-sm btn-info">管理</a></td>
                        <td><a href="./del.php?id=<?php $user_list[$i]; ?>" class="btn btn-sm btn-danger">削除</a></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

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