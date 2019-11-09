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

    if($sql->getLevel() == 0) {
        header("Location: ./");
        exit;
    } else if($sql->getLevel() == 1 || $sql->getLevel() == 3) {
        $_GET['id'] = $_SESSION['user_id'];
    }

    if(empty($_GET['id'])) {
        header("Location: ./user.php");
        exit;
    }

    $sql->deleteKey($_GET['id'], $_GET['key_id']);

    header("Location: ./key.php?id=" . $_GET['id']);
?>