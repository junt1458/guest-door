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
    
    $key_list = $sql->getKeyList($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Guest Door - Dashboard</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css?t=<?php echo time(); ?>">
</head>

<body class="block-ovr ">
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="./">Guest Door</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
            aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active btn-nav">
                    <a class="nav-link" href="./">ダッシュボード <span class="sr-only">(current)</span></a>
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
        <div class="key_list">
            <?php for($i = 0; $i < count($key_list); $i++) : ?>
            <div class="key_detail">
                <h3><?php echo $key_list[$i]["name"]; ?></h3>
                <h2>状態: <?php echo $key_list[$i]["status"]; ?></h2>
                <?php if($key_list[$i]["active_until"] !== "2199-01-01 00:00:00") : ?>
                    <h2><?php echo $key_list[$i]["active_until"]; ?> まで</h2>
                <?php elseif($key_list[$i]["use_count"] !== "無制限") : ?>
                    <h2>残り <?php echo $key_list[$i]["use_count"]; ?>回</h2>
                <?php else : ?>
                    <h2>無制限キー</h2>
                <?php endif; ?>
                <h2>種類: <?php echo $key_list[$i]["type"]; ?></h2>

                <?php if($key_list[$i]["status"] === "使用可能") : ?>
                    <?php if($key_list[$i]["type"] === "仮想キー") : ?>
                    <a href="./use.php?key=<?php echo $key_list[$i]["name"]; ?>" class="btn btn-sm btn-primary use-b">入室</a>
                    <?php else : ?>
                    <a href="javascript:void(0);" class="btn btn-sm btn-primary disabled use-b">カードリーダーにかざしてください</a>
                    <?php endif; ?>
                    <a href="./stt.php?key=<?php echo $key_list[$i]["name"]; ?>" class="btn btn-sm btn-warning use-b">利用停止</a>
                <?php elseif($key_list[$i]["status"] === "使用中") :?>
                    <?php if($key_list[$i]["type"] === "仮想キー") : ?>
                    <a href="./use.php?key=<?php echo $key_list[$i]["name"]; ?>" class="btn btn-sm btn-primary use-b">退室</a>
                    <?php else : ?>
                    <a href="javascript:void(0);" class="btn btn-sm btn-primary disabled use-b">カードリーダーにかざしてください</a>
                    <?php endif; ?>
                    <a href="./stt.php?key=<?php echo $key_list[$i]["name"]; ?>" class="btn btn-sm btn-warning use-b">利用停止</a>
                <?php elseif($key_list[$i]["status"] === "停止中") : ?>
                    <a href="javascript:void(0);" class="btn btn-sm btn-primary disabled use-b">利用停止中です</a>
                    <a href="./stt.php?key=<?php echo $key_list[$i]["name"]; ?>" class="btn btn-sm btn-warning use-b">利用再開</a>
                <?php else : ?>
                    <a href="javascript:void(0);" class="btn btn-sm btn-primary disabled use-b">このカードは無効です</a>
                    <a href="javascript:void(0);" class="btn btn-sm btn-warning disabled use-b">状態操作はできません</a>
                <?php endif; ?>
            </div>
            <?php endfor; ?>
        </div>
        <?php if(count($key_list) == 0) : ?>
        <div class="alert alert-danger" role="alert">
            キーが登録されていません。<br>
            管理者へお問い合わせください。
        </div>
        <?php endif; ?>
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
