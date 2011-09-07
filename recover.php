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
get_template();

ini_set('session.use_trans_sid', '0');

// Begin the session
session_start();

dbconn();

   $lang = array_merge( load_language('global'), load_language('recover') );
   
   if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
      
    if(empty($captchaSelection) || $_SESSION['simpleCaptchaAnswer'] != $captchaSelection){
        header('Location: login.php');
        exit();
    }
    $email = trim($_POST["email"]);
    if (!validemail($email))
      stderr("{$lang['stderr_errorhead']}", "{$lang['stderr_invalidemail']}");
    $res = sql_query("SELECT * FROM users WHERE email=" . sqlesc($email) . " LIMIT 1") or sqlerr();
    $arr = mysql_fetch_assoc($res) or stderr("{$lang['stderr_errorhead']}", "{$lang['stderr_notfound']}");

    $sec = mksecret();

    sql_query("UPDATE users SET editsecret=" . sqlesc($sec) . " WHERE id=" . $arr["id"]) or sqlerr();
    if (!mysql_affected_rows())
      stderr("{$lang['stderr_errorhead']}", "{$lang['stderr_dberror']}");

    $hash = md5($sec . $email . $arr["passhash"] . $sec);


$body = sprintf($lang['email_request'], $email, $_SERVER["REMOTE_ADDR"], $INSTALLER09['baseurl'], $arr["id"], $hash).$INSTALLER09['site_name'];


    @mail($arr["email"], "{$INSTALLER09['site_name']} {$lang['email_subjreset']}", $body, "From: {$INSTALLER09['site_email']}") or stderr("{$lang['stderr_errorhead']}", "{$lang['stderr_nomail']}");
    
    stderr($lang['stderr_successhead'], $lang['stderr_confmailsent']);
    }
    elseif($_GET)
    {
    $id = 0 + $_GET["id"];
    $md5 = $_GET["secret"];

    if (!$id)
      httperr();

    $res = sql_query("SELECT username, email, passhash, editsecret FROM users WHERE id = $id");
    $arr = mysql_fetch_assoc($res) or httperr();

    $email = $arr["email"];
    $sec = $arr['editsecret'];
    
    if ($md5 != md5($sec . $email . $arr["passhash"] . $sec))
      httperr();

    $newpassword = make_password();
    $sec = mksecret();

    $newpasshash = make_passhash( $sec, md5($newpassword) );

    @sql_query("UPDATE users SET secret=" . sqlesc($sec) . ", editsecret='', passhash=" . sqlesc($newpasshash) . " WHERE id=$id AND editsecret=" . sqlesc($arr["editsecret"]));

    if (!mysql_affected_rows())
      stderr("{$lang['stderr_errorhead']}", "{$lang['stderr_noupdate']}");

    $body = sprintf($lang['email_newpass'], $arr["username"], $newpassword, $INSTALLER09['baseurl']).$INSTALLER09['site_name'];

  
    @mail($email, "{$INSTALLER09['site_name']} {$lang['email_subject']}", $body, "From: {$INSTALLER09['site_email']}")
      or stderr($lang['stderr_errorhead'], $lang['stderr_nomail']);
    stderr($lang['stderr_successhead'], sprintf($lang['stderr_mailed'], $email));
    }
    else
    {  
      
    $HTMLOUT = '';
    
    $HTMLOUT .= "<script type='text/javascript' src='scripts/jquery.simpleCaptcha-0.2.js'></script>
      <script type='text/javascript'>
	    /*<![CDATA[*/
	    $(document).ready(function () {
	    $('#captcharecover').simpleCaptcha();
      });
      /*]]>*/
      </script>
      <h1>{$lang['recover_unamepass']}</h1>
      <p>{$lang['recover_form']}</p>
      <form method='post' action='recover.php'>
      <table border='1' cellspacing='0' cellpadding='10'>
      <tr>
      <td align='left' class='rowhead' colspan='2' id='captcharecover'></td>
      </tr>
      <tr>
      <td class='rowhead'>{$lang['recover_regdemail']}</td>
      <td><input type='text' size='40' name='email' /></td></tr>
      <tr>
      <td colspan='2' align='center'><input type='submit' value='{$lang['recover_btn']}' class='btn' /></td>
      </tr>
      </table>
      </form>";

      print stdhead($lang['head_recover']). $HTMLOUT . stdfoot();
    }

?>