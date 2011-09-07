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
|   $Date$
|   $Revision$
|   $Author$ x0r,Bigjoos/pdq
|   $URL$
+------------------------------------------------
*/
require_once(INCL_DIR.'bittorrent.php');

function deadtime() {
    global $INSTALLER09;
    return time() - floor($INSTALLER09['announce_interval'] * 1.3);
}

function docleanup() {
	global $INSTALLER09, $queries, $mc1;
   set_time_limit(1200);
   $result = sql_query("show processlist") or sqlerr(__FILE__, __LINE__);
   while ($row = mysql_fetch_array($result)) {
   if (($row["Time"] > 100) || ($row["Command"] == "Sleep")) {
   $sql = "kill " . $row["Id"] . "";
   sql_query($sql) or sqlerr(__FILE__, __LINE__);
   }
   }
   ignore_user_abort(1);
	do {
		$res = sql_query("SELECT id FROM torrents");
		$ar = array();
		while ($row = mysql_fetch_array($res,MYSQL_NUM)) {
			$id = $row[0];
			$ar[$id] = 1;
		}

		if (!count($ar))
			break;

		$dp = opendir($INSTALLER09['torrent_dir']);
		if (!$dp)
			break;

		$ar2 = array();
		while (($file = readdir($dp)) !== false) {
			if (!preg_match('/^(\d+)\.torrent$/', $file, $m))
				continue;
			$id = $m[1];
			$ar2[$id] = 1;
			if (isset($ar[$id]) && $ar[$id])
				continue;
			$ff = $INSTALLER09['torrent_dir'] . "/$file";
			unlink($ff);
		}
		closedir($dp);

		if (!count($ar2))
			break;

		$delids = array();
		foreach (array_keys($ar) as $k) {
			if (isset($ar2[$k]) && $ar2[$k])
				continue;
			$delids[] = $k;
			unset($ar[$k]);
		}
		if (count($delids))
			sql_query("DELETE FROM torrents WHERE id IN (" . join(",", $delids) . ")");

		$res = sql_query("SELECT torrent FROM peers GROUP BY torrent");
		$delids = array();
		while ($row = mysql_fetch_array($res,MYSQL_NUM)) {
			$id = $row[0];
			if (isset($ar[$id]) && $ar[$id])
				continue;
			$delids[] = $id;
		}
		if (count($delids))
			sql_query("DELETE FROM peers WHERE torrent IN (" . join(",", $delids) . ")");

		$res = sql_query("SELECT torrent FROM files GROUP BY torrent");
		$delids = array();
		while ($row = mysql_fetch_array($res,MYSQL_NUM)) {
			$id = $row[0];
			if (isset($ar[$id]) && $ar[$id])
				continue;
			$delids[] = $id;
		}
		if (count($delids))
			sql_query("DELETE FROM files WHERE torrent IN (" . join(",", $delids) . ")");
	} while (0);

	$deadtime = deadtime();
	sql_query("DELETE FROM peers WHERE last_action < $deadtime");

	$deadtime = TIME_NOW -$INSTALLER09['max_dead_torrent_time'];
	sql_query("UPDATE torrents SET visible='no' WHERE visible='yes' AND last_action < $deadtime");

	$deadtime = TIME_NOW - $INSTALLER09['signup_timeout'];
	sql_query("DELETE FROM users WHERE status = 'pending' AND added < $deadtime AND last_login < $deadtime AND last_access < $deadtime");

	/** sync torrent counts - pdq **/
  $tsql = 'SELECT t.id, t.seeders, (
  SELECT COUNT(*)
  FROM peers
  WHERE torrent = t.id AND seeder = "yes"
) AS seeders_num,
t.leechers, (
  SELECT COUNT(*)
  FROM peers
  WHERE torrent = t.id
  AND seeder = "no"
) AS leechers_num,
t.comments, (
  SELECT COUNT(*)
  FROM comments
  WHERE torrent = t.id
) AS comments_num
FROM torrents AS t
ORDER BY t.id ASC';

$updatetorrents = array();

$tq = sql_query($tsql);
	while ($t = mysql_fetch_assoc($tq)) {
 
    if ($t['seeders'] != $t['seeders_num'] || $t['leechers'] != $t['leechers_num'] || $t['comments'] != $t['comments_num'])
        $updatetorrents[] = '('.$t['id'].', '.$t['seeders_num'].', '.$t['leechers_num'].', '.$t['comments_num'].')';
}
mysql_free_result($tq);

if (count($updatetorrents))
    sql_query('INSERT INTO torrents (id, seeders, leechers, comments) VALUES '.implode(', ', $updatetorrents).
        ' ON DUPLICATE KEY UPDATE seeders = VALUES(seeders), leechers = VALUES(leechers), comments = VALUES(comments)');
