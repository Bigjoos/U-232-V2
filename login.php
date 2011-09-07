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
require_once(CLASS_DIR.'page_verify.php');
dbconn();
global $CURUSER;
if(!$CURUSER){
get_template();
}

ini_set('session.use_trans_sid', '0');

$lang = array_merge( load_language('global'), load_language('login') );
$newpage = new page_verify(); 
$newpage->create('takelogin');

 
  //== 09 failed logins
	function left ()
	{
	global $INSTALLER09;
	$total = 0;
	$ip = sqlesc(getip());
	$fail = sql_query("SELECT SUM(attempts) FROM failedlogins WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
	list($total) = mysql_fetch_row($fail);
	$left = $INSTALLER09['failedlogins'] - $total;
	if ($left <= 2)
	$left = "<font color='red' size='4'>" . $left . "</font>";
	else
	$left = "<font color='green' size='4'>" . $left . "</font>";
	return $left;
	}
	//== End Failed logins

    $HTMLOUT = '';

    unset($returnto);
    if (!empty($_GET["returnto"])) {
      $returnto = htmlspecialchars($_GET["returnto"]);
      if (!isset($_GET["nowarn"])) 
      {
        $HTMLOUT .= "<h1>{$lang['login_not_logged_in']}</h1>\n";
        $HTMLOUT .= "{$lang['login_error']}";
      }
    }

    $value = array('...','...','...','...','...','...');
    $value[rand(1,count($value)-1)] = 'X';
    $HTMLOUT .= "<script type='text/javascript' src='scripts/jquery.js'></script>
    <script type='text/javascript' src='scripts/jquery.simpleCaptcha-0.2.js'></script>
    <script type='text/javascript'>
	  /*<![CDATA[*/
	  $(document).ready(function () {
	  $('#captchalogin').simpleCaptcha();
    });
    /*]]>*/
    </script>
    <form method='post' action='takelogin.php'>
    <noscript>Javascript must be enabled to login and use this site</noscript>
    <p>Note: You need cookies enabled to log in.</p>                                              
    <p>Note: if your experiencing login issues delete your old cookies.</p>  
    <b>[{$INSTALLER09['failedlogins']}]</b> Failed logins in a row will ban your ip from access<br />You have <b> " . left() ." </b> login attempt(s) remaining.<br /><br />
    <table border='0' cellpadding='5'>
      <tr>
        <td class='rowhead'>{$lang['login_username']}</td>
        <td align='left'><input type='text' size='40' name='username' /></td>
      </tr>
      <tr>
        <td class='rowhead'>{$lang['login_password']}</td>
        <td align='left'><input type='password' size='40' name='password' /></td>
      </tr>	
     <tr>
     <td class='rowhead'>Use ssl</td>
     <td>
     <label for='ssl'>Browse the site using a secure connection just this session <input type='checkbox' name='use_ssl' checked='checked' value='1' id='ssl'/></label><br/>
     <label for='ssl2'>Browse the site using a secure connection permanently<input type='checkbox' name='perm_ssl' value='1' id='ssl2'/></label>
     </td>
     </tr>
     <tr>
     <td align='left' class='rowhead' colspan='2' id='captchalogin'></td>
     </tr>
     <tr>
     <td align='center' colspan='2'>Now click the button marked <strong>X</strong></td>
     </tr>
     <tr>
     <td colspan='2' align='center'>";
     for ($i=0; $i < count($value); $i++) {
     $HTMLOUT .= "<input name=\"submitme\" type=\"submit\" value=\"".$value[$i]."\" class=\"btn\" />";
     }
     $HTMLOUT .= "</td></tr></table>";
     if (isset($returnto))
     $HTMLOUT .= "<input type='hidden' name='returnto' value='" . htmlentities($returnto) . "' />\n";
     $HTMLOUT .= "</form>
     {$lang['login_signup']}{$lang['login_forgot']}";
     
     
    print stdhead("{$lang['login_login_btn']}") . $HTMLOUT . stdfoot();

?>