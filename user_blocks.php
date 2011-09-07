<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
/*
+------------------------------------------------
|   $Date$ 10022011
|   $Revision$ 1.0
|   $Author$ pdq,Bigjoos
|   $User block system
|   
+------------------------------------------------
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require(INCL_DIR.'html_functions.php');
require(INCL_DIR.'user_functions.php');
require(CLASS_DIR.'class_blocks_index.php');

dbconn(false);
loggedinorreturn();

$lang = load_language('global');

$id = (isset($_GET['id']) ? $_GET['id'] : $CURUSER['id']);
if (!is_valid_id($id) || $CURUSER['class'] < UC_MODERATOR)
    $id = $CURUSER['id'];

if ($CURUSER['got_blocks'] == 'no'){
stderr("Error", "Time shall unfold what plighted cunning hides\n\nWho cover faults, at last shame them derides.... Go to your Karma bonus page and buy this unlock before trying to access it.");
die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $updateset = array();
    $setbits_index_page = $clrbits_index_page = $setbits_global_stdhead = $clrbits_global_stdhead = 0;
    
    //==Index
    if (isset($_POST['ie_alert']))
    	$setbits_index_page |= block_index::IE_ALERT;
    else
    	$clrbits_index_page |= block_index::IE_ALERT;
    
    if (isset($_POST['news']))
    	$setbits_index_page |= block_index::NEWS;
    else
    	$clrbits_index_page |= block_index::NEWS;
    
    if (isset($_POST['shoutbox']))
    	$setbits_index_page |= block_index::SHOUTBOX;
    else
    	$clrbits_index_page |= block_index::SHOUTBOX;
    
    if (isset($_POST['active_users']))
    	$setbits_index_page |= block_index::ACTIVE_USERS;
    else
    	$clrbits_index_page |= block_index::ACTIVE_USERS;
    
    if (isset($_POST['last_24_active_users']))
    	$setbits_index_page |= block_index::LAST_24_ACTIVE_USERS;
    else
    	$clrbits_index_page |= block_index::LAST_24_ACTIVE_USERS;
    
    if (isset($_POST['irc_active_users']))
    	$setbits_index_page |= block_index::IRC_ACTIVE_USERS;
    else
    	$clrbits_index_page |= block_index::IRC_ACTIVE_USERS;
    
    if (isset($_POST['birthday_active_users']))
    	$setbits_index_page |= block_index::BIRTHDAY_ACTIVE_USERS;
    else
    	$clrbits_index_page |= block_index::BIRTHDAY_ACTIVE_USERS;
    
    if (isset($_POST['stats']))
    	$setbits_index_page |= block_index::STATS;
    else
    	$clrbits_index_page |= block_index::STATS;
    
    if (isset($_POST['disclaimer']))
    	$setbits_index_page |= block_index::DISCLAIMER;
    else
    	$clrbits_index_page |= block_index::DISCLAIMER;
    
    if (isset($_POST['latest_user']))
    	$setbits_index_page |= block_index::LATEST_USER;
    else
    	$clrbits_index_page |= block_index::LATEST_USER;
    
    if (isset($_POST['forumposts']))
    	$setbits_index_page |= block_index::FORUMPOSTS;
    else
    	$clrbits_index_page |= block_index::FORUMPOSTS;
    
    if (isset($_POST['latest_torrents']))
    	$setbits_index_page |= block_index::LATEST_TORRENTS;
    else
    	$clrbits_index_page |= block_index::LATEST_TORRENTS;
    
    if (isset($_POST['latest_torrents_scroll']))
    	$setbits_index_page |= block_index::LATEST_TORRENTS_SCROLL;
    else
    	$clrbits_index_page |= block_index::LATEST_TORRENTS_SCROLL;
    
    if (isset($_POST['announcement']))
    	$setbits_index_page |= block_index::ANNOUNCEMENT;
    else
    	$clrbits_index_page |= block_index::ANNOUNCEMENT;
    
    if (isset($_POST['donation_progress']))
    	$setbits_index_page |= block_index::DONATION_PROGRESS;
    else
    	$clrbits_index_page |= block_index::DONATION_PROGRESS;
    
    if (isset($_POST['advertisements']))
    	$setbits_index_page |= block_index::ADVERTISEMENTS;
    else
    	$clrbits_index_page |= block_index::ADVERTISEMENTS;
    
    if (isset($_POST['radio']))
    	$setbits_index_page |= block_index::RADIO;
    else
    	$clrbits_index_page |= block_index::RADIO;
    
    if (isset($_POST['torrentfreak']))
    	$setbits_index_page |= block_index::TORRENTFREAK;
    else
    	$clrbits_index_page |= block_index::TORRENTFREAK;
    
    if (isset($_POST['xmas_gift']))
    	$setbits_index_page |= block_index::XMAS_GIFT;
    else
    	$clrbits_index_page |= block_index::XMAS_GIFT;
    
    if (isset($_POST['active_poll']))
    	$setbits_index_page |= block_index::ACTIVE_POLL;
    else
    	$clrbits_index_page |= block_index::ACTIVE_POLL;
    
    //==Stdhead
    if (isset($_POST['stdhead_freeleech']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_FREELEECH;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_FREELEECH;
    
    if (isset($_POST['stdhead_demotion']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_DEMOTION;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_DEMOTION;
    
    if (isset($_POST['stdhead_newpm']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_NEWPM;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_NEWPM;
    
    if (isset($_POST['stdhead_staff_message']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_STAFF_MESSAGE;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_STAFF_MESSAGE;
    
    if (isset($_POST['stdhead_reports']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_REPORTS;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_REPORTS;
    
    if (isset($_POST['stdhead_uploadapp']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_UPLOADAPP;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_UPLOADAPP;
    
    if (isset($_POST['stdhead_happyhour']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_HAPPYHOUR;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_HAPPYHOUR;
    
    if (isset($_POST['stdhead_crazyhour']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_CRAZYHOUR;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_CRAZYHOUR;
    
    if ($setbits_index_page)
      $updateset[] = 'index_page = (index_page | '.$setbits_index_page.')';
    
    if ($clrbits_index_page)
      $updateset[] = 'index_page = (index_page & ~'.$clrbits_index_page.')';
      
    if ($setbits_global_stdhead)
      $updateset[] = 'global_stdhead = (global_stdhead | '.$setbits_global_stdhead.')';
    
    if ($clrbits_global_stdhead)
      $updateset[] = 'global_stdhead = (global_stdhead & ~'.$clrbits_global_stdhead.')';
    
    if (count($updateset))
      sql_query('UPDATE user_blocks SET '.implode(',', $updateset).' WHERE userid = '.$id) or sqlerr(__FILE__, __LINE__);
      $mc1->delete_value('blocks::'.$id);
      header('Location: '.$INSTALLER09['baseurl'].'/user_blocks.php');
      exit();
    }
    
        //==Index
        $checkbox_ie_alert = ((curuser::$blocks['index_page'] & block_index::IE_ALERT) ? ' checked="checked"' : '');
        $checkbox_news = ((curuser::$blocks['index_page'] & block_index::NEWS) ? ' checked="checked"' : '');
        $checkbox_shoutbox = ((curuser::$blocks['index_page'] & block_index::SHOUTBOX) ? ' checked="checked"' : '');
        $checkbox_active_users = ((curuser::$blocks['index_page'] & block_index::ACTIVE_USERS) ? ' checked="checked"' : '');
        $checkbox_active_24h_users = ((curuser::$blocks['index_page'] & block_index::LAST_24_ACTIVE_USERS) ? ' checked="checked"' : '');
        $checkbox_active_irc_users = ((curuser::$blocks['index_page'] & block_index::IRC_ACTIVE_USERS) ? ' checked="checked"' : '');
        $checkbox_active_birthday_users = ((curuser::$blocks['index_page'] & block_index::BIRTHDAY_ACTIVE_USERS) ? ' checked="checked"' : '');
        $checkbox_stats = ((curuser::$blocks['index_page'] & block_index::STATS) ? ' checked="checked"' : '');
        $checkbox_disclaimer = ((curuser::$blocks['index_page'] & block_index::DISCLAIMER) ? ' checked="checked"' : '');
        $checkbox_latest_user = ((curuser::$blocks['index_page'] & block_index::LATEST_USER) ? ' checked="checked"' : '');
        $checkbox_latest_forumposts = ((curuser::$blocks['index_page'] & block_index::FORUMPOSTS) ? ' checked="checked"' : '');
        $checkbox_latest_torrents = ((curuser::$blocks['index_page'] & block_index::LATEST_TORRENTS) ? ' checked="checked"' : '');
        $checkbox_latest_torrents_scroll = ((curuser::$blocks['index_page'] & block_index::LATEST_TORRENTS_SCROLL) ? ' checked="checked"' : '');
        $checkbox_announcement = ((curuser::$blocks['index_page'] & block_index::ANNOUNCEMENT) ? ' checked="checked"' : '');
        $checkbox_donation_progress = ((curuser::$blocks['index_page'] & block_index::DONATION_PROGRESS) ? ' checked="checked"' : '');
        $checkbox_ads = ((curuser::$blocks['index_page'] & block_index::ADVERTISEMENTS) ? ' checked="checked"' : '');
        $checkbox_radio = ((curuser::$blocks['index_page'] & block_index::RADIO) ? ' checked="checked"' : '');
        $checkbox_torrentfreak = ((curuser::$blocks['index_page'] & block_index::TORRENTFREAK) ? ' checked="checked"' : '');
        $checkbox_xmasgift = ((curuser::$blocks['index_page'] & block_index::XMAS_GIFT) ? ' checked="checked"' : '');
        $checkbox_active_poll = ((curuser::$blocks['index_page'] & block_index::ACTIVE_POLL) ? ' checked="checked"' : '');
        //==Stdhead
        $checkbox_global_freeleech = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_FREELEECH) ? ' checked="checked"' : '');
        $checkbox_global_demotion = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_DEMOTION) ? ' checked="checked"' : '');
        $checkbox_global_message_alert = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_NEWPM) ? ' checked="checked"' : '');
        $checkbox_global_staff_message_alert = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_STAFF_MESSAGE) ? ' checked="checked"' : '');
        $checkbox_global_staff_report = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_REPORTS) ? ' checked="checked"' : '');
        $checkbox_global_staff_uploadapp = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_UPLOADAPP) ? ' checked="checked"' : '');
        $checkbox_global_happyhour = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_HAPPYHOUR) ? ' checked="checked"' : '');
        $checkbox_global_crazyhour = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_CRAZYHOUR) ? ' checked="checked"' : '');

        $HTMLOUT='';
        $HTMLOUT .= begin_frame();

        $HTMLOUT .= '<form action="" method="post">
        <div><h1>Index Settings</h1></div>
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable IE alert?</b>
        <div style="color: gray;">Check this option if you want to enable the IE user alert.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="ie_alert" value="yes"'.$checkbox_ie_alert.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable News?</b>
        <div style="color: gray;">Check this option if you want to enable the News Block.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="news" value="yes"'.$checkbox_news.' /></div></td>
        </tr></table>

        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Shoutbox?</b>
        <div style="color: gray;">Check this option if you want to enable the Shoutbox.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="shoutbox" value="yes"'.$checkbox_shoutbox.' /></div></td>
        </tr></table>

        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Active Users?</b>
        <div style="color: gray;">Check this option if you want to enable the Active Users.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="active_users" value="yes"'.$checkbox_active_users.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Active Users Over 24hours?</b>
        <div style="color: gray;">Check this option if you want to enable the Active Users visited over 24hours.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="last_24_active_users" value="yes"'.$checkbox_active_24h_users.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Active Irc Users?</b>
        <div style="color: gray;">Check this option if you want to enable the Active Irc Users.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="irc_active_users" value="yes"'.$checkbox_active_irc_users.' /></div></td>
        </tr></table>
      
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Birthday Users?</b>
        <div style="color: gray;">Check this option if you want to enable the Active Birthday Users.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="birthday_active_users" value="yes"'.$checkbox_active_birthday_users.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Site Stats?</b>
        <div style="color: gray;">Check this option if you want to enable the Stats.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stats" value="yes"'.$checkbox_stats.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Disclaimer?</b>
        <div style="color: gray;">Check this option if you want to enable Disclaimer.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="disclaimer" value="yes"'.$checkbox_disclaimer.' /></div></td>
        </tr></table>  
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Latest User?</b>
        <div style="color: gray;">Check this option if you want to enable Latest User.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="latest_user" value="yes"'.$checkbox_latest_user.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Latest Forum Posts?</b>
        <div style="color: gray;">Check this option if you want to enable latest Forum Posts.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="forumposts" value="yes"'.$checkbox_latest_forumposts.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Latest torrents?</b>
        <div style="color: gray;">Check this option if you want to enable latest torrents.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="latest_torrents" value="yes"'.$checkbox_latest_torrents.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Latest torrents scroll?</b>
        <div style="color: gray;">Check this option if you want to enable latest torrents marquee.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="latest_torrents_scroll" value="yes"'.$checkbox_latest_torrents_scroll.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Announcement?</b>
        <div style="color: gray;">Check this option if you want to enable the Announcement Block.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="announcement" value="yes"'.$checkbox_announcement.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Donation Progress?</b>
        <div style="color: gray;">Check this option if you want to enable the Donation Progress.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="donation_progress" value="yes"'.$checkbox_donation_progress.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Advertisements?</b>
        <div style="color: gray;">Check this option if you want to enable the Advertisements.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="advertisements" value="yes"'.$checkbox_ads.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Radio?</b>
        <div style="color: gray;">Check this option if you want to enable the site radio.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="radio" value="yes"'.$checkbox_radio.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Torrent Freak?</b>
        <div style="color: gray;">Check this option if you want to enable the torrent freak news.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="torrentfreak" value="yes"'.$checkbox_torrentfreak.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Xmas Gift?</b>
        <div style="color: gray;">Check this option if you want to enable the Christmas Gift.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="xmas_gift" value="yes"'.$checkbox_xmasgift.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Poll?</b>
        <div style="color: gray;">Check this option if you want to enable the Active Poll.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="active_poll" value="yes"'.$checkbox_active_poll.' /></div></td>
        </tr></table>
    
        <div><h1>Stdhead Settings</h1></div>
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Freeleech?</b>
        <div style="color: gray;">Enable "freeleech mark" in stdhead</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_freeleech" value="yes"'.$checkbox_global_freeleech.' /></div></td>
        </tr></table>';
        
        if($CURUSER['class'] >= UC_STAFF){
        $HTMLOUT.='<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Demotion</b>
        <div style="color: gray;">Enable the global demotion alert block</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_demotion" value="yes"'.$checkbox_global_demotion.' /></div></td>
        </tr></table>';
        }
        $HTMLOUT.='<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Message block?</b>
        <div style="color: gray;">Enable message alert block</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_newpm" value="yes"'.$checkbox_global_message_alert.' /></div></td>
        </tr></table>';
        if($CURUSER['class'] >= UC_STAFF){
        $HTMLOUT.='<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Staff Warning?</b>
        <div style="color: gray;">Shows a warning if there is a new message for staff</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_staff_message" value="yes"'.$checkbox_global_staff_message_alert.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Staff Reports?</b>
        <div style="color: gray;">Enable reports alert in stdhead</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_reports" value="yes"'.$checkbox_global_staff_report.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Upload App Alert?</b>
        <div style="color: gray;">Enable upload application alerts in stdhead</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_uploadapp" value="yes"'.$checkbox_global_staff_uploadapp.' /></div></td>
        </tr></table>';
        }
    
        $HTMLOUT.='<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Happyhour?</b>
        <div style="color: gray;">Enable happy hour alerts in stdhead</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_happyhour" value="yes"'.$checkbox_global_happyhour.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>CrazyHour?</b>
        <div style="color: gray;">Enable crazyhour alerts in stdhead</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_crazyhour" value="yes" '.$checkbox_global_crazyhour.' /></div></td>
        </tr></table><input type="submit" name="submit" value="Submit" class="btn" tabindex="2" accesskey="s" /></form>';
    
        $HTMLOUT .= end_frame();
    
print stdhead("User Blocks Config") . $HTMLOUT . stdfoot();
?>