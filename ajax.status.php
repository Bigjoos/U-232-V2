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
dbconn();
loggedinorreturn();

function url2short($x) {
 preg_match_all('/((http|https)\:\/\/[^()<>\s]+)/i',$x,$t);
 if(isset($t[0])) {
  foreach($t[0] as $l){
   if(strpos($l,'is.gd'))
   continue;
    $shorturls[1][] = file_get_contents('http://is.gd/api.php?longurl='.urlencode($l));
    $shorturls[0][] = $l;
  }
  if(isset($shorturls))  
  $x = str_replace($shorturls[0],$shorturls[1],$x);
 }
 return $x;
}
function jsonmsg($arr) {
 return json_encode(array('msg'=>$arr[0],'status'=>$arr[1]));
}
 $vdo = array('edit'=>1,'delete'=>1,'new'=>1);
$do = isset($_POST['action']) && isset($vdo[$_POST['action']]) ? $_POST['action'] : '';
$id = isset($_POST['id']) ? 0+$_POST['id'] : '';
$ss = isset($_POST['ss']) && !empty($_POST['ss']) ? $_POST['ss'] : '';
switch($do) {
 case 'edit':
  if(!empty($ss)) {
     if(mysql_query('UPDATE ustatus SET last_status = '.sqlesc(url2short($ss)).', last_update = '.TIME_NOW.' WHERE userid ='.$CURUSER['id']))
      $return = jsonmsg(array($ss,true));
     else 
      $return = jsonmsg(array('There was an error, mysql error'.mysql_error(),false));
  } else
   $return = jsonmsg(array('nothing to update, string empty',false));
 break;
 case 'delete':
   $status_history = unserialize($CURUSER['archive']);
   if(isset($status_history[$id])) {
     unset($status_history[$id]);
     if(mysql_query('UPDATE ustatus SET archive = '.sqlesc(serialize($status_history)).' WHERE userid = '.$CURUSER['id']))
      $return = jsonmsg(array('ok',true));
     else
      $return = jsonmsg(array('there was an error',false));
   } else 
     $return = jsonmsg(array('incorrect id',false));
 break;
 case 'new':
   $status_archive = ((isset($CURUSER['archive']) && is_array(unserialize($CURUSER['archive']))) ? unserialize($CURUSER['archive']) : array());
   if(!empty($CURUSER['last_status']))
     $status_archive[] = array('status'=>$CURUSER['last_status'],'date'=>$CURUSER['last_update']);
   if(mysql_query('INSERT INTO ustatus(userid,last_status,last_update,archive) VALUES('.$CURUSER['id'].','.sqlesc(url2short($ss)).','.TIME_NOW.','.sqlesc(serialize($status_archive)).') ON DUPLICATE KEY UPDATE last_status=values(last_status),last_update=values(last_update),archive=values(archive)'))
     $return = jsonmsg(array('<h2>Status update successful</h2>',true));
  else 
     $return = jsonmsg(array('There was an error, mysql error'.mysql_error(),false));
 break;
 default:
  $return = jsonmsg(array('Unknow action',false));
}
echo $return;
?>
