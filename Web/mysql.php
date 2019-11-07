<?php
class SQL_Proc {
    function host() {
        return "localhost";
    }

    function port() {
        return "3306";
    }

    function user() {
        return "root";
    }

    function pass() {
        return "TEST_PASSWD";
    }

    function db() {
        return "guest_door";
    }

    function isLogin() {
        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $id = (empty($_SESSION['user_id'])) ? "" : $_SESSION['user_id'];
        $pass = (empty($_SESSION['user_pass'])) ? "" : $_SESSION['user_pass'];
        $id = mysqli_real_escape_string($link, $id);
        $pass = mysqli_real_escape_string($link, $pass);

        $result = mysqli_query($link, "SELECT user, pass FROM Users WHERE user='" . $id . "' and pass=SHA1('" . $pass . "')");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        mysqli_close($link);
        return (mysqli_num_rows($result) == 1);
    }

    function renewPass($new_pass) {
        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $id = (empty($_SESSION['user_id'])) ? "" : $_SESSION['user_id'];
        $pass = (empty($new_pass)) ? "" : $new_pass;
        $id = mysqli_real_escape_string($link, $id);
        $pass = mysqli_real_escape_string($link, $pass);

        $result = mysqli_query($link, "UPDATE Users SET pass=SHA1('" . $pass . "') WHERE user='" . $id . "';");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        mysqli_close($link);
    }
}
?>