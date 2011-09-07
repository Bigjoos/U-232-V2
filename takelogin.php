<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'password_functions.php');
require_once(CLASS_DIR.'page_verify.php');
global $CURUSER;
if(!$CURUSER){
get_template();
}

$sha = sha1($_SERVER['REMOTE_ADDR']);
if (is_file('' . $INSTALLER09['dictbreaker'] . '/' . $sha) && filemtime('' . $INSTALLER09['dictbreaker'] . '/' . $sha) > (time()-8)) {
@fclose(@fopen('' . $INSTALLER09['dictbreaker'] . '/' . $sha, 'w'));
die('Minimum 8 seconds between login attempts :)');
}

// 09 failed logins thanks to pdq - Retro
function failedloginscheck () {
global $INSTALLER09;
$total = 0;
$ip = sqlesc(getip());
$res = sql_query("SELECT SUM(attempts) FROM failedlogins WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
list($total) = mysql_fetch_row($res);
if ($total >= $INSTALLER09['failedlogins']) {
mysql_query("UPDATE failedlogins SET banned = 'yes' WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
stderr("Login Locked!", "You have been <b>Exceeded</b> the allowed maximum login attempts without successful login, therefore your ip address <b>(".htmlspecialchars($ip).")</b> has been locked for 24 hours.");
}
}
//==End

if (!mkglobal('username:password:captchaSelection:submitme'))
      die('Something went wrong');
      
    if ($submitme != 'X')
    stderr('Ha Ha', 'You Missed, You plonker !');
      
    session_start();
    if(empty($captchaSelection) || $_SESSION['simpleCaptchaAnswer'] != $captchaSelection){
        header('Location: login.php');
        exit();
    }

    dbconn();
    
    $lang = array_merge( load_language('global'), load_language('takelogin') );
    
    $newpage = new page_verify(); 
    $newpage->check('takelogin');

    function bark($text = 'Username or password incorrect')
    {
    global $lang;
    @fclose(@fopen(''.$INSTALLER09['dictbreaker'].'/' . sha1($_SERVER['REMOTE_ADDR']), 'w'));
    stderr($lang['tlogin_failed'], $text);
    }
    
    failedloginscheck ();
    
    $res = sql_query("SELECT id, passhash, secret, enabled FROM users WHERE username = " . sqlesc($username) . " AND status = 'confirmed'");
    $row = mysql_fetch_assoc($res);

    if (!$row)
      bark();
    
    if (!$row)
	  {
    $ip = sqlesc(getip());
    $added = sqlesc(time());
    $fail = (@mysql_fetch_row(@sql_query("select count(*) from failedlogins where ip=$ip"))) or sqlerr(__FILE__, __LINE__);
    if ($fail[0] == 0)
    sql_query("INSERT INTO failedlogins (ip, added, attempts) VALUES ($ip, $added, 1)") or sqlerr(__FILE__, __LINE__);
    else
    sql_query("UPDATE failedlogins SET attempts = attempts + 1 where ip=$ip") or sqlerr(__FILE__, __LINE__);
    @fclose(@fopen('' . $INSTALLER09['dictbreaker'] . '/' . sha1($_SERVER['REMOTE_ADDR']), 'w'));
    bark();
	  }
    if ($row['passhash'] != make_passhash( $row['secret'], md5($password) ) ) {
    $ip = sqlesc(getip());
    $added = sqlesc(time());
    $fail = (@mysql_fetch_row(@sql_query("select count(*) from failedlogins where ip=$ip"))) or sqlerr(__FILE__, __LINE__);
    if ($fail[0] == 0)
    sql_query("INSERT INTO failedlogins (ip, added, attempts) VALUES ($ip, $added, 1)") or sqlerr(__FILE__, __LINE__);
    else
    sql_query("UPDATE failedlogins SET attempts = attempts + 1 where ip=$ip") or sqlerr(__FILE__, __LINE__);
    @fclose(@fopen('' . $INSTALLER09['dictbreaker'] . '/' . sha1($_SERVER['REMOTE_ADDR']), 'w'));
    $to = ($row["id"]);
    $subject="Failed login";
	  $msg = "[color=red]Security alert[/color]\n Account: ID=".$row['id']." Somebody (probably you, ".$username." !) tried to login but failed!". "\nTheir [b]Ip Address [/b] was : ". $ip . "\n If this wasn't you please report this event to a {$INSTALLER09['site_name']} staff member\n - Thank you.\n";
	  $sql = "INSERT INTO messages (sender, receiver, msg, subject, added) VALUES('System', '$to', ". sqlesc($msg).", ". sqlesc($subject).", $added);";
	  $res = sql_query($sql) or sqlerr(__FILE__, __LINE__);
	  stderr("Login failed !", "<b>Error</b>: Username or password entry incorrect <br />Have you forgotten your password? <a href='{$INSTALLER09['baseurl']}/resetpw.php'><b>Recover</b></a> your password !");
	  bark();
    }
    
    ////Start IP logger ////
        $ip = sqlesc(getip());
        $added = sqlesc(time());
        $userid = sqlesc($row["id"]);
        $res = mysql_query("SELECT * FROM ips WHERE ip =$ip AND userid =$userid") or die(mysql_error());
        if (mysql_num_rows($res) == 0 ) {
        sql_query("INSERT INTO ips (userid, ip, lastlogin, type) VALUES ($userid, $ip , $added, 'Login')") or die(mysql_error());
        }
        else
        {
        sql_query("UPDATE ips SET lastlogin = $added where ip=$ip AND userid = $userid") or sqlerr(__FILE__, __LINE__);
        }
        //// End Ip logger /////

    if ($row['enabled'] == 'no')
      bark($lang['tlogin_disabled']);

    $passh = md5($row["passhash"].$_SERVER["REMOTE_ADDR"]);
    logincookie($row["id"], $passh);
   
    if(isset($_POST['use_ssl']) && $_POST['use_ssl'] == 1 && !isset($_SERVER['HTTPS']))
    $INSTALLER09['baseurl'] = str_replace('http','https',$INSTALLER09['baseurl']);

    if(isset($_POST['perm_ssl']) && $_POST['perm_ssl'] == 1)
    mysql_query('UPDATE users SET ssluse = 2 WHERE id = '.$row['id']) or sqlerr(__FILE__,__LINE__);
   
    $ip = sqlesc(getip());
	  sql_query("DELETE FROM failedlogins WHERE ip = $ip");

      header("Location: {$INSTALLER09['baseurl']}/index.php");

?>