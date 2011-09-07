<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
function linkcolor($num) {
    if (!$num)
    return "red";
    return "green";
}

function readMore($text, $char, $link)
{
    return (strlen($text) > $char ? substr(htmlspecialchars($text), 0, $char-1) . "...<br /><a href='$link'>Read more...</a>": htmlspecialchars($text));
}

function torrenttable($res, $variant = "index") {
    global $INSTALLER09, $CURUSER, $lang, $free, $mc1;
    $htmlout = '';
    require_once(INCL_DIR.'getpre.php');
    /** ALL FREE/DOUBLE **/
    foreach($free as $fl) {
    switch ($fl['modifier']) {
    case 1:
    $free_display = '[Free]';
    break;
    case 2:
   $free_display = '[Double]';
    break;
    case 3:
    $free_display = '[Free and Double]';
    break;
}
$all_free_tag = ($fl['modifier'] != 0 && ($fl['expires'] > TIME_NOW || $fl['expires'] == 1) ? ' <a class="info" href="#">
            <b>'.$free_display.'</b> 
            <span>'. ($fl['expires'] != 1 ? '
            Expires: '.get_date($fl['expires'], 'DATE').'<br />
            ('.mkprettytime($fl['expires'] - time()).' to go)</span></a><br />' : 'Unlimited</span></a><br />') : '');
}

    
    $prevdate ="";
    $count_get = 0;
    $oldlink = $char = $description = $type = $sort = $row = '';
    foreach ($_GET as $get_name => $get_value) {
        $get_name = strip_tags(str_replace(array("\"", "'"), array("", ""), $get_name));
        $get_value = strip_tags(str_replace(array("\"", "'"), array("", ""), $get_value));
        if ($get_name != "sort" && $get_name != "type") {
            if ($count_get > 0) {
                $oldlink = $oldlink . "&amp;" . $get_name . "=" . $get_value;
            } else {
                $oldlink = ($oldlink) . $get_name . "=" . $get_value;
            }
            $count_get++;
        }
    }

    if ($count_get > 0) {
        $oldlink = $oldlink . "&amp;";
    }
    
    $links = array('link1','link2','link3','link4','link5','link6','link7','link8','link9');
    $i =1;
    foreach($links as $link) {
    if(isset($_GET['sort']) && $_GET['sort'] == $i)
	  $$link = (isset($_GET['type']) && $_GET['type'] == 'desc') ? 'asc' : 'desc';
    else
	  $$link = 'desc';
    $i++;
    }
  
    $htmlout .= "<table border='1' cellspacing='0' cellpadding='5'>
   <tr>
   <td class='colhead' align='center'>{$lang["torrenttable_type"]}</td>
   <td class='colhead' align='left'><a href='{$_SERVER["PHP_SELF"]}?{$oldlink}sort=1&amp;type={$link1}'>{$lang["torrenttable_name"]}</a></td>
   <td class='colhead' align='left'>{$lang["torrenttable_subtitles"]}</td>
   <td class='colhead' align='left'><img src='{$INSTALLER09['pic_base_url']}zip.gif' border='0' alt='Download' title='Download' /></td>";
   
   $htmlout.= ($variant == 'index' ? "<td class='colhead' align='center'><a href='{$INSTALLER09['baseurl']}/bookmarks.php'><img src='{$INSTALLER09['pic_base_url']}bookmark.gif'  border='0' alt='Bookmark' title='Go To My Bookmarks' /></a></td>" : '');

   if ($variant == "mytorrents")
   {
   $htmlout .= "<td class='colhead' align='center'>{$lang["torrenttable_edit"]}</td>\n";
   $htmlout .= "<td class='colhead' align='center'>{$lang["torrenttable_visible"]}</td>\n";
   }
 
   $htmlout .= "<td class='colhead' align='right'><a href='{$_SERVER["PHP_SELF"]}?{$oldlink}sort=2&amp;type={$link2}'>{$lang["torrenttable_files"]}</a></td>
   <td class='colhead' align='right'><a href='{$_SERVER["PHP_SELF"]}?{$oldlink}sort=3&amp;type={$link3}'>{$lang["torrenttable_comments"]}</a></td>
   <td class='colhead' align='center'><a href='{$_SERVER["PHP_SELF"]}?{$oldlink}sort=4&amp;type={$link4}'>{$lang["torrenttable_added"]}</a></td>
   <td class='colhead' align='center'><a href='{$_SERVER["PHP_SELF"]}?{$oldlink}sort=5&amp;type={$link5}'>{$lang["torrenttable_size"]}</a></td>
   <td class='colhead' align='center'><a href='{$_SERVER["PHP_SELF"]}?{$oldlink}sort=6&amp;type={$link6}'>{$lang["torrenttable_snatched"]}</a></td>
   <td class='colhead' align='right'><a href='{$_SERVER["PHP_SELF"]}?{$oldlink}sort=7&amp;type={$link7}'>{$lang["torrenttable_seeders"]}</a></td>
   <td class='colhead' align='right'><a href='{$_SERVER["PHP_SELF"]}?{$oldlink}sort=8&amp;type={$link8}'>{$lang["torrenttable_leechers"]}</a></td>";


   if ($variant == 'index')
   $htmlout .= "<td class='colhead' align='center'><a href='{$_SERVER["PHP_SELF"]}?{$oldlink}sort=9&amp;type={$link9}'>{$lang["torrenttable_uppedby"]}</a></td>\n";
   $htmlout .= "</tr>\n";

    while ($row = mysql_fetch_assoc($res)) 
    {
       $id = $row["id"];
       if ($row["sticky"] == "yes")
       $htmlout .= "<tr class='highlight'>\n";
       else
       $htmlout .= "<tr class='browse'>\n";
       $htmlout .= "<td align='center' style='padding: 0px'>";
       if (isset($row["cat_name"])) 
       {
       $htmlout .= "<a href='browse.php?cat={$row['category']}'>";
       if (isset($row["cat_pic"]) && $row["cat_pic"] != "")
       $htmlout .= "<img border='0' src='{$INSTALLER09['pic_base_url']}caticons/{$row['cat_pic']}' alt='{$row['cat_name']}' />";
       else
       {
       $htmlout .= $row["cat_name"];
       }
       $htmlout .= "</a>";
       }
       else
       {
       $htmlout .= "-";
       }
       $htmlout .= "</td>\n";
       $dispname = htmlspecialchars($row["name"]);
       $checked = ((!empty($row['checked_by']) && $CURUSER['class'] >= UC_USER) ? "&nbsp;<img src='{$INSTALLER09['pic_base_url']}mod.gif' width='15' border='0' alt='Checked - by ".htmlspecialchars($row['checked_by'])."' title='Checked - by ".htmlspecialchars($row['checked_by'])."' />" : "");
       $poster = empty($row["poster"]) ? "<img src=\'{$INSTALLER09['pic_base_url']}noposter.png\' width=\'150\' height=\'220\' border=\'0\' alt=\'Poster\' title=\'poster\' />" : "<img src=\'".htmlspecialchars($row['poster'])."\' width=\'150\' height=\'220\' border=\'0\' alt=\'Poster\' title=\'poster\' />";
       $rating = empty($row["rating"]) ? "No votes yet":"".ratingpic($row["rating"]).""; 
       
       if ($row["descr"])
       $descr = str_replace("\"", "&quot;", readMore($row["descr"], 350, "details.php?id=" . $row["id"] . "&amp;hit=1"));
       $htmlout .= "<td align='left'><a href='details.php?";
     
       if ($variant == "mytorrents")
       $htmlout .= "returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;";
       $htmlout .= "id=$id";
       if ($variant == "index")
       $htmlout .= "&amp;hit=1";
       $pre = (!empty($row["pretime"]) ? "&nbsp;Uploaded: ".get_pretime($row["pretime"])." after pre." : "No pretime was found" );
       $newgenre = (!empty($row["newgenre"]) ? "<i>(" . htmlspecialchars($row["newgenre"]) . ")</i>" : "" );
       $sticky = ($row['sticky']=="yes" ? "<img src='{$INSTALLER09['pic_base_url']}sticky.gif' style='border:none' alt='Sticky' title='Sticky !' />" : "");
       $nuked = ($row["nuked"] == "yes" ? "<img src='{$INSTALLER09['pic_base_url']}nuked.gif' style='border:none' alt='Nuked'  align='right' title='Reason :".htmlspecialchars($row["nukereason"])."' />" : "");
       $release_group = ($row['release_group']=="scene" ? "&nbsp;<img src='{$INSTALLER09['pic_base_url']}scene.gif' title='Scene' alt='Scene' style='border:none' />" : ($row['release_group']=="p2p" ? "&nbsp;<img src='{$INSTALLER09['pic_base_url']}p2p.gif' title='P2P' alt='P2P' />" : "")); 
       $viponly = ($row["vip"]== "1" ? "<img src='{$INSTALLER09['pic_base_url']}/star.png' border='0' alt='Vip Torrent' title='Vip Torrent' />" : "");
       /** FREE Torrent **/
       $free_tag = ($row['free'] != 0 ? ' <a class="info" href="#"><b>[FREE]</b> <span>'. ($row['free'] > 1 ? 'Expires: '.get_date($row['free'], 'DATE').'<br />('.mkprettytime($row['free'] - TIME_NOW).' to go)<br />' : 'Unlimited<br />').'</span></a>' : $all_free_tag);
       /** Freeslot Slot in Use **/
       $isdlfree = ($row['tid'] == $id && $row['uid'] == $CURUSER['id'] && $row['freeslot'] != 0 ? '<a class="info" href="#"><img src="'.$INSTALLER09['baseurl'].'/pic/freedownload.gif" alt="" /><span>Freeleech slot in use<br />'.($row['freeslot'] != 0 ? ($row['freeslot'] > 1 ? 'Expires: '.get_date($row['freeslot'], 'DATE').'<br />('.mkprettytime($row['freeslot'] - TIME_NOW).' to go)<br />' : 'Unlimited<br />') : '').'</span></a>' : '');
       /** Double Upload Slot in Use **/
       $isdouble = ($row['tid'] == $id && $row['uid'] == $CURUSER['id'] && $row['doubleup'] != 0 ? ' <a class="info" href="#"><img src="'.$INSTALLER09['baseurl'].'/pic/doubleseed.gif" alt="" /><span>Double Upload slot in use<br />'.($row['doubleup'] != 0 ? ($row['doubleup'] > 1 ? 'Expires: '.get_date($row['doubleup'], 'DATE').'<br />('.mkprettytime($row['doubleup'] - TIME_NOW).' to go)<br />' : 'Unlimited<br />') : '').'</span></a>' : '');
       $htmlout .= "' onmouseover=\"Tip('<b>" . CutName($dispname, 80) . "</b><br /><b>Added:&nbsp;".get_date($row['added'],'DATE',0,1)."</b><br /><b>Size:&nbsp;".mksize(htmlspecialchars($row["size"])) ."</b><br /><b>Seeders:&nbsp;".htmlspecialchars($row["seeders"]) ."</b><br /><b>Leechers:&nbsp;".htmlspecialchars($row["leechers"]) ."</b><br /><b>Rating:&nbsp;".htmlspecialchars($rating) ."</b><br />$poster');\" onmouseout=\"UnTip();\"><b>" . CutName($dispname, 45) . "</b></a>&nbsp;&nbsp;<a href=\"javascript:klappe_descr('descr" . $row["id"] . "');\" ><img src=\"/pic/plus.gif\" border=\"0\" alt=\"Show torrent info in this page\" title=\"Show torrent info in this page\" /></a>&nbsp;&nbsp;$viponly&nbsp;$release_group&nbsp;$sticky&nbsp;".($row['added'] >= $CURUSER['last_browse'] ? " <img src='{$INSTALLER09['pic_base_url']}newb.png' border='0' alt='New !' title='New !' />" : "")."&nbsp;$checked&nbsp;$free_tag&nbsp;$nuked<br />\n".$isdlfree.$isdouble."&nbsp;$newgenre&nbsp;$pre</td>\n";
     
       $movie_cat = array("3","5","6","10","11"); //add here your movie category 
       $htmlout .="<td align=\"center\" nowrap=\"nowrap\" >\n";
       if (in_array($row["category"], $movie_cat) && !empty($row["subs"]) )
       {
       $subs_array = explode(",",$row["subs"]);
       require_once(CACHE_DIR.'subs.php');
       foreach ($subs_array as $k => $sid) {
       foreach ($subs as $sub){
       if ($sub["id"] == $sid)
       $htmlout .="<img border=\"0\" width=\"16px\" style=\"padding:3px;\"src=\"".$sub["pic"]."\" alt=\"".$sub["name"]."\" title=\"".$sub["name"]."\" />";
       }
       }
       }else
       $htmlout .="---";
       $htmlout .="</td>";

	     if ($variant == "mytorrents")
       $htmlout .= "<td align='center'><a href=\"download.php?torrent=".$id."\"><img src='".$INSTALLER09['pic_base_url']."zip.gif' border='0' alt='Download This Torrent!' title='Download This Torrent!' /></a></td>\n";
	        
       if ($variant == "mytorrents")  
       $htmlout .= "<td align='center'><a href='edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id={$row['id']}'>".$lang["torrenttable_edit"]."</a></td>\n";
       $htmlout.= ($variant == "index" ? "<td align='center'><a href=\"download.php?torrent=".$id."\"><img src='".$INSTALLER09['pic_base_url']."zip.gif' border='0' alt='Download This Torrent!' title='Download This Torrent!' /></a></td>" : "");
        
       if ($variant == "mytorrents") 
       {
       $htmlout .= "<td align='right'>";
       if ($row["visible"] == "no")
       $htmlout .= "<b>".$lang["torrenttable_not_visible"]."</b>";
       else
       $htmlout .= "".$lang["torrenttable_visible"]."";
       $htmlout .= "</td>\n";
       }
        
       $bookmarked = (!isset($row["bookmark"]) ? "<a href='bookmark.php?torrent=".$id."&amp;action=add'><img src='".$INSTALLER09['pic_base_url']."bookmark.gif' border='0' alt='Bookmark it!' title='Bookmark it!' /></a>":"<a href='bookmark.php?torrent=".$id."&amp;action=delete'><img src='".$INSTALLER09['pic_base_url']."plus2.gif' border='0' alt='Delete Bookmark!' title='Delete Bookmark!' /></a>");
        
       if ($variant == "index")  
       $htmlout.="<td align='right'>{$bookmarked}</td>"; 

       if ($row["type"] == "single")
       {
       $htmlout .= "<td align='right'>{$row["numfiles"]}</td>\n";
       }
       else 
       {
       if ($variant == "index")
       {
       $htmlout .= "<td align='right'><b><a href='filelist.php?id=$id'>" . $row["numfiles"] . "</a></b></td>\n";
       }
       else
       {
       $htmlout .= "<td align='right'><b><a href='filelist.php?id=$id'>" . $row["numfiles"] . "</a></b></td>\n";
       }
       }

       if (!$row["comments"])
       {
       $htmlout .= "<td align='right'>{$row["comments"]}</td>\n";
       }
       else 
       {
       if ($variant == "index")
       {
       $htmlout .= "<td align='right'><b><a href='details.php?id=$id&amp;hit=1&amp;tocomm=1'>" . $row["comments"] . "</a></b></td>\n";
       }
       else
       {
       $htmlout .= "<td align='right'><b><a href='details.php?id=$id&amp;page=0#startcomments'>" . $row["comments"] . "</a></b></td>\n";
       }
       }

       $htmlout .= "<td align='center'><span style='white-space: nowrap;'>" . str_replace(",", "<br />", get_date( $row['added'],'')) . "</span></td>\n";
       $htmlout .= "<td align='center'>" . str_replace(" ", "<br />", mksize($row["size"])) . "</td>\n";
       
       if ($row["times_completed"] != 1)
       $_s = "".$lang["torrenttable_time_plural"]."";
       else
       $_s = "".$lang["torrenttable_time_singular"]."";
       $htmlout .= "<td align='center'><a href='snatches.php?id=$id'>" . number_format($row["times_completed"]) . "<br />$_s</a></td>\n";

       if ($row["seeders"]) 
       {
       if ($variant == "index")
       {
       if ($row["leechers"]) $ratio = $row["seeders"] / $row["leechers"]; else $ratio = 1;
       $htmlout .= "<td align='right'><b><a href='peerlist.php?id=$id#seeders'><font color='" .get_slr_color($ratio) . "'>{$row["seeders"]}</font></a></b></td>\n";
       }
       else
       {
       $htmlout .= "<td align='right'><b><a class='".linkcolor($row["seeders"])."' href='peerlist.php?id=$id#seeders'>{$row["seeders"]}</a></b></td>\n";
       }
       }
       else
       {
       $htmlout .= "<td align='right'><span class='".linkcolor($row["seeders"])."'>".$row["seeders"]."</span></td>\n";
       }

       if ($row["leechers"]) 
       {
       if ($variant == "index")
       $htmlout .= "<td align='right'><b><a href='peerlist.php?id=$id#leechers'>".number_format($row["leechers"])."</a></b></td>\n";
       else
       $htmlout .= "<td align='right'><b><a class='".linkcolor($row["leechers"])."' href='peerlist.php?id=$id#leechers'>{$row["leechers"]}</a></b></td>\n";
       }
       else
       $htmlout .= "<td align='right'>0</td>\n";
        
       if ($variant == "index") {
       if ($row["anonymous"] == "yes") {
       $htmlout .= "<td align='center'><i>Anonymous</i></td>\n";
       }
       else {
       $htmlout .= "<td align='center'>".(isset($row["username"]) ? ("<a href='{$INSTALLER09['baseurl']}/userdetails.php?id=".$row["owner"]."'><b>{$row["username"]}</b></a>") : "<i>(".$lang["torrenttable_unknown_uploader"].")</i>") . "</td>\n";
       }
       }
       $htmlout .= "</tr>\n";
       $htmlout .="<tr id=\"kdescr{$row["id"]}\" style=\"display:none;\"><td width=\"100%\" colspan=\"13\">{$descr}</td></tr>\n";
       }
       $htmlout .= "</table>\n";
       return $htmlout;
       }  
        
function commenttable($rows, $variant = 'torrent') {
	  require_once(INCL_DIR.'html_functions.php');
	  global $CURUSER, $INSTALLER09;
	  $lang = load_language( 'torrenttable_functions' );
	  $htmlout = '';
	  $count = 0;
	  $variant_options = array('torrent' => 'details', 'request' => 'viewrequests', 'offer' => 'viewoffers');                  
    if (isset($variant_options[$variant])) 
    $locale_link = $variant_options[$variant];
    else
    return;
    $extra_link = ($variant == 'request' ? '&type=request' : ($variant == 'offer' ? '&type=offer' : ''));
	  $htmlout .= begin_main_frame();
	  $htmlout .= begin_frame();
	  foreach ($rows as $row) {
		$htmlout .= "<p class='sub'>#{$row["id"]} {$lang["commenttable_by"]} ";
    if (isset($row["username"])) {
    if ($row['anonymous'] == 'yes') {
    $htmlout .= ($CURUSER['class'] >= UC_MODERATOR ? 'Anonymous - Posted by: <b>'.htmlspecialchars($row['username']).'</b> ID: '.$row['user'].'' : 'Anonymous').' ';
    } else {
    $title = $row["title"];
    if ($title == "")
    $title = get_user_class_name($row["class"]);
    else
    $title = htmlspecialchars($title);
    $username = htmlspecialchars($row['username']);    
    $avatar1 = empty($row["avatar"]) ? "<img src=\'{$INSTALLER09['pic_base_url']}default_avatar.gif\' width=\'150\' height=\'150\' border=\'0\' alt=\'Avatar\' title=\'Avatar\' />" : "<img src=\'".htmlspecialchars($row['avatar'])."\' width=\'150\' height=\'220\' border=\'0\' alt=\'Avatar\' title=\'Avatar\' />";       
    $htmlout .= "<a name='comm{$row["id"]}' onmouseover=\"Tip('<b>$username</b><br />$avatar1');\" onmouseout=\"UnTip();\" href='userdetails.php?id={$row["user"]}'><b>".htmlspecialchars($row["username"])."</b></a>".($row["donor"] == "yes" ? "<img src='{$INSTALLER09['pic_base_url']}star.gif' alt='".$lang["commenttable_donor_alt"]."' />" : "") . ($row["warned"] == "yes" ? "<img src='{$INSTALLER09['pic_base_url']}warned.gif' alt='".$lang["commenttable_warned_alt"]."' />" : "")." ($title)\n";
    }
    }
		else
    $htmlout .= "<a name='comm{$row["id"]}'><i>(".$lang["commenttable_orphaned"].")</i></a>\n";
		$htmlout .= get_date( $row['added'],'');
		$htmlout .= ($row["user"] == $CURUSER["id"] || $CURUSER["class"] >= UC_STAFF ? "- [<a href='comment.php?action=edit&amp;cid=".$row['id'].$extra_link."&amp;tid=".$row[$variant]."'>".$lang["commenttable_edit"]."</a>]" : "") .
		($CURUSER["class"] >= UC_VIP ? " - [<a href='report.php?type=Comment&amp;id=".$row['id']."'>Report this Comment</a>]" : "") .
		($CURUSER["class"] >= UC_STAFF ? " - [<a href='comment.php?action=delete&amp;cid=".$row['id'].$extra_link."&amp;tid=".$row[$variant]."'>".$lang["commenttable_delete"]."</a>]" : "") .
		($row["editedby"] && $CURUSER["class"] >= UC_STAFF ? "- [<a href='comment.php?action=vieworiginal&amp;cid=".$row['id'].$extra_link."&amp;tid=".$row[$variant]."'>".$lang["commenttable_view_original"]."</a>]" : "") . "</p>\n";
		$avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");
		if (!$avatar)
	  $avatar = "{$INSTALLER09['pic_base_url']}default_avatar.gif";
		$text = format_comment($row["text"]);
    if ($row["editedby"])
    $text .= "<p><font size='1' class='small'>".$lang["commenttable_last_edited_by"]." <a href='userdetails.php?id={$row['editedby']}'><b>{$row['username']}</b></a> ".$lang["commenttable_last_edited_at"]." ".get_date($row['editedat'],'DATE')."</font></p>\n";
		$htmlout .= begin_table(true);
		$htmlout .= "<tr valign='top'>\n";
		$htmlout .= "<td align='center' width='150' style='padding: 0px'><img src='{$avatar}' alt='' /><br />".get_reputation($row, 'comments')."</td>\n";
		$htmlout .= "<td class='text'>$text</td>\n";
		$htmlout .= "</tr>\n";
    $htmlout .= end_table();
    }
	  $htmlout .= end_frame();
	  $htmlout .= end_main_frame();
	  return $htmlout;
    }
?>