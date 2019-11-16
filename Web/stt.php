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
    
    if(empty($_GET['key'])) {
        header("Location: ./index.php");
        exit;
    }

    $sql->toggleKeyStatus($_GET['key'], $_SESSION['user_id']);
    header("Location: ./index.php");
?>