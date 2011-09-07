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
require_once(INCL_DIR.'html_functions.php');
dbconn();
loggedinorreturn();

  $lang = array_merge( load_language('global') );
  define('IN_LOTTERY','yeah');
  $lottery_root = ROOT_DIR.'lottery'.DIRECTORY_SEPARATOR;
  $valid = array('config'=>array('minclass'=>UC_MODERATOR,'file'=>$lottery_root.'config.php'),
                 'viewtickets'=>array('minclass'=>UC_MODERATOR,'file'=>$lottery_root.'viewtickets.php'),
                 'tickets'=> array('minclass'=>UC_USER,'file'=>$lottery_root.'tickets.php'),
                );
  $do = isset($_GET['do']) && in_array($_GET['do'],array_keys($valid)) ? $_GET['do'] : '';
  
  //print('<pre>'.print_r($valid,1));
  //print($do);
  switch(true) {
    case $do == 'config' && $CURUSER['class'] >= $valid['config']['minclass'] :
      require_once($valid['config']['file']);
    break;
    case $do == 'viewtickets' && $CURUSER['class'] >= $valid['viewtickets']['minclass'] :
      require_once($valid['viewtickets']['file']);
    break;
    case $do == 'tickets' && $CURUSER['class'] >= $valid['tickets']['minclass'] :
      require_once($valid['tickets']['file']);
    break;
    default : 
      $html = begin_main_frame();
      //get config from database 
      $lconf = sql_query('SELECT * FROM lottery_config') or sqlerr(__FILE__,__LINE__);
      while($ac = mysql_fetch_assoc($lconf))
        $lottery_config[$ac['name']] = $ac['value'];
      if(!$lottery_config['enable'])
        $html .= stdmsg('Sorry','Lottery is closed a the moment');
      if($lottery_config['end_date'] > TIME_NOW)
        $html .= stdmsg('Lottery in progress',"Lottery started on <b>".get_date($lottery_config['start_date'],'LONG')."</b> and ends on <b>".get_date($lottery_config['end_date'],'LONG')."</b> remaining <span style='color:#ff0000;'>".mkprettytime($lottery_config['end_date']-TIME_NOW)."</span>");
      //get last lottery data:
      $uids = (strpos($lottery_config['lottery_winners'],'|') ? explode('|',$lottery_config['lottery_winners']) : $lottery_config['lottery_winners']);
      $last_winners = array();
        $qus = sql_query('SELECT id,username FROM users WHERE id '.(is_array($uids) ? 'IN ('.join(',',$uids).')' : '='.$uids)) or sqlerr(__FILE__,__LINE__);
          while($aus = mysql_fetch_assoc($qus))
            $last_winners[] = "<a href='userdetails.php?id={$aus['id']}'>{$aus['username']}</a>";
      $html .= stdmsg('Last lottery',"<ul style='text-align:left;'>
        <li>Last winners: ".join(', ',$last_winners)."</li>
        <li>Amount won	(each): ".$lottery_config['lottery_winners_amount']."</li>
        <li>Date of last lottery: ".get_date($lottery_config['lottery_winners_time'],'LONG')."</li>
      </ul>");
      $html .= "<p style='text-align:center'>".($CURUSER['class'] >= UC_MODERATOR ? "<a href='lottery.php?do=viewtickets'>[View bought tickets]</a>&nbsp;&nbsp;<a href='lottery.php?do=config'>[Lottery configuration]</a>&nbsp;&nbsp;" : "")."<a href='lottery.php?do=tickets'>[Buy tickets]</a></p>";

      $html .= end_main_frame();
      print(stdhead('Lottery').$html.stdfoot());    
  }
?>