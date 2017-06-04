<?php
$installed = file_exists('./installed.txt');

if($installed) {
    die('数据库已安装');
}

// 接收参数
$data_P = array();
foreach($_REQUEST as $k=>$v) {
    $data_P[strtoupper($k)] = htmlspecialchars(trim($v));
}


// 加载配置文件
$install_config = include('./config.php');
$project_config = include($install_config['config_file']);

// 将数据库设定写到配置文件中
foreach($project_config as $k=>$v) {
    if(isset($data_P[$k]) && !empty($data_P[$k])) {
        $project_config[$k] = $data_P[$k];
    }
}
unset($k,$v); // 释放内存
file_put_contents($install_config['config_file'], "<?php".PHP_EOL."return ".var_export($project_config, true).";");

// 读取数据库备份文件
$sql = file_get_contents($install_config['sql_file']);
// 如果有表前缀，将添加表前缀
$table = array();
if(!empty($project_config['DB_PREFIX'])) {
    preg_match_all('/table(.+)?`(\w+)`/i', $sql, $match);
    if(isset($match[2]) && !empty($match[2])) {
        $table = array_unique($match[2]);
        foreach($table as $i=>$t_name) {
            $replacement[$i] = '`'.$project_config['DB_PREFIX'].$t_name.'`';
            $table[$i] = '/`'.$t_name.'`/';
        }
        /// 替换
        $sql = preg_replace($table, $replacement, $sql);
    }
}

// 将表添加到数据库
/// 连接mysql
$mysqli = @new MySQLi($project_config['DB_HOST'],
    $project_config['DB_USER'],
    $project_config['DB_PWD'],
    '',
    $project_config['DB_PORT']);
    
if($mysqli->connect_errno) {
    die($mysqli->connect_error);
}
///设置字符集
$mysqli->set_charset('utf8');
/// 选择数据库
$mysqli->select_db($project_config['DB_NAME']);
if($mysqli->errno) {
    // 没有指定数据库
    $result = $mysqli->query('create database if not exists `'.$project_config['DB_NAME'].'` DEFAULT CHARACTER SET UTF8 COLLATE utf8_general_ci');
    // 重新选择数据库
    $mysqli->select_db($project_config['DB_NAME']);
}

///写入数据
$mysqli->multi_query($sql);

/// 关闭数据库连接
$mysqli->close();
unset($mysqli);
file_put_contents('./installed.txt', 'done');
echo '安装成功，请删除 install 文件夹';









