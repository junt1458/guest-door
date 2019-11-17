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
    
    for ($i = 0; $i < count($key_list); $i++) {
        $key_detail = $key_list[$i];
        if ($key_detail["name"] === $_GET['key'] && $key_detail["type"] === "仮想キー" ) {
            $kid = $key_detail['kid'];
            if($key_detail["status"] === "使用可能") {
                $res = `screen -S Guest-Door-Py -p 0 -X stuff "I_READ_${kid}^M"`;
                sleep(1);
            } else if($key_detail["status"] === "使用中") {
                $res = `screen -S Guest-Door-Py -p 0 -X stuff "O_READ_${kid}^M"`;
                usleep(500000);
            }
        }
    }
    
    header("Location: ./");
?>
