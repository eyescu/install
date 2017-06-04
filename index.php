<?php
$installed = file_exists('./installed.txt');

if($installed) {
    die('数据库已安装');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>数据库安装</title>
</head>
<body>
    <form action="./install.php" method="post">
        数据库地址<input type="text" name="db_host" value="192.168.1.100"><br>
        端口<input type="text" name="db_port" value="3306"><br>
        数据库名<input type="text" name="db_name" value=""><br>
        用户名<input type="text" name="db_user" value="root"><br>
        密码<input type="password" name="db_pwd" value="root"><br>
        前缀<input type="text" name="db_prefix"><br>
        <input type="submit" value="安装">
    </form>
</body>
</html>