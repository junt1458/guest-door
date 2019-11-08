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

    $sql->deleteUser($_GET['id']);

    header("Location: ./user.php");
?>