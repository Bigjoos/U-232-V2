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
|   $Date$ 070810
|   $Revision$ 2.0
|   $Author$ Bigjoos
|   $input putyn,pdq,snuggs
|   $URL$
|   $news
|   
+------------------------------------------------
*/
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
require_once(INCL_DIR.'bbcode_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_STAFF);

$HTMLOUT='';
$stdfoot = array(/** include js **/'js' => array('shout'));
$lang = array_merge( $lang, load_language('ad_news') );
$mode = isset($_GET["mode"]) ?$_GET["mode"] : '';

//==Delete news
if ($mode == 'delete') {
    $newsid = (int)$_GET['newsid'];
    if (!is_valid_id($newsid))
    stderr("Error", "Invalid ID.");
    $hash = md5('the@@saltto66??' . $newsid . 'add' . '@##mu55y==');
    $sure='';
    $returnto = isset($_POST['returnto']) ? htmlentities($_POST['returnto']) : '';
    $sure = (isset($_GET['sure']) ? intval($_GET['sure']) : '');
    if (!$sure)
        stderr("Confirm Delete", "Do you really want to delete this news entry? Click\n" . "<a href='staffpanel.php?tool=news&amp;action=news&amp;mode=delete&amp;sure=1&amp;h=$hash&amp;newsid=$newsid&amp;returnto=staffpanel.php?tool=news'>here</a> if you are sure.", false);
    if ($_GET['h'] != $hash)
        stderr('Error', 'what are you doing?');
    function deletenewsid($newsid)
    {
        
        global $CURUSER, $mc1;
        sql_query("DELETE FROM news WHERE id = $newsid AND userid = $CURUSER[id]");
        $mc1->delete_value('latest_news_');               
    }
    $HTMLOUT.= deletenewsid($newsid);
    header("Refresh: 3; url=staffpanel.php?tool=news");
    $HTMLOUT .="<h2>News entry deleted - Please wait while you are redirected!</h2>";
    echo stdhead('News') . $HTMLOUT . stdfoot();
    die;
    }

//==Add news
if ($mode == 'add') {
    $body = isset($_POST['body']) ? $_POST['body'] : '';
    $sticky = isset($_POST['sticky']) ? $_POST['sticky'] : 'yes';
    if (!$body)
        stderr("Error", "The news item cannot be empty!");
    $title = htmlentities($_POST['title']);
    if (!$title)
        stderr("Error", "The news title cannot be empty!");
    $added = isset($_POST["added"]) ?$_POST["added"] : '';
    if (!$added)
        $added = time();
    sql_query("INSERT INTO news (userid, added, body, title, sticky) VALUES (" . $CURUSER['id'] . "," . sqlesc($added) . ", " . sqlesc($body) . ", " . sqlesc($title) . ", " . sqlesc($sticky) . ")") or sqlerr(__FILE__, __LINE__);
    mysql_affected_rows() == 1 ?$warning = "News entry was added successfully." : stderr("oopss", "Something's wrong !! .");
    $mc1->delete_value('latest_news_');
}

//==Edit/change news
if ($mode == 'edit') {
    $newsid = (int)$_GET["newsid"];
    if (!is_valid_id($newsid))
        stderr("Error", "Invalid news item ID.");
    $res = sql_query("SELECT * FROM news WHERE id=" . sqlesc($newsid)) or sqlerr(__FILE__, __LINE__);
    if (mysql_num_rows($res) != 1)
        stderr("Error", "No news item with that ID .");
    $arr = mysql_fetch_assoc($res);
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $body = isset($_POST['body']) ? $_POST['body'] : '';
        $sticky = isset($_POST['sticky']) ? $_POST['sticky'] : 'yes';
        if ($body == "")
        stderr("Error", "Body cannot be empty!");
        $title = htmlentities($_POST['title']);
        if ($title == "")
        stderr("Error", "Title cannot be empty!");
        $body = sqlesc($body);
        $sticky = sqlesc($sticky);
        $editedat = sqlesc(time());
        sql_query("UPDATE news SET body=$body, sticky=$sticky, title=" . sqlesc($title) . " WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);
        $mc1->delete_value('latest_news_');
        $returnto = isset($_POST['returnto']) ? htmlentities($_POST['returnto']) : '';
        if ($returnto != "")
        header("Location: $returnto");
        else
        $warning = "News item was edited successfully.";
        } else {
        $returnto = isset($_POST['returnto']) ? htmlentities($_POST['returnto']) : '';
        $HTMLOUT .="<h1>Edit News Item</h1>
        <form method='post' name='compose' action='staffpanel.php?tool=news&amp;action=news&amp;mode=edit&amp;newsid=$newsid'>
        <input type='hidden' name='returnto' value='$returnto' />
        <table border='1' cellspacing='0' cellpadding='5'>
        <tr><td><input type='text' name='title' value='" . htmlspecialchars($arr['title']) . "' /></td></tr>
        <tr><td align='left' style='padding: 0px'>
         ".textbbcode("compose", "body", htmlspecialchars($arr["body"])) . "</td></tr>
        <tr><td colspan='2' class='rowhead'>Sticky<input type='radio' " . ($arr["sticky"] == "yes" ? " checked='checked'" : "") . " name='sticky' value='yes' />Yes<input name='sticky' type='radio' value='no' " . ($arr["sticky"] == "no" ? " checked='checked'" : "") . " />No</td></tr>
        <tr><td colspan='2' align='center'><input type='submit' value='Okay' class='btn' /></td></tr>
        </table>
        </form>\n";
        echo  stdhead('News Page') . $HTMLOUT . stdfoot($stdfoot);
        die;
    }
}

//==Final Actions
$res = sql_query("SELECT * FROM news ORDER BY sticky, added DESC") or sqlerr(__FILE__, __LINE__);
   $HTMLOUT .= begin_main_frame();
   $HTMLOUT .= begin_frame();
    if (!empty($warning))
    $HTMLOUT .="<p><font size='-3'>($warning)</font></p>";
    $HTMLOUT .="<form method='post' name='compose' action='staffpanel.php?tool=news&amp;action=news&amp;mode=add'>
    <h1>Submit News Item</h1><table border='1' cellspacing='0' cellpadding='5'>
    <tr><td><input type='text' name='title' value='" . htmlspecialchars($res['title']) . "' /></td></tr>\n";
    $HTMLOUT .="<tr>
    <td align='left' style='padding: 0px'>".textbbcode("compose", "body")."</td></tr>";
    $HTMLOUT .="<tr><td colspan='2' class='rowhead'>Sticky<input type='radio' checked='checked' name='sticky' value='yes' />Y<input name='sticky' type='radio' value='no' />N</td></tr>\n
    <tr><td colspan='2' class='rowhead'><input type='submit' value='Okay' class='btn' /></td></tr>\n
    </table></form><br /><br />\n";
    
    while ($arr = mysql_fetch_assoc($res)) {
        $newsid = $arr["id"];
        $body = $arr["body"];
        $title = $arr["title"];
        $userid = $arr["userid"];
        $added = get_date($arr["added"], 'LONG',0,1);
        $res2 = sql_query("SELECT id, username, class, warned, chatpost, pirate, king, leechwarn, enabled, donor, added FROM users WHERE id =$userid") or sqlerr(__FILE__, __LINE__);
        $arr2 = mysql_fetch_assoc($res2);
        $postername = $arr2["username"];
        $by = "<b>".format_username($arr2)."</b>";
        $hash = md5('the@@saltto66??' . $newsid . 'add' . '@##mu55y==');
        $returnto = isset($_POST['returnto']) ? htmlentities($_POST['returnto']) : '';
        $HTMLOUT .="<table border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
        $added&nbsp;---&nbsp;by&nbsp$by
        - [<a href='staffpanel.php?tool=news&amp;action=news&amp;mode=edit&amp;newsid=$newsid'><b>Edit</b></a>]
        - [<a href='staffpanel.php?tool=news&amp;action=news&amp;mode=delete&amp;newsid=$newsid&amp;sure=1&amp;h=$hash'><b>Delete</b></a>]
        </td></tr></table>\n";
        $HTMLOUT .= begin_table(true);
        $HTMLOUT .="<tr valign='top'><td class='comment'><b>" . htmlentities($title) . "</b><br />" . format_comment($body) . "</td></tr>\n";
        $HTMLOUT .= end_table();
    }
    $HTMLOUT .= end_frame();
    $HTMLOUT .= end_main_frame();
echo  stdhead('News Page') . $HTMLOUT . stdfoot($stdfoot);
die;
?>