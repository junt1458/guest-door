<?php
class SQL_Proc {
    function host() {
        return "localhost";
    }

    function port() {
        return "3306";
    }

    function user() {
        return "test";
    }

    function pass() {
        return "TEST_PASSWD";
    }

    function db() {
        return "guest_door";
    }

    /**
     * ランダム文字列生成 (英数字)
     * https://qiita.com/TetsuTaka/items/bb020642e75458217b8a
     * $length: 生成する文字数
     */
    function makeRandStr($length) {
        $str = array_merge(range('0', '9'), range('A', 'Z'));
        $r_str = null;
        for ($i = 0; $i < $length; $i++) {
            $r_str .= $str[rand(0, count($str) - 1)];
        }
        return $r_str;
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

        $level = -1;

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

    function addKey($user_id, $key_id, $use_count, $key_name, $active_until, $key_type) {
        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $user = mysqli_real_escape_string($link, $user_id);
        $key_i = mysqli_real_escape_string($link, $key_id);
        $use_c = mysqli_real_escape_string($link, $use_count);
        $key_n = mysqli_real_escape_string($link, $key_name);
        $acitve_u = mysqli_real_escape_string($link, $active_until);
        $key_type = mysqli_real_escape_string($link, $key_type);

        $result = mysqli_query($link, "SELECT * FROM App_Keys WHERE user='" . $user . "' and key_name = '" . $key_n . "';");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        if(mysqli_num_rows($result) != 0) {
            mysqli_close($link);
            return 0;
        }

        $result = mysqli_query($link, "INSERT INTO App_Keys (key_id, user, use_count, key_name, key_type, active_until, paused, using_key) VALUES ('" . $key_i . "', '" . $user . "', " . $use_c . ", '" . $key_n . "', " . $key_type . ", '" . $acitve_u . "', false, false);");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        $result = mysqli_query($link, "INSERT INTO Logs (user, action, key_name) VALUES ('" . mysqli_real_escape_string($link, $_SESSION['user_id']) . "', 2, '" . $user . "-" . $key_n . "');");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        mysqli_close($link);
        return -1;
    }

    function deleteUser($user_id) {
        $user_list = array();

        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $result = mysqli_query($link, "DELETE FROM Users WHERE user='" . mysqli_real_escape_string($link, $user_id) . "'");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }
        
        $result = mysqli_query($link, "DELETE FROM App_Keys WHERE user='" . mysqli_real_escape_string($link, $user_id) . "'");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        mysqli_free_result($result);

        mysqli_close($link);
    }

    function deleteKey($user_id, $key_id) {
        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $user = mysqli_real_escape_string($link, $user_id);
        $key = mysqli_real_escape_string($link, $key_id);
        $result = mysqli_query($link, "DELETE FROM App_Keys WHERE user='" . $user . "' and key_name='" . $key . "'");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        $result = mysqli_query($link, "INSERT INTO Logs (user, action, key_name) VALUES ('" . mysqli_real_escape_string($link, $_SESSION['user_id']) . "', 3, '" . $user . "-" . $key . "');");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }
        mysqli_free_result($result);

        mysqli_close($link);
    }

    function updatePermission($user_id, $permission) {
        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $result = mysqli_query($link, "UPDATE Users SET permission=" . mysqli_real_escape_string($link, $permission) . " WHERE user='" . mysqli_real_escape_string($link, $user_id) . "'");
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

    function getKeyList($user_id) {
        date_default_timezone_set('Asia/Tokyo');
        $key_list = array();
    
        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $id = mysqli_real_escape_string($link, $user_id);

        $result = mysqli_query($link, "SELECT * FROM App_Keys WHERE user='" . $id . "' ORDER BY id");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        while($row = mysqli_fetch_assoc($result)) {
            $status = "使用可能";
            if($row['paused']) {
                $status = "停止中";
            }
            if($row['use_count'] <= 0 && $row['use_count'] != -1) {
                $status = "無効";
            }
            if(date_create_from_format("Y-m-d G:i:s", $row['active_until'])->format('U') < time()) {
                $status = "無効";
            }
            if($row['using_key']) {
                $status = "使用中";
            }
            $data = array(
                "name"=>$row['key_name'],
                "kid"=>$row['key_id'],
                "created_at"=>$row['created_at'],
                "use_count"=>(($row['use_count'] == -1) ? "無制限" : $row['use_count']),
                "active_until"=>$row['active_until'],
                "type"=>(($row['key_type'] == 0) ? "物理キー" : "仮想キー"),
                "status"=>$status
            );
            array_push($key_list, $data);
        }

        mysqli_free_result($result);

        mysqli_close($link);

        return $key_list;
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

    function toggleKeyStatus($key_, $user_) {
        $link = mysqli_connect($this->host() . ":" . $this->port(), $this->user(), $this->pass(), $this->db());
        if(mysqli_connect_errno()) {
            die("データベースに接続できません。" . mysqli_connect_error() . PHP_EOL);
            exit;
        }

        $user = mysqli_real_escape_string($link, $user_);
        $key = mysqli_real_escape_string($link, $key_);      

        $result = mysqli_query($link, "SELECT * FROM App_Keys WHERE user='" . $user . "' and key_name='" . $key . "'");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }
        
        $upd = 0;
        $st_id = 0;
        $err = 0;
        while($row = mysqli_fetch_assoc($result)) {
            if (date_create_from_format("Y-m-d G:i:s", $row['active_until'])->format('U') < time() || $row['use_count'] <= 0 && $row['use_count'] != -1) {
                $err = 1;
            }
            if($row['paused']) {
                $upd = 0;
                $st_id = 5;
            } else {
                $upd = 1;
                $st_id = 4;
            }
        }
        if($err) {
            mysqli_free_result($result);
            mysqli_close($link);
            return;
        }

        $result = mysqli_query($link, "UPDATE App_Keys SET paused=" . mysqli_real_escape_string($link, $upd) . " WHERE user='" . $user . "' and key_name='" . $key . "'");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }

        $result = mysqli_query($link, "INSERT INTO Logs (user, action, key_name) VALUES ('" . mysqli_real_escape_string($link, $_SESSION['user_id']) . "', " . $st_id . ", '" . $key . "');");
        if(!$result) {
            die("クエリーに失敗しました。" . mysqli_error($link));
            exit;
        }
        mysqli_free_result($result);

        mysqli_close($link);
    }
}
?>
