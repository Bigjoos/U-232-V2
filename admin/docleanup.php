<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
if ( ! defined( 'IN_INSTALLER09_ADMIN' ) )
{
	$HTMLOUT='';
	$HTMLOUT .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<title>Error!</title>
		</head>
		<body>
	<div style='font-size:33px;color:white;background-color:red;text-align:center;'>Incorrect access<br />You cannot access this file directly.</div>
	</body></html>";
	echo $HTMLOUT;
	exit();
}

require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'html_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_SYSOP, true, true);

$lang = array_merge( $lang, load_language('ad_docleanup') );
$HTMLOUT ='';

function calctime($val)
{
    $days = intval($val / 86400);
    $val -= $days * 86400;
    $hours = intval($val / 3600);
    $val -= $hours * 3600;
    $mins = intval($val / 60);
    $secs = $val - ($mins * 60);
    return $days . " Days, " . $hours . " Hours, " . $mins . " Minutes, " . $secs . " Seconds";
}

if (!function_exists('memory_get_usage')) {
    function memory_get_usage()
    {
        if (substr(PHP_OS, 0, 3) == 'WIN') {
            if (substr(PHP_OS, 0, 3) == 'WIN') {
                $output = array();
                exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output);

                return preg_replace('/[\D]/', '', $output[5]) * 1024;
            }
        } else {
            $pid = getmypid();
            exec("ps -eo%mem,rss,pid | grep $pid", $output);
            $output = explode(" ", $output[0]);
            return $output[1] * 1024;
        }
    }
}

$HTMLOUT .= begin_main_frame('Cleanups');
$HTMLOUT .= begin_table();

$HTMLOUT .="<tr><td class='table'>Cleanup Name</td>
<td class='table'>Last Run</td>
<td class='table'>Runs every</td>
<td class='table'>Scheduled to run</td>
</tr>";

$res = sql_query("SELECT arg, value_u FROM avps");
while ($arr = mysql_fetch_assoc($res)) {
    switch ($arr['arg']) {
        case 'lastcleantime': $arg = $INSTALLER09['autoclean_interval'];
            break;
        case 'lastslowcleantime': $arg = $INSTALLER09['autoslowclean_interval'];
            break;
        case 'lastslowcleantime2': $arg = $INSTALLER09['autoslowclean_interval2'];
            break;
       case 'lastlottocleantime': $arg = $INSTALLER09['lotteryclean_interval'];
            break;
        case 'lastoptimizedbtime': $arg = $INSTALLER09['optimizedb_interval'];
            break;
        case 'lastbackuptime': $arg = $INSTALLER09['autobackup_interval'];
            break;
    }

    $HTMLOUT .="<tr><td>".$arr['arg']."</td>
    <td>".get_date($arr['value_u'], 'DATE',1,0) . " (" .get_date($arr['value_u'], 'LONG',1,0) . ")</td>
    <td>" . calctime($arg) . "</td>
    <td>" . calctime($arr['value_u'] - (time() - $arg)) . "</td>
    </tr>";
}
$HTMLOUT .= end_table();


$HTMLOUT .="<form action='staffpanel.php?tool=docleanup&amp;action=docleanup' method='post'>
<table align='center'>
<tr>
<td class='table'>
<input type='checkbox' name='docleanup' />Do cleanup
&nbsp;&nbsp;&nbsp;&nbsp;
<input type='checkbox' name='doslowcleanup' />Do slow cleanup
&nbsp;&nbsp;&nbsp;&nbsp;
<input type='checkbox' name='doslowcleanup2' />Do slow cleanup 2
&nbsp;&nbsp;&nbsp;&nbsp;
<input type='checkbox' name='dolotterycleanup' />Do lotto clean
&nbsp;&nbsp;&nbsp;&nbsp;
<input type='checkbox' name='dooptimizedb' />Do optimization
&nbsp;&nbsp;&nbsp;&nbsp;
<input type='checkbox' name='dobackupdb' />Do mysql backup
<input type='submit' value='Submit' />
</td></tr></table>
</form>";


if (isset($_POST['docleanup'])) {
    sql_query("UPDATE avps SET value_u = " . TIME_NOW . " WHERE arg = 'lastcleantime'") or sqlerr(__FILE__, __LINE__);
    require_once(INCL_DIR.'cleanup.php');
    docleanup();
    header('Refresh: 2; url='.$INSTALLER09['baseurl'].'/staffpanel.php?tool=docleanup');
    $HTMLOUT .="<br /><h1>Cleanup Done</h1>";
}

if (isset($_POST['doslowcleanup'])) {
    sql_query("UPDATE avps SET value_u = " . TIME_NOW . " WHERE arg = 'lastslowcleantime'") or sqlerr(__FILE__, __LINE__);
    require_once(INCL_DIR.'cleanup.php');
    doslowcleanup();
    header('Refresh: 2; url='.$INSTALLER09['baseurl'].'/staffpanel.php?tool=docleanup');
   $HTMLOUT .="<br /><h1>Slow Cleanup Done</h1>";
}

if (isset($_POST['doslowcleanup2'])) {
    sql_query("UPDATE avps SET value_u = " . TIME_NOW . " WHERE arg = 'lastslowcleantime2'") or sqlerr(__FILE__, __LINE__);
    require_once(INCL_DIR.'cleanup.php');
    doslowcleanup2();
    header('Refresh: 2; url='.$INSTALLER09['baseurl'].'/staffpanel.php?tool=docleanup');
   $HTMLOUT .="<br /><h1>Slow Cleanup 2 Done</h1>";
}

if (isset($_POST['dolotterycleanup'])) {
    sql_query("UPDATE avps SET value_u = " . TIME_NOW . " WHERE arg = 'lastlottocleantime'") or sqlerr(__FILE__, __LINE__);
    require_once(INCL_DIR.'cleanup.php');
    dolotterycleanup();
    header('Refresh: 2; url='.$INSTALLER09['baseurl'].'/staffpanel.php?tool=docleanup');
   $HTMLOUT .="<br /><h1>Lottery Cleanup Done</h1>";
}

if (isset($_POST['dooptimizedb'])) {
    sql_query("UPDATE avps SET value_u = " . TIME_NOW . " WHERE arg = 'lastoptimizedbtime'") or sqlerr(__FILE__, __LINE__);
    require_once(INCL_DIR.'cleanup.php');
    dooptimizedb();
    header('Refresh: 2; url='.$INSTALLER09['baseurl'].'/staffpanel.php?tool=docleanup');
    $HTMLOUT .="<br /><h1>Optimization Done</h1>";
}

if (isset($_POST['dobackupdb'])) {
    sql_query("UPDATE avps SET value_u = " . TIME_NOW . " WHERE arg = 'lastbackuptime'") or sqlerr(__FILE__, __LINE__);
    require_once(INCL_DIR.'cleanup.php');
    dobackupdb();
    header('Refresh: 2; url='.$INSTALLER09['baseurl'].'/staffpanel.php?tool=docleanup');
    $HTMLOUT .="<br /><h1>Mysql backup Done</h1>";
}

$HTMLOUT .="Memory usage:" . memory_get_usage() . "<br /><br />";
$HTMLOUT .= end_main_frame();
echo stdhead('Doclean Up') . $HTMLOUT . stdfoot();
?>