unset($updatetorrents);
  //=== Update karma seeding bonus... made nicer by devinkray :D
      //==   Updated and optimized by pdq :)
      //=== Using this will work for multiple torrents UP TO 5!... change the 5 to whatever... 1 to give the karma for only 1 torrent at a time, or 100 to make it unlimited (almost) your choice :P
      ///====== Seeding bonus per torrent
      $res = sql_query('SELECT COUNT(torrent) As tcount, userid, seedbonus FROM peers LEFT JOIN users ON users.id = userid WHERE seeder = "yes" AND connectable = "yes" GROUP BY userid') or sqlerr(__FILE__, __LINE__);
      if (mysql_num_rows($res) > 0) {
        while ($arr = mysql_fetch_assoc($res)) {
            if ($arr['tcount'] >= 1000)
                $arr['tcount'] = 5;
            $users_buffer[] = '(' . $arr['userid'] . ',0.225 * ' . $arr['tcount'] . ')';
            $update['seedbonus'] = ($arr['seedbonus']+0.225*$arr['tcount']);
            $mc1->begin_transaction('MyUser_'.$arr['userid']);
				    $mc1->update_row(false, array('seedbonus' => $update['seedbonus']));
		        $mc1->commit_transaction(900);
		        $mc1->begin_transaction('user'.$arr['userid']);
				    $mc1->update_row(false, array('seedbonus' => $update['seedbonus']));
		        $mc1->commit_transaction(900);
		        
        }
        $count = count($users_buffer);
		    if ($count > 0){
        sql_query("INSERT INTO users (id,seedbonus) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE seedbonus=seedbonus+values(seedbonus)") or sqlerr(__FILE__, __LINE__);
        write_log("Cleanup - ".$count." users received seedbonus");
        }
        unset ($users_buffer, $update, $count);
    }
  //== End
   //==Irc idle mod - pdq 
    $res = sql_query("SELECT id, seedbonus, irctotal FROM users WHERE onirc = 'yes'") or sqlerr(__FILE__, __LINE__);	
		if (mysql_num_rows($res) > 0)
		{
				while ($arr = mysql_fetch_assoc($res))
				{
			  $users_buffer[] = '('.$arr['id'].',0.225,'.$INSTALLER09['autoclean_interval'].')'; // .250 karma
				//$users_buffer[] = '('.$arr['id'].',15728640,'.$INSTALLER09['autoclean_interval'].')'; // 15 mb						
				$update['seedbonus'] = ($arr['seedbonus']+0.225);
        $update['irctotal'] = ($arr['irctotal']+$INSTALLER09['autoclean_interval']);
				$mc1->begin_transaction('user'.$arr['id']);
				$mc1->update_row(false, array('seedbonus' => $update['seedbonus'], 'irctotal' => $update['irctotal']));
		    $mc1->commit_transaction(900);
		    $mc1->begin_transaction('MyUser_'.$arr['id']);
				$mc1->update_row(false, array('seedbonus' => $update['seedbonus']));
		    $mc1->commit_transaction(900);
				}
				$count = count($users_buffer);
		    if ($count > 0){
				sql_query("INSERT INTO users (id,seedbonus,irctotal) VALUES ".implode(', ',$users_buffer)." ON DUPLICATE key UPDATE seedbonus=seedbonus+values(seedbonus),irctotal=irctotal+values(irctotal)") or sqlerr(__FILE__,__LINE__);
			  //sql_query("INSERT INTO users (id,uploaded,irctotal) VALUES ".implode(', ',$users_buffer)." ON DUPLICATE key UPDATE uploaded=uploaded+values(uploaded),irctotal=irctotal+values(irctotal)") or sqlerr(__FILE__,__LINE__);
				write_log("Cleanup ".$count." users idling on IRC");
				}
				unset ($users_buffer, $update, $count);
		}
   //== End
   //== 09 Stats
   $registered = get_row_count('users');
   $unverified = get_row_count('users', "WHERE status='pending'");
   $torrents = get_row_count('torrents');
   $seeders = get_row_count('peers', "WHERE seeder='yes'");
   $leechers = get_row_count('peers', "WHERE seeder='no'");
   $torrentstoday = get_row_count('torrents', 'WHERE added > '.time().' - 86400'); 
   $donors = get_row_count('users', "WHERE donor ='yes'");
   $unconnectables = get_row_count("peers", " WHERE connectable='no'");
   $forumposts = get_row_count("posts");
   $forumtopics = get_row_count("topics");
   $dt = sqlesc(time() - 300); // Active users last 5 minutes
   $numactive = get_row_count("users", "WHERE last_access >= $dt");
   $torrentsmonth = get_row_count('torrents', 'WHERE added > '.time().' - 2592000'); 
   $gender_na= get_row_count('users', "WHERE gender='N/A'");
   $gender_male= get_row_count('users', "WHERE gender='Male'");
   $gender_female= get_row_count('users', "WHERE gender='Female'");
   $powerusers = get_row_count('users', "WHERE class='1'");
   $disabled = get_row_count('users', "WHERE enabled='no'");
   $uploaders = get_row_count('users', "WHERE class='3'");
   $moderators = get_row_count('users', "WHERE class='4'");
   $administrators = get_row_count('users', "WHERE class='5'");
   $sysops = get_row_count('users', "WHERE class='6'");
   sql_query("UPDATE stats SET regusers = '$registered', unconusers = '$unverified', torrents = '$torrents', seeders = '$seeders', leechers = '$leechers', unconnectables = '$unconnectables', torrentstoday = '$torrentstoday', donors = '$donors', forumposts = '$forumposts', forumtopics = '$forumtopics', numactive = '$numactive', torrentsmonth = '$torrentsmonth', gender_na = '$gender_na', gender_male = '$gender_male', gender_female = '$gender_female', powerusers = '$powerusers', disabled = '$disabled', uploaders = '$uploaders', moderators = '$moderators', administrators = '$administrators', sysops = '$sysops' WHERE id = '1' LIMIT 1");
   //=== delete from now viewing after 15 minutes
	 sql_query('DELETE FROM now_viewing WHERE added < '.(time() - 900));
   //=== fix any messed up counts
	 $forums = sql_query('SELECT f.id, count( DISTINCT t.id ) AS topics, count(p.id) AS posts
                          FROM forums f
                          LEFT JOIN topics t ON f.id = t.forum_id
                          LEFT JOIN posts p ON t.id = p.topic_id
                          GROUP BY f.id');
	 while ($forum = mysql_fetch_assoc($forums))
	 {
	 $forum['posts'] = $forum['topics'] > 0 ? $forum['posts'] : 0;
	 sql_query('update forums set post_count = '.$forum['posts'].', topic_count = '.$forum['topics'].' where id='.$forum['id']);
	 }
   write_log("Autoclean-------------------- Auto cleanup Complete using $queries queries --------------------");
   }
    
  function doslowcleanup()
  {
  global $INSTALLER09, $queries, $mc1;
  set_time_limit(1200);
  $result = sql_query("show processlist") or sqlerr(__FILE__, __LINE__);
  while ($row = mysql_fetch_array($result)) {
  if (($row["Time"] > 100) || ($row["Command"] == "Sleep")) {
  $sql = "kill " . $row["Id"] . "";
  sql_query($sql) or sqlerr(__FILE__, __LINE__);
  }
  }
  ignore_user_abort(1);
  //== Delete expired announcements and processors
  sql_query("DELETE announcement_process FROM announcement_process LEFT JOIN users ON announcement_process.user_id = users.id WHERE users.id IS NULL");
  sql_query("DELETE FROM announcement_main WHERE expires < ".sqlesc(time()));
  sql_query("DELETE announcement_process FROM announcement_process LEFT JOIN announcement_main ON announcement_process.main_id = announcement_main.main_id WHERE announcement_main.main_id IS NULL");
  // Remove expired readposts...
  $dt = time() - $INSTALLER09["readpost_expiry"];
  sql_query("DELETE readposts FROM readposts "."LEFT JOIN posts ON readposts.lastpostread = posts.id "."WHERE posts.added < $dt");
  //==Putyns HappyHour
  $f = $INSTALLER09['happyhour'];
  $happy = unserialize(file_get_contents($f));
  $happyHour = strtotime($happy["time"]);
  $curDate = time();
  $happyEnd = $happyHour + 3600;
  if ($happy["status"] == 0) {
  write_log("Happy hour was @ " . get_date($happyHour, 'LONG',1,0) . " and Catid " . $happy["catid"] . " ");
  happyFile("set");
  } elseif (($curDate > $happyEnd) && $happy["status"] == 1)
  happyFile("reset");
  //== End 
  //=== Updated remove custom smilies by Bigjoos/pdq:)
    $res = sql_query("SELECT id, modcomment FROM users WHERE smile_until < ". TIME_NOW ." AND smile_until <> '0'") or sqlerr(__FILE__, __LINE__);
    $msgs_buffer = $users_buffer = array();
    if (mysql_num_rows($res) > 0) {
        $subject = "Custom smilies expired.";
        $msg = "Your Custom smilies have timed out and has been auto-removed by the system. If you would like to have them again, exchange some Karma Bonus Points again. Cheers!\n";
        while ($arr = mysql_fetch_assoc($res)) {
            $modcomment = $arr['modcomment'];
            $modcomment =  get_date( time(), 'DATE', 1 ) . " - Custom smilies Automatically Removed By System.\n". $modcomment;
            $modcom =  sqlesc($modcomment);
            $msgs_buffer[] = '(0,' . $arr['id'] . ','. TIME_NOW .', ' . sqlesc($msg) . ', ' . sqlesc($subject) . ' )';
            $users_buffer[] = '(' . $arr['id'] . ', \'0\', ' . $modcom . ')';
            $mc1->begin_transaction('user'.$arr['id']);
            $mc1->update_row(false, array('smile_until' => 0, 'modcomment' => $modcomment));
		        $mc1->commit_transaction(900);
		        $mc1->begin_transaction('MyUser_'.$arr['id']);
            $mc1->update_row(false, array('smile_until' => 0));
		        $mc1->commit_transaction(900);
        }
        $count = count($users_buffer);
            if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, smile_until, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE smile_until=values(smile_until),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
        write_log("Cleanup - Removed Custom smilies from ".$count." members");
        }
        unset ($users_buffer, $msgs_buffer, $count);
    }
    //=== Updated remove karma vip by Bigjoos/pdq - change class number '1' in the users_buffer and $update[class'] to whatever is under your vip class number
    $res = sql_query("SELECT id, modcomment FROM users WHERE vip_added='yes' AND vip_until < ".TIME_NOW."") or sqlerr(__FILE__, __LINE__);
    $msgs_buffer = $users_buffer = array();
    if (mysql_num_rows($res) > 0) {
         $subject = "VIP status expired.";
         $msg = "Your VIP status has timed out and has been auto-removed by the system. Become a VIP again by donating to {$INSTALLER09['site_name']} , or exchanging some Karma Bonus Points. Cheers !\n";
         while ($arr = mysql_fetch_assoc($res)) {
            $modcomment = $arr['modcomment'];
            $modcomment =  get_date( time(), 'DATE', 1 ) . " - Vip status Automatically Removed By System.\n". $modcomment;
            $modcom =  sqlesc($modcomment);
            $msgs_buffer[] = '(0,' . $arr['id'] . ','. TIME_NOW .', ' . sqlesc($msg) . ', ' . sqlesc($subject) . ')';
            $users_buffer[] = '(' . $arr['id'] . ',1, \'no\', \'0\' , ' . $modcom . ')';
            $mc1->begin_transaction('user'.$arr['id']);
            $mc1->update_row(false, array('class' => 1, 'vip_added' => 'no', 'vip_until' => 0, 'modcomment' => $modcomment));
            $mc1->commit_transaction(900);
            $mc1->begin_transaction('MyUser_'.$arr['id']);
            $mc1->update_row(false, array('class' => 1, 'vip_added' => 'no', 'vip_until' => 0));
            $mc1->commit_transaction(900);
        }
        $count = count($users_buffer);
        if ($count > 0){
        sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
        sql_query("INSERT INTO users (id, class, vip_added, vip_until, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE class=values(class),vip_added=values(vip_added),vip_until=values(vip_until),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
        write_log("Cleanup - Karma Vip status expired on - ".$count." Member(s)");
        }
        unset ($users_buffer, $msgs_buffer, $count);
        status_change($arr['id']); //== For Retros announcement mod
    }
  //=== Anonymous profile by Bigjoos/pdq:)
    $res = sql_query("SELECT id, modcomment FROM users WHERE anonymous_until < ". TIME_NOW ." AND anonymous_until <> '0'") or sqlerr(__FILE__, __LINE__);
    $msgs_buffer = $users_buffer = array();
    if (mysql_num_rows($res) > 0) {
        $subject = "Anonymous profile expired.";
        $msg = "Your Anonymous profile has timed out and has been auto-removed by the system. If you would like to have it again, exchange some Karma Bonus Points again. Cheers!\n";
        while ($arr = mysql_fetch_assoc($res)) {
            $modcomment = $arr['modcomment'];
            $modcomment =  get_date( time(), 'DATE', 1 ) . " - Anonymous profile Automatically Removed By System.\n". $modcomment;
            $modcom =  sqlesc($modcomment);
            $msgs_buffer[] = '(0,' . $arr['id'] . ','. TIME_NOW .', ' . sqlesc($msg) . ', ' . sqlesc($subject) . ' )';
            $users_buffer[] = '(' . $arr['id'] . ', \'0\', \'no\', ' . $modcom . ')';
            $mc1->begin_transaction('user'.$arr['id']);
            $mc1->update_row(false, array('anonymous_until' => 0, 'anonymous' => 'no', 'modcomment' => $modcomment));
            $mc1->commit_transaction(900);
            $mc1->begin_transaction('MyUser_'.$arr['id']);
            $mc1->update_row(false, array('anonymous_until' => 0, 'anonymous' => 'no'));
            $mc1->commit_transaction(900);
        }
        $count = count($users_buffer);
        if ($count > 0){
        sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
        sql_query("INSERT INTO users (id, anonymous_until, anonymous, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE anonymous_until=values(anonymous_until),anonymous=values(anonymous), modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
        write_log("Cleanup - Removed Anonymous profile from ".$count." members");
        }
        unset ($users_buffer, $msgs_buffer, $count);
    }
    //==End
	 //==delete torrents by putyn
	$days = 30;
	$dt = (TIME_NOW - ($days * 86400));
	$res = sql_query("SELECT id, name FROM torrents WHERE added < $dt AND seeders='0' AND leechers='0'");
	while ($arr = mysql_fetch_assoc($res))
	{
		sql_query("DELETE peers.*, files.*,comments.*,snatched.*, thanks.*, bookmarks.*, coins.*, ratings.*, torrents.* FROM torrents 
				 LEFT JOIN peers ON peers.torrent = torrents.id
				 LEFT JOIN files ON files.torrent = torrents.id
				 LEFT JOIN comments ON comments.torrent = torrents.id
				 LEFT JOIN thanks ON thanks.torrentid = torrents.id
				 LEFT JOIN bookmarks ON bookmarks.torrentid = torrents.id
				 LEFT JOIN coins ON coins.torrentid = torrents.id
				 LEFT JOIN ratings ON ratings.torrent = torrents.id
				 LEFT JOIN snatched ON snatched.torrentid = torrents.id
				 WHERE torrents.id = {$arr['id']}") or sqlerr(__FILE__, __LINE__);
				 @unlink("{$INSTALLER09['torrent_dir']}/{$arr['id']}.torrent");
		write_log("Torrent {$arr['id']} ({$arr['name']}) was deleted by system (older than $days days and no seeders)");
	}
	// ===Clear funds after one month
    $secs = 30 * 86400;
    $dt = sqlesc(time() - $secs);
    sql_query("DELETE FROM funds WHERE added < $dt");
    $mc1->delete_value('totalfunds_');
    // ===End
    //== Donation Progress Mod Updated For Tbdev 2009/2010 by Bigjoos/pdq
    $res = sql_query("SELECT id, modcomment, vipclass_before FROM users WHERE donor='yes' AND donoruntil < ". TIME_NOW ." AND donoruntil <> '0'") or sqlerr(__FILE__, __LINE__);
    $msgs_buffer = $users_buffer = array();
    if (mysql_num_rows($res) > 0) {
        $subject = "Donor status removed by system.";
        $msg = "Your Donor status has timed out and has been auto-removed by the system, and your Vip status has been removed. We would like to thank you once again for your support to {$INSTALLER09['site_name']}. If you wish to re-new your donation, Visit the site paypal link. Cheers!\n";
        while ($arr = mysql_fetch_assoc($res)) {
            $modcomment = $arr['modcomment'];
            $modcomment =  get_date( time(), 'DATE', 1 ) . " - Donation status Automatically Removed By System.\n". $modcomment;
            $modcom =  sqlesc($modcomment);
            $msgs_buffer[] = '(0,' . $arr['id'] . ','. TIME_NOW .', ' . sqlesc($msg) . ',' . sqlesc($subject) . ')';
            $users_buffer[] = '(' . $arr['id'] . ','.$arr['vipclass_before'].',\'no\',\'0\', ' . $modcom . ')';
            $update['class'] = ($arr['vipclass_before']);
            $mc1->begin_transaction('user'.$arr['id']);
            $mc1->update_row(false, array('class' => $update['class'], 'donor' => 'no', 'donor_until' => 0, 'modcomment' => $modcomment));
            $mc1->commit_transaction(900);
            $mc1->begin_transaction('MyUser_'.$arr['id']);
            $mc1->update_row(false, array('class' => $update['class'], 'donor' => 'no', 'donor_until' => 0));
            $mc1->commit_transaction(900);
        }
        $count = count($users_buffer);
        if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, class, donor, donoruntil, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE class=values(class),
            donor=values(donor),donoruntil=values(donoruntil),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Cleanup: Donation status expired - ".$count." Member(s)");
        }
        unset ($users_buffer, $msgs_buffer, $update, $count);
    }
    //===End===//
	  //== 09 Auto leech warn by Bigjoos/pdq
    //== Updated/modified autoleech warning script 
    $minratio = 0.3; // ratio < 0.4
    $downloaded = 10 * 1024 * 1024 * 1024; // + 10 GB
    $length = 3 * 7; // Give 3 weeks to let them sort there shit
    $res = sql_query("SELECT id, modcomment FROM users WHERE enabled='yes' AND class = ".UC_USER." AND leechwarn = '0' AND uploaded / downloaded < $minratio AND downloaded >= $downloaded AND immunity = '0'") or sqlerr(__FILE__, __LINE__);
    $msgs_buffer = $users_buffer = array();
    if (mysql_num_rows($res) > 0) {
        $dt = sqlesc(time());
        $subject = "Auto leech warned";
        $msg = "You have been warned and your download rights have been removed due to your low ratio. You need to get a ratio of 0.5 within the next 3 weeks or your Account will be disabled.";
        $leechwarn = TIME_NOW + ($length * 86400);
        while ($arr = mysql_fetch_assoc($res)) {
            $modcomment = $arr['modcomment'];
            $modcomment =  get_date( time(), 'DATE', 1 ) . " - Automatically Leech warned and downloads disabled By System.\n". $modcomment;
            $modcom =  sqlesc($modcomment);
            $msgs_buffer[] = '(0,' . $arr['id'] . ', '. TIME_NOW .', ' . sqlesc($msg) . ', ' . sqlesc($subject) . ')';
            $users_buffer[] = '(' . $arr['id'] . ',' . $leechwarn . ',\'0\', ' . $modcom . ')';
            $update['leechwarn'] = ($leechwarn);
            $mc1->begin_transaction('user'.$arr['id']);
            $mc1->update_row(false, array('leechwarn' => $update['leechwarn'], 'downloadpos' => 0, 'modcomment' => $modcomment));
            $mc1->commit_transaction(900);
            $mc1->begin_transaction('MyUser_'.$arr['id']);
            $mc1->update_row(false, array('leechwarn' => $update['leechwarn'], 'downloadpos' => 0));
            $mc1->commit_transaction(900);
        }
        $count = count($users_buffer);
        if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, leechwarn, downloadpos, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE leechwarn=values(leechwarn),downloadpos=values(downloadpos),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Cleanup: System applied auto leech Warning(s) to  ".$count." Member(s)");
        }
        unset ($users_buffer, $msgs_buffer, $update, $count);
    }
    //End
    //== 09 Auto leech warn by Bigjoos/pdq
    //== Updated/Modified autoleech warn system - Remove warning and enable downloads
    $minratio = 0.5; // ratio > 0.5
    $res = sql_query("SELECT id, modcomment FROM users WHERE downloadpos = '0' AND leechwarn > '1' AND uploaded / downloaded >= $minratio") or sqlerr(__FILE__, __LINE__);
    $msgs_buffer = $users_buffer = array();
    if (mysql_num_rows($res) > 0) {
       $subject = "Auto leech warning removed";
        $msg = "Your warning for a low ratio has been removed and your downloads enabled. We highly recommend you to keep your ratio positive to avoid being automatically warned again.\n";
        while ($arr = mysql_fetch_assoc($res)) {
            $modcomment = $arr['modcomment'];
            $modcomment =  get_date( time(), 'DATE', 1 ) . " - Leech warn removed and download enabled By System.\n". $modcomment;
            $modcom =  sqlesc($modcomment);
            $msgs_buffer[] = '(0,' . $arr['id'] . ','. TIME_NOW .', ' . sqlesc($msg) . ',  ' . sqlesc($subject) . ')';
            $users_buffer[] = '(' . $arr['id'] . ', \'0\', \'1\', ' . $modcom . ')';
            $mc1->begin_transaction('user'.$arr['id']);
            $mc1->update_row(false, array('leechwarn' => 0, 'downloadpos' => 0, 'modcomment' => $modcomment));
            $mc1->commit_transaction(900);
            $mc1->begin_transaction('MyUser_'.$arr['id']);
            $mc1->update_row(false, array('leechwarn' => 0, 'downloadpos' => 0));
            $mc1->commit_transaction(900);
        }
        $count = count($users_buffer);
        if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, leechwarn, downloadpos, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE leechwarn=values(leechwarn),downloadpos=values(downloadpos),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Cleanup: System removed auto leech Warning(s) and renabled download(s) - ".$count." Member(s)");
        }
        unset ($users_buffer, $msgs_buffer, $count);
    }
    //==End
    //== 09 Auto leech warn by Bigjoos/pdq
    //== Disabled expired leechwarns
    $res = sql_query("SELECT id, modcomment FROM users WHERE leechwarn > '1' AND leechwarn < ". TIME_NOW ." AND leechwarn <> '0' ") or sqlerr(__FILE__, __LINE__);
    $users_buffer = array();
    if (mysql_num_rows($res) > 0) {
        while ($arr = mysql_fetch_assoc($res)) {
            $modcomment = $arr['modcomment'];
            $modcomment =  get_date( time(), 'DATE', 1 ) . " - User disabled - Low ratio.\n". $modcomment;
            $modcom =  sqlesc($modcomment);
            $users_buffer[] = '(' . $arr['id'] . ' , \'0\', \'no\', ' . $modcom . ')';
            $mc1->begin_transaction('user'.$arr['id']);
            $mc1->update_row(false, array('leechwarn' => 0, 'enabled' => 'no', 'modcomment' => $modcomment));
            $mc1->commit_transaction(900);
            $mc1->begin_transaction('MyUser_'.$arr['id']);
            $mc1->update_row(false, array('leechwarn' => 0, 'enabled' => 'no'));
            $mc1->commit_transaction(900);
        }
        $count = count($users_buffer);
        if ($count > 0){
            sql_query("INSERT INTO users (id, leechwarn, enabled, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE class=values(class),leechwarn=values(leechwarn),enabled=values(enabled),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Cleanup: Disabled ".$count." Member(s) - Leechwarns expired");
        }
        unset ($users_buffer, $count);
    }
    //==End
  //== 09 Auto invite by Bigjoos/pdq
	$ratiocheck  =  1.0;
	$joined = (time() - 86400*90);
    $res = sql_query("SELECT id, uploaded, invites, downloaded, modcomment FROM users WHERE invites='1' AND class = ".UC_USER." AND uploaded / downloaded <= $ratiocheck AND enabled='yes' AND added < $joined") or sqlerr(__FILE__, __LINE__);
    $msgs_buffer = $users_buffer = array();
    if (mysql_num_rows($res) > 0) {
        $subject ="Auto Invites";
        $msg = "Congratulations, your user group met a set out criteria therefore you have been awarded 2 invites  :)\n Please use them carefully. Cheers ".$INSTALLER09['site_name']." staff.\n";
        while ($arr = mysql_fetch_assoc($res)) {
            $ratio = number_format($arr['uploaded'] / $arr['downloaded'], 3);
            $modcomment = $arr['modcomment'];
            $modcomment =  get_date( time(), 'DATE', 1 ) . " - Awarded 2 bonus invites by System (UL=" . mksize($arr['uploaded']) . ", DL=" . mksize($arr['downloaded']) . ", R=" . $ratio . ") .\n". $modcomment;
            $modcom =  sqlesc($modcomment);
            $msgs_buffer[] = '(0,' . $arr['id'] . ', '. TIME_NOW .', ' . sqlesc($msg) . ', ' . sqlesc($subject) . ')';
            $users_buffer[] = '(' . $arr['id'] . ', 2, ' . $modcom . ')'; //== 2 in the user_buffer is award amount :)
            $update['invites'] = ($arr['invites']+2); //== 2 in the user_buffer is award amount :)
            $mc1->begin_transaction('user'.$arr['id']);
            $mc1->update_row(false, array('invites' => $update['invites'], 'modcomment' => $modcomment));
            $mc1->commit_transaction(900);
            $mc1->begin_transaction('MyUser_'.$arr['id']);
            $mc1->update_row(false, array('invites' => $update['invites']));
            $mc1->commit_transaction(900);
        }
        $count = count($users_buffer);
        if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, invites, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE invites = invites+values(invites), modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Cleanup: Awarded 2 bonus invites to ".$count." member(s) ");
        }
        unset ($users_buffer, $msgs_buffer, $update, $count);
    }
    //==
  ////== Delete ips
  $dt =  TIME_NOW -  62 * 86400;
  sql_query("DELETE FROM ips WHERE access < $dt");
  write_log("Slowautoclean -------------------- Delayed cleanup Complete using $queries queries --------------------");
  }

  function doslowcleanup2()
  {
    global $INSTALLER09, $queries, $mc1;
    set_time_limit(1200);
    $result = sql_query("show processlist") or sqlerr(__FILE__, __LINE__);
    while ($row = mysql_fetch_array($result)) {
    if (($row["Time"] > 100) || ($row["Command"] == "Sleep")) {
    $sql = "kill " . $row["Id"] . "";
    sql_query($sql) or sqlerr(__FILE__, __LINE__);
    }
    }
    ignore_user_abort(1);
    //===09 hnr by sir_snugglebunny
    //=== hit and run part... after 3 days, add the mark of Cain... adjust $secs value if you wish
          $secs = 3 * 86400;
          $hnr = time() - $secs;
          $res = sql_query('SELECT id FROM snatched WHERE hit_and_run <> \'0\' AND hit_and_run < '.sqlesc($hnr).'') or sqlerr(__FILE__, __LINE__);    
          while ($arr = mysql_fetch_assoc($res))
          {
          sql_query('UPDATE snatched SET mark_of_cain = \'yes\' WHERE id='.sqlesc($arr['id'])) or sqlerr(__FILE__, __LINE__);
          }
    //=== hit and run... disable Downloading rights if they have 3 marks of cain 
          $res_fuckers = sql_query('SELECT COUNT(*) AS poop, snatched.userid, users.username, users.modcomment, users.hit_and_run_total, users.downloadpos FROM snatched LEFT JOIN users ON snatched.userid = users.id WHERE snatched.mark_of_cain = \'yes\' AND users.hnrwarn = \'no\' AND users.immunity = \'0\' GROUP BY snatched.userid') or sqlerr(__FILE__, __LINE__);     
          while ($arr_fuckers = mysql_fetch_assoc($res_fuckers))
          {
                if ($arr_fuckers['poop'] > 3 && $arr_fuckers['downloadpos'] == 1)
                {
                //=== set them to no DLs
                $subject = sqlesc('Download disabled by System');
                $msg = sqlesc("Sorry ".$arr_fuckers['username'].",\n Because you have 3 or more torrents that have not been seeded to either a 1:1 ratio, or for the expected seeding time, your downloading rights have been disabled by the Auto system !\nTo get your Downloading rights back is simple,\n just start seeding the torrents in your profile [ click your username, then click your [url=".$INSTALLER09['baseurl']."/userdetails.php?id=".$arr_fuckers['userid']."&completed=1]Completed Torrents[/url] link to see what needs seeding ] and your downloading rights will be turned back on by the Auto system after the next clean-time [ updates 4 times per hour ].\n\nDownloads are disabled after a member has three or more torrents that have not been seeded to either a 1 to 1 ratio, OR for the required seed time [ please see the [url=".$INSTALLER09['baseurl']."/faq.php]FAQ[/url] or [url=".$INSTALLER09['baseurl']."/rules.php]Site Rules[/url] for more info ]\n\nIf this message has been in error, or you feel there is a good reason for it, please feel free to PM a staff member with your concerns.\n\n we will do our best to fix this situation.\n\nBest of luck!\n ".$INSTALLER09['site_name']." staff.\n");
                $modcomment = $arr_fuckers['modcomment'];
                $modcomment =  get_date( time(), 'DATE', 1 ) . " - Download rights removed for H and R - AutoSystem.\n". $modcomment;
                $modcom =  sqlesc($modcomment);
                sql_query("INSERT INTO messages (sender, receiver, added, msg, subject, poster) VALUES(0, {$arr_fuckers['userid']}, ". TIME_NOW .", $msg, $subject, 0)") or sqlerr(__FILE__, __LINE__);       
                sql_query('UPDATE users SET hit_and_run_total = hit_and_run_total + '.$arr_fuckers['poop'].', downloadpos = \'0\', hnrwarn = \'yes\', modcomment = '.$modcom.'  WHERE downloadpos = \'1\' AND id='.sqlesc($arr_fuckers['userid'])) or sqlerr(__FILE__, __LINE__);
                $update['hit_and_run_total'] = ($arr_fuckers['hit_and_run_total']+$arr_fuckers['poop']);
                $mc1->begin_transaction('user'.$arr_fuckers['userid']);
                $mc1->update_row(false, array('hit_and_run_total' => $update['hit_and_run_total'], 'downloadpos' => 0, 'hnrwarn' =>'yes', 'modcomment' => $modcomment));
                $mc1->commit_transaction(900);
                $mc1->begin_transaction('MyUser_'.$arr_fuckers['userid']);
                $mc1->update_row(false, array('hit_and_run_total' => $update['hit_and_run_total'], 'downloadpos' => 0, 'hnrwarn' =>'yes'));
                $mc1->commit_transaction(900);
                }
          }
    //=== hit and run... turn their DLs back on if they start seeding again
    $res_good_boy = sql_query('SELECT id, username, modcomment FROM users WHERE hnrwarn = \'yes\' AND downloadpos = \'0\'') or sqlerr(__FILE__, __LINE__);
    while ($arr_good_boy = mysql_fetch_assoc($res_good_boy))
          {
          $res_count = sql_query('SELECT COUNT(*) FROM snatched WHERE userid = '.sqlesc($arr_good_boy['id']).' AND mark_of_cain = \'yes\'') or sqlerr(__FILE__, __LINE__);
          $arr_count = mysql_fetch_row($res_count);
                if ($arr_count[0] < 3)
                {
                //=== set them to yes DLs
                $subject = sqlesc('Download restored by System');
                $msg = sqlesc("Hi ".$arr_good_boy['username'].",\n Congratulations ! Because you have seeded the torrents that needed seeding, your downloading rights have been restored by the Auto System !\n\nhave fun !\n ".$INSTALLER09['site_name']." staff.\n");
                $modcomment = $arr_good_boy['modcomment'];
                $modcomment =  get_date( time(), 'DATE', 1 ) . " - Download rights restored from H and R - AutoSystem.\n". $modcomment;
                $modcom =  sqlesc($modcomment);
                sql_query("INSERT INTO messages (sender, receiver, added, msg, subject, poster) VALUES(0, ".sqlesc($arr_good_boy['id']).", ". TIME_NOW .", $msg, $subject, 0)") or sqlerr(__FILE__, __LINE__);
                sql_query('UPDATE users SET downloadpos = \'1\', hnrwarn = \'no\', modcomment = '.$modcom.'  WHERE id = '.sqlesc($arr_good_boy['id'])) or sqlerr(__FILE__, __LINE__);
                $mc1->begin_transaction('user'.$arr_good_boy['id']);
                $mc1->update_row(false, array('downloadpos' => 1, 'hnrwarn' =>'no', 'modcomment' => $modcomment));
                $mc1->commit_transaction(900);
                $mc1->begin_transaction('MyUser_'.$arr_good_boy['id']);
                $mc1->update_row(false, array('downloadpos' => 1, 'hnrwarn' =>'no'));
                $mc1->commit_transaction(900);
                }
          }
          //==End

  sql_query("UPDATE `freeslots` SET `double` = 0 WHERE `double` != 0 AND `double` < ".TIME_NOW) or sqlerr(__FILE__, __LINE__); 
  sql_query("UPDATE `freeslots` SET `free` = 0 WHERE `free` != 0 AND `free` < ".TIME_NOW) or sqlerr(__FILE__, __LINE__); 
  sql_query("DELETE FROM `freeslots` WHERE `double` = 0 AND `free` = 0") or sqlerr(__FILE__, __LINE__);
  sql_query("UPDATE `users` SET `free_switch` = 0 WHERE `free_switch` > 1 AND `free_switch` < ".TIME_NOW) or sqlerr(__FILE__, __LINE__);
  sql_query("UPDATE `torrents` SET `free` = 0 WHERE `free` > 1 AND `free` < ".TIME_NOW) or sqlerr(__FILE__, __LINE__);
  sql_query("UPDATE `users` SET `downloadpos` = 1 WHERE `downloadpos` > 1 AND `downloadpos` < ".TIME_NOW) or sqlerr(__FILE__, __LINE__);
  sql_query("UPDATE `users` SET `uploadpos` = 1 WHERE `uploadpos` > 1 AND `uploadpos` < ".TIME_NOW) or sqlerr(__FILE__, __LINE__);
  sql_query("UPDATE `users` SET `chatpost` = 1 WHERE `chatpost` > 1 AND `chatpost` < ".TIME_NOW) or sqlerr(__FILE__, __LINE__);
  sql_query("UPDATE `users` SET `avatarpos` = 1 WHERE `avatarpos` > 1 AND `avatarpos` < ".TIME_NOW) or sqlerr(__FILE__, __LINE__);
  sql_query("UPDATE `users` SET `immunity` = 0 WHERE `immunity` > 1 AND `immunity` < ".TIME_NOW) or sqlerr(__FILE__, __LINE__);
  sql_query("UPDATE `users` SET `warned` = 0 WHERE `warned` > 1 AND `warned` < ".TIME_NOW) or sqlerr(__FILE__, __LINE__);
  sql_query("UPDATE `users` SET `pirate` = 0 WHERE `pirate` > 1 AND `pirate` < ".TIME_NOW) or sqlerr(__FILE__, __LINE__);
  sql_query("UPDATE `users` SET `king` = 0 WHERE `king` > 1 AND `king` < ".TIME_NOW) or sqlerr(__FILE__, __LINE__);
  //== Delete old backup's
  $days = 7;
  $res = sql_query("SELECT id, name FROM dbbackup WHERE added < ".sqlesc(time() - ($days * 86400))) or sqlerr(__FILE__, __LINE__);
  if (mysql_num_rows($res) > 0)
  {
  $ids = array();
  while ($arr = mysql_fetch_assoc($res))
  {
  $ids[] = (int)$arr['id'];
  $filename = $INSTALLER09['backup_dir'].'/'.$arr['name'];
  if (is_file($filename))
  unlink($filename);
  }
  sql_query('DELETE FROM dbbackup WHERE id IN ('.implode(', ', $ids).')') or sqlerr(__FILE__, __LINE__);
  }
  //== end
  //== Delete inactive user accounts
	$secs = 350*86400;
	$dt = (time() - $secs);
	$maxclass = UC_POWER_USER;
	sql_query("DELETE FROM users WHERE parked='no' AND status='confirmed' AND class <= $maxclass AND last_access < $dt");
	 //== Delete parked user accounts
	 $secs = 175*86400; // change the time to fit your needs
	 $dt = (time() - $secs);
	 $maxclass = UC_POWER_USER;
	 sql_query("DELETE FROM users WHERE parked='yes' AND status='confirmed' AND class <= $maxclass AND last_access < $dt");
	//== Delete shout
  $secs = 2 * 86400;
  $dt = sqlesc(time() - $secs);
  sql_query("DELETE FROM shoutbox WHERE " . time() . " - date > $secs") or sqlerr(__FILE__, __LINE__);
  //== Updated promote power users
  $limit = 25*1024*1024*1024;
	$minratio = 1.05;
	$maxdt = (time() - 86400*28);
    $res = sql_query("SELECT id, uploaded, downloaded, invites, modcomment FROM users WHERE class = ".UC_USER." AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND enabled='yes' AND added < $maxdt") or sqlerr(__FILE__, __LINE__);
    $msgs_buffer = $users_buffer = array();
    if (mysql_num_rows($res) > 0) {
        $subject ="Auto Promotion";
        $msg = "Congratulations, you have been Auto-Promoted to [b]Power User[/b]. :)\n You get one extra invite.\n";
        while ($arr = mysql_fetch_assoc($res)) {
            $ratio = number_format($arr['uploaded'] / $arr['downloaded'], 3);
            $modcomment = $arr['modcomment'];
            $modcomment =  get_date( time(), 'DATE', 1 ) . " - Promoted to Power User by System (UL=" . mksize($arr['uploaded']) . ", DL=" . mksize($arr['downloaded']) . ", R=" . $ratio . ").\n". $modcomment;
            $modcom =  sqlesc($modcomment);
            $msgs_buffer[] = '(0,' . $arr['id'] . ', '. TIME_NOW .', ' . sqlesc($msg) . ', ' . sqlesc($subject) . ')';
            $users_buffer[] = '(' . $arr['id'] . ', 1, 1, ' . $modcom . ')';
            $update['invites'] = ($arr['invites'] + 1);
            $mc1->begin_transaction('user'.$arr['id']);
            $mc1->update_row(false, array('class' => 1, 'invites' => $update['invites'], 'modcomment' => $modcomment));
            $mc1->commit_transaction(900);
            $mc1->begin_transaction('MyUser_'.$arr['id']);
            $mc1->update_row(false, array('class' => 1, 'invites' => $update['invites']));
            $mc1->commit_transaction(900);
        }
        $count = count($users_buffer);
        if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, class, invites, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE class=values(class), invites = invites+values(invites), modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Cleanup: Promoted ".$count." member(s) from User to Power User");
        }
        unset ($users_buffer, $msgs_buffer, $update, $count);
        status_change($arr['id']); //== For Retros announcement mod
    }
    //== Updated demote power users
    $minratio = 0.85;
    $res = sql_query("SELECT id, uploaded, downloaded, modcomment FROM users WHERE class = ".UC_POWER_USER." AND uploaded / downloaded < $minratio") or sqlerr(__FILE__, __LINE__);
    $subject ="Auto Demotion";
    $msgs_buffer = $users_buffer = array();
    if (mysql_num_rows($res) > 0) {
        $msg = "You have been auto-demoted from [b]Power User[/b] to [b]User[/b] because your share ratio has dropped below  $minratio.\n";
        while ($arr = mysql_fetch_assoc($res)) {
            $ratio = number_format($arr['uploaded'] / $arr['downloaded'], 3);
            $modcomment = $arr['modcomment'];
            $modcomment =  get_date( time(), 'DATE', 1 ) . " - Demoted To User by System (UL=" . mksize($arr['uploaded']) . ", DL=" . mksize($arr['downloaded']) . ", R=" . $ratio . ").\n". $modcomment;
            $modcom =  sqlesc($modcomment);
            $msgs_buffer[] = '(0,' . $arr['id'] . ', '. TIME_NOW .', ' . sqlesc($msg) . ', ' . sqlesc($subject) . ')';
            $users_buffer[] = '(' . $arr['id'] . ', 0, ' . $modcom . ')';
            $mc1->begin_transaction('user'.$arr['id']);
            $mc1->update_row(false, array('class' => 0, 'modcomment' => $modcomment));
            $mc1->commit_transaction(900);
            $mc1->begin_transaction('MYuser_'.$arr['id']);
            $mc1->update_row(false, array('class' => 0));
            $mc1->commit_transaction(900);
        }
        $count = count($users_buffer);
        if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, class, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE class=values(class),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Cleanup: Demoted ".$count." member(s) from Power User to User");
            status_change($arr['id']);
        }
        unset ($users_buffer, $msgs_buffer, $count);
        status_change($arr['id']); //== For Retros announcement mod
    }
  //==End
  //sql_query("UPDATE avps SET value_i = 0, value_s = '0' WHERE arg = 'sitepot' AND value_u < ".TIME_NOW." AND value_s = '1'") or sqlerr(__file__, __line__);
  write_log("Slowautoclean2 -------------------- Delayed cleanup 2 Complete using $queries queries--------------------");
  }

  function dolotterycleanup()
  {
    global $INSTALLER09, $queries;
    set_time_limit(1200);
    $result = sql_query("show processlist") or sqlerr(__FILE__, __LINE__);
    while ($row = mysql_fetch_array($result)) {
        if (($row["Time"] > 100) || ($row["Command"] == "Sleep")) {
            $sql = "kill " . $row["Id"] . "";
            sql_query($sql) or sqlerr(__FILE__, __LINE__);
        }
    }
    ignore_user_abort(1);
    $lconf = sql_query('SELECT * FROM lottery_config') or sqlerr(__FILE__,__LINE__);
   while($aconf = mysql_fetch_assoc($lconf))
    $lottery_config[$aconf['name']] = $aconf['value'];
   if($lottery_config['enable'] && TIME_NOW > $lottery_config['end_date']) {
   $q = sql_query('SELECT t.user as uid, u.seedbonus, u.modcomment FROM tickets as t LEFT JOIN users as u ON u.id = t.user ORDER BY RAND() ') or sqlerr(__FILE__,__LINE__);
   while($a = mysql_fetch_assoc($q))
    $tickets[] = $a;

   shuffle($tickets);
   $lottery['winners']= array();
   $lottery['total_tickets'] = count($tickets);
   for($i=0;$i<$lottery['total_tickets'];$i++) {
     if(!isset($lottery['winners'][$tickets[$i]['uid']]))
      $lottery['winners'][$tickets[$i]['uid']] = $tickets[$i];
     if($lottery_config['total_winners'] == count($lottery['winners']))
      break;
   } 
   if($lottery_config['use_prize_fund'])
     $lottery['total_pot'] = $lottery_config['prize_fund'];
   else
     $lottery['total_pot'] = $lottery['total_tickets'] * $lottery_config['ticket_amount'];    

   $lottery['user_pot'] = round($lottery['total_pot']/$lottery_config['total_winners'],2);
    $msg['subject'] = sqlesc('You have won the lottery');
    $msg['body'] = sqlesc('Congratulations, You have won : '.($lottery['user_pot']).'. This has been added to your seedbonus total amount. Thanks for playing Lottery.');
   foreach($lottery['winners'] as $winner) {
      $_userq[] = '('.$winner['uid'].','.($winner['seedbonus']+$lottery['user_pot']).','.sqlesc("User won the lottery: " . ($lottery['user_pot']) . " at " . get_date(TIME_NOW,'LONG') . "\n" . $winner['modcomment']).')';
      $_pms[] = '(0,'.$winner['uid'].','.$msg['subject'].','.$msg['body'].','.TIME_NOW.')';
   }
   $lconfig_update = array('(\'enable\',0)','(\'lottery_winners_time\','.TIME_NOW.')', '(\'lottery_winners_amount\','.$lottery['user_pot'].')', '(\'lottery_winners\',\''.join('|',array_keys($lottery['winners'])).'\')');
   if(count($_userq))
    sql_query('INSERT INTO users(id,seedbonus,modcomment) VALUES '.join(',',$_userq).' ON DUPLICATE KEY UPDATE seedbonus = values(seedbonus), modcomment = values(modcomment)') or die(mysql_error());
   if(count($_pms))
    sql_query('INSERT INTO messages(sender, receiver, subject, msg, added) VALUES '.join(',',$_pms)) or die(mysql_error());
    sql_query('INSERT INTO lottery_config(name,value) VALUES '.join(',',$lconfig_update).' ON DUPLICATE KEY UPDATE value=values(value)') or die(mysql_error());
    sql_query('DELETE FROM tickets') or die(mysql_error());
   }
    //==End 09 seedbonus lottery by putyn
    write_log("Lottery clean-------------------- lottery Complete using $queries queries --------------------");
    }

  function dooptimizedb()
  {
  global $INSTALLER09, $queries;
  set_time_limit(1200);
  $result = sql_query("show processlist") or sqlerr(__FILE__, __LINE__);
  while ($row = mysql_fetch_array($result)) {
  if (($row["Time"] > 100) || ($row["Command"] == "Sleep")) {
  $sql = "kill " . $row["Id"] . "";
  sql_query($sql) or sqlerr(__FILE__, __LINE__);
  }
  }
  ignore_user_abort(1);
  $alltables = sql_query("SHOW TABLES") or sqlerr(__FILE__, __LINE__);
  while ($table = mysql_fetch_assoc($alltables)) {
  foreach ($table as $db => $tablename) {
  $sql = "OPTIMIZE TABLE $tablename";
  /* Preg match the sql incase it was hijacked somewhere!(will use CHECK|ANALYZE|REPAIR|later) */
  if (preg_match('@^(CHECK|ANALYZE|REPAIR|OPTIMIZE)[[:space:]]TABLE[[:space:]]' . $tablename . '$@i', $sql))
   sql_query($sql) or die("<b>Something was not right!</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . htmlspecialchars(mysql_error()));
   }
   }
   @mysql_free_result($alltables);
   write_log("Auto-optimizedb--------------------Auto Optimization Complete using $queries queries --------------------");
   }
   
   function dobackupdb()
   {
   global $INSTALLER09, $queries;
    set_time_limit(1200);
    $result = sql_query("show processlist") or sqlerr(__FILE__, __LINE__);
    while ($row = mysql_fetch_array($result)) {
        if (($row["Time"] > 100) || ($row["Command"] == "Sleep")) {
            $sql = "kill " . $row["Id"] . "";
        sql_query($sql) or sqlerr(__FILE__, __LINE__);
        }
    }
    ignore_user_abort(1);
    /* Your db-globals */
    global $INSTALLER09, $queries, $CURUSER, $backupdir;
    $mysql_host = $INSTALLER09['mysql_host'];
	  $mysql_user = $INSTALLER09['mysql_user'];
	  $mysql_pass = $INSTALLER09['mysql_pass'];
	  $mysql_db = $INSTALLER09['mysql_db'];
    /* Change to the name of your backup directory */
    //$backupdir = $INSTALLER09['backup_dir'];
    $backupdir = 'include/backup';
    /* Compute day, month, year, hour and min. */
    $today = getdate();
    $day = $today['mday'];
    if ($day < 10) {
        $day = "0$day";
    }
    $month = $today['mon'];
    if ($month < 10) {
        $month = "0$month";
    }
    $year = $today['year'];
    $hour = $today['hours'];
    $min = $today['minutes'];
    $sec = "00";
    /*
    Execute mysqldump command.
    It will produce a file named $mysql_db-$year$month$day-$hour$min.gz
    under $DOCUMENT_ROOT/$backupdir
    getenv('DOCUMENT_ROOT'),
    */
    /*
    //== Windows mysqldump
    system(sprintf('c:\webdev\mysql\bin\mysqldump --opt -h %s -u %s -p%s %s > %s/%s/%s-%s-%s-%s.sql', $mysql_host, $mysql_user, $mysql_pass, $mysql_db, getenv('DOCUMENT_ROOT'), $backupdir, $mysql_db, $day, $month, $year));
    */
    
    //== Liux mysqldump
    system(sprintf( '/usr/bin/mysqldump --opt -h %s -u %s -p%s %s  > %s/%s/%s-%s-%s-%s.sql', $mysql_host, $mysql_user, $mysql_pass, $mysql_db, getenv('DOCUMENT_ROOT'), $backupdir, $mysql_db, $day, $month, $year));
    $ext = $mysql_db.'-'.date('d').'-'.date('m').'-'.date('Y').".sql";
	  sql_query("INSERT INTO dbbackup (name, added, userid) VALUES (".sqlesc($ext).", ".sqlesc(time()).", ".$INSTALLER09['site']['owner'].")") or sqlerr(__FILE__, __LINE__);
    write_log("Auto-dbbackup----------------------Auto Back Up Complete using $queries queries---------------------");
}
?>