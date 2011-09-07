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
require_once(INCL_DIR.'pager_functions.php');
dbconn(false);
loggedinorreturn();
parked();

$lang = array_merge( load_language('global'));

$HTMLOUT ="";

require_once(INCL_DIR.'class_check.php');
class_check(UC_ADMINISTRATOR);

  if (strtoupper (substr (PHP_OS, 0, 3) == 'WIN'))
  {
    $windows = 1;
    $unix = 0;
  }
  else
  {
    $windows = 0;
    $unix = 1;
  }

  $register_globals = (bool)ini_get ('register_gobals');
  $system = ini_get ('system');
  $unix = (bool)$unix;
  $win = (bool)$windows;
  if ($register_globals)
  {
  $ip = getenv (REMOTE_ADDR);
  $self = $PHP_SELF;
  }
  else
  {
    $action = isset($_POST["action"]) ? $_POST["action"] : '';
    //$action = $_POST['action'];
    $host = isset($_POST["host"]) ? $_POST["host"] : '';
    //$host = $_POST['host'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $self = $_SERVER['SCRIPT_NAME'];
  }

  if ($action == 'do')
  {
    $host = preg_replace ('/[^A-Za-z0-9.]/', '', $host);
    $HTMLOUT .= '<div class="error">';
    $HTMLOUT .= 'Trace Output:<br />';
    $HTMLOUT .= '<pre>';
    if ($unix)
    {
    system ('' . 'traceroute ' . $host);
    system ('killall -q traceroute');
    }
    else
    {
    system ('' . 'tracert ' . $host);
    }

    $HTMLOUT .= '</pre>';
    $HTMLOUT .= 'done ...</div>';
  }
  else
  {
    $HTMLOUT .= '
    <p><font size="2">Your IP is: ' . $ip . '</font></p>
    <form method="post" action="traceroute.php">
    Enter IP or Host <input type="text" id="specialboxn" name="host" value="' . $ip . '" />
    <input type="hidden" name="action" value="do" /><input type="submit" value="Traceroute!" class="button" />
   </form>';
    $HTMLOUT .= "<br /><b>{$system}</b>";
  }

print  stdhead('Traceroute') . $HTMLOUT . stdfoot();
?>
