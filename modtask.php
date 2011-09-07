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
dbconn(false);
loggedinorreturn();
$lang = array_merge( load_language('global'), load_language('modtask') );
$newpage = new page_verify(); 
$newpage->check('mdk1@@9');

    function write_info($text)
    {
    $text = sqlesc($text);
    $added = sqlesc(time());
    sql_query("INSERT INTO infolog (added, txt) VALUES($added, $text)") or sqlerr(__FILE__, __LINE__);
    }

    function resize_image($in)
    {
    $out = array(
    'img_width'  => $in['cur_width'],
    'img_height' => $in['cur_height']);
    if ( $in['cur_width'] > $in['max_width'] )
    {
    $out['img_width']  = $in['max_width'];
    $out['img_height'] = ceil( ( $in['cur_height'] * ( ( $in['max_width'] * 100 ) / $in['cur_width'] ) ) / 100 );
    $in['cur_height'] = $out['img_height'];
    $in['cur_width']  = $out['img_width'];
    }
    if ( $in['cur_height'] > $in['max_height'] )
    {
    $out['img_height']  = $in['max_height'];
    $out['img_width']   = ceil( ( $in['cur_width'] * ( ( $in['max_height'] * 100 ) / $in['cur_height'] ) ) / 100 );
    }
    return $out;
    }

    if ($CURUSER['class'] < UC_MODERATOR) stderr("{$lang['modtask_user_error']}", "{$lang['modtask_try_again']}");
    //== Correct call to script
    if ((isset($_POST['action'])) && ($_POST['action'] == "edituser"))
    {
    //== Set user id
    if (isset($_POST['userid'])) 
    $userid = (int)$_POST['userid'];
    else 
    stderr("{$lang['modtask_user_error']}", "{$lang['modtask_try_again']}");
    //== And verify...
    if (!is_valid_id($userid)) stderr("{$lang['modtask_error']}", "{$lang['modtask_bad_id']}");
    require_once(CLASS_DIR.'validator.php');
    if (!validate($_POST['validator'], "ModTask_$userid" )) die ("Invalid" );
    //== Fetch current user data...
    $res = sql_query("SELECT * FROM users WHERE id=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
    $user = mysql_fetch_assoc($res) or sqlerr(__FILE__, __LINE__);
    if ($CURUSER['class'] <= $user['class'] && ($CURUSER['id']!= $userid && $CURUSER['class'] < UC_ADMINISTRATOR))
		stderr('Error','You cannot edit someone of the same or higher class.. injecting stuff arent we? Action logged');
    if (($user['immunity'] >= 1) && ($CURUSER['class'] < UC_MAX))
    stderr("Error", "This user is immune to your commands !");
   
    $updateset = $useredit['update'] = array();
   
    $modcomment = (isset($_POST['modcomment']) && $CURUSER['class'] == UC_MAX) ? $_POST['modcomment'] : $user['modcomment'];
    //== Set class
    if ((isset($_POST['class'])) && (($class = $_POST['class']) != $user['class']))
    {
    if ($class >= UC_MAX || ($class >= $CURUSER['class']) || ($user['class'] >= $CURUSER['class']))
    stderr("{$lang['modtask_user_error']}", "{$lang['modtask_try_again']}");
    if (!valid_class($class) || $CURUSER['class'] <= $_POST['class']) stderr( ("Error"), "Bad class :P");
    //== Notify user
    $what = ($class > $user['class'] ? "{$lang['modtask_promoted']}" : "{$lang['modtask_demoted']}");
    $msg = sqlesc(sprintf($lang['modtask_have_been'], $what)." '" . get_user_class_name($class) . "' {$lang['modtask_by']} ".$CURUSER['username']);
    $added = time();
    sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    $updateset[] = "class = ".sqlesc($class);
    $useredit['update'][] = ''.$what.' to ' . get_user_class_name($class) . '';
    $modcomment = get_date( time(), 'DATE', 1 ) . " - $what to '" . get_user_class_name($class) . "' by $CURUSER[username].\n". $modcomment;
    }
    // === add donated amount to user and to funds table
    if ((isset($_POST['donated'])) && (($donated = $_POST['donated']) != $user['donated'])) {
       $added = sqlesc(time());
       sql_query("INSERT INTO funds (cash, user, added) VALUES ($donated, $userid, $added)") or sqlerr(__file__, __line__);
       $updateset[] = "donated = " . sqlesc($donated);
       $updateset[] = "total_donated = $user[total_donated] + " . sqlesc($donated);
       $mc1->delete_value('totalfunds_');
    }
    // ====end
    
    // === Set donor - Time based
    if ((isset($_POST['donorlength'])) && ($donorlength = 0 + $_POST['donorlength'])) {
       if ($donorlength == 255) {    
       $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_donor_set']}" . $CURUSER['username'] . ".\n" . $modcomment;
       $msg = sqlesc("You have received donor status from " . $CURUSER['username']);
       $subject = sqlesc("Thank You for Your Donation!");
       $updateset[] = "donoruntil = '0'";
       } else {
       $donoruntil = (time() + $donorlength * 604800);
       $dur = $donorlength . " week" . ($donorlength > 1 ? "s" : "");
       $msg = sqlesc("Dear " . $user['username'] . "
       :wave:
       Thanks for your support to {$INSTALLER09['site_name']} !
       Your donation helps us in the costs of running the site!
       As a donor, you are given some bonus gigs added to your uploaded amount, the status of VIP, and the warm fuzzy feeling you get inside for helping to support this site that we all know and love :smile:

       so, thanks again, and enjoy!
       cheers,
       {$INSTALLER09['site_name']} Staff

       PS. Your donator status will last for $dur and can be found on your user details page and can only be seen by you :smile: It was set by " .$CURUSER['username']);
       $subject = sqlesc("Thank You for Your Donation!");
       $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_donor_set']}" . $CURUSER['username'] . ".\n" . $modcomment;
       $updateset[] = "donoruntil = " . sqlesc($donoruntil);
       $updateset[] = "vipclass_before = " . $user["class"];
       }
       $added = sqlesc(time());
       sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $userid, $msg, $added)") or sqlerr(__file__, __line__);
       $updateset[] = "donor = 'yes'";
       $useredit['update'][] = 'Donor = Yes';
       $res = sql_query("SELECT class FROM users WHERE id = $userid") or sqlerr(__file__,__line__);
       $arr = mysql_fetch_array($res);
       if ($user['class'] < UC_UPLOADER)
       $updateset[] = "class = ".UC_VIP."";
       }
    
    // === add to donor length // thanks to CoLdFuSiOn
    if ((isset($_POST['donorlengthadd'])) && ($donorlengthadd = 0 + $_POST['donorlengthadd'])) {
       $donoruntil = $user["donoruntil"];
       $dur = $donorlengthadd . " week" . ($donorlengthadd > 1 ? "s" : "");
       $msg = sqlesc("Dear " . $user['username'] . "
       :wave:
       Thanks for your continued support to {$INSTALLER09['site_name']} !
       Your donation helps us in the costs of running the site. Everything above the current running costs will go towards next months costs!
       As a donor, you are given some bonus gigs added to your uploaded amount, and, you have the the status of VIP, and the warm fuzzy feeling you get inside for helping to support this site that we all know and love :smile:

       so, thanks again, and enjoy!
       cheers,
       {$INSTALLER09['site_name']} Staff

        PS. Your donator status will last for an extra $dur on top of your current donation status, and can be found on your user details page and can only be seen by you :smile: It was set by " .$CURUSER['username']);

        $subject = sqlesc("Thank You for Your Donation... Again!");
        $modcomment = get_date( time(), 'DATE', 1 ) . " - Donator status set for another $dur by " . $CURUSER['username'] .".\n" . $modcomment;
        $donorlengthadd = $donorlengthadd * 7;
        sql_query("UPDATE users SET vipclass_before=".$user["class"].", donoruntil = IF(donoruntil=0, ".TIME_NOW." + 86400 * $donorlengthadd, donoruntil + 86400 * $donorlengthadd) WHERE id = $userid") or sqlerr(__file__, __line__); 
        $added = sqlesc(time());
        sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $userid, $msg, $added)") or sqlerr(__file__, __line__);
        $updateset[] = "donated = $user[donated] + " . sqlesc($_POST['donated']);
        $updateset[] = "total_donated = $user[total_donated] + " . sqlesc($_POST['donated']);
    }
    // === end add to donor length
    
    // === Clear donor if they were bad
    if (isset($_POST['donor']) && (($donor = $_POST['donor']) != $user['donor'])) {
        $updateset[] = "donor = " . sqlesc($donor);
        $updateset[] = "donoruntil = '0'";
        $updateset[] = "donated = '0'";
        $updateset[] = "class = " . $user["vipclass_before"];
        $useredit['update'][] = 'Donor = No';
        if ($donor == 'no') {
        $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_donor_removed']} " . $CURUSER['username'] .".\n" . $modcomment;
        $msg = sqlesc(sprintf($lang['modtask_donor_removed']) . $CURUSER['username']);
        $added = sqlesc(time());
        $subject = sqlesc("Donator status expired.");
        sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $userid, $msg, $added)") or sqlerr(__file__, __line__);
        }
    }
    // ===end
    //== Enable / Disable
    if ((isset($_POST['enabled'])) && (($enabled = $_POST['enabled']) != $user['enabled']))
    {
    if ($enabled == 'yes')
    $modcomment = get_date( time(), 'DATE', 1 ) . " {$lang['modtask_enabled']}" . $CURUSER['username'] . ".\n" . $modcomment;
    else
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_disabled']}" . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = "enabled = " . sqlesc($enabled);
    $useredit['update'][] = 'Enabled = '.$enabled.'';
    }
    //== Set download posssible Time based
    if (isset($_POST['downloadpos']) && ($downloadpos =
    0 + $_POST['downloadpos']))
    {
    unset($disable_pm);
    if (isset($_POST['disable_pm']))
        $disable_pm = $_POST['disable_pm'];
    $subject = sqlesc('Notification!');
    $added = time();
    
    if ($downloadpos == 255)
    {
        $modcomment = get_date($added, 'DATE', 1)." - Download disablement by ".
		$CURUSER['username'].".\nReason: $disable_pm\n".$modcomment;
        $msg = sqlesc("Your Downloading rights have been disabled by ".$CURUSER['username'].($disable_pm ?
            "\n\nReason: $disable_pm" : ''));
        $updateset[] = 'downloadpos = 0';
        $useredit['update'][] = 'Download possible = No';
    } elseif ($downloadpos == 42)
    {
        $modcomment = get_date($added, 'DATE', 1)." - Download disablement status removed by ".
		$CURUSER['username'].".\n".$modcomment;
        $msg = sqlesc("Your Downloading rights have been restored by ".
		$CURUSER['username'].".");
		$updateset[] = 'downloadpos = 1';
    $useredit['update'][] = 'Download possible = Yes';
    } else
    {
        $downloadpos_until = ($added + $downloadpos * 604800);
        $dur = $downloadpos.' week'.($downloadpos > 1 ? 's' : '');
        $msg = sqlesc("You have received $dur Download disablement from ".
		$CURUSER['username'].($disable_pm ? "\n\nReason: $disable_pm" : ''));
        $modcomment = get_date($added, 'DATE', 1)." - Download disablement for $dur by ".
		$CURUSER['username'].".\nReason: $disable_pm\n".$modcomment;
        $updateset[] = "downloadpos = ".$downloadpos_until;
        $useredit['update'][] = 'Downloads disabled  = '.$downloadpos_until.'';
    }
    sql_query("INSERT INTO messages (sender, receiver, subject, msg, added) 
	             VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__file__, __line__);
   }
   //== Set upload posssible Time based
    if (isset($_POST['uploadpos']) && ($uploadpos =
    0 + $_POST['uploadpos']))
    {
    unset($updisable_pm);
    if (isset($_POST['updisable_pm']))
        $updisable_pm = $_POST['updisable_pm'];
    $subject = sqlesc('Notification!');
    $added = time();
    
    if ($uploadpos == 255)
    {
        $modcomment = get_date($added, 'DATE', 1)." - Upload disablement by ".
		$CURUSER['username'].".\nReason: $updisable_pm\n".$modcomment;
        $msg = sqlesc("Your Uploading rights have been disabled by ".$CURUSER['username'].($updisable_pm ?
            "\n\nReason: $updisable_pm" : ''));
        $updateset[] = 'uploadpos = 0';
        $useredit['update'][] = 'Uploads enabled = No';
    } elseif ($uploadpos == 42)
    {
        $modcomment = get_date($added, 'DATE', 1)." - Upload disablement status removed by ".
		$CURUSER['username'].".\n".$modcomment;
        $msg = sqlesc("Your Uploading rights have been restored by ".
		$CURUSER['username'].".");
		$updateset[] = 'uploadpos = 1';
    $useredit['update'][] = 'Uploads enabled = Yes';
    } else
    {
        $uploadpos_until = ($added + $uploadpos * 604800);
        $dur = $uploadpos.' week'.($uploadpos > 1 ? 's' : '');
        $msg = sqlesc("You have received $dur Upload disablement from ".
		$CURUSER['username'].($updisable_pm ? "\n\nReason: $updisable_pm" : ''));
        $modcomment = get_date($added, 'DATE', 1)." - Upload disablement for $dur by ".
		$CURUSER['username'].".\nReason: $updisable_pm\n".$modcomment;
        $updateset[] = "uploadpos = ".$uploadpos_until;
        $useredit['update'][] = 'Uploads disabled  = '.$uploadpos_until.'';
    }
    sql_query("INSERT INTO messages (sender, receiver, subject, msg, added) 
	          VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__file__, __line__);
   }
    //== Set Pm posssible Time based
    if (isset($_POST['sendpmpos']) && ($sendpmpos =
    0 + $_POST['sendpmpos']))
    {
    unset($pmdisable_pm);
    if (isset($_POST['pmdisable_pm']))
        $pmdisable_pm = $_POST['pmdisable_pm'];
    $subject = sqlesc('Notification!');
    $added = time();
    
    if ($sendpmpos == 255)
    {
        $modcomment = get_date($added, 'DATE', 1)." - Pm disablement by ".
		$CURUSER['username'].".\nReason: $pmdisable_pm\n".$modcomment;
        $msg = sqlesc("Your Pm rights have been disabled by ".$CURUSER['username'].($pmdisable_pm ?
            "\n\nReason: $pmdisable_pm" : ''));
        $updateset[] = 'sendpmpos = 0';
        $useredit['update'][] = 'Private messages enabled = No';
    } elseif ($sendpmpos == 42)
    {
        $modcomment = get_date($added, 'DATE', 1)." - Pm disablement status removed by ".
		$CURUSER['username'].".\n".$modcomment;
        $msg = sqlesc("Your Pm rights have been restored by ".
		$CURUSER['username'].".");
		$updateset[] = 'sendpmpos = 1';
    $useredit['update'][] = 'Private messages enabled = Yes';
    } else
    {
        $sendpmpos_until = ($added + $sendpmpos * 604800);
        $dur = $sendpmpos.' week'.($sendpmpos > 1 ? 's' : '');
        $msg = sqlesc("You have received $dur Pm disablement from ".
		$CURUSER['username'].($pmdisable_pm ? "\n\nReason: $pmdisable_pm" : ''));
        $modcomment = get_date($added, 'DATE', 1)." - Pm disablement for $dur by ".
		$CURUSER['username'].".\nReason: $pmdisable_pm\n".$modcomment;
        $updateset[] = "sendpmpos = ".$sendpmpos_until;
        $useredit['update'][] = 'Private messages disabled = '.$sendpmpos_until.'';
    }
    sql_query("INSERT INTO messages (sender, receiver, subject, msg, added) 
	             VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__file__, __line__);
   }
   //== Set shoutbox posssible Time based
    if (isset($_POST['chatpost']) && ($chatpost =
    0 + $_POST['chatpost']))
    {
    unset($chatdisable_pm);
    if (isset($_POST['chatdisable_pm']))
        $chatdisable_pm = $_POST['chatdisable_pm'];
    $subject = sqlesc('Notification!');
    $added = time();
    
    if ($chatpost == 255)
    {
        $modcomment = get_date($added, 'DATE', 1)." - Shout disablement by ".
		$CURUSER['username'].".\nReason: $chatdisable_pm\n".$modcomment;
        $msg = sqlesc("Your Shoutbox rights have been disabled by ".$CURUSER['username'].($chatdisable_pm ?
            "\n\nReason: $chatdisable_pm" : ''));
        $updateset[] = 'chatpost = 0';
        $useredit['update'][] = 'Shoutbox enabled = No';
    } elseif ($chatpost == 42)
    {
        $modcomment = get_date($added, 'DATE', 1)." - Shoutbox disablement status removed by ".
		$CURUSER['username'].".\n".$modcomment;
        $msg = sqlesc("Your Shoutbox rights have been restored by ".
		$CURUSER['username'].".");
		$updateset[] = 'chatpost = 1';
    $useredit['update'][] = 'Shoutbox enabled = Yes';
    } else
    {
        $chatpost_until = ($added + $chatpost * 604800);
        $dur = $chatpost.' week'.($chatpost > 1 ? 's' : '');
        $msg = sqlesc("You have received $dur Shoutbox disablement from ".
		$CURUSER['username'].($chatdisable_pm ? "\n\nReason: $chatdisable_pm" : ''));
        $modcomment = get_date($added, 'DATE', 1)." - Shoutbox disablement for $dur by ".
		$CURUSER['username'].".\nReason: $chatdisable_pm\n".$modcomment;
        $updateset[] = "chatpost = ".$chatpost_until;
        $useredit['update'][] = 'Shoutbox disabled = '.$chatpost_until.'';
    }
    sql_query("INSERT INTO messages (sender, receiver, subject, msg, added) 
	             VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__file__, __line__);
   }
   //== Set Immunity Status Time based
   if (isset($_POST['immunity']) && ($immunity =
   0 + $_POST['immunity']))
   {
   unset($immunity_pm);
    if (isset($_POST['immunity_pm']))
        $immunity_pm = $_POST['immunity_pm'];
    $subject = sqlesc('Notification!');
    $added = time();

    if ($immunity == 255)
    {
        $modcomment = get_date($added, 'DATE', 1)." - Immune Status enabled by ".
		$CURUSER['username'].".\nReason: $immunity_pm\n".$modcomment;
        $msg = sqlesc("You have received immunity Status from ".$CURUSER['username'].($immunity_pm ?
            "\n\nReason: $immunity_pm" : ''));
        $updateset[] = 'immunity = 1';
    } elseif ($immunity == 42)
    {
        $modcomment = get_date($added, 'DATE', 1)." - Immunity Status removed by ".
		$CURUSER['username'].".\n".$modcomment;
        $msg = sqlesc("Your Immunity Status has been removed by ".
		$CURUSER['username'].".");
		$updateset[] = 'immunity = 0';
    } else
    {
        $immunity_until = ($added + $immunity * 604800);
        $dur = $immunity.' week'.($immunity > 1 ? 's' : '');
        $msg = sqlesc("You have received $dur Immunity Status from ".
		$CURUSER['username'].($immunity_pm ? "\n\nReason: $immunity_pm" : ''));
        $modcomment = get_date($added, 'DATE', 1)." - Immunity Status for $dur by ".
		$CURUSER['username'].".\nReason: $immunity_pm\n".$modcomment;
        $updateset[] = "immunity = ".$immunity_until;
    }

    sql_query("INSERT INTO messages (sender, receiver, subject, msg, added) 
	             VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__file__, __line__);
   }
   //== Set leechwarn Status Time based
   if (isset($_POST['leechwarn']) && ($leechwarn =
   0 + $_POST['leechwarn']))
   {
   unset($leechwarn_pm);
    if (isset($_POST['leechwarn_pm']))
        $leechwarn_pm = $_POST['leechwarn_pm'];
    $subject = sqlesc('Notification!');
    $added = time();

    if ($leechwarn == 255)
    {
        $modcomment = get_date($added, 'DATE', 1)." - leechwarn Status enabled by ".
		$CURUSER['username'].".\nReason: $leechwarn_pm\n".$modcomment;
        $msg = sqlesc("You have received leechwarn Status from ".$CURUSER['username'].($leechwarn_pm ?
            "\n\nReason: $leechwarn_pm" : ''));
        $updateset[] = 'leechwarn = 1';
    } elseif ($leechwarn == 42)
    {
        $modcomment = get_date($added, 'DATE', 1)." - leechwarn Status removed by ".
		$CURUSER['username'].".\n".$modcomment;
        $msg = sqlesc("Your leechwarn Status has been removed by ".
		$CURUSER['username'].".");
		$updateset[] = 'leechwarn = 0';
    } else
    {
        $leechwarn_until = ($added + $leechwarn * 604800);
        $dur = $leechwarn.' week'.($leechwarn > 1 ? 's' : '');
        $msg = sqlesc("You have received $dur leechwarn Status from ".
		$CURUSER['username'].($leechwarn_pm ? "\n\nReason: $leechwarn_pm" : ''));
        $modcomment = get_date($added, 'DATE', 1)." - leechwarn Status for $dur by ".
		$CURUSER['username'].".\nReason: $leechwarn_pm\n".$modcomment;
        $updateset[] = "leechwarn = ".$leechwarn_until;
    }

    sql_query("INSERT INTO messages (sender, receiver, subject, msg, added) 
	             VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__file__, __line__);

   }
   //= Set warn Status Time based
   if (isset($_POST['warned']) && ($warned =
   0 + $_POST['warned']))
   {
   unset($warned_pm);
    if (isset($_POST['warned_pm']))
        $warned_pm = $_POST['warned_pm'];
    $subject = sqlesc('Notification!');
    $added = time();

    if ($warned == 255)
    {
        $modcomment = get_date($added, 'DATE', 1)." - warned Status enabled by ".
		$CURUSER['username'].".\nReason: $warned_pm\n".$modcomment;
        $msg = sqlesc("You have received warned Status from ".$CURUSER['username'].($warned_pm ?
            "\n\nReason: $warned_pm" : ''));
        $updateset[] = 'warned = 1';
    } elseif ($warned == 42)
    {
        $modcomment = get_date($added, 'DATE', 1)." - warned Status removed by ".
		$CURUSER['username'].".\n".$modcomment;
        $msg = sqlesc("Your warned Status has been removed by ".
		$CURUSER['username'].".");
		$updateset[] = 'warned = 0';
    } else
    {
        $warned_until = ($added + $warned * 604800);
        $dur = $warned.' week'.($warned > 1 ? 's' : '');
        $msg = sqlesc("You have received $dur warned Status from ".
		$CURUSER['username'].($warned_pm ? "\n\nReason: $warned_pm" : ''));
        $modcomment = get_date($added, 'DATE', 1)." - warned Status for $dur by ".
		$CURUSER['username'].".\nReason: $warned_pm\n".$modcomment;
        $updateset[] = "warned = ".$warned_until;
    }

    sql_query("INSERT INTO messages (sender, receiver, subject, msg, added) 
	             VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__file__, __line__);
   }
   //== Add remove uploaded
	  if ($CURUSER['class'] >= UC_ADMINISTRATOR) {
		$uploadtoadd = 0 + $_POST["amountup"];
		$downloadtoadd = 0 +  $_POST["amountdown"];
		$formatup = $_POST["formatup"];
		$formatdown = $_POST["formatdown"];
		$mpup = $_POST["upchange"];
		$mpdown = $_POST["downchange"];
		if($uploadtoadd > 0)	{
			if($mpup == "plus"){
				$newupload = $user["uploaded"] + ($formatup == 'mb' ? ($uploadtoadd * 1048576) : ($uploadtoadd * 1073741824));
				$modcomment = get_date( time(), 'DATE', 1 ) . " {$lang['modtask_add_upload']} (".$uploadtoadd." ".$formatup .") {$lang['modtask_by']} " . $CURUSER['username'] ."\n" . $modcomment;
			}
			else{
				$newupload = $user["uploaded"] - ($formatup == 'mb' ? ($uploadtoadd * 1048576) : ($uploadtoadd * 1073741824));
				if ($newupload >= 0)
						$modcomment =  get_date( time(), 'DATE', 1 ) . " {$lang['modtask_subtract_upload']} (".$uploadtoadd." ".$formatup .") {$lang['modtask_by']} " . $CURUSER['username'] ."\n" . $modcomment;
			}
			if ($newupload >= 0)
				$updateset[] =  "uploaded = ".sqlesc($newupload)."";
		    $useredit['update'][] = 'Uploaded total altered from '. mksize($uploadtoadd) .' to '. mksize($newupload).'';
		}
		if($downloadtoadd > 0)	 {
			if($mpdown == "plus"){
				$newdownload = $user["downloaded"] + ($formatdown == 'mb' ? ($downloadtoadd * 1048576) : ($downloadtoadd * 1073741824));
				$modcomment = get_date( time(), 'DATE', 1 ) . " {$lang['modtask_added_download']} (".$downloadtoadd." ".$formatdown .") {$lang['modtask_by']} " . $CURUSER['username'] ."\n" . $modcomment;
			}
			else{
				$newdownload = $user["downloaded"] - ($formatdown == 'mb' ? ($downloadtoadd * 1048576) : ($downloadtoadd * 1073741824));
				if ($newdownload >= 0)						
				$modcomment = get_date( time(), 'DATE', 1 ) . " {$lang['modtask_subtract_download']} (".$downloadtoadd." ".$formatdown .") {$lang['modtask_by']} " . $CURUSER['username'] ."\n" . $modcomment;
			}
			if ($newdownload >= 0)
					$updateset[] =  "downloaded = ".sqlesc($newdownload)."";
		      $useredit['update'][] = 'Downloaded total altered from '. mksize($downloadtoadd) .' to '. mksize($newdownload) .'';
		}
	}
	//== End add/remove upload
    //== Change Custom Title
    if ((isset($_POST['title'])) && (($title = $_POST['title']) != ($curtitle = $user['title'])))
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_custom_title']}'".$title."' from '".$curtitle."'{$lang['modtask_by']}" . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = "title = " . sqlesc($title);
    $useredit['update'][] = 'Custom title altered';
    }
    //== The following code will place the old passkey in the mod comment and create
    //== a new passkey. This is good practice as it allows usersearch to find old
    //== passkeys by searching the mod comments of members.
    //== Reset Passkey
    if ((isset($_POST['resetpasskey'])) && ($_POST['resetpasskey']))
    {
    $newpasskey = md5($user['username'].time().$user['passhash']);
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_passkey']}".sqlesc($user['passkey'])."{$lang['modtask_reset']}".sqlesc($newpasskey)." {$lang['modtask_by']} " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = "passkey=".sqlesc($newpasskey);
    $useredit['update'][] = 'Passkey '.sqlesc($user['passkey']).' reset to '.$newpasskey.''; 
    }
    //== seedbonus
    if ((isset($_POST['seedbonus'])) && (($seedbonus = $_POST['seedbonus']) != ($curseedbonus = $user['seedbonus'])))
    {
    $modcomment = get_date(time(), 'DATE', 1)." - Seedbonus amount changed to '".$seedbonus."' from '".$curseedbonus."' by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = 'seedbonus = '.sqlesc($seedbonus);
    $useredit['update'][] = 'Seedbonus points total adjusted';
    }
    //== Reputation
    if ((isset($_POST['reputation'])) && (($reputation = $_POST['reputation']) != ($curreputation = $user['reputation'])))
    {
    $modcomment = get_date(time(), 'DATE', 1)." - Reputation points changed to '".$reputation."' from '".$curreputation."' by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = 'reputation = '.sqlesc($reputation);
    $useredit['update'][] = 'Reputation points total adjusted';
    }
    /* This code is for use with the safe mod comment modification. If you have installed
    the safe mod comment mod, then uncomment this section...
    */
    //== Add Comment to ModComment
    if ((isset($_POST['addcomment'])) && ($addcomment = trim($_POST['addcomment'])))
    {
    $modcomment = get_date(time(), 'DATE', 1)." - ".$addcomment." - " . $CURUSER['username'] . ".\n" . $modcomment;
    } 
    //== Avatar Changed
    if ((isset($_POST['avatar'])) && (($avatar = $_POST['avatar']) != ($curavatar = $user['avatar'])))
    {
      
      $avatar = trim( urldecode( $avatar ) );
  
      if ( preg_match( "/^http:\/\/$/i", $avatar ) 
        or preg_match( "/[?&;]/", $avatar ) 
        or preg_match("#javascript:#is", $avatar ) 
        or !preg_match("#^https?://(?:[^<>*\"]+|[a-z0-9/\._\-!]+)$#iU", $avatar ) 
      )
      {
        $avatar='';
      }
      
      if( !empty($avatar) ) 
      {
        $img_size = @GetImageSize( $avatar );

        if($img_size == FALSE || !in_array($img_size['mime'], $INSTALLER09['allowed_ext']))
          stderr("{$lang['modtask_user_error']}", "{$lang['modtask_not_image']}");

        if($img_size[0] < 5 || $img_size[1] < 5)
          stderr("{$lang['modtask_user_error']}", "{$lang['modtask_image_small']}");
      
        if ( ( $img_size[0] > $INSTALLER09['av_img_width'] ) OR ( $img_size[1] > $INSTALLER09['av_img_height'] ) )
        { 
            $image = resize_image( array(
                             'max_width'  => $INSTALLER09['av_img_width'],
                             'max_height' => $INSTALLER09['av_img_height'],
                             'cur_width'  => $img_size[0],
                             'cur_height' => $img_size[1]
                        )      );
                        
          }
          else 
          {
            $image['img_width'] = $img_size[0];
            $image['img_height'] = $img_size[1];
          }
      
        $updateset[] = "av_w = " . sqlesc($image['img_width']);
        $updateset[] = "av_h = " . sqlesc($image['img_height']);
      }
      $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_avatar_change']}".htmlspecialchars($curavatar)."{$lang['modtask_to']}".htmlspecialchars($avatar)."{$lang['modtask_by']}" . $CURUSER['username'] . ".\n" . $modcomment;
      $updateset[] = "avatar = ".sqlesc($avatar);
      $useredit['update'][] = 'Avatar changed';
    }
    //== sig checks
    if ((isset($_POST['signature'])) && (($signature = $_POST['signature']) != ($cursignature = $user['signature'])))
    {
      
      $signature = trim( urldecode( $signature ) );
  
      if ( preg_match( "/^http:\/\/$/i", $signature ) 
        or preg_match( "/[?&;]/", $signature ) 
        or preg_match("#javascript:#is", $signature ) 
        or !preg_match("#^https?://(?:[^<>*\"]+|[a-z0-9/\._\-!]+)$#iU", $signature ) 
      )
      {
        $signature='';
      }
      
      if( !empty($signature) ) 
      {
        $img_size = @GetImageSize( $signature );

        if($img_size == FALSE || !in_array($img_size['mime'], $INSTALLER09['allowed_ext']))
          stderr("{$lang['modtask_user_error']}", "{$lang['modtask_not_image']}");

        if($img_size[0] < 5 || $img_size[1] < 5)
          stderr("{$lang['modtask_user_error']}", "{$lang['modtask_image_small']}");
      
        if ( ( $img_size[0] > $INSTALLER09['sig_img_width'] ) OR ( $img_size[1] > $INSTALLER09['sig_img_height'] ) )
        { 
            $image = resize_image( array(
                             'max_width'  => $INSTALLER09['sig_img_width'],
                             'max_height' => $INSTALLER09['sig_img_height'],
                             'cur_width'  => $img_size[0],
                             'cur_height' => $img_size[1]
                        )      );
                        
          }
          else 
          {
            $image['img_width'] = $img_size[0];
            $image['img_height'] = $img_size[1];
          }
      
        $updateset[] = "sig_w = " . sqlesc($image['img_width']);
        $updateset[] = "sig_h = " . sqlesc($image['img_height']);
      }
      $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_signature_change']}".htmlspecialchars($cursignature)."{$lang['modtask_to']}".htmlspecialchars($signature)."{$lang['modtask_by']}" . $CURUSER['username'] . ".\n" . $modcomment;
      $updateset[] = "signature = ".sqlesc($signature);
      $useredit['update'][] = 'Signature changed';
      }
    //==End
    //=== allow invites
     if ((isset($_POST['invite_on'])) && (($invite_on = $_POST['invite_on']) != $user['invite_on'])){  
     $modcomment = get_date( time(), 'DATE', 1 ) . " - Invites allowed changed from $user[invite_on] to $invite_on by " . $CURUSER['username'] . ".\n" . $modcomment;
     $updateset[] = "invite_on = " . sqlesc($invite_on);
     $useredit['update'][] = 'Invites enabled = '.htmlspecialchars($invite_on).'';
     }
     //== change invites
     if ((isset($_POST['invites'])) && (($invites = $_POST['invites']) != ($curinvites = $user['invites'])))
     {
     $modcomment = get_date( time(), 'DATE', 1 ) . " - Invite amount changed to '".$invites."' from '".$curinvites."' by " . $CURUSER['username'] . ".\n" . $modcomment;
     $updateset[] = "invites = " . sqlesc($invites);
     $useredit['update'][] = 'Invites total adjusted';
     }
    //== Fls Support
    if ((isset($_POST['support'])) && (($support = $_POST['support']) != $user['support']))
    {
    if ($support == 'yes')
    {
    $modcomment = get_date(time(), 'DATE', 1)." - Promoted to FLS by " . $CURUSER['username'] . ".\n" . $modcomment;
    }
    elseif ($support == 'no')
    {
    $modcomment = get_date(time(), 'DATE', 1)." - Demoted from FLS by " . $CURUSER['username'] . ".\n" . $modcomment;
    }
    else
    stderr("{$lang['modtask_user_error']}", "{$lang['modtask_try_again']}");
    $supportfor = $_POST['supportfor'];
    $updateset[] = "support = " . sqlesc($support);
    $updateset[] = "supportfor = ".sqlesc($supportfor);
    $useredit['update'][] = 'Support  = '.$support.'';
    $useredit['update'][] = 'Support  = '.$supportfor.'';
    }
    //== change freeslots
    if ((isset($_POST['freeslots'])) && (($freeslots = $_POST['freeslots']) != ($curfreeslots = $user['freeslots'])))
    {
    $modcomment = get_date(time(), 'DATE', 1)." - freeslots amount changed to '".$freeslots."' from '".$curfreeslots."' by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = 'freeslots = '.sqlesc($freeslots);
    $useredit['update'][] = 'Freeeslots total adjusted = Yes';
    }
    //== Set Freeleech Status Time based
    if (isset($_POST['free_switch']) && ($free_switch =
    0 + $_POST['free_switch']))
    {
    unset($free_pm);
    if (isset($_POST['free_pm']))
        $free_pm = $_POST['free_pm'];
    $subject = sqlesc('Notification!');
    $added = time();

    if ($free_switch == 255)
    {
        $modcomment = get_date($added, 'DATE', 1)." - Freeleech Status enabled by ".
		$CURUSER['username'].".\nReason: $free_pm\n".$modcomment;
        $msg = sqlesc("You have received Freeleech Status from ".$CURUSER['username'].($free_pm ?
            "\n\nReason: $free_pm" : ''));
        $updateset[] = 'free_switch = 1';
        $useredit['update'][] = 'Freeleech enabled = Yes';
    } elseif ($free_switch == 42)
    {
    $modcomment = get_date($added, 'DATE', 1)." - Freeleech Status removed by ".
		$CURUSER['username'].".\n".$modcomment;
    $msg = sqlesc("Your Freeleech Status has been removed by ".
		$CURUSER['username'].".");
		$updateset[] = 'free_switch = 0';
    $useredit['update'][] = 'Freeleech enabled = No';
    } else
    {
        $free_until = ($added + $free_switch * 604800);
        $dur = $free_switch.' week'.($free_switch > 1 ? 's' : '');
        $msg = sqlesc("You have received $dur Freeleech Status from ".
		$CURUSER['username'].($free_pm ? "\n\nReason: $free_pm" : ''));
        $modcomment = get_date($added, 'DATE', 1)." - Freeleech Status for $dur by ".
		$CURUSER['username'].".\nReason: $free_pm\n".$modcomment;
        $updateset[] = "free_switch = ".$free_until;
    $useredit['update'][] = 'Freeleech enabled = '.$free_until.'';
    }
    sql_query("INSERT INTO messages (sender, receiver, subject, msg, added) 
	             VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__file__, __line__);
    }
    //== Set higspeed Upload Enable / Disable
    if ((isset($_POST['highspeed'])) && (($highspeed = $_POST['highspeed']) != $user['highspeed'])) {
        if ($highspeed == 'yes') {
            $modcomment = get_date( time(), 'DATE', 1 ) . " - Highspeed Upload enabled by " . $CURUSER['username'] .".\n" . $modcomment;
            $subject = sqlesc("Highspeed uploader status.");
            $msg = sqlesc("You  have been set as a high speed uploader by  " . $CURUSER['username'] .". You can now upload torrents using highspeeds without being flagged as a cheater  .");
            $added = sqlesc(time());
            sql_query("INSERT INTO messages (sender, receiver, msg, subject, added) VALUES (0, $userid, $msg, $subject, $added)") or sqlerr(__file__, __line__);
        } elseif ($highspeed == 'no') {
            $modcomment = get_date( time(), 'DATE', 1 ) . " - Highspeed Upload disabled by " . $CURUSER['username'] .".\n" . $modcomment;
            $subject = sqlesc("Highspeed uploader status.");
            $msg = sqlesc("Your highspeed upload setting has been disabled by " . $CURUSER['username'] .". Please PM " . $CURUSER['username'] . " for the reason why.");
            $added = sqlesc(time());
            sql_query("INSERT INTO messages (sender, receiver, msg, subject, added) VALUES (0, $userid, $msg, $subject, $added)") or sqlerr(__file__, __line__);
        } 
        else
        die(); //== Error
        $updateset[] = "highspeed = " . sqlesc($highspeed);
        $useredit['update'][] = 'Highspeed uploader enabled = '.$highspeed.'';
        }
        //== Parked accounts
	      if ((isset($_POST['parked'])) && (($parked = $_POST['parked']) != $user['parked']))
	      {
	      if ($parked == 'yes')
	      {
	      $modcomment = get_date( time(), 'DATE', 1 ) . " - Account Parked by " . $CURUSER['username'] . ".\n" . $modcomment;
	      }
	      elseif ($parked == 'no')
	      {
	      $modcomment = get_date( time(), 'DATE', 1 ) . " - Account UnParked by " . $CURUSER['username'] . ".\n" . $modcomment;
	      }
	      else
	      stderr("{$lang['modtask_user_error']}", "{$lang['modtask_try_again']}");
	      $updateset[] = "parked = " . sqlesc($parked);
         $useredit['update'][] = 'Account parked = '.$parked.'';
        } 
	      //== end parked
    //=== suspend account
    if ((isset($_POST['suspended'])) && (($suspended = $_POST['suspended']) != ($suspended = $user['suspended']))){
    $suspended_reason = $_POST['suspended_reason'];
    if (!$suspended_reason)
    stderr('Error', 'You must enter a reason to suspend this account!');
    if ($_POST['suspended'] === 'yes'){
    $modcomment = get_date( time(), 'DATE', 1 ). " - This account has been suspended by " . $CURUSER['username'] . " reason: ".sqlesc($suspended_reason).".\n" . $modcomment;
    $updateset[] = "downloadpos = '0'";
    $updateset[] = "uploadpos = '0'";
    $updateset[] = "forum_post = 'no'";
    $updateset[] = "invite_on = 'no'";
    $useredit['update'][] = 'Account suspended = Yes';
    $subject = sqlesc('Account Suspended!');
    $msg = sqlesc("Your account has been suspended by " . $CURUSER['username'] . ".\n[b]The Reason:[/b]\n".sqlesc($suspended_reason).".\n\nWhile your account is suspended, your posting - uploading - downloading - commenting - invites will not work, and the only people that you can PM are staff members.\n\nIf you feel this suspension is in error, please feel free to contact a staff member. \n\ncheers,\n".$INSTALLER09['site_name']." Staff");
    //=== post to forum
    //*** you'll need Retro's auto_post() function. Also, look at the $updateset[] stuff to be sure you use them ;)
    //$body = sqlesc("Account for [b][url=".$INSTALLER09['baseurl']."/member_details.php?id=".$user["id"]."]".$user["username"]."[/url][/b] has been suspended by " . $CURUSER['username'] . "\n\n [b]reason:[/b]\n ".sqlesc($suspended_reason).".\n" );
    //auto_post( $subject , $body );
    } 

    if ($_POST['suspended'] === 'no'){
    $modcomment = get_date( time(), 'DATE', 1 ) . " - This account has been UN-suspended by " . $CURUSER['username'] . " reason: ".sqlesc($suspended_reason).".\n" . $modcomment;
    $updateset[] = "downloadpos = '1'";
    $updateset[] = "uploadpos = '1'";
    $updateset[] = "forum_post = 'yes'";
    $updateset[] = "invite_on = 'yes'";
    $useredit['update'][] = 'Account suspended = No';
    $subject = sqlesc("Account Un-Suspended!");
    $msg = sqlesc("Your account has had it's suspension lifted by " . $CURUSER['username'] . ".\n[b]The Reason:[/b]\n".sqlesc($suspended_reason).". \n\ncheers,\n".$INSTALLER09['site_name']." Staff");
    }
    $updateset[] = 'suspended = ' . sqlesc($_POST['suspended']);
    $added = time();
    sql_query("INSERT INTO messages (sender, subject, receiver, added, msg) VALUES(0, $subject, $user[id], $added, $msg)");
    }
    //=== hit and runs
    if ((isset($_POST['hit_and_run_total'])) && (($hit_and_run_total = $_POST['hit_and_run_total']) != $user['hit_and_run_total'])){
    $modcomment = get_date( time(), 'DATE', 1 ). " - Hit and runs set to $hit_and_run_total. was $user[hit_and_run_total]  by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = 'hit_and_run_total = ' . sqlesc($hit_and_run_total);
    $useredit['update'][] = 'Hit and run total adjusted = Yes'; 
    }
    //=== Forum Post Enable / Disable
    if ((isset($_POST['forum_post'])) && (($forum_post = $_POST['forum_post']) != $user['forum_post']))
    {
    if ($forum_post == 'yes')
    {
    $modcomment = get_date(time(), 'DATE', 1)." - Posting enabled by ".$CURUSER['username'].".\n" . $modcomment;
    $msg = sqlesc('Your Posting rights have been given back by '.$CURUSER['username'].'. You can post to forum again.');
    }
    else
    {
    $modcomment = get_date(time(), 'DATE', 1)." - Posting disabled by ".$CURUSER['username'].".\n" . $modcomment;
    $msg = sqlesc('Your Posting rights have been removed by '.$CURUSER['username'].', Please PM '.$CURUSER['username'].' for the reason why.');
    }
	  sql_query('INSERT INTO messages (sender, receiver, msg, subject, added) VALUES (0, '.$user['id'].', '.$msg.', \'Posting rights\', '.time().')');
    $updateset[] = 'forum_post = ' . sqlesc($forum_post);
    $useredit['update'][] = 'Forum post enabled = '.$forum_post.'';
    } 
    //=== signature rights
    if ((isset($_POST['signature_post'])) && (($signature_post = $_POST['signature_post']) != $user['signature_post']))
    {
    if ($signature_post == 'no')
    {
    $modcomment = get_date( time(), 'DATE', 1 )." - Signature rights turned off by ".$CURUSER['username'].".\n" . $modcomment;
	  $msg = sqlesc('Your Signature rights turned off by '.$CURUSER['username'].'. PM them for more information.');
    }
    else
    {
    $modcomment = get_date( time(), 'DATE', 1 )." - Signature rights turned on by ".$CURUSER['username'].".\n" . $modcomment;
	  $msg = sqlesc('Your Signature rights turned back on by '.$CURUSER['username'].'.');
    }
	  sql_query('INSERT INTO messages (sender, receiver, msg, subject, added) VALUES (0, '.$user['id'].', '.$msg.', \'Signature rights\', '.time().')');
    $updateset[] = 'signature_post = ' . sqlesc($signature_post);
    $useredit['update'][] = 'Signature post enabled = '.$signature_post.'';
    } 
    //=== avatar rights
    if ((isset($_POST['avatar_rights'])) && (($avatar_rights = $_POST['avatar_rights']) != $user['avatar_rights']))
    {
    if ($avatar_rights == 'no')
    {
    $modcomment = get_date( time(), 'DATE', 1 )." - Avatar rights turned off by ".$CURUSER['username'].".\n" . $modcomment;
    }
    else
    {
    $modcomment = get_date( time(), 'DATE', 1 )." - Avatar rights turned on by ".$CURUSER['username'].".\n" . $modcomment;
    }
    $updateset[] = 'avatar_rights = ' . sqlesc($avatar_rights);
    $useredit['update'][] = 'Avatar rights enabled = '.$avatar_rights.'';
    } 
    //=== offensive avatar
    if ((isset($_POST['offensive_avatar'])) && (($offensive_avatar = $_POST['offensive_avatar']) != $user['offensive_avatar']))
    {
    if ($offensive_avatar == 'no')
    {
    $modcomment = get_date( time(), 'DATE', 1 )." - Offensive avatar set to no by ".$CURUSER['username'].".\n" . $modcomment;
	  $msg = sqlesc('Your avatar has been set to not offensive by '.$CURUSER['username'].'.');
    }
    else
    {
    $modcomment = get_date( time(), 'DATE', 1 )." - Offensive avatar set to yes by ".$CURUSER['username'].".\n" . $modcomment;
	  $msg = sqlesc('Your avatar has been set to offensive by '.$CURUSER['username'].' PM them to ask why.');
    }
	  sql_query('INSERT INTO messages (sender, receiver, msg, subject, added) VALUES (0, '.$user['id'].', '.$msg.', \'Offensive avatar\', '.time().')');	
    $updateset[] = 'offensive_avatar = ' . sqlesc($offensive_avatar);
    $useredit['update'][] = 'Offensive avatar enabled = '.$offensive_avatar.'';
    } 
    //=== view offensive avatar
    if ((isset($_POST['view_offensive_avatar'])) && (($view_offensive_avatar = $_POST['view_offensive_avatar']) != $user['view_offensive_avatar']))
    {
    if ($view_offensive_avatar == 'no')
    {
    $modcomment = get_date( time(), 'DATE', 1 )." - View offensive avatar set to no by ".$CURUSER['username'].".\n" . $modcomment;
    }
    else
    {
    $modcomment = get_date( time(), 'DATE', 1 )." - View offensive avatar set to yes by ".$CURUSER['username'].".\n" . $modcomment;
    }
    $updateset[] = 'view_offensive_avatar = ' . sqlesc($view_offensive_avatar);
    $useredit['update'][] = 'View offensive avatar enabled = '.$view_offensive_avatar.'';
    } 
    //=== paranoia
    if ((isset($_POST['paranoia'])) && (($paranoia = $_POST['paranoia']) != $user['paranoia']))
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . " - Paranoia changed to '".intval($_POST['paranoia'])."' from '".intval($user['paranoia'])."' by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = 'paranoia = ' . sqlesc($paranoia);
    $useredit['update'][] = 'Paranoia level changed';
    }
    //=== website
    if ((isset($_POST['website'])) && (($website = $_POST['website']) != $user['website']))
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . " - website changed to '".strip_tags($_POST['website'])."' from '".htmlspecialchars($user['website'])."' by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = 'website = ' . sqlesc($website);
    $useredit['update'][] = 'Website changed';
    }
    //=== google_talk
    if ((isset($_POST['google_talk'])) && (($google_talk = $_POST['google_talk']) != $user['google_talk']))
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . " - google_talk changed to '".strip_tags($_POST['google_talk'])."' from '".htmlspecialchars($user['google_talk'])."' by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = 'google_talk = ' . sqlesc($google_talk);
    $useredit['update'][] = 'Google talk address changed';
    }
    //=== msn
    if ((isset($_POST['msn'])) && (($msn = $_POST['msn']) != $user['msn']))
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . " - msn changed to '".strip_tags($_POST['msn'])."' from '".htmlspecialchars($user['msn'])."' by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = 'msn = ' . sqlesc($msn);
    $useredit['update'][] = 'Msn address changed';
    }
    //=== aim
    if ((isset($_POST['aim'])) && (($aim = $_POST['aim']) != $user['aim']))
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . " - aim changed to '".strip_tags($_POST['aim'])."' from '".htmlspecialchars($user['aim'])."' by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = 'aim = ' . sqlesc($aim);
    $useredit['update'][] = 'AIM address changed';
    } 
    //=== yahoo
    if ((isset($_POST['yahoo'])) && (($yahoo = $_POST['yahoo']) != $user['yahoo']))
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . " - yahoo changed to '".strip_tags($_POST['yahoo'])."' from '".htmlspecialchars($user['yahoo'])."' by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = 'yahoo = ' . sqlesc($yahoo);
    $useredit['update'][] = 'Yahoo address changed';
    }
    //=== icq
    if ((isset($_POST['icq'])) && (($icq = $_POST['icq']) != $user['icq']))
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . " - icq changed to '".strip_tags($_POST['icq'])."' from '".htmlspecialchars($user['icq'])."' by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = 'icq = ' . sqlesc($icq);
    $useredit['update'][] = 'ICQ address changed';
    }
    
    //== Add ModComment... (if we changed stuff we update otherwise we dont include this..)
		if (($CURUSER['class'] == UC_MAX && ($user['modcomment'] != $_POST['modcomment'] || $modcomment != $_POST['modcomment'])) || ($CURUSER['class'] < UC_MAX && $modcomment != $user['modcomment']))
		$updateset[] = "modcomment = " . sqlesc($modcomment);
    if (sizeof($updateset)>0) 
    sql_query("UPDATE users SET " . implode(", ", $updateset) . " WHERE id=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
    status_change($userid);
    //== 09 Updated Sysop log - thanks to pdq
     write_info("User account $userid (<a href='userdetails.php?id=$userid'>$user[username]</a>)\nThings edited: ".join(', ', $useredit['update'])." by <a href='userdetails.php?id=$CURUSER[id]'>$CURUSER[username]</a>");
    //== Memcache - delete the keys
    $mc1->delete_value('inbox_new_'.$userid);
    $mc1->delete_value('inbox_new_sb_'.$userid);
    $mc1->delete_value('user'.$userid);
    $mc1->delete_value('MyUser_'.$userid);
    $returnto = $_POST["returnto"];
    header("Location: {$INSTALLER09['baseurl']}/$returnto");
    stderr("{$lang['modtask_user_error']}", "{$lang['modtask_try_again']}");
    }
    stderr("{$lang['modtask_user_error']}", "{$lang['modtask_no_idea']}");
?>