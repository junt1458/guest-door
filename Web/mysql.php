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

    function getLevel() {
        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $id = (empty($_SESSION['user_id'])) ? "" : $_SESSION['user_id'];
        $pass = (empty($_SESSION['user_pass'])) ? "" : $_SESSION['user_pass'];
        $id = mysqli_real_escape_string($link, $id);
        $pass = mysqli_real_escape_string($link, $pass);

        $result = mysqli_query($link, "SELECT permission FROM Users WHERE user='" . $id . "' and pass=SHA1('" . $pass . "')");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        $level = 0;

        while($row = mysqli_fetch_assoc($result)) {
            $level = intval($row['permission']);
        }

        mysqli_free_result($result);

        mysqli_close($link);
        return $level;
    }

    function addUser($user_id, $pass, $permission) {
        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $id = (empty($_SESSION['user_id'])) ? "" : $_SESSION['user_id'];
        $pass = (empty($_SESSION['user_pass'])) ? "" : $_SESSION['user_pass'];
        $id = mysqli_real_escape_string($link, $id);
        $pass = mysqli_real_escape_string($link, $pass);

        $result = mysqli_query($link, "SELECT user, pass FROM Users WHERE user='" . $user_id . "'");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        if(mysqli_num_rows($result) != 0) {
            mysqli_close($link);
            return 0;
        }
        $result = mysqli_query($link, "INSERT INTO Users (user, pass, permission) VALUES ('" . $user_id . "', SHA1('" . $pass . "'), " . $permission . ");");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        mysqli_close($link);
        return -1;
    }

    function getUserLevel($user_id) {
        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $id = mysqli_real_escape_string($link, $user_id);

        $result = mysqli_query($link, "SELECT permission FROM Users WHERE user='" . $id . "'");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        $level = 0;

        while($row = mysqli_fetch_assoc($result)) {
            $level = intval($row['permission']);
        }

        mysqli_free_result($result);

        mysqli_close($link);
        return $level;
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

    function deleteUser($user_id) {
        $user_list = array();

        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $result = mysqli_query($link, "DELETE FROM Users WHERE user='" . $user_id . "'");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        mysqli_free_result($result);

        mysqli_close($link);
    }

    function updatePermission($user_id, $permission) {
        $user_list = array();

        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $result = mysqli_query($link, "UPDATE Users SET permission=" . $permission . " WHERE user='" . $user_id . "'");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        mysqli_free_result($result);

        mysqli_close($link);
    }

    function getUserList() {
        $user_list = array();

        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $id = (empty($_SESSION['user_id'])) ? "" : $_SESSION['user_id'];
        $pass = (empty($new_pass)) ? "" : $new_pass;
        $id = mysqli_real_escape_string($link, $id);
        $pass = mysqli_real_escape_string($link, $pass);

        $result = mysqli_query($link, "SELECT user, pass FROM Users ORDER BY id");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        while($row = mysqli_fetch_assoc($result)) {
            array_push($user_list, $row['user']);
        }

        mysqli_free_result($result);

        mysqli_close($link);

        return $user_list;
    }

    const action_str = array(
        0=>"入室",
        1=>"退室",
        2=>"キー追加",
        3=>"キー削除",
        4=>"キー無効化",
        5=>"キー有効化"
    );

    function getLogs($filter) {
        $user_list = array();

        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $id = (empty($_SESSION['user_id'])) ? "" : $_SESSION['user_id'];
        $pass = (empty($new_pass)) ? "" : $new_pass;
        $id = mysqli_real_escape_string($link, $id);
        $pass = mysqli_real_escape_string($link, $pass);

        $where = "";
        if($filter == 1) {
            $where = " WHERE action=0 or action=1";
        } else if($filter == 2) {
            $where = " WHERE action=2 or action=3 or action=4 or action=5";
        }
        $result = mysqli_query($link, "SELECT * FROM Logs" . $where . " ORDER BY id DESC;");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        while($row = mysqli_fetch_assoc($result)) {
            $arr = array(
                "date"=>$row['date'],
                "user"=>$row['user'],
                "action"=>$this::action_str[$row['action']],
                "key"=>$row['key_name']
            );
            array_push($user_list, $arr);
        }

        mysqli_free_result($result);

        mysqli_close($link);

        return $user_list;
    }
}
?>