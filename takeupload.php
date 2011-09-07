<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'benc.php');
require_once(INCL_DIR.'user_functions.php');
require_once(CLASS_DIR.'page_verify.php');
require_once(INCL_DIR.'getpre.php');
dbconn(); 
loggedinorreturn();
ini_set('upload_max_filesize', $INSTALLER09['max_torrent_size']);

    $lang = array_merge( load_language('global'), load_language('takeupload') );
    $newpage = new page_verify(); 
    $newpage->check('taud');
    
    if ($CURUSER['class'] < UC_UPLOADER OR $CURUSER["uploadpos"] == 0 || $CURUSER["uploadpos"] > 1 || $CURUSER['suspended'] == 'yes')
    {
    header( "Location: {$INSTALLER09['baseurl']}/upload.php" );
    exit();
    }

    /*
    function ircbot($messages)
    {
    $bot = array('ip' => '127.0.0.1',
			     'port' => 35789,
				 'pass' => 'bWFtYWFyZW1lcmU',
				 'pidfile'=> 'C:/Windrop/pid.Bert', //path to the pid. file
				 'sleep'=>5,
				);
    if(empty($messages))
		return; #die ('Empty message');

    if(!file_exists($bot['pidfile']))
		return; #die ('Bot not online');
		
    if($bot['hand'] = fsockopen($bot['ip'], $bot['port'] , $errno, $errstr, 45))
    {
		sleep($bot['sleep']);
		if(is_array($messages)) {
			foreach($messages as $message) {
				fputs($bot['hand'], $bot['pass'].' '. $message."\n");
				sleep($bot['sleep']);
			}
      } else {
			fputs($bot['hand'], $bot['pass'].' '. $messages."\n");
			sleep($bot['sleep']);
		}
		fclose($bot['hand']);
    }
    }
    */
    foreach(explode(":","descr:type:name") as $v) {
      if (!isset($_POST[$v]))
        stderr($lang['takeupload_failed'], $lang['takeupload_no_formdata']);
    }

    if (!isset($_FILES["file"]))
      stderr($lang['takeupload_failed'], $lang['takeupload_no_formdata']);

    if (!empty($_POST['url'])){
    $url = unesc($_POST['url']);
    }
    else
    {
    $url = '';
    }

    if (!empty($_POST['poster'])){
    $poster = unesc($_POST['poster']);
    }
    else
    {
    $poster = '';
    }
    
    $f = $_FILES["file"];
    $fname = unesc($f["name"]);
    if (empty($fname))
    stderr($lang['takeupload_failed'], $lang['takeupload_no_filename']);
     
    if(isset($_POST['uplver']) && $_POST['uplver'] == 'yes') {
    $anonymous = "yes";
    $anon = "Anonymous";
    } else {
    $anonymous = "no";
    $anon = $CURUSER["username"];
    }
      
    if(isset($_POST['allow_comments']) && $_POST['allow_comments'] == 'yes') {
    $allow_comments = "no";
    $disallow = "Yes";
    } else {
    $allow_comments = "yes";
    $disallow = "No";
    }
      
    if (isset($_POST["music"])){
    $genre = implode(",", $_POST['music']);}
    elseif (isset($_POST["movie"])){
    $genre = implode(",", $_POST['movie']);}
    elseif (isset($_POST["game"])){
    $genre = implode(",", $_POST['game']);}
    elseif (isset($_POST["apps"])){
    $genre = implode(",", $_POST['apps']);}
    else
    {
    $genre = '';
    }
      
    $nfo = sqlesc('');
    /////////////////////// NFO FILE ////////////////////////	
    if(isset($_FILES['nfo']) && !empty($_FILES['nfo']['name'])) {
    $nfofile = $_FILES['nfo'];
    
    if ($nfofile['name'] == '')
      stderr($lang['takeupload_failed'], $lang['takeupload_no_nfo']);

    if ($nfofile['size'] == 0)
      stderr($lang['takeupload_failed'], $lang['takeupload_0_byte']);

    if ($nfofile['size'] > 65535)
      stderr($lang['takeupload_failed'], $lang['takeupload_nfo_big']);

    $nfofilename = $nfofile['tmp_name'];

    if (@!is_uploaded_file($nfofilename))
      stderr($lang['takeupload_failed'], $lang['takeupload_nfo_failed']);

    $nfo = sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", @file_get_contents($nfofilename)));
    }
    /////////////////////// NFO FILE END /////////////////////
   
   /// Set Freeleech on Torrent Time Based
   $free = 0;
   if (isset($_POST['free_length']) && ($free_length = 0 + $_POST['free_length']))
   {
    if ($free_length == 255)
        $free = 1;

    elseif ($free_length == 42)
        $free = (86400 + time());

    else
        $free = (time() + $free_length * 604800);
   }
   /// end
 
   $descr = unesc($_POST["descr"]);
   if (!$descr)
   stderr($lang['takeupload_failed'], $lang['takeupload_no_descr']);
   
   if(isset($_POST['strip']) && $_POST['strip'])
   { 
   require_once(INCL_DIR.'strip.php');
   $descr = preg_replace("/[^\\x20-\\x7e\\x0a\\x0d]/", " ", $descr);
   strip($descr);
   }
   
    $catid = (0 + $_POST["type"]);
    if (!is_valid_id($catid))
      stderr($lang['takeupload_failed'], $lang['takeupload_no_cat']);

    $subs = isset($_POST["subs"]) ? implode(",", $_POST['subs']) : "";	
    //$movie_cat = array("3","5","6","10","11"); //add here your movie category 
    //if (empty($subs) && in_array($catid, $movie_cat))
    //stderr ("Error", "Select a subtitle!");

    $release_group_array =  array('scene' =>1, 'p2p' =>1, 'none' =>1);
    $release_group = isset($_POST['release_group']) && isset($release_group_array[$_POST['release_group']]) ? $_POST['release_group'] : 'none'; 

    if (!validfilename($fname))
      stderr($lang['takeupload_failed'], $lang['takeupload_invalid']);
    if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
      stderr($lang['takeupload_failed'], $lang['takeupload_not_torrent']);
    $shortfname = $torrent = $matches[1];
    if (!empty($_POST["name"]))
      $torrent = unesc($_POST["name"]);

    $tmpname = $f["tmp_name"];
    if (!is_uploaded_file($tmpname))
      stderr($lang['takeupload_failed'], $lang['takeupload_eek']);
    if (!filesize($tmpname))
      stderr($lang['takeupload_failed'], $lang['takeupload_no_file']);

    $dict = bdec_file($tmpname, $INSTALLER09['max_torrent_size']);
    if (!isset($dict))
      stderr($lang['takeupload_failed'], $lang['takeupload_not_benc']);


    function dict_check($d, $s) {
      global $lang;
      if ($d["type"] != "dictionary")
        stderr($lang['takeupload_failed'], $lang['takeupload_not_dict']);
      $a = explode(":", $s);
      $dd = $d["value"];
      $ret = array();
      $t='';
      foreach ($a as $k) {
        unset($t);
        if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
          $k = $m[1];
          $t = $m[2];
        }
        if (!isset($dd[$k]))
          stderr($lang['takeupload_failed'], $lang['takeupload_no_keys']);
        if (isset($t)) {
          if ($dd[$k]["type"] != $t)
            stderr($lang['takeupload_failed'], $lang['takeupload_invalid_entry']);
          $ret[] = $dd[$k]["value"];
        }
        else
          $ret[] = $dd[$k];
      }
      return $ret;
    }

    function dict_get($d, $k, $t) {
      global $lang;
      if ($d["type"] != "dictionary")
        stderr($lang['takeupload_failed'], $lang['takeupload_not_dict']);
      $dd = $d["value"];
      if (!isset($dd[$k]))
        return;
      $v = $dd[$k];
      if ($v["type"] != $t)
        stderr($lang['takeupload_failed'], $lang['takeupload_dict_type']);
      return $v["value"];
    }

    list($ann, $info) = dict_check($dict, "announce(string):info");

    $tmaker = (isset($dict['value']['created by']) && !empty($dict['value']['created by']['value'])) ? sqlesc($dict['value']['created by']['value']) : sqlesc($lang['takeupload_unkown']);


    list($dname, $plen, $pieces) = dict_check($info, "name(string):piece length(integer):pieces(string)");
     
    if (strlen($pieces) % 20 != 0)
      stderr($lang['takeupload_failed'], $lang['takeupload_pieces']);

    $filelist = array();
    $totallen = dict_get($info, "length", "integer");
    if (isset($totallen)) {
      $filelist[] = array($dname, $totallen);
      $type = "single";
    }
    else {
      $flist = dict_get($info, "files", "list");
      if (!isset($flist))
        stderr($lang['takeupload_failed'], $lang['takeupload_both']);
      if (!count($flist))
        stderr($lang['takeupload_failed'], $lang['takeupload_no_files']);
      $totallen = 0;
      foreach ($flist as $fn) {
        list($ll, $ff) = dict_check($fn, "length(integer):path(list)");
        $totallen += $ll;
        $ffa = array();
        foreach ($ff as $ffe) {
          if ($ffe["type"] != "string")
            stderr($lang['takeupload_failed'], $lang['takeupload_error']);
          $ffa[] = $ffe["value"];
        }
        if (!count($ffa))
          stderr($lang['takeupload_failed'], $lang['takeupload_error']);
        $ffe = implode("/", $ffa);
        $filelist[] = array($ffe, $ll);
      }
      $type = "multi";
    }
    
    $dict['value']['announce']=bdec(benc_str($INSTALLER09['announce_urls'][0]));  // change announce url to local
    $dict['value']['info']['value']['private']=bdec('i1e');  // add private tracker flag
    $dict['value']['info']['value']['source']=bdec(benc_str( "{$INSTALLER09['baseurl']} {$INSTALLER09['site_name']}")); // add link for bitcomet users
    $dict['value']['comment'] = bdec(benc_str("In using this torrent you are bound by the {$INSTALLER09['site_name']} Confidentiality Agreement By Law")); // change torrent comment
    unset($dict['value']['announce-list']); // remove multi-tracker capability
    unset($dict['value']['nodes']); // remove cached peers (Bitcomet & Azareus)
    $dict=bdec(benc($dict)); // double up on the becoding solves the occassional misgenerated infohash 
    list($ann, $info) = dict_check($dict, "announce(string):info");
    $infohash = sha1($info["string"]);
    unset($info);
    
    /*
    $pretime = 0 + @file_get_contents('http://ransackedcrew.net/gettime.php?name='.trim($torrent));
	  if ($pretime > 0){
	  $pretime = get_pretime($pretime);
	  } else {
	  $pretime = '0';
	  }
	  pretime,
	  $pretime,
    */
    
    // Replace punctuation characters with spaces
    $torrent = str_replace("_", " ", $torrent);
    $vip = (isset($_POST["vip"]) ? "1" : "0");
    $ret = sql_query("INSERT INTO torrents (search_text, filename, owner, visible, vip, release_group, newgenre, poster, anonymous, allow_comments, info_hash, name, size, numfiles, type, url, subs, descr, ori_descr, category, free, save_as, added, last_action, nfo, client_created_by) VALUES (" .
        implode(",", array_map("sqlesc", array(searchfield("$shortfname $dname $torrent"), $fname, $CURUSER["id"], "no", $vip, $release_group, $genre, $poster, $anonymous, $allow_comments, $infohash, $torrent, $totallen, count($filelist), $type, $url, $subs, $descr, $descr, 0 + $_POST["type"], $free, $dname))) .
        ", " . time() . ", " . time() . ", $nfo, $tmaker)");
    if (!$ret) {
      if (mysql_errno() == 1062)
        stderr($lang['takeupload_failed'], $lang['takeupload_already']);
      stderr($lang['takeupload_failed'], "mysql puked: ".mysql_error());
    }
    
    $id = mysql_insert_id();
    $mc1->delete_value('MyPeers_'.$CURUSER['id']);
    //$mc1->delete_value('lastest_tor_');  // 
    $mc1->delete_value('last5_tor_');
    if ($CURUSER["paranoia"] > 1)
    $message = "New Torrent : [url={$INSTALLER09['baseurl']}/details.php?id=$id] " . htmlspecialchars($torrent) . "[/url] Uploaded - Anonymous User";
    else
    $message = "New Torrent : [url={$INSTALLER09['baseurl']}/details.php?id=$id] " . htmlspecialchars($torrent) . "[/url] Uploaded by " . htmlspecialchars($CURUSER["username"]) . "";

    //$messages = "\0035 [ \0034{$INSTALLER09['site_name']} \0035]  \0035[ \0033New Torrent: \0035] [\003 $torrent \0035] \0035[ \003Uploaded By:\00310 $anon \0035] \0035[\0035 Pretime:\00310 " .(get_pretime($pretime)) . "\0035] [\003 Size:\0037 ".mksize($totallen)." \0035] [\003 Link:\00314 {$INSTALLER09['baseurl']}/details.php?id=$id \0035]";
    
    sql_query("DELETE FROM files WHERE torrent = $id");

    function file_list($arr,$id)
    {
        foreach($arr as $v)
            $new[] = "($id,".sqlesc($v[0]).",".$v[1].")";
        return join(",",$new);
    }

    sql_query("INSERT INTO files (torrent, filename, size) VALUES ".file_list($filelist,$id));
  
	  $fp = fopen("{$INSTALLER09['torrent_dir']}/$id.torrent", "w");
    if ($fp)
    {
    @fwrite($fp, benc($dict), strlen(benc($dict)));
    fclose($fp);
    }
    //===add karma 
    sql_query("UPDATE users SET seedbonus = seedbonus+15.0 WHERE id = ".sqlesc($CURUSER["id"])."") or sqlerr(__FILE__, __LINE__);
    //===end
    write_log(sprintf($lang['takeupload_log'], $id, $torrent, $CURUSER['username']));
    autoshout($message);
    //ircbot($messages);
   
    /* RSS feeds */
    if (($fd1 = @fopen("rss.xml", "w")) && ($fd2 = fopen("rssdd.xml", "w")))
    {
      $cats = "";
      $res = sql_query("SELECT id, name FROM categories");
      while ($arr = mysql_fetch_assoc($res))
        $cats[$arr["id"]] = $arr["name"];
      $s = "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n<rss version=\"0.91\">\n<channel>\n" .
        "<title>{$INSTALLER09['site_name']}</title>\n<description>Installer09 is the best!</description>\n<link>{$INSTALLER09['baseurl']}/</link>\n";
      @fwrite($fd1, $s);
      @fwrite($fd2, $s);
      $r = sql_query("SELECT id,name,descr,filename,category FROM torrents ORDER BY added DESC LIMIT 15") or sqlerr(__FILE__, __LINE__);
      while ($a = mysql_fetch_assoc($r))
      {
        $cat = $cats[$a["category"]];
        $s = "<item>\n<title>" . htmlspecialchars($a["name"] . " ($cat)") . "</title>\n" .
          "<description>" . htmlspecialchars($a["descr"]) . "</description>\n";
        @fwrite($fd1, $s);
        @fwrite($fd2, $s);
        @fwrite($fd1, "<link>{$INSTALLER09['baseurl']}/details.php?id=$a[id]&amp;hit=1</link>\n</item>\n");
        $filename = htmlspecialchars($a["filename"]);
        @fwrite($fd2, "<link>{$INSTALLER09['baseurl']}/download.php/$a[id]/$filename</link>\n</item>\n");
      }
      $s = "</channel>\n</rss>\n";
      @fwrite($fd1, $s);
      @fwrite($fd2, $s);
      @fclose($fd1);
      @fclose($fd2);
    }

    /* Email notifs */
    /*******************

    $res = mysql_query("SELECT name FROM categories WHERE id=$catid") or sqlerr();
    $arr = mysql_fetch_assoc($res);
    $cat = $arr["name"];
    $res = mysql_query("SELECT email FROM users WHERE enabled='yes' AND notifs LIKE '%[cat$catid]%'") or sqlerr();
    $uploader = $CURUSER['username'];

    $size = mksize($totallen);
    $description = ($html ? strip_tags($descr) : $descr);

    $body = <<<EOD
A new torrent has been uploaded.

Name: $torrent
Size: $size
Category: $cat
Uploaded by: $uploader

Description
-------------------------------------------------------------------------------
$description
-------------------------------------------------------------------------------

You can use the URL below to download the torrent (you may have to login).

{$INSTALLER09['baseurl']}/details.php?id=$id&hit=1

-- 
{$INSTALLER09['site_name']}
EOD;

    $to = "";
    $nmax = 100; // Max recipients per message
    $nthis = 0;
    $ntotal = 0;
    $total = mysql_num_rows($res);
    while ($arr = mysql_fetch_row($res))
    {
      if ($nthis == 0)
        $to = $arr[0];
      else
        $to .= "," . $arr[0];
      ++$nthis;
      ++$ntotal;
      if ($nthis == $nmax || $ntotal == $total)
      {
        if (!mail("Multiple recipients <{$INSTALLER09['site_email']}>", "New torrent - $torrent", $body,
        "From: {$INSTALLER09['site_email']}\r\nBcc: $to"))
        stderr("Error", "Your torrent has been been uploaded. DO NOT RELOAD THE PAGE!\n" .
          "There was however a problem delivering the e-mail notifcations.\n" .
          "Please let an administrator know about this error!\n");
        $nthis = 0;
      }
    }
    *******************/
header("Location: {$INSTALLER09['baseurl']}/details.php?id=$id&uploaded=1");
?>