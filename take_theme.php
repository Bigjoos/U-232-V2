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
dbconn(false);
loggedinorreturn();

$lang = array_merge( load_language('global') );

$HTMLOUT='';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $sid = isset($_POST['stylesheet']) ? (int)$_POST['stylesheet'] : 1;
  if($sid > 0 && $sid != $CURUSER['id'])
  sql_query('UPDATE users SET stylesheet='.$sid.' WHERE id = '.$CURUSER['id']) or sqlerr(__FILE__,__LINE__);
  $mc1->delete_value('MyUser_'.$CURUSER['id']);
  $HTMLOUT .="<script language='javascript' type='text/javascript'>
        opener.location.reload(true);
        self.close();
      </script>";
}

$HTMLOUT .="<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<title>Choose theme</title>
</head>

<body style='background:#666666;color:#CCCCCC;'>
  <div align='center' style='width:200px'><fieldset>
    <legend>Change theme</legend>
  <form action='take_theme.php' method='post'>
            <p align='center'>
          <select name='stylesheet' onchange='this.form.submit();' size='1' style='font-family: Verdana; font-size: 8pt; color: #000000; border: 1px solid #808080; background-color: #ececec'>";

   $out='';
   $ss_r = mysql_query("SELECT id, name from stylesheets ORDER BY id ASC") or sqlerr(__FILE__,__LINE__);
   while($ar = mysql_fetch_assoc($ss_r))
   $out .= '<option value="'.$ar['id'].'" '.($ar['id'] == $CURUSER['stylesheet'] ? 'selected=\'selected\'' : '').'>'.$ar['name'].'</option>';
   $HTMLOUT .= $out;
   $HTMLOUT .="</select>
   <input type='button' value='Close' onclick='self.close()' /></p></form></fieldset></div></body></html>";
print $HTMLOUT;
exit();
?>