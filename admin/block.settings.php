<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
 
/*Block settings by elephant*/

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


	$block_set_cache = CACHE_DIR.'block_settings_cache.php';

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] )
	{
	unset($_POST['submit']);
	block_cache();
	exit;
	}
	
/////////////////////////////
//	cache block function
/////////////////////////////
function block_cache()
	{
		global $block_set_cache;
		
		$block_out = "<"."?php\n\n\$BLOCKS = array(\n";
		
		foreach( $_POST as $k => $v)
		{
			$block_out .= ($k == 'block_undefined') ? "\t'{$k}' => '".htmlspecialchars($v)."',\n" : "\t'{$k}' => ".intval($v).",\n";
		}
		

		$block_out .= "\n);\n\n?".">";
		
		if( is_file( $block_set_cache ) && is_writable( pathinfo($block_set_cache, PATHINFO_DIRNAME) ) )
		{
			$filenum = fopen ( $block_set_cache, 'w' );
			ftruncate( $filenum, 0 );
			fwrite( $filenum, $block_out );
			fclose( $filenum );
		}
		
		redirect('staffpanel.php?tool=block.settings&amp;action=block.settings', 'Block Settings Have Been Updated!', 3);
	}
	
function get_cache_array() 
	{
		return array(
                       	'ie_user_alert'              => 1,
                       	'active_users_on'              => 1,
                       	'active_24h_users_on'              => 1,
                       	'active_irc_users_on'              => 1,
                       	'active_birthday_users_on'              => 1,
                    	  'disclaimer_on'                => 1,
                    	  'shoutbox_on'                  => 1,
                        'news_on'                      => 1,
                        'stats_on'                     => 1,
                        'latest_user_on'                     => 1,
                        'forum_posts_on'                     => 1,
                        'latest_torrents_on'                     => 1,
                        'latest_torrents_scroll_on'                     => 1,
                        'announcement'                     => 1,
                        'donation_progress_on'                     => 1,
                        'ads_on'                     => 1,
                        'radio_on'                     => 1,
                        'torrentfreak_on'                     => 1,
                        'xmas_gift_on'                     => 1,
                        'active_poll_on'                     => 1,
                        'global_demotion_on'             => 1,
                        'global_staff_warn_on'         => 1,
                        'global_message_on'          => 1,
                        'global_staff_uploadapp_on'           => 1,
                        'global_staff_report_on'           => 1,
                        'global_freeleech_on'          => 1,
                        'global_happyhour_on'          => 1,
                        'global_crazyhour_on'          => 1,
					);
	}
	
	if ( ! is_file( $block_set_cache ) )
	{
		$BLOCKS = get_cache_array();
	}
	else
	{
		require_once $block_set_cache;
		
		if( ! is_array($BLOCKS))
		{	
			$BLOCKS = get_cache_array();
		}
	}
	

$HTMLOUT = '';
$HTMLOUT .='
<div class="global_icon"><img src="PIC/blocks.png" alt="" title="Block" class="global_image" width="25"/></div>
 <div class="global_head">Block Settings</div><br />
  <div class="global_text"><br />
   <form action="staffpanel.php?tool=block.settings&amp;action=block.settings" method="post">
<div><h1>  Index Settings</h1></div>
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable IE alert?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the IE user alert.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#ie_user_alert#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable News?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the News Block.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#news_on#></div></td>
    </tr></table>

    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Shoutbox?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the Shoutbox.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#shoutbox_on#></div></td>
    </tr></table>

    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Active Users?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the Active Users.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#active_users_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Active Users Over 24hours?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the Active Users visited over 24hours.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#active_24h_users_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Active Irc Users?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the Active Irc Users.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#active_irc_users_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Birthday Users?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the Active Birthday Users.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#active_birthday_users_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Site Stats?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the Stats.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#stats_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Disclaimer?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable Disclaimer.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#disclaimer_on#></div></td>
    </tr></table>  
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Latest User?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable Latest User.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#latest_user_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Latest Forum Posts?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable latest Forum Posts.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#forum_posts_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Latest torrents?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable latest torrents.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#latest_torrents_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Latest torrents scroll?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable latest torrents marquee.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#latest_torrents_scroll_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Announcement?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the Announcement Block.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#announcement_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Donation Progress?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the Donation Progress.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#donation_progress_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Advertisements?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the Advertisements.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#ads_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Radio?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the site radio.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#radio_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Torrent Freak?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the torrent freak news.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#torrentfreak_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Xmas Gift?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the Christmas Gift.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#xmas_gift_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Poll?</b>
    <div style="color: gray;">Set this option to "Yes" if you want to enable the Active Poll.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#active_poll_on#></div></td>
    </tr></table>
    
    <div><h1>Stdhead Settings</h1></div>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Freeleech?</b>
    <div style="color: gray;">Enable "freeleech mark" in stdhead</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_freeleech_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Demotion</b>
    <div style="color: gray;">Enable the global demotion alert block</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_demotion_on#></div></td>
    </tr></table>

    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Message block?</b>
    <div style="color: gray;">Enable message alert block</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_message_on#></div></td>
    </tr></table>

    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Staff Warning?</b>
    <div style="color: gray;">Shows a warning if there is a new message for staff</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_staff_warn_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Staff Reports?</b>
    <div style="color: gray;">Enable reports alert in stdhead</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_staff_report_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Upload App Alert?</b>
    <div style="color: gray;">Enable upload application alerts in stdhead</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_staff_uploadapp_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Happyhour?</b>
    <div style="color: gray;">Enable happy hour alerts in stdhead</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_happyhour_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>CrazyHour?</b>
    <div style="color: gray;">Enable crazyhour alerts in stdhead</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_crazyhour_on#></div></td>
    </tr></table>
    
    
<input type="submit" name="submit" value="Submit" class="btn" tabindex="2" accesskey="s" />
</form>
</div>';

$HTMLOUT = preg_replace_callback( "|<#(.*?)#>|", "template_out", $HTMLOUT);



echo stdhead("Block Settings") , $HTMLOUT , stdfoot();


function template_out($matches)
	{
	  global $BLOCKS;

	  return 'Yes &nbsp; <input name="'.$matches[1].'" value="1" '.($BLOCKS[$matches[1]] == 1 ? 'checked="checked"' : "").' type="radio" />&nbsp;&nbsp;&nbsp;<input name="'.$matches[1].'" value="0" '.($BLOCKS[$matches[1]] == 1 ? "" : 'checked="checked"').' type="radio" /> &nbsp; No';

	}


function redirect($url, $text, $time=2) {
		global $INSTALLER09;
		
		$page_title  = "Admin Blocks Redirection";
		$page_detail = "<em>Redirecting...</em>";
		
		$html = "
		<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='refresh' content=\"{$time}; url={$INSTALLER09['baseurl']}/{$url}\" />
		<title>Block Settings</title>
    <link rel='stylesheet' href='./templates/1/1.css' type='text/css' />
    </head>
    <body>
    <div>
	  <div>Redirecting</div>
		<div style='padding:8px'>
		<div style='font-size:12px'>$text
		<br />
		<br />
		<a href='{$INSTALLER09['baseurl']}/{$url}'>Click here if not redirected...</a>
		</div>
		</div>
		</div></body></html>";
		echo $html;
		exit;
	}         
            

?>