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
|   $Date$ 010810
|   $Revision$ 2.0
|   $Author$ Bigjoos
|   $URL$
|   $takeeditcp
|   
+------------------------------------------------
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once(CLASS_DIR.'page_verify.php');
require_once(INCL_DIR.'password_functions.php');
dbconn();
loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('takeeditcp') );
    $newpage = new page_verify(); 
    $newpage->check('tkepe');



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
    
    $action = isset($_POST["action"]) ? htmlspecialchars(trim($_POST["action"])) : '';
    $urladd = $changedemail = $birthday ='';
    $updateset = array();
    //== Avatars stuffs
    if ($action == "avatar") {
    $avatars = (isset($_POST['avatars']) && $_POST['avatars'] === 'yes' ? 'yes' : 'no');
    $offensive_avatar = (isset($_POST['offensive_avatar']) && $_POST['offensive_avatar'] === 'yes' ? 'yes' : 'no');
    $view_offensive_avatar = (isset($_POST['view_offensive_avatar']) && $_POST['view_offensive_avatar'] === 'yes' ? 'yes' : 'no');
    $avatar = trim( urldecode( $_POST["avatar"] ) );
    if ( preg_match( "/^http:\/\/$/i", $avatar ) 
    or preg_match( "/[?&;]/", $avatar ) 
    or preg_match("#javascript:#is", $avatar ) 
    or !preg_match("#^https?://(?:[^<>*\"]+|[a-z0-9/\._\-!]+)$#iU", $avatar ) )
    {
    $avatar='';
    }
    
    if( !empty($avatar) ) 
    {
    $img_size = @GetImageSize( $avatar );
    if($img_size == FALSE || !in_array($img_size['mime'], $INSTALLER09['allowed_ext']))
    stderr($lang['takeeditcp_user_error'], $lang['takeeditcp_image_error']);
    if($img_size[0] < 5 || $img_size[1] < 5)
    stderr($lang['takeeditcp_user_error'], $lang['takeeditcp_small_image']);
    if ( ( $img_size[0] > $INSTALLER09['av_img_width'] ) OR ( $img_size[1] > $INSTALLER09['av_img_height'] ) )
    { 
    $image = resize_image( array(
    'max_width'  => $INSTALLER09['av_img_width'],
    'max_height' => $INSTALLER09['av_img_height'],
    'cur_width'  => $img_size[0],
    'cur_height' => $img_size[1]));
    }
    else 
    {
    $image['img_width'] = $img_size[0];
    $image['img_height'] = $img_size[1];
    }  
    $updateset[] = "av_w = " . sqlesc($image['img_width']);
    $updateset[] = "av_h = " . sqlesc($image['img_height']);
    }
  
    
    $updateset[] = 'offensive_avatar = '.sqlesc($offensive_avatar);
    $updateset[] = 'view_offensive_avatar = '.sqlesc($view_offensive_avatar);
    $updateset[] = "avatar = " . sqlesc($avatar);
    $updateset[] = 'avatars = '.sqlesc($avatars);
    $action = "avatar";
    } 
    //== Signature stuffs
    elseif ($action == "signature") {
    $signatures = (isset($_POST['signatures']) && $_POST["signatures"] != "" ? "yes" : "no");
    $signature = trim( urldecode( $_POST["signature"] ) );
    if ( preg_match( "/^http:\/\/$/i", $signature ) 
    or preg_match( "/[?&;]/", $signature ) 
    or preg_match("#javascript:#is", $signature ) 
    or !preg_match("#^https?://(?:[^<>*\"]+|[a-z0-9/\._\-!]+)$#iU", $signature ))
    {
    $signature='';
    } 
    if( !empty($signature) ) 
    {
    $img_size = @GetImageSize( $signature );
    if($img_size == FALSE || !in_array($img_size['mime'], $INSTALLER09['allowed_ext']))
    stderr('USER ERROR', 'Not an image or unsupported image!');
    if($img_size[0] < 5 || $img_size[1] < 5)
    stderr('USER ERROR', 'Image is too small');
    if ( ( $img_size[0] > $INSTALLER09['sig_img_width'] ) OR ( $img_size[1] > $INSTALLER09['sig_img_height'] ) )
    { 
    $image = resize_image( array(
    'max_width'  => $INSTALLER09['sig_img_width'],
    'max_height' => $INSTALLER09['sig_img_height'],
    'cur_width'  => $img_size[0],
    'cur_height' => $img_size[1]));
    }
    else
    {
    $image['img_width'] = $img_size[0];
    $image['img_height'] = $img_size[1];
    }  
    $updateset[] = "sig_w = " . sqlesc($image['img_width']);
    $updateset[] = "sig_h = " . sqlesc($image['img_height']); 
    $updateset[] = "signature = " . sqlesc("[img]".$signature."[/img]\n");
    }
    
    $updateset[] = "signatures = '$signatures'";
    
    if (isset($_POST["info"]) && (($info = $_POST["info"]) != $CURUSER["info"])){
    $updateset[] = "info = " . sqlesc($info);
    }

    $action = "signature";
    } 
    //== Security Stuffs
    elseif ($action == "security") {
    if(isset($_POST['ssluse']) && ($ssluse = (int)$_POST['ssluse']) && ($ssluse != $CURUSER['ssluse']))
    $updateset[] = "ssluse = ".$ssluse;
    if (!mkglobal("email:chpassword:passagain:chmailpass:secretanswer"))
    stderr("Error", $lang['takeeditcp_no_data']);
    if ($chpassword != "") 
    {
    if (strlen($chpassword) > 40)
    stderr("Error", $lang['takeeditcp_pass_long']);
    if ($chpassword != $passagain)
    stderr("Error", $lang['takeeditcp_pass_not_match']);
    $secret = mksecret();
    $passhash = make_passhash( $secret, md5($chpassword) );
    $updateset[] = "secret = " . sqlesc($secret);
    $updateset[] = "passhash = " . sqlesc($passhash);
    logincookie($CURUSER["id"], md5($passhash.$_SERVER["REMOTE_ADDR"]));
    }
    if ($email != $CURUSER["email"]) 
    {
    if (!validemail($email))
    stderr("Error", $lang['takeeditcp_not_valid_email']);
    $r = @sql_query("SELECT id FROM users WHERE email=" . sqlesc($email)) or sqlerr();
    if ( mysql_num_rows($r) > 0 || ($CURUSER["passhash"] != make_passhash( $CURUSER['secret'], md5($chmailpass) ) ) )
    stderr("Error", $lang['takeeditcp_address_taken']);
    $changedemail = 1;
    }
    if ($secretanswer != '') {
    if (strlen($secretanswer) > 40) 
    stderr("Sorry", "secret answer is too long (max is 40 chars)");
    if (strlen($secretanswer) < 6) 
    stderr("Sorry", "secret answer is too sort (min is 6 chars)");
    $new_secret_answer = md5($secretanswer);
    $updateset[] = "hintanswer = " . sqlesc($new_secret_answer);
    }
    if(get_parked() == '1'){
    if (isset($_POST["parked"]) && ($parked = $_POST["parked"]) != $CURUSER["parked"]){
    $updateset[] = "parked = " . sqlesc($parked);
    }
    }
    if(get_anonymous() != '0'){
    $anonymous = (isset($_POST['anonymous']) && $_POST["anonymous"] != "" ? "yes" : "no");
    $updateset[] = "anonymous = ".sqlesc($anonymous);
    }
    if (isset($_POST["hidecur"]) && ($hidecur = $_POST["hidecur"]) != $CURUSER["hidecur"]){
    $updateset[] = "hidecur = " . sqlesc($hidecur);
    }
    if (isset($_POST["show_email"]) && ($show_email = $_POST["show_email"]) != $CURUSER["show_email"]){
    $updateset[] = "show_email= " . sqlesc($show_email);
    }
    if (isset($_POST["paranoia"]) && ($paranoia = $_POST["paranoia"]) != $CURUSER["paranoia"]){
    $updateset[] = "paranoia= " . sqlesc($paranoia);
    } 
    if (isset($_POST["changeq"]) && (($changeq = (int)$_POST["changeq"]) != $CURUSER["passhint"]) && is_valid_id($changeq)){
    $updateset[] = "passhint = " . sqlesc($changeq);
    }
  
    if ($changedemail) {
    $sec = mksecret();
    $hash = md5($sec . $email . $sec);
    $obemail = urlencode($email);
    $updateset[] = "editsecret = " . sqlesc($sec);
    $thishost = $_SERVER["HTTP_HOST"];
    $thisdomain = preg_replace('/^www\./is', "", $thishost);
    $body = str_replace(array('<#USERNAME#>', '<#SITENAME#>', '<#USEREMAIL#>', '<#IP_ADDRESS#>', '<#CHANGE_LINK#>'),
    array($CURUSER['username'], $INSTALLER09['site_name'], $email, $_SERVER['REMOTE_ADDR'], "{$INSTALLER09['baseurl']}/confirmemail.php?uid={$CURUSER['id']}&key=$hash&email=$obemail"),
    $lang['takeeditcp_email_body']);
    mail($email, "$thisdomain {$lang['takeeditcp_confirm']}", $body, "From: {$INSTALLER09['site_email']}");
    $urladd .= "&mailsent=1";
    }
    $action = "security";
    } 
    //== Torrent stuffs
    elseif ($action == "torrents") {
    $pmnotif = isset($_POST["pmnotif"]) ? $_POST["pmnotif"] : '';
    $emailnotif = isset($_POST["emailnotif"]) ? $_POST["emailnotif"] : '';
    $notifs = ($pmnotif == 'yes' ? "[pm]" : "");
    $notifs .= ($emailnotif == 'yes' ? "[email]" : "");
    $r = @sql_query("SELECT id FROM categories") or sqlerr();
    $rows = mysql_num_rows($r);
    for ($i = 0; $i < $rows; ++$i)
    {
    $a = mysql_fetch_assoc($r);
    if (isset($_POST["cat{$a['id']}"]) && $_POST["cat{$a['id']}"] == 'yes')
    $notifs .= "[cat{$a['id']}]";
    }
    $updateset[] = "notifs = '$notifs'";
    $viewscloud = (isset($_POST['viewscloud']) && $_POST["viewscloud"] != "" ? "yes" : "no");{
    $updateset[] = "viewscloud = ".sqlesc($viewscloud);
    }
    $clear_new_tag_manually = (isset($_POST['clear_new_tag_manually']) && $_POST["clear_new_tag_manually"] != "" ? "yes" : "no");{
    $updateset[] = "clear_new_tag_manually = " . sqlesc($clear_new_tag_manually);
    }
    $action = "torrents";
    } 
    //== Personal stuffs
    elseif ($action == "personal") {
    //custom-title check
    if (isset($_POST["title"]) && $CURUSER["class"] >= UC_VIP && ($title = $_POST["title"]) != $CURUSER["title"]) {
    $notallow = array("sysop", "administrator", "admin", "mod", "moderator", "vip", "motherfucker");
    if (in_array(strtolower($title), ($notallow)))
    stderr("Error", "Invalid custom title!");
    $updateset[] = "title = " . sqlesc($title);
    }
    //status update
    if(isset($_POST['status']) && ($status = $_POST['status']) && !empty($status)) {
     $status_archive = ((isset($CURUSER['archive']) && is_array(unserialize($CURUSER['archive']))) ? unserialize($CURUSER['archive']) : array());
     if(!empty($CURUSER['last_status']))
     $status_archive[] = array('status'=>$CURUSER['last_status'],'date'=>$CURUSER['last_update']);
     sql_query('INSERT INTO ustatus(userid,last_status,last_update,archive) VALUES('.$CURUSER['id'].','.sqlesc($status).','.TIME_NOW.','.sqlesc(serialize($status_archive)).') ON DUPLICATE KEY UPDATE last_status=values(last_status),last_update=values(last_update),archive=values(archive)') or sqlerr(__FILE__,__LINE__);
    }
    //end status update;
    if (isset($_POST['stylesheet']) && (($stylesheet = (int)$_POST['stylesheet']) != $CURUSER['stylesheet']) && is_valid_id($stylesheet))
    $updateset[] = 'stylesheet = ' . sqlesc($stylesheet);
    if (isset($_POST["country"]) && (($country = $_POST["country"]) != $CURUSER["country"]) && is_valid_id($country))
    $updateset[] = "country = $country";
    if (isset($_POST["torrentsperpage"]) && (($torrentspp = min(100, 0 + $_POST["torrentsperpage"])) != $CURUSER["torrentsperpage"]))
    $updateset[] = "torrentsperpage = $torrentspp";
    if (isset($_POST["topicsperpage"]) && (($topicspp = min(100, 0 + $_POST["topicsperpage"])) != $CURUSER["topicsperpage"]))
    $updateset[] = "topicsperpage = $topicspp";
    if (isset($_POST["postsperpage"]) && (($postspp = min(100, 0 + $_POST["postsperpage"])) != $CURUSER["postsperpage"]))
    $updateset[] = "postsperpage = $postspp";
    if (isset($_POST["gender"]) && ($gender = $_POST["gender"]) != $CURUSER["gender"])
    $updateset[] = "gender = " . sqlesc($gender);
    $shoutboxbg = 0 + $_POST["shoutboxbg"];
    $updateset[] = "shoutboxbg = " . sqlesc($shoutboxbg);
    if(isset($_POST["user_timezone"]) && preg_match('#^\-?\d{1,2}(?:\.\d{1,2})?$#', $_POST['user_timezone']))
    $updateset[] = "time_offset = " . sqlesc($_POST['user_timezone']);
    $updateset[] = "auto_correct_dst = " .(isset($_POST['checkdst']) ? 1 : 0);
    $updateset[] = "dst_in_use = " .(isset($_POST['manualdst']) ? 1 : 0);
    if (isset($_POST["google_talk"]) && ($google_talk = $_POST["google_talk"]) != $CURUSER["google_talk"]){
    $updateset[] = "google_talk= " . sqlesc($google_talk);
    }
    if (isset($_POST["msn"]) && ($msn = $_POST["msn"]) != $CURUSER["msn"]){
    $updateset[] = "msn= " . sqlesc($msn);
    }
    if (isset($_POST["aim"]) && ($aim = $_POST["aim"]) != $CURUSER["aim"]){
    $updateset[] = "aim= " . sqlesc($aim);
    }
    if (isset($_POST["yahoo"]) && ($yahoo = $_POST["yahoo"]) != $CURUSER["yahoo"]){
    $updateset[] = "yahoo= " . sqlesc($yahoo);
    }
    if (isset($_POST["icq"]) && ($icq = $_POST["icq"]) != $CURUSER["icq"]){
    $updateset[] = "icq= " . sqlesc($icq);
    }
    if (isset($_POST["website"]) && ($website = $_POST["website"]) != $CURUSER["website"]){
    $updateset[] = "website= " . sqlesc($website);
    }

    if ($CURUSER['birthday'] == "0000-00-00") {
    $year = isset($_POST["year"]) ? 0 + $_POST["year"] : 0;
    $month = isset($_POST["month"]) ? 0 + $_POST["month"] : 0;
    $day = isset($_POST["day"]) ? 0 + $_POST["day"] : 0;
    $birthday = date("$year.$month.$day");
    if ($year == '0000')
    stderr("Error", "Please set your birth year.");
    if ($month == '00')
    stderr("Error","Please set your birth month.");
    if ($day == '00')
    stderr("Error","Please set your birth day.");
    if (!checkdate($month, $day, $year))
	  stderr("Error", "<br /><div align='center'><font color='red' size='+1'>The date entered is not a valid date, please try again</font></div><br />"); 
    $updateset[] = "birthday = ".sqlesc($birthday);
    $mc1->delete_value('birthdayusers');
    }
    $action = "personal";
    } 
    //== Pm stuffs
    elseif ($action == "pm") {
    $acceptpms_choices = array('yes' => 1, 'friends' => 2, 'no' => 3);
    $acceptpms = (isset($_POST['acceptpms']) ? $_POST['acceptpms'] : 'all');
    if (isset($acceptpms_choices[$acceptpms]))
    $updateset[] = "acceptpms = " . sqlesc($acceptpms);
    $deletepms = isset($_POST["deletepms"]) ? "yes" : "no";
    $updateset[] = "deletepms = '$deletepms'";
    $savepms = (isset($_POST['savepms']) && $_POST["savepms"] != "" ? "yes" : "no");
    $updateset[] = "savepms = '$savepms'";
    if (isset($_POST["subscription_pm"]) && ($subscription_pm = $_POST["subscription_pm"]) != $CURUSER["subscription_pm"]){  
    $updateset[] = "subscription_pm = " . sqlesc($subscription_pm);
    }
    if (isset($_POST["pm_on_delete"]) && ($pm_on_delete = $_POST["pm_on_delete"]) != $CURUSER["pm_on_delete"]){  
    $updateset[] = "pm_on_delete = " . sqlesc($pm_on_delete);
    }
    if (isset($_POST["commentpm"]) && ($commentpm = $_POST["commentpm"]) != $CURUSER["commentpm"]){  
    $updateset[] = "commentpm = " . sqlesc($commentpm);
    }
    $action = "pm";
    }
    //== End == then update the sets :)
    if (sizeof($updateset)>0) 
    sql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
    $mc1->delete_value('MyUser_'.$CURUSER['id']);
    header("Location: {$INSTALLER09['baseurl']}/usercp.php?edited=1&action=$action" . $urladd);
?>