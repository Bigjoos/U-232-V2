<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
//==Start execution time
$q['start'] = microtime(true);
//==End
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
require_once(CACHE_DIR.'free_cache.php');
require_once(INCL_DIR.'function_happyhour.php');
//==Start memcache
require_once(CLASS_DIR.'class_cache.php');
$mc1 = NEW CACHE();
//==Block class
class curuser {
public static $blocks  = array();
}
$CURBLOCK = & curuser::$blocks;
require CLASS_DIR.'class_blocks_stdhead.php';
/**** validip/getip courtesy of manolete <manolete@myway.com> ****/
// IP Validation
function validip($ip)
{
	if (!empty($ip) && $ip == long2ip(ip2long($ip)))
	{
		// reserved IANA IPv4 addresses
		// http://www.iana.org/assignments/ipv4-address-space
		$reserved_ips = array (
				array('0.0.0.0','0.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
		);

		foreach ($reserved_ips as $r)
		{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}

//=== new and faster get IP function by Pandora
function getip()
{
   $ip = $_SERVER['REMOTE_ADDR'];

   if (isset($_SERVER['HTTP_VIA']))
   {
   $forwarded_for = (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? (string) $_SERVER['HTTP_X_FORWARDED_FOR'] : '';

      if ($forwarded_for != $ip)
      {
      $ip = $forwarded_for;
      $nums = sscanf($ip, '%d.%d.%d.%d');
      if ($nums[0] === null || $nums[1] === null || $nums[2] === null || $nums[3] === null || $nums[0] == 10 || ($nums[0] == 172 && $nums[1] >= 16 && $nums[1] <= 31) || ($nums[0] == 192 && $nums[1] == 168) || $nums[0] == 239 || $nums[0] == 0 || $nums[0] == 127)
      $ip = $_SERVER['REMOTE_ADDR'];
      }
   }

return $ip;
}

function dbconn($autoclean = false)
{
    global $INSTALLER09;

    if (!@mysql_connect($INSTALLER09['mysql_host'], $INSTALLER09['mysql_user'], $INSTALLER09['mysql_pass']))
    {
	  switch (mysql_errno())
	  {
		case 1040:
		case 2002:
			if ($_SERVER['REQUEST_METHOD'] == "GET")
				die("<html><head><meta http-equiv='refresh' content=\"5 $_SERVER[REQUEST_URI]\"></head><body><table border='0' width='100%' height='100%'><tr><td><h3 align='center'>The server load is very high at the moment. Retrying, please wait...</h3></td></tr></table></body></html>");
			else
				die("Too many users. Please press the Refresh button in your browser to retry.");
        default:
    	    die("[" . mysql_errno() . "] dbconn: mysql_connect: " . mysql_error());
      }
    }
    mysql_select_db($INSTALLER09['mysql_db'])
        or die('dbconn: mysql_select_db: ' . mysql_error());
    mysql_set_charset('utf8');
    userlogin();
    if ($autoclean)
        register_shutdown_function("autoclean");
}

function status_change($id) {
sql_query('UPDATE announcement_process SET status = 0 WHERE user_id = '.sqlesc($id).' AND status = 1');
}

function hashit($var,$addtext="")
{
return md5("Th15T3xt".$addtext.$var.$addtext."is5add3dto66uddy6he@water...");
}

function userlogin() {
        global $INSTALLER09, $mc1, $CURBLOCK;
        unset($GLOBALS["CURUSER"]);
        $dt = time();
        $ip = getip();
	      $nip = ip2long($ip);
        if (isset($CURUSER)) 
        return;
        require_once(INCL_DIR.'user_functions.php');
        require_once(CACHE_DIR.'bans_cache.php');
        if(count($bans) > 0)
        {
        foreach($bans as $k) {
        if($nip >= $k['first'] && $nip <= $k['last']) {
        header("HTTP/1.0 403 Forbidden");
        print "<html><body><h1>403 Forbidden</h1>Unauthorized IP address.</body></html>\n";
        exit();
        }
        }
        unset($bans);
        }

        if (!$INSTALLER09['site_online'] || !get_mycookie('uid') || !get_mycookie('pass')|| !get_mycookie('hashv') )
        return;
        $id = 0 + get_mycookie('uid');
        if (!$id OR (strlen( get_mycookie('pass') ) != 32) OR (get_mycookie('hashv') != hashit($id,get_mycookie('pass'))))
        return;
        // ==Retro's Announcement mod
        $prefix = '09skan';
        /** let's cache $CURUSER - pdq **/
        $row = $mc1->get_value('MyUser_'.$id);
        if ($row === false) { // $row not found
        $res = sql_query("SELECT ".$prefix.".*, ann_main.subject AS curr_ann_subject, ann_main.body AS curr_ann_body, s.last_status, s.last_update, s.archive FROM users AS ".$prefix." LEFT JOIN announcement_main AS ann_main " . "ON ann_main.main_id = ".$prefix.".curr_ann_id LEFT JOIN ustatus as s ON s.userid = ".$prefix.".id WHERE ".$prefix.".id = $id AND ".$prefix.".enabled='yes' AND ".$prefix.".status = 'confirmed'") or sqlerr(__FILE__, __LINE__); 
        if(mysql_num_rows($res) == 0) {
        logoutcookie();	
        return;
        }
        $row = mysql_fetch_assoc($res);
        //== Do all ints and floats
        $row['id'] = (int)$row['id'];
        $row['added'] = (int)$row['added'];
        $row['last_login'] = (int)$row['last_login'];
        $row['last_access'] = (int)$row['last_access'];
        $row['curr_ann_last_check'] = (int)$row['curr_ann_last_check'];
        $row['curr_ann_id'] = (int)$row['curr_ann_id'];
        $row['stylesheet'] = (int)$row['stylesheet'];
        $row['class'] = (int)$row['class'];
        $row['override_class']  = (int)$row['override_class'];
        $row['av_w'] = (int)$row['av_w'];
        $row['av_h'] = (int)$row['av_h'];
        $row['uploaded'] = (float)$row['uploaded'];
        $row['downloaded'] = (float)$row['downloaded'];
        $row['country'] = (int)$row['country'];
        $row['warned'] = (int)$row['warned'];
        $row['torrentsperpage'] = (int)$row['torrentsperpage'];
        $row['topicsperpage'] = (int)$row['topicsperpage'];
        $row['postsperpage'] = (int)$row['postsperpage'];
        $row['reputation'] = (int)$row['reputation'];
        $row['time_offset'] = (float)$row['time_offset'];
        $row['dst_in_use'] = (int)$row['dst_in_use'];
        $row['auto_correct_dst'] = (int)$row['auto_correct_dst'];
        $row['chatpost'] = (int)$row['chatpost'];
        $row['smile_until'] = (int)$row['smile_until'];
        $row['seedbonus'] = (float)$row['seedbonus'];
        $row['vip_until'] = (int)$row['vip_until'];
        $row['freeslots'] = (int)$row['freeslots'];
        $row['free_switch'] = (int)$row['free_switch'];
        $row['invites'] = (int)$row['invites'];
        $row['invitedby'] = (int)$row['invitedby'];
        $row['anonymous'] = $row['anonymous'];
        $row['uploadpos'] = (int)$row['uploadpos'];
        $row['forumpost'] = (int)$row['forumpost'];
        $row['downloadpos'] = (int)$row['downloadpos'];
        $row['immunity'] = (int)$row['immunity'];
        $row['leechwarn'] = (int)$row['leechwarn'];
        $row['last_browse'] = (int)$row['last_browse'];
        $row['sig_w'] = (int)$row['sig_w'];
        $row['sig_h'] = (int)$row['sig_h'];
        $row['forum_access'] = (int)$row['forum_access'];
        $row['hit_and_run_total'] = (int)$row['hit_and_run_total'];
        $row['donoruntil'] = (int)$row['donoruntil'];
        $row['donated'] = (int)$row['donated'];
        $row['total_donated'] = (float)$row['total_donated'];
        $row['vipclass_before'] = (int)$row['vipclass_before'];
        $row['passhint'] = (int)$row['passhint'];
        $row['avatarpos'] = (int)$row['avatarpos'];
        $row['sendpmpos'] = (int)$row['sendpmpos'];
        $row['invitedate'] = (int)$row['invitedate'];
        $row['anonymous_until'] = (int)$row['anonymous_until'];
        $row['pirate'] = (int)$row['pirate'];
        $row['king'] = (int)$row['king'];
        $row['ssluse'] = (int)$row['ssluse'];     
        $row['paranoia'] = (int)$row['paranoia'];
        $row['parked_until'] = (int)$row['parked_until'];
        $row['bjwins'] = (int)$row['bjwins'];
        $row['bjlosses'] = (int)$row['bjlosses'];
        $row['irctotal'] = (int)$row['irctotal'];
        $row['last_access_numb'] = (int)$row['last_access_numb'];
        $row['onlinetime'] = (int)$row['onlinetime'];
        $ratio = ($row['downloaded'] > 0 ? $row['uploaded'] / $row['downloaded'] : 0);
        $row['ratio'] = number_format($ratio, 2);
        $row['rep'] = get_reputation($row);
        $mc1->cache_value('MyUser_'.$id, $row, $INSTALLER09['expires']['curuser']); // set $Cache
        unset($res);
        }
 
        if (get_mycookie('pass') !== md5($row["passhash"].$_SERVER["REMOTE_ADDR"]))
        return;
        
        //==Allowed staff
        if ($row["class"]>=UC_STAFF){
	      $allowed_ID =  $INSTALLER09['allowed_staff']['id'];
	      if (!in_array(((int)$row["id"]),$allowed_ID,true)){
	      $msg = "Fake Account Detected: Username: ".$row["username"]." - UserID: ".$row["id"]." - UserIP : ".getip();
	      /** Demote and disable **/
        sql_query("UPDATE users SET enabled = 'no', class = 0 WHERE id =".sqlesc($row["id"])."") or sqlerr(__file__, __line__);
	      write_log($msg);
	      autoshout($msg);
	      logoutcookie();
	      }
        }
  
	      // If curr_ann_id > 0 but curr_ann_body IS NULL, then force a refresh
	      if (($row['curr_ann_id'] > 0) AND ($row['curr_ann_body'] == NULL)) {
	      $row['curr_ann_id'] = 0;
	      $row['curr_ann_last_check']	= '0';
	      }
			  // If elapsed > 10 minutes, force a announcement refresh.
			  if (($row['curr_ann_last_check'] != '0') AND ($row['curr_ann_last_check']) < (time($dt) - 600))
			  $row['curr_ann_last_check'] = '0';

 	      if (($row['curr_ann_id'] == 0) AND ($row['curr_ann_last_check'] == '0'))
 	      { // Force an immediate check...
 		    $query = sprintf('SELECT m.*,p.process_id FROM announcement_main AS m '.
 			  'LEFT JOIN announcement_process AS p ON m.main_id = p.main_id '.
 			  'AND p.user_id = %s '.
 			  'WHERE p.process_id IS NULL '.
 			  'OR p.status = 0 '.
 			  'ORDER BY m.main_id ASC '.
 			  'LIMIT 1',
 	       sqlesc($row['id']));

 	       $result = sql_query($query);

 	       if (mysql_num_rows($result))
 	       { // Main Result set exists
 	       $ann_row = mysql_fetch_assoc($result);
 	       $query = $ann_row['sql_query'];
 	       // Ensure it only selects...
 	       if (!preg_match('/\\ASELECT.+?FROM.+?WHERE.+?\\z/', $query)) die();
 	       // The following line modifies the query to only return the current user
 	       // row if the existing query matches any attributes.
 	       $query .= ' AND u.id = '.sqlesc($row['id']).' LIMIT 1';
         $result = sql_query($query);

 	       if (mysql_num_rows($result))
 	       { // Announcement valid for member
 	       $row['curr_ann_id'] = $ann_row['main_id'];

 	       // Create two row elements to hold announcement subject and body.
 	       $row['curr_ann_subject'] = $ann_row['subject'];
 	       $row['curr_ann_body'] = $ann_row['body'];

 	       // Create additional set for main UPDATE query.
 	       $add_set = ', curr_ann_id = '.sqlesc($ann_row['main_id']);
 	       $status = 2;
 	       }
 	       else
 	       {
         // Announcement not valid for member...
 	       $add_set = ', curr_ann_last_check = '.sqlesc($dt);
 	       $status = 1;
 	       }

 	       // Create or set status of process
 	       if ($ann_row['process_id'] === NULL)
 	       {
 	       // Insert Process result set status = 1 (Ignore)
 	       $query = sprintf('INSERT INTO announcement_process (main_id, '.'user_id, status) VALUES (%s, %s, %s)',sqlesc($ann_row['main_id']),sqlesc($row['id']),sqlesc($status));
 	       }
 	       else
 	       {
 	       // Update Process result set status = 2 (Read)
 	       $query = sprintf('UPDATE announcement_process SET status = %s '.'WHERE process_id = %s', sqlesc($status), sqlesc($ann_row['process_id']));
 	       }
 	       sql_query($query);
 	       }
 	       else
 	       {
         // No Main Result Set. Set last update to now...
 	       $add_set = ', curr_ann_last_check = '.sqlesc($dt);
 	       }
 	       unset($result);
 	       unset($ann_row);
 	       }

         if($row['ssluse'] > 1 && !isset($_SERVER['HTTPS']) && !defined('NO_FORCE_SSL')) {
         $INSTALLER09['baseurl'] = str_replace('http','https',$INSTALLER09['baseurl']);  
         header('Location: '.$INSTALLER09['baseurl'].$_SERVER['REQUEST_URI']); 
	       exit();		 
         } 
         //== bitwise curuser bloks by pdq
         $blocks_key = 'blocks::'.$row['id'];
         $CURBLOCK = $mc1->get_value($blocks_key);
         if ($CURBLOCK === false) {
         $c_sql = sql_query('SELECT * FROM user_blocks WHERE userid = '.$row['id']) or sqlerr(__FILE__, __LINE__);
         if (mysql_num_rows($c_sql) == 0) {
         sql_query('INSERT INTO user_blocks(userid) VALUES('.$row['id'].')');
         header('Location: index.php');
         die();
         }
         $CURBLOCK = mysql_fetch_assoc($c_sql);
         $CURBLOCK['index_page'] = (int)$CURBLOCK['index_page'];
         $CURBLOCK['global_stdhead'] = (int)$CURBLOCK['global_stdhead'];  
         $mc1->cache_value($blocks_key, $CURBLOCK, 0);
         }
         
         //== online time pdq
         $userupdate0 = 'onlinetime = onlinetime + 0';
         $new_time = TIME_NOW - $row['last_access_numb'];
         if ($new_time < 300){
         	$userupdate0 = "onlinetime = onlinetime + ".$new_time;
         }
         $userupdate1 = "last_access_numb = ".TIME_NOW;
         //end online-time
             
         $add_set = (isset($add_set))?$add_set:'';
         if ($row['ip'] !== $ip) {
         sql_query("UPDATE users SET last_access=".TIME_NOW.", $userupdate0, $userupdate1, ip=".sqlesc($ip).$add_set." WHERE id=".$row['id']);// or die(mysql_error());
         $mc1->delete_value('MyUser_'.$row['id']);
         }
         elseif (($row['last_access'] != '0') AND (($row['last_access']) < (time($dt) - 180))/** 3 mins **/) {
         sql_query("UPDATE users SET last_access=".TIME_NOW.", $userupdate0, $userupdate1, ip=".sqlesc($ip).$add_set." WHERE id=".$row['id']);// or die(mysql_error());
         $mc1->delete_value('MyUser_'.$row['id']);
         }
         //==
         if ($row['override_class'] < $row['class']) $row['class'] = $row['override_class']; // Override class and save in GLOBAL array below.
         $GLOBALS["CURUSER"] = $row;
         get_template();
         }
                 
  function autoclean()
  {
	global $INSTALLER09;
	/* Better cleanup function with db-optimization and slow clean by x0r @ tbdev.net */
	$w00p = sql_query("SELECT arg, value_u FROM avps") or sqlerr(__FILE__, __LINE__);
	while ($row = mysql_fetch_assoc($w00p))
	{
	if ($INSTALLER09['docleanup'] == 1 && $row['arg'] == "lastcleantime" && ($row['value_u'] + $INSTALLER09['autoclean_interval']) < TIME_NOW)
	{
	sql_query("UPDATE avps SET value_u = ".TIME_NOW." WHERE arg = 'lastcleantime'") or sqlerr(__FILE__, __LINE__);
  require_once(INCL_DIR.'cleanup.php');
  docleanup();
  }
	else if ($INSTALLER09['doslowcleanup'] == 1 && $row['arg'] == "lastslowcleantime" && ($row['value_u'] + $INSTALLER09['autoslowclean_interval']) < TIME_NOW)
	{
	sql_query("UPDATE avps SET value_u = ".TIME_NOW." WHERE arg = 'lastslowcleantime'") or sqlerr(__FILE__, __LINE__);
	require_once(INCL_DIR.'cleanup.php');
	doslowcleanup();
	}
	else if ($INSTALLER09['doslowleanup2'] == 1 && $row['arg'] == "lastslowcleantime2" && ($row['value_u'] + $INSTALLER09['autoslowclean_interval2']) < TIME_NOW)
	{
	sql_query("UPDATE avps SET value_u = ".TIME_NOW." WHERE arg = 'lastslowcleantime2'") or sqlerr(__FILE__, __LINE__);
	require_once(INCL_DIR.'cleanup.php');
	doslowcleanup2();
	}
	else if ($INSTALLER09['lotterycleanup'] == 1 && $row['arg'] == "lastlottocleantime" && ($row['value_u'] + $INSTALLER09['lotteryclean_interval']) < TIME_NOW)
	{
	sql_query("UPDATE avps SET value_u = ".TIME_NOW." WHERE arg = 'lastlottocleantime'") or sqlerr(__FILE__, __LINE__);
	require_once(INCL_DIR.'cleanup.php');
	dolotterycleanup();
	}
	else if ($INSTALLER09['optimizedb'] == 1 && $row['arg'] == "lastoptimizedbtime" && ($row['value_u'] + $INSTALLER09['optimizedb_interval']) < TIME_NOW)
	{
	sql_query("UPDATE avps SET value_u = ".TIME_NOW." WHERE arg = 'lastoptimizedbtime'") or sqlerr(__FILE__, __LINE__);
	require_once(INCL_DIR.'cleanup.php');
	dooptimizedb();
	}
	else if ($INSTALLER09['dobackup'] == 1 && $row['arg'] == "lastbackuptime" && ($row['value_u'] + $INSTALLER09['autobackup_interval']) < TIME_NOW)
	{
	sql_query("UPDATE avps SET value_u = ".TIME_NOW." WHERE arg = 'lastbackuptime'") or sqlerr(__FILE__, __LINE__);
	require_once(INCL_DIR.'cleanup.php');
	dobackupdb();
	}
	}
	mysql_free_result($w00p);
	return;
  }
  
  function get_template(){
	global $CURUSER, $INSTALLER09;
	if(isset($CURUSER)){
		if(file_exists(TEMPLATE_DIR."{$CURUSER['stylesheet']}/template.php")){
			require_once(TEMPLATE_DIR."{$CURUSER['stylesheet']}/template.php");
		}else{
			if(isset($INSTALLER09)){
				if(file_exists(TEMPLATE_DIR."{$INSTALLER09['stylesheet']}/template.php")){
			require_once(TEMPLATE_DIR."{$INSTALLER09['stylesheet']}/template.php");
				}else{
					echo "Sorry, Templates do not seem to be working properly and missing some code. Please report this to the programmers/owners.";
				}
			}else{
				if(file_exists(TEMPLATE_DIR."1/template.php")){
					require_once(TEMPLATE_DIR. "1/template.php");
				}else{
					echo "Sorry, Templates do not seem to be working properly and missing some code. Please report this to the programmers/owners.";
				}
			}
		}
	}else{
	if(file_exists(TEMPLATE_DIR."{$INSTALLER09['stylesheet']}/template.php")){
			require_once(TEMPLATE_DIR."{$INSTALLER09['stylesheet']}/template.php");
		}else{
			echo "Sorry, Templates do not seem to be working properly and missing some code. Please report this to the programmers/owners.";
		}
	}
	if(!function_exists("stdhead")){
		echo "stdhead function missing";
		function stdhead($title="", $message=true){
			return "<html><head><title>$title</title></head><body>";
		}
	}
	if(!function_exists("stdfoot")){
		echo "stdfoot function missing";
		function stdfoot(){
			return "</body></html>";
		}
	}
	if(!function_exists("stdmsg")){
		echo "stdmgs function missing";
		function stdmsg($title, $message){
			return "<b>".$title."</b><br />$message";
		}
	}
	if(!function_exists("StatusBar")){
		echo "StatusBar function missing";
		function StatusBar(){
			global $CURUSER, $lang;
			return "{$lang['gl_msg_welcome']}, {$CURUSER['username']}";
		}
	}
}

function unesc($x) {
    if (get_magic_quotes_gpc())
        return stripslashes($x);
    return $x;
}

function mksize($bytes)
{
	if ($bytes < 1000 * 1024)
		return number_format($bytes / 1024, 2) . " kB";
	elseif ($bytes < 1000 * 1048576)
		return number_format($bytes / 1048576, 2) . " MB";
	elseif ($bytes < 1000 * 1073741824)
		return number_format($bytes / 1073741824, 2) . " GB";
	else
		return number_format($bytes / 1099511627776, 2) . " TB";
}

function mkprettytime($s) {
    if ($s < 0)
        $s = 0;
    $t = array();
    foreach (array("60:sec","60:min","24:hour","0:day") as $x) {
        $y = explode(":", $x);
        if ($y[0] > 1) {
            $v = $s % $y[0];
            $s = floor($s / $y[0]);
        }
        else
            $v = $s;
        $t[$y[1]] = $v;
    }

    if ($t["day"])
        return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    if ($t["hour"])
        return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
        return sprintf("%d:%02d", $t["min"], $t["sec"]);
}

function mkglobal($vars) {
    if (!is_array($vars))
        $vars = explode(":", $vars);
    foreach ($vars as $v) {
        if (isset($_GET[$v]))
            $GLOBALS[$v] = unesc($_GET[$v]);
        elseif (isset($_POST[$v]))
            $GLOBALS[$v] = unesc($_POST[$v]);
        else
            return 0;
    }
    return 1;
}

function validfilename($name) {
    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}

function validemail($email) {
    return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
}

function sqlesc($x) {
    return "'".mysql_real_escape_string($x)."'";
}

function sqlwildcardesc($x) {
    return str_replace(array("%","_"), array("\\%","\\_"), mysql_real_escape_string($x));
}

function httperr($code = 404) {
    header("HTTP/1.0 404 Not found");
    echo "<h1>Not Found</h1>\n";
    echo "<p>Sorry pal :(</p>\n";
    exit();
}

function logincookie($id, $passhash, $updatedb = 1, $expires = 0x7fffffff)
{
    set_mycookie( "uid", $id, $expires );
    set_mycookie( "pass", $passhash, $expires );
    set_mycookie( "hashv", hashit($id,$passhash), $expires );
    if ($updatedb)
      @sql_query("UPDATE users SET last_login = ".TIME_NOW." WHERE id = $id");
}

function set_mycookie( $name, $value="", $expires_in=0, $sticky=1 )
    {
		global $INSTALLER09;
		
		if ( $sticky == 1 )
    {
      $expires = time() + 60*60*24*365;
    }
		else if ( $expires_in )
		{
			$expires = time() + ( $expires_in * 86400 );
		}
		else
		{
			$expires = FALSE;
		}
		
		$INSTALLER09['cookie_domain'] = $INSTALLER09['cookie_domain'] == "" ? ""  : $INSTALLER09['cookie_domain'];
    $INSTALLER09['cookie_path']   = $INSTALLER09['cookie_path']   == "" ? "/" : $INSTALLER09['cookie_path'];
      	
		if ( PHP_VERSION < 5.2 )
		{
      if ( $INSTALLER09['cookie_domain'] )
      {
        @setcookie( $INSTALLER09['cookie_prefix'].$name, $value, $expires, $INSTALLER09['cookie_path'], $INSTALLER09['cookie_domain'] . '; HttpOnly' );
      }
      else
      {
        @setcookie( $INSTALLER09['cookie_prefix'].$name, $value, $expires, $INSTALLER09['cookie_path'] );
      }
    }
    else
    {
      @setcookie( $INSTALLER09['cookie_prefix'].$name, $value, $expires, $INSTALLER09['cookie_path'], $INSTALLER09['cookie_domain'], NULL, TRUE );
    }
			
}

function get_mycookie($name) 
    {
      global $INSTALLER09;
      
    	if ( isset($_COOKIE[$INSTALLER09['cookie_prefix'].$name]) AND !empty($_COOKIE[$INSTALLER09['cookie_prefix'].$name]) )
    	{
    		return urldecode($_COOKIE[$INSTALLER09['cookie_prefix'].$name]);
    	}
    	else
    	{
    		return FALSE;
    	}
}

function logoutcookie() {
    set_mycookie('uid', '-1');
    set_mycookie('pass', '-1');
    set_mycookie('hashv', '-1');
}

function loggedinorreturn() {
    global $CURUSER, $INSTALLER09;
    if (!$CURUSER) {
        header("Location: {$INSTALLER09['baseurl']}/login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]));
        exit();
    }
}


function searchfield($s) {
    return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}

function genrelist() {
   global $mc1, $INSTALLER09;
    if (($ret = $mc1->get_value('genrelist')) == false) {
        $ret = array();
        $res = sql_query("SELECT id, image, name FROM categories ORDER BY name");
        while ($row = mysql_fetch_assoc($res))
        $ret[] = $row;
        $mc1->cache_value('genrelist', $ret, $INSTALLER09['expires']['genrelist']);
    }
    return $ret;  
}

function get_row_count($table, $suffix = "")
{
  if ($suffix)
  $suffix = " $suffix";
  ($r = sql_query("SELECT COUNT(*) FROM $table$suffix")) or die(mysql_error());
  ($a = mysql_fetch_row($r)) or die(mysql_error());
  return $a[0];
}


function stderr($heading, $text)
{
    $htmlout = stdhead();
    $htmlout .= stdmsg($heading, $text);
    $htmlout .= stdfoot();
    
    echo $htmlout;
    exit();
}
	
// Basic MySQL error handler
function sqlerr($file = '', $line = '') {
    global $INSTALLER09, $CURUSER;
    
		$the_error    = mysql_error();
		$the_error_no = mysql_errno();

    	if ( SQL_DEBUG == 0 )
    	{
			exit();
    	}
     	else if ( $INSTALLER09['sql_error_log'] AND SQL_DEBUG == 1 )
		{
			$_error_string  = "\n===================================================";
			$_error_string .= "\n Date: ". date( 'r' );
			$_error_string .= "\n Error Number: " . $the_error_no;
			$_error_string .= "\n Error: " . $the_error;
			$_error_string .= "\n IP Address: " . $_SERVER['REMOTE_ADDR'];
			$_error_string .= "\n in file ".$file." on line ".$line;
			$_error_string .= "\n URL:".$_SERVER['REQUEST_URI'];
			$_error_string .= "\n Username: {$CURUSER['username']}[{$CURUSER['id']}]";
			
			if ( $FH = @fopen( $INSTALLER09['sql_error_log'], 'a' ) )
			{
				@fwrite( $FH, $_error_string );
				@fclose( $FH );
			}
			
			echo "<html><head><title>MySQL Error</title>
					<style>P,BODY{ font-family:arial,sans-serif; font-size:11px; }</style></head><body>
		    		   <blockquote><h1>MySQL Error</h1><b>There appears to be an error with the database.</b><br />
		    		   You can try to refresh the page by clicking <a href=\"javascript:window.location=window.location;\">here</a>
				  </body></html>";
		}
		else
		{
    		$the_error = "\nSQL error: ".$the_error."\n";
	    	$the_error .= "SQL error code: ".$the_error_no."\n";
	    	$the_error .= "Date: ".date("l dS \of F Y h:i:s A");
    	
	    	$out = "<html>\n<head>\n<title>MySQL Error</title>\n
	    		   <style>P,BODY{ font-family:arial,sans-serif; font-size:11px; }</style>\n</head>\n<body>\n
	    		   <blockquote>\n<h1>MySQL Error</h1><b>There appears to be an error with the database.</b><br />
	    		   You can try to refresh the page by clicking <a href=\"javascript:window.location=window.location;\">here</a>.
	    		   <br /><br /><b>Error Returned</b><br />
	    		   <form name='mysql'><textarea rows=\"15\" cols=\"60\">".htmlentities($the_error, ENT_QUOTES)."</textarea></form><br>We apologise for any inconvenience</blockquote></body></html>";
    		   
    
	       	echo $out;
		}
		
        exit();
}
    
function get_dt_num()
{
  return gmdate("YmdHis");
}

function write_log($text)
{
  $text = sqlesc($text);
  $added = TIME_NOW;
  sql_query("INSERT INTO sitelog (added, txt) VALUES($added, $text)") or sqlerr(__FILE__, __LINE__);
}

function sql_timestamp_to_unix_timestamp($s)
{
  return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
}

function unixstamp_to_human( $unix=0 )
    {
    	$offset = get_time_offset();
    	$tmp    = gmdate( 'j,n,Y,G,i', $unix + $offset );
    	
    	list( $day, $month, $year, $hour, $min ) = explode( ',', $tmp );
  
    	return array( 'day'    => $day,
                    'month'  => $month,
                    'year'   => $year,
                    'hour'   => $hour,
                    'minute' => $min );
    }
    
function get_time_offset() {
    
    	global $CURUSER, $INSTALLER09;
    	$r = 0;
    	
    	$r = ( ($CURUSER['time_offset'] != "") ? $CURUSER['time_offset'] : $INSTALLER09['time_offset'] ) * 3600;
			
      if ( $INSTALLER09['time_adjust'] )
      {
        $r += ($INSTALLER09['time_adjust'] * 60);
      }
      
      if ( $CURUSER['dst_in_use'] )
      {
        $r += 3600;
      }
        
        return $r;
}
    
function get_date($date, $method, $norelative=0, $full_relative=0)
    {
        global $INSTALLER09;
        
        static $offset_set = 0;
        static $today_time = 0;
        static $yesterday_time = 0;
        $time_options = array( 
        'JOINED' => $INSTALLER09['time_joined'],
        'SHORT'  => $INSTALLER09['time_short'],
				'LONG'   => $INSTALLER09['time_long'],
				'TINY'   => $INSTALLER09['time_tiny'] ? $INSTALLER09['time_tiny'] : 'j M Y - G:i',
				'DATE'   => $INSTALLER09['time_date'] ? $INSTALLER09['time_date'] : 'j M Y'
				);
        
        if ( ! $date )
        {
            return '--';
        }
        
        if ( empty($method) )
        {
        	$method = 'LONG';
        }
        
        if ($offset_set == 0)
        {
        	$GLOBALS['offset'] = get_time_offset();
			
          if ( $INSTALLER09['time_use_relative'] )
          {
            $today_time     = gmdate('d,m,Y', ( time() + $GLOBALS['offset']) );
            $yesterday_time = gmdate('d,m,Y', ( (time() - 86400) + $GLOBALS['offset']) );
          }	
        
          $offset_set = 1;
        }
        
        if ( $INSTALLER09['time_use_relative'] == 3 )
        {
        	$full_relative = 1;
        }
        
        if ( $full_relative and ( $norelative != 1 ) )
        {
          $diff = time() - $date;
          
          if ( $diff < 3600 )
          {
            if ( $diff < 120 )
            {
              return '< 1 minute ago';
            }
            else
            {
              return sprintf( '%s minutes ago', intval($diff / 60) );
            }
          }
          else if ( $diff < 7200 )
          {
            return '< 1 hour ago';
          }
          else if ( $diff < 86400 )
          {
            return sprintf( '%s hours ago', intval($diff / 3600) );
          }
          else if ( $diff < 172800 )
          {
            return '< 1 day ago';
          }
          else if ( $diff < 604800 )
          {
            return sprintf( '%s days ago', intval($diff / 86400) );
          }
          else if ( $diff < 1209600 )
          {
            return '< 1 week ago';
          }
          else if ( $diff < 3024000 )
          {
            return sprintf( '%s weeks ago', intval($diff / 604900) );
          }
          else
          {
            return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
          }
        }
        else if ( $INSTALLER09['time_use_relative'] and ( $norelative != 1 ) )
        {
          $this_time = gmdate('d,m,Y', ($date + $GLOBALS['offset']) );
          
          if ( $INSTALLER09['time_use_relative'] == 2 )
          {
            $diff = time() - $date;
          
            if ( $diff < 3600 )
            {
              if ( $diff < 120 )
              {
                return '< 1 minute ago';
              }
              else
              {
                return sprintf( '%s minutes ago', intval($diff / 60) );
              }
            }
          }
          
            if ( $this_time == $today_time )
            {
              return str_replace( '{--}', 'Today', gmdate($INSTALLER09['time_use_relative_format'], ($date + $GLOBALS['offset']) ) );
            }
            else if  ( $this_time == $yesterday_time )
            {
              return str_replace( '{--}', 'Yesterday', gmdate($INSTALLER09['time_use_relative_format'], ($date + $GLOBALS['offset']) ) );
            }
            else
            {
              return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
            }
        }
        else
        {
          return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
        }
}

function ratingpic($num) {
    global $INSTALLER09;
    $r = round($num * 2) / 2;
    if ($r < 1 || $r > 5)
        return;
    return "<img src=\"{$INSTALLER09['pic_base_url']}ratings/{$r}.gif\" border=\"0\" alt=\"Rating: $num / 5\" title=\"Rating: $num / 5\" />";
}

function hash_pad($hash) {
    return str_pad($hash, 20);
}

function CutName ($txt, $len=45){
return (strlen($txt)>$len ? substr($txt,0,$len-1) .'...':$txt);
}

    function load_language($file='') {
    global $INSTALLER09;
    if( !isset($GLOBALS['CURUSER']) OR empty($GLOBALS['CURUSER']['language']) )
    {
    if( !file_exists(LANG_DIR."lang_{$file}.php") )
    {
    stderr('SYSTEM ERROR', 'Can\'t find language files');
    }   
    require_once(LANG_DIR."lang_{$file}.php");
    return $lang;
    }
    if( !file_exists(LANG_DIR."lang_{$file}.php") )
    {
    stderr('SYSTEM ERROR', 'Can\'t find language files');
    }
    else
    {
    require_once LANG_DIR."lang_{$file}.php"; 
    }   
    return $lang;
}

function flood_limit($table) {
global $CURUSER,$INSTALLER09,$lang;
	if(!file_exists($INSTALLER09['flood_file']) || !is_array($max = unserialize(file_get_contents($INSTALLER09['flood_file']))))
		return;
	if(!isset($max[$CURUSER['class']]))
	return;
	$tb = array('posts'=>'posts.userid','comments'=>'comments.user','messages'=>'messages.sender');
	$q = sql_query('SELECT min('.$table.'.added) as first_post, count('.$table.'.id) as how_many FROM '.$table.' WHERE '.$tb[$table].' = '.$CURUSER['id'].' AND '.time().' - '.$table.'.added < '.$INSTALLER09['flood_time']);
	$a = mysql_fetch_assoc($q);
	if($a['how_many'] > $max[$CURUSER['class']])
  stderr($lang['gl_sorry'] ,$lang['gl_flood_msg'].''.mkprettytime($INSTALLER09['flood_time'] - (time() - $a['first_post'])));
}

//==Sql query count
$q['query_stat'] = 0;
$q['querytime'] = 0;
function sql_query($query) {
    global $queries, $q, $querytime, $query_stat;
	  $q = isset($q) && is_array($q) ? $q : array();
	  $q['query_stat']= isset($q['query_stat']) && is_array($q['query_stat']) ? $q['query_stat'] : array();
    $queries++;
    $query_start_time  = microtime(true); // Start time
    $result            = mysql_query($query);
    $query_end_time    = microtime(true); // End time
    $query_time        = ($query_end_time - $query_start_time);
    $querytime = $querytime + $query_time;
    $q['querytime']    = (isset($q['querytime']) ? $q['querytime'] : 0) + $query_time;
    $query_time        = substr($query_time, 0, 8);
    $q['query_stat'][] = array('seconds' => $query_time, 'query' => $query);
    return $result;
    }
    
    if (file_exists("install/index.php")){
    $HTMLOUT='';
    $HTMLOUT .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
    <html xmlns='http://www.w3.org/1999/xhtml'>
    <head>
    <title>Warning</title>
    </head>
    <body><div style='font-size:33px;color:white;background-color:red;text-align:center;'>Delete the install directory</div></body></html>";
    print $HTMLOUT;
    exit();
    }
?>