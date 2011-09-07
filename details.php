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
require_once(INCL_DIR.'bbcode_functions.php');
require_once(INCL_DIR.'pager_functions.php');
require_once(INCL_DIR.'torrenttable_functions.php');
require_once(INCL_DIR.'html_functions.php');
require_once(IMDB_DIR.'imdb.class.php');
dbconn(false);
loggedinorreturn();


    $lang = array_merge( load_language('global'), load_language('details') );
    parked();
    $stdfoot = array(/** include js **/'js' => array('popup','jquery.thanks','wz_tooltip','java_klappe'));
    $HTMLOUT = '';
    if (!isset($_GET['id']) || !is_valid_id($_GET['id']))
    stderr("{$lang['details_user_error']}", "{$lang['details_bad_id']}"); 
      
    $id = (int)$_GET["id"];
    
    if (isset($_GET["hit"])) 
    {
      sql_query("UPDATE torrents SET views = views + 1 WHERE id = $id");
        header("Location: {$INSTALLER09['baseurl']}/details.php?id=$id");
      exit();
    }

    $res = sql_query("SELECT torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.checked_by, torrents.filename, torrents.points, LENGTH(torrents.nfo) AS nfosz, torrents.last_action AS lastseed, torrents.numratings, torrents.name, IF(torrents.numratings < {$INSTALLER09['minvotes']}, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.comments, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.poster, torrents.url, torrents.numfiles, torrents.anonymous, torrents.free, torrents.allow_comments, torrents.nuked, torrents.nukereason, torrents.last_reseed, torrents.vip, torrents.category, torrents.subs, categories.name AS cat_name, users.username, users.reputation, freeslots.free AS freeslot, freeslots.double AS doubleslot, freeslots.tid AS slotid, freeslots.uid AS slotuid FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN users ON torrents.owner = users.id LEFT JOIN freeslots ON (torrents.id=freeslots.tid AND freeslots.uid = {$CURUSER['id']}) WHERE torrents.id = $id")
	  or sqlerr();
    $row = mysql_fetch_assoc($res);
    
 
    $owned = $moderator = 0;
	  if ($CURUSER["class"] >= UC_MODERATOR)
		$owned = $moderator = 1;
	  elseif ($CURUSER["id"] == $row["owner"])
		$owned = 1;
		
    if ($row["vip"] == "1" && $CURUSER["class"] < UC_VIP)
    stderr("VIP Access Required", "You must be a VIP In order to view details or download this torrent! You may become a Vip By Donating to our site. Donating ensures we stay online to provide you more Vip-Only Torrents!");
		
    if (!$row || ($row["banned"] == "yes" && !$moderator))
	  stderr("{$lang['details_error']}", "{$lang['details_torrent_id']}");
	
		if ($CURUSER["id"] == $row["owner"] ||$CURUSER["class"] >= UC_MODERATOR)
			$owned = 1;
		else
			$owned = 0;
			
		$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		if (isset($_GET["uploaded"])) {
			$HTMLOUT .= "<h2>{$lang['details_success']}</h2>\n";
			$HTMLOUT .= "<p>{$lang['details_start_seeding']}</p>\n";
		}
		elseif (isset($_GET["edited"])) {
			$HTMLOUT .= "<h2>{$lang['details_success_edit']}</h2>\n";
			if (isset($_GET["returnto"]))
				$HTMLOUT .= "<p><b>{$lang['details_go_back']}<a href='" . htmlspecialchars($_GET["returnto"]) . "'>{$lang['details_whence']}</a>.</b></p>\n";
		}

    elseif (isset($_GET["reseed"]))
    $HTMLOUT.="<h2>PM was sent! Now wait for a seeder !</h2>\n";
    
    elseif (isset($_GET["rated"]))
			$HTMLOUT .= "<h2>{$lang['details_rating_added']}</h2>\n";
    
    //==pdq's Torrent Moderation
    if ($CURUSER['class'] >= UC_MODERATOR) {
        if (isset($_GET["checked"]) &&  $_GET["checked"] == 1) {
            sql_query("UPDATE torrents SET checked_by = ".sqlesc($CURUSER['username'])." WHERE id =$id LIMIT 1");
            write_log("Torrent <a href={$INSTALLER09['baseurl']}/details.php?id=$id>($row[name])</a> was checked by $CURUSER[username]");
            header("Location: {$INSTALLER09["baseurl"]}/details.php?id=$id&checked=done#Success");		
        }
        elseif (isset($_GET["rechecked"]) &&  $_GET["rechecked"] == 1) {
            sql_query("UPDATE torrents SET checked_by = ".sqlesc($CURUSER['username'])." WHERE id =$id LIMIT 1");
            write_log("Torrent <a href={$INSTALLER09['baseurl']}/details.php?id=$id>($row[name])</a> was re-checked by $CURUSER[username]");
            header("Location: {$INSTALLER09["baseurl"]}/details.php?id=$id&rechecked=done#Success");		
        }
        elseif (isset($_GET["clearchecked"]) &&  $_GET["clearchecked"] == 1) {
            sql_query("UPDATE torrents SET checked_by = '' WHERE id =$id LIMIT 1");
            write_log("Torrent <a href={$INSTALLER09["baseurl"]}/details.php?id=$id>($row[name])</a> was un-checked by $CURUSER[username]");
            header("Location: {$INSTALLER09["baseurl"]}/details.php?id=$id&clearchecked=done#Success");		
        }
        if (isset($_GET["checked"]) &&  $_GET["checked"] == 'done')
            $HTMLOUT .="<h2><a name='Success'>Successfully checked {$CURUSER['username']}!</a></h2>";
        if (isset($_GET["rechecked"]) &&  $_GET["rechecked"] == 'done')
            $HTMLOUT .="<h2><a name='Success'>Successfully re-checked {$CURUSER['username']}!</a></h2>";
        if (isset($_GET["clearchecked"]) &&  $_GET["clearchecked"] == 'done')
            $HTMLOUT .="<h2><a name='Success'>Successfully un-checked {$CURUSER['username']}!</a></h2>";
    }
    // end

    $s = htmlentities( $row["name"], ENT_QUOTES );
		$HTMLOUT .= "<h1>$s</h1>\n";
    /** free mod for TBDev 09 by pdq **/
    $clr = '#FF6600'; /// font color	
    $freeimg = '<img src="pic/freedownload.gif" border="0" alt="" />';
    $doubleimg = '<img src="pic/doubleseed.gif" border="0" alt="" />';	
	  $HTMLOUT .= '
    <div id="balloon1" class="balloonstyle">
    Once chosen this torrent will be Freeleech '.$freeimg.' until '.get_date($row['freeslot'], 'DATE').' and can be resumed or started over using the regular download link. Doing so will result in one Freeleech Slot being taken away from your total.</div>
    <div id="balloon2" class="balloonstyle">
    Once chosen this torrent will be Doubleseed '.$doubleimg.' until '.get_date($row['doubleslot'], 'DATE').' and can be resumed or started over using the regular download link. Doing so will result in one Freeleech Slot being taken away from your total.</div>
    <script type="text/javascript" src="scripts/balloontip.js"></script>';
     /** end **/
    $HTMLOUT .= "<table align='center' width='750' border='1' cellspacing='0' cellpadding='5'>\n";
		$url = "edit.php?id=" . $row["id"];
		if (isset($_GET["returnto"])) {
			$addthis = "&amp;returnto=" . urlencode($_GET["returnto"]);
			$url .= $addthis;
			$keepget = $addthis;
		}
		$editlink = "a href=\"$url\" class=\"sublink\"";
    if (!($CURUSER["downloadpos"] == 0 && $CURUSER["id"] != $row["owner"] OR $CURUSER["downloadpos"] > 1)) {
		/** free mod for TBDev 09 by pdq **/
    require ROOT_DIR.'/mods/free_details.php';
    /** end **/
    ///== Mod by dokty - Tbdev.net
    $blasd = sql_query("SELECT points FROM coins WHERE torrentid=".sqlesc($id)." AND userid=" .sqlesc($CURUSER["id"]));
    $sdsa = mysql_fetch_assoc($blasd) or $sdsa["points"] = 0;
    $HTMLOUT .= tr("Points", "<b>In total " . htmlspecialchars($row["points"]) . " Points given to this torrent of which " . htmlspecialchars($sdsa["points"]) . " from you.<br /><br />By clicking on the coins you can give points to the uploader of this torrent.</b><br /><br />
    <a href='{$INSTALLER09['baseurl']}/coins.php?id=$id&amp;points=10'>
    <img src='{$INSTALLER09['pic_base_url']}10coin.jpg' alt='10 Points' title='10 Points' border='0' /></a>
    &nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/coins.php?id=$id&amp;points=20'>
    <img src='{$INSTALLER09['pic_base_url']}20coin.jpg' alt='20 Points' title='20 Points' border='0' /></a>
    &nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/coins.php?id=$id&amp;points=50'>
    <img src='{$INSTALLER09['pic_base_url']}50coin.jpg' alt='50 Points' title='50 Points' border='0' /></a>
    &nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/coins.php?id=$id&amp;points=100'>
    <img src='{$INSTALLER09['pic_base_url']}100coin.jpg' alt='100 Points' title='100 Points' border='0' /></a>
    &nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/coins.php?id=$id&amp;points=200'>
    <img src='{$INSTALLER09['pic_base_url']}200coin.gif' alt='200 Points' title='200 Points' border='0' /></a>
    &nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/coins.php?id=$id&amp;points=500'>
    <img src='{$INSTALLER09['pic_base_url']}500coin.gif' alt='500 Points' title='500 Points' border='0' /></a>
    &nbsp;&nbsp;<a href='{$INSTALLER09['baseurl']}/coins.php?id=$id&amp;points=1000'>
    <img src='{$INSTALLER09['pic_base_url']}1000coin.gif' alt='1000 Points' title='1000 Points' border='0' /></a>", 1);
    // //////////end bonus points for uploader///////
    /** pdq's ratio afer d/load **/
    $downl = ($CURUSER["downloaded"] + $row["size"]);
    $sr = $CURUSER["uploaded"] / $downl;
    switch (true)
    {
	  case ($sr >= 4):
		$s = "w00t";
		break;
	  case ($sr >= 2):
		$s = "grin";
		break;
	  case ($sr >= 1):
		$s = "smile1";
		break;
	  case ($sr >= 0.5):
		$s = "noexpression";
		break;
	  case ($sr >= 0.25):
		$s = "sad";
		break;
		case ($sr > 0.00):
		$s = "cry";
		break;
	  default;
		$s = "w00t";
		break;
    }
    $sr = floor($sr * 1000) / 1000;
	  $sr = "<font color='".get_ratio_color($sr)."'>".number_format($sr, 3)."</font>&nbsp;&nbsp;<img src=\"pic/smilies/{$s}.gif\" alt=\"\" />";
    if ($row['free'] >= 1 || $isfree['yep'] || $frees != 0 || $CURUSER['free_switch'] != 0) {
    $HTMLOUT .= "<tr><td align='right' class='heading'>Ratio After Download</td><td><del>{$sr}&nbsp;&nbsp;Your new ratio if you download this torrent.</del> <b><font size='' color='#FF0000'>[FREE]</font></b>&nbsp;(Only upload stats are recorded)</td></tr>";
    }else{
    $HTMLOUT .= "<tr><td align='right' class='heading'>Ratio After Download</td><td>{$sr}&nbsp;&nbsp;Your new ratio if you download this torrent.</td></tr>";
    }
		//==End
    $HTMLOUT .= tr("{$lang['details_info_hash']}", $row["info_hash"]);
    }
    else {
    $HTMLOUT .= tr("{$lang['details_download']}", "{$lang['details_dloadpos']}");
    }
    
  if (!empty($row["descr"]))
	$HTMLOUT .= "<tr><td style='vertical-align:top'><b>{$lang['details_description']}</b></td><td><a href=\"javascript: klappe_news('a1')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica1\" alt=\"[Hide/Show]\" /></a><div id=\"ka1\" style=\"display: none;\"><div style='background-color:#d9e2ff;width:100%;height:150px;overflow: auto'>". str_replace(array("\n", "  "), array("<br />\n", "&nbsp; "), format_comment( $row["descr"] ))."</div></div></td></tr>";
	//==09 Poster mod
  if (!empty($row["poster"]))
  $HTMLOUT .= tr("{$lang['details_poster']}", "<a href=\"javascript: klappe_news('a2')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica2\" alt=\"[Hide/Show]\" /></a><div id=\"ka2\" style=\"display: none;\"><img src='".$row["poster"]."' alt='Poster' title='Poster' /></div>", 1);
  else
  $HTMLOUT .= tr("{$lang['details_poster']}", "<a href=\"javascript: klappe_news('a2')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica2\" alt=\"[Hide/Show]\" /></a><div id=\"ka2\" style=\"display: none;\"><img src='{$INSTALLER09['pic_base_url']}noposter.png' alt='Poster' title='Poster' /></div>", 1);
	
	//==09 auto imdb mod 
  $smallth = '';
  if (($row["url"] != "")AND(strpos($row["url"], 'imdb'))AND(strpos($row["url"], 'title')))
  {
  $rurl = trim($row["url"]);
  $thenumbers = ltrim(strrchr($rurl,'tt'),'tt');  
  $thenumbers = ($thenumbers[strlen($thenumbers)-1] == "/" ? substr($thenumbers,0,strlen($thenumbers)-1) : $thenumbers); 
  $thenumbers = preg_replace("[^A-Za-z0-9]", "", $thenumbers);
  $movie = new imdb ($thenumbers); 
  $movieid = $thenumbers;
  $movie->setid ($movieid);
  $country = $movie->country ();
  $director = $movie->director();
  $write = $movie->writing();
  $produce = $movie->producer();
  $cast = $movie->cast();
  $plot = $movie->plot ();
  $compose = $movie->composer();
  $gen = $movie->genres();
  $plotoutline = $movie->plotoutline();
  $trailers = $movie->trailers();
  $mvlang = $movie->language();
  $mvrating = $movie->rating();
 
  if (($photo_url = $movie->photo_localurl() ) != FALSE) {
  $smallth = '<img width="85" src="'.$photo_url.'" alt="Imdb Picture" />';
  }
  
  $imdb='';
  $imdb.= "<a href=\"javascript: klappe_news('a3')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica3\" alt=\"[Hide/Show]\" /></a><div id=\"ka3\" style=\"display: none;\">\n";
  $imdb.= "<strong><font color=\"blue\">#######################################################################</font><br />\n";
  $imdb.= "<font color=\"red\" size=\"3\">Information:</font><br />\n";
  $imdb.= "<font color=\"blue\">#######################################################################</font></strong><br />\n";
  $imdb.= "<strong><font color=\"red\"> Title: </font></strong>" . "".$movie->title()."<br />\n";
  $imdb.= "<strong><font color=\"red\"> Year: </font></strong>" . "".$movie->year()."<br />\n";
  $imdb.= "<strong><font color=\"red\"> Runtime: </font></strong>" . "".$movie->runtime()."" . " mins<br />\n";
  $imdb.= "<strong><font color=\"red\"> Votes: </font></strong>" . "".$movie->votes()."<br />\n";

  if (!empty($mvrating)) {
	$imdb.= "<strong><font color=\"red\"> Rating: </font></strong>" . "$mvrating<br />\n";
  }

  if (!empty($mvlang)) {
	$imdb.= "<strong><font color=\"red\"> Language: </font></strong>" . "$mvlang<br />\n";
  }

  if (!empty($country)) {
	$imdb.= "<strong><font color=\"red\"> Country: </font></strong>";
	for ($i = 0; $i + 1 < count ($country); $i++) {
	$imdb.="$country[$i], ";
	}
	$imdb.= "$country[$i]<br />\n";
  }

  if (!empty($gen)) {
	$imdb.= "<strong><font color=\"red\"> All Genres: </font></strong>";
	for ($i = 0; $i + 1 < count($gen); $i++) {
	$imdb.= "$gen[$i], ";
	}
	$imdb.= "$gen[$i]<br />\n";
  }

  if (!empty($plotoutline)) { 
	$imdb.= "<strong><font color=\"red\"> Plot Outline: </font></strong>" . "$plotoutline</div><br />\n";
  }

  if (!empty($director)) {
	$imdb.= "<strong><font color=\"red\"> Director: </font></strong>";
	for ($i = 0; $i < count ($director); $i++) {
	$imdb.= "<a target=\"_blank\" href=\"http://www.imdb.com/name/nm" . "".$director[$i]["imdb"]."" ."\">" . "".$director[$i]["name"]."" . "</a><br />\n";
	}
  }

  if (!empty($write)) {
	$imdb.= "<strong><font color=\"red\"> Writing By: </font></strong>";
	for ($i = 0; $i < count ($write); $i++) {
		$imdb.= "<a target=\"_blank\" href=\"http://www.imdb.com/name/nm" . "".$write[$i]["imdb"]."" ."\">" . "".$write[$i]["name"]."" . "</a> ";
	}
  }

  if (!empty($produce)) {
	$imdb.= "<br />\n<strong><font color=\"red\"> Produced By: </font></strong>";
	for ($i = 0; $i < count ($produce); $i++) {
	$imdb.= "<a target=\"_blank\" href=\"http://www.imdb.com/name/nm" . "".$produce[$i]["imdb"]."" ." \">" . "".$produce[$i]["name"]."" . "</a> ";
	}	
  }

  if (!empty($compose)) {
	$imdb.= "<br />\n<strong><font color=\"red\"> Music: </font></strong>"; 
	for ($i = 0; $i < count($compose); $i++) {
	$imdb.= "<a target=\"_blank\" href=\"http://www.imdb.com/name/nm" . "".$compose[$i]["imdb"]."" ." \">" . "".$compose[$i]["name"]."" . "</a> "; 
	}
	}

  if (!empty($plot)) {
	$imdb.= "<br /><br />\n\n<strong><font color=\"blue\">#######################################################################</font><br />\n";
	$imdb.= "<font color=\"red\" size=\"3\"> Description:</font><br />\n";
	$imdb.= "<font color=\"blue\">#######################################################################</font></strong>"; 
	for ($i = 0; $i < count ($plot); $i++) {
	$imdb.= "<br />\n<font color=\"red\">...</font> ";
  $imdb.= "$plot[$i]";
	}
  }

  $imdb.= "<br /><br />\n\n<strong><font color=\"blue\">#######################################################################</font><br />\n";
  $imdb.= "<font color=\"red\" size=\"3\"> Cast:</font><br />\n";
  $imdb.= "<font color=\"blue\">#######################################################################</font></strong><br />\n";

  for ($i = 0; $i < count ($cast); $i++) {
	if ($i > 9) {
  break;
	}
  $imdb.= "<font color=\"red\">...</font> " . "<a target=\"_blank\" href=\"http://www.imdb.com/name/nm" . "".$cast[$i]["imdb"]."" ."\">" . "".$cast[$i]["name"]."" . "</a> " . " as <strong><font color=\"red\">" . "".$cast[$i]["role"]."" . " </font></strong><br />\n";
  }

  if (!empty($trailers)) {
  $imdb.= "<br /><br />\n\n<strong><font color=\"blue\">#######################################################################</font><br />\n";
  $imdb.= "<font color=\"red\" size=\"3\"> Trailers:</font><br />\n";
  $imdb.= "<font color=\"blue\">#######################################################################</font></strong><br />\n";

	for ($i=0;$i<count($trailers);++$i) {
	if ($i > 14) {
	break;
	}
	$imdb.= "<a target=\"_blank\" href='".$trailers[$i]."'>".$trailers[$i]."</a><br />\n";
	}
  }
  $imdb = str_replace('&','&amp;',$imdb);
  $HTMLOUT .= tr("Auto imdb$smallth", $imdb."</div>", 1);
  }
  //end auto imdb
  
  $movie_cat = array("3","5","6","10","11"); //add here your movie category 
	if (in_array($row["category"], $movie_cat) && !empty($row["subs"]) )
  {
	$HTMLOUT .="<tr><td class='rowhead'>Subtitles</td><td align='left'>";
	$subs_array = explode(",",$row["subs"]);
  foreach ($subs_array as $k => $sid) {
	require_once(CACHE_DIR.'subs.php');
	foreach ($subs as $sub){
	if ($sub["id"] == $sid)
	$HTMLOUT .="<img border=\"0\" width=\"25px\" style=\"padding:3px;\"src=\"".$sub["pic"]."\" alt=\"".$sub["name"]."\" title=\"".$sub["name"]."\" />";
	}
	}
	$HTMLOUT .="</td></tr>\n";
  }
    if ($CURUSER["class"] >= UC_POWER_USER && $row["nfosz"] > 0)
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['details_nfo']}</td><td align='left'><a href='viewnfo.php?id=$row[id]'><b>{$lang['details_view_nfo']}</b></a> (" .mksize($row["nfosz"]) . ")</td></tr>\n";
      
		if ($row["visible"] == "no")
			$HTMLOUT .= tr("{$lang['details_visible']}", "<b>{$lang['details_no']}</b>{$lang['details_dead']}", 1);
		if ($moderator)
			$HTMLOUT .= tr("{$lang['details_banned']}", $row["banned"]);

    if ($row["nuked"] == "yes")
    $HTMLOUT .= "<tr><td class='rowhead'><b>Nuked</b></td><td align='left'><img src='{$INSTALLER09['pic_base_url']}nuked.gif' alt='Nuked' title='Nuked' /></td></tr>\n";
    if (!empty($row["nukereason"]))
    $HTMLOUT .= "<tr><td class='rowhead'><b>Nuke-Reason</b></td><td align='left'>".htmlspecialchars($row["nukereason"])."</td></tr>\n";

		if (isset($row["cat_name"]))
			$HTMLOUT .= tr("{$lang['details_type']}", $row["cat_name"]);
		else
			$HTMLOUT .= tr("{$lang['details_type']}", "{$lang['details_none']}");
		
		$s = "";
		$s .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td valign=\"top\" class=\"embedded\">";
		if (!isset($row["rating"])) {
			if ($INSTALLER09['minvotes'] > 1) {
				$s .= "none yet (needs at least {$INSTALLER09['minvotes']} votes and has got ";
				if ($row["numratings"])
					$s .= "only " . $row["numratings"];
				else
					$s .= "none";
				$s .= ")";
			}
			else
				$s .= "No votes yet";
		}
		else {
			$rpic = ratingpic($row["rating"]);
			if (!isset($rpic))
				$s .= "invalid?";
			else
				$s .= "$rpic (" . $row["rating"] . " out of 5 with " . $row["numratings"] . " vote(s) total)";
		}
		$s .= "\n";
		$s .= "</td><td class=\"embedded\">$spacer</td><td valign=\"top\" class=\"embedded\">";

			$ratings = array(
					5 => "Kewl!",
					4 => "Pretty good",
					3 => "Decent",
					2 => "Pretty bad",
					1 => "Sucks!");
			if (!$owned || $moderator) {
			if (!empty($row['numratings'])){
      $xres = sql_query("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $CURUSER["id"]);
      $xrow = mysql_fetch_assoc($xres);
      }
      if (!empty($xrow))
					$s .= "(you rated this torrent as \"" . $xrow["rating"] . " - " . $ratings[$xrow["rating"]] . "\")";
				  else {
					$s .= "<form method=\"post\" action=\"takerate.php\"><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
					$s .= "<select name=\"rating\">\n";
					$s .= "<option value=\"0\">(add rating)</option>\n";
					foreach ($ratings as $k => $v) {
						$s .= "<option value=\"$k\">$k - $v</option>\n";
					}
					$s .= "</select>\n";
					$s .= "<input type=\"submit\" value=\"Vote!\" />";
					$s .= "</form>\n";
				}
			}
		
		$s .= "</td></tr></table>";
		$HTMLOUT .= tr("Rating", $s, 1);
		
  	$HTMLOUT .= tr("{$lang['details_last_seeder']}", "{$lang['details_last_activity']}" .get_date( $row['lastseed'],'',0,1));
		$HTMLOUT .= tr("{$lang['details_size']}",mksize($row["size"]) . " (" . number_format($row["size"]) . "{$lang['details_bytes']})");
		$HTMLOUT .= tr("{$lang['details_added']}", get_date( $row['added'],"{$lang['details_long']}"));
		$HTMLOUT .= tr("{$lang['details_views']}", $row["views"]);
		$HTMLOUT .= tr("{$lang['details_hits']}", $row["hits"]);
		$HTMLOUT .= tr("{$lang['details_snatched']}", ($row["times_completed"] > 0 ? "<a href='./snatches.php?id=$id'>$row[times_completed] {$lang['details_times']}</a>" : "0 {$lang['details_times']}"), 1);
    //==Reputation
    $member_reputation = get_reputation($row, 'torrents');
    $HTMLOUT.= "<tr><td class='rowhead' valign='top' align='right' width='1%'>Torrent<br/>{$lang['details_rep']}</td><td align='left' width='99%'>
    {$member_reputation} (counts towards uploaders Reputation)<br />
    </td></tr>";
		//==Anonymous
		if($row['anonymous'] == 'yes') {
    if ($CURUSER['class'] < UC_UPLOADER)
    $uprow = "<i>Anonymous</i>";
    else
    $uprow = "<i>Anonymous</i> (<a href='userdetails.php?id=$row[owner]'><b>$row[username]</b></a>)";
    }
    else {
		$uprow = (isset($row["username"]) ? ("<a href='./userdetails.php?id=" . $row["owner"] . "'><b>" . htmlspecialchars($row["username"]) . "</b></a>") : "<i>{$lang['details_unknown']}</i>");
		}
		if ($owned)
		$uprow .= " $spacer<$editlink><b>{$lang['details_edit']}</b></a>";
		$HTMLOUT .= tr("Upped by", $uprow, 1);
    //==pdq's Torrent Moderation
    if ($CURUSER['class'] >= UC_MODERATOR) {
       if (!empty($row['checked_by'])) {
           $checked_by = sql_query("SELECT id FROM users WHERE username='$row[checked_by]'");
           $checked = mysql_fetch_array($checked_by);
           $HTMLOUT .="<tr><td class='rowhead'>Checked by</td><td align='left'><a href='{$INSTALLER09["baseurl"]}/userdetails.php?id=".$checked['id']."'><strong>
           ".htmlspecialchars($row['checked_by'])."</strong></a> 
           <img src='{$INSTALLER09['pic_base_url']}mod.gif' width='15' border='0' alt='Checked' title='Checked - by ".htmlspecialchars($row['checked_by'])."' />
           <a href='{$INSTALLER09["baseurl"]}/details.php?id=".$row['id']."&amp;rechecked=1'><small><em><strong>[Re-Check this torrent]</strong></em></small></a> 
            <a href='{$INSTALLER09["baseurl"]}/details.php?id=".$row['id']."&amp;clearchecked=1'><small><em><strong>[Un-Check this torrent]</strong></em></small></a>  * STAFF Eyes Only *</td></tr>";
       }
       else {
       $HTMLOUT .="<tr><td class='rowhead'>Checked by</td><td align='left'><font color='#ff0000'><strong>NOT CHECKED!</strong></font> 
       <a href='{$INSTALLER09["baseurl"]}/details.php?id=".$row['id']."&amp;checked=1'>
       <small><em><strong>[Check this torrent]</strong></em></small></a>  * STAFF Eyes Only *</td></tr>";
       }
   }
   // end
		if ($row["type"] == "multi") {  
		if (!isset($_GET["filelist"]))
		$HTMLOUT .= tr("{$lang['details_num_files']}<br /><a href=\"./filelist.php?id=$id\" class=\"sublink\">{$lang['details_list']}</a>", $row["numfiles"] . " files", 1);
	  else {
	  $HTMLOUT .= tr("{$lang['details_num-files']}", $row["numfiles"] . "{$lang['details_files']}", 1);	
	  }
		}
		
		$HTMLOUT .= tr("{$lang['details_peers']}<br /><a href=\"peerlist.php?id=$id#seeders\" class=\"sublink\">{$lang['details_list']}</a>", $row["seeders"] . " seeder(s), " . $row["leechers"] . " leecher(s) = " . ($row["seeders"] + $row["leechers"]) . "{$lang['details_peer_total']}", 1);
		
		//==putyns thanks mod
		$HTMLOUT .= tr($lang['details_thanks'],'
	  <script type="text/javascript">
		/*<![CDATA[*/
		$(document).ready(function() {
			var tid = '.$id.';
			show_thanks(tid);
		});
		/*]]>*/
		</script>
		<noscript><iframe id="thanked" src ="thanks.php?torrentid='.$id.'" style="width:500px;height:50px;border:none;overflow:auto;">
	  <p>Your browser does not support iframes. And it has Javascript disabled!</p>
	  </iframe></noscript>
	  <div id="thanks_holder"></div>',1);
		//==End
		//==Report Torrent Link
		$HTMLOUT .= tr("Report Torrent", "<form action='report.php?type=Torrent&amp;id=$id' method='post'><input class='button' type='submit' name='submit' value='Report This Torrent' /> For breaking the <a href='rules.php'>rules</a></form>", 1);
		//==09 Reseed
		$next_reseed = 0; 
  	if ($row["last_reseed"] > 0)
	  $next_reseed = ($row["last_reseed"] + 172800 ); //add 2 days 
	  $reseed = "<form method=\"post\" action=\"./takereseed.php\">
	  <select name=\"pm_what\">
	  <option value=\"last10\">last10</option>
	  <option value=\"owner\">uploader</option>
	  </select>
	  <input type=\"submit\"  ".(($next_reseed > time()) ? "disabled='disabled'" : "" )." value=\"SendPM\" />
	  <input type=\"hidden\" name=\"uploader\" value=\"" . $row["owner"] . "\" />
	  <input type=\"hidden\" name=\"reseedid\" value=\"$id\" />
	  </form>";	
	  $HTMLOUT .= tr("Request reseed", $reseed,1);
		//==End
		$HTMLOUT .= "<tr><td class='rowhead'>Status update</td><td><input type='button' onclick='status_showbox(\"{$CURUSER['username']} is viewing details for torrent {$INSTALLER09['baseurl']}/details.php?id={$row['id']}\")' value='do it!'/></td></tr>";
		$HTMLOUT .= "</table>";
		$HTMLOUT .= "<h1>{$lang['details_comments']}<a href='details.php?id=$id'>" . htmlentities( $row["name"], ENT_QUOTES ) . "</a></h1>\n";

    if ($row["allow_comments"] == "yes" || $CURUSER['class'] >= UC_MODERATOR && $CURUSER['class'] <= UC_SYSOP) {
    $HTMLOUT .= "<p><a name=\"startcomments\"></a></p>\n";
    } else {
    $HTMLOUT .="<table width='750' border=\"1\" cellspacing=\"0\" cellpadding=\"5\">
    <tr>
    <td class='colhead' align='left' colspan='2'><a name='startcomments'>&nbsp;</a><b>{$lang['details_com_disabled']}</b></td>
    </tr>
    </table>\n";
    print stdhead("{$lang['details_details']}\"" . htmlentities($row["name"], ENT_QUOTES) . "\"") . $HTMLOUT . stdfoot($stdfoot);
    die();
    }
    
    $commentbar = "<p align='center'><a class='index' href='comment.php?action=add&amp;tid=$id'>{$lang['details_add_comment']}</a></p>\n";

    $count = $row['comments'];

    if (!$count) 
    {
      $HTMLOUT .= "<h2>{$lang['details_no_comment']}</h2>\n";
    }
    else 
    {
		$pager = pager(20, $count, "details.php?id=$id&amp;", array('lastpagedefault' => 1));

		$subres = sql_query("SELECT comments.id, text, user, torrent, comments.added, comments.anonymous, editedby, editedat, avatar, av_w, av_h, offavatar, warned, reputation, username, title, class, donor FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = $id ORDER BY comments.id ".$pager['limit']) or sqlerr(__FILE__, __LINE__);
		
		$allrows = array();
		while ($subrow = mysql_fetch_assoc($subres))
			$allrows[] = $subrow;

		$HTMLOUT .= $commentbar;
		$HTMLOUT .= $pager['pagertop'];

		$HTMLOUT .= commenttable($allrows);

		$HTMLOUT .= $pager['pagerbottom'];
	}

    $HTMLOUT .= $commentbar;

///////////////////////// HTML OUTPUT ////////////////////////////
    print stdhead("{$lang['details_details']}\"" . htmlentities($row["name"], ENT_QUOTES) . "\"") . $HTMLOUT . stdfoot($stdfoot);
?>