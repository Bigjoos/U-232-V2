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
require_once(INCL_DIR.'password_functions.php');
require_once(CLASS_DIR.'page_verify.php');
dbconn();
get_template();

$lang = load_language('global');
$newpage = new page_verify(); 
$newpage->check('tkIs');
$res = mysql_query("SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);
if ($arr[0] >= $INSTALLER09['invites']) 	
stderr($lang['stderr_errorhead'], sprintf($lang['stderr_ulimit'], $INSTALLER09['maxusers']));

if(!$INSTALLER09['openreg_invites'])
    stderr('Sorry', 'Invite Signups are closed presently');

if (!mkglobal("wantusername:wantpassword:passagain:email:invite:captchaSelection:submitme:passhint:hintanswer"))
die();

if ($submitme != 'X')
  stderr('Ha Ha', 'You Missed, You plonker !');
  
 if(empty($captchaSelection) || $_SESSION['simpleCaptchaAnswer'] != $captchaSelection){
 header('Location: invite_signup.php');
 exit();
 }

function validusername($username) {
if ($username == "")
return false;
// The following characters are allowed in user names
$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
for ($i = 0; $i < strlen($username); ++$i)
if (strpos($allowedchars, $username[$i]) === false)
return false;
return true; 
}

if (empty($wantusername) || empty($wantpassword) || empty($email) || empty($invite) || empty($passhint) || empty($hintanswer))
stderr("Error","Don't leave any fields blank.");

if(!blacklist($wantusername))
 stderr($lang['takesignup_user_error'],sprintf($lang['takesignup_badusername'],htmlspecialchars($wantusername)));

if (strlen($wantusername) > 12)
stderr("Error","Sorry, username is too long (max is 12 chars)");

if ($wantpassword != $passagain)
stderr("Error","The passwords didn't match! Must've typoed. Try again.");

if (strlen($wantpassword) < 6)
stderr("Error","Sorry, password is too short (min is 6 chars)");

if (strlen($wantpassword) > 40)
stderr("Error","Sorry, password is too long (max is 40 chars)");

if ($wantpassword == $wantusername)
stderr("Error","Sorry, password cannot be same as user name.");

if (!validemail($email))
stderr("Error","That doesn't look like a valid email address.");

if (!validusername($wantusername))
stderr("Error","Invalid username.");

if (!(isset($_POST['day']) || isset($_POST['month']) || isset($_POST['year'])))
	  stderr('Error','You have to fill in your birthday.');

    if (checkdate($_POST['month'], $_POST['day'], $_POST['year']))
	  $birthday = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
    else
	  stderr('Error','You have to fill in your birthday correctly.');

    if ((date('Y') - $_POST['year']) < 17)
     stderr('Error','You must be at least 18 years old to register.');

// make sure user agrees to everything...
if ($_POST["rulesverify"] != "yes" || $_POST["faqverify"] != "yes" || $_POST["ageverify"] != "yes")
stderr("Error","Sorry, you're not qualified to become a member of this site.");

// check if email addy is already in use
$a = (@mysql_fetch_row(sql_query('SELECT COUNT(*) FROM users WHERE email = ' . sqlesc($email)))) or die(mysql_error());
if ($a[0] != 0)
stderr('Error', 'The e-mail address <b>' . htmlspecialchars($email) . '</b> is already in use.');
/*
//=== check if ip addy is already in use
$c = (@mysql_fetch_row(sql_query("select count(*) from users where ip='" . $_SERVER['REMOTE_ADDR'] . "'"))) or die(mysql_error());
if ($c[0] != 0)
stderr("Error", "The ip " . $_SERVER['REMOTE_ADDR'] . " is already in use. We only allow one account per ip address.");
*/
// TIMEZONE STUFF
    if(isset($_POST["user_timezone"]) && preg_match('#^\-?\d{1,2}(?:\.\d{1,2})?$#', $_POST['user_timezone']))
    {
    $time_offset = sqlesc($_POST['user_timezone']);
    }
    else
    { 
    $time_offset = isset($INSTALLER09['time_offset']) ? sqlesc($INSTALLER09['time_offset']) : '0'; }
    // have a stab at getting dst parameter?
    $dst_in_use = localtime(time() + ($time_offset * 3600), true);
    // TIMEZONE STUFF END

$select_inv = mysql_query('SELECT sender, receiver, status FROM invite_codes WHERE code = ' . sqlesc($invite)) or die(mysql_error());
$rows = mysql_num_rows($select_inv);
$assoc = mysql_fetch_assoc($select_inv);

if ($rows == 0)
stderr("Error","Invite not found.\nPlease request a invite from one of our members.");

if ($assoc["receiver"]!=0)
stderr("Error","Invite already taken.\nPlease request a new one from your inviter.");

    $secret = mksecret();
    $wantpasshash = make_passhash( $secret, md5($wantpassword) );
    $editsecret = ( !$arr[0] ? "" : make_passhash_login_key() );
    $wanthintanswer = md5($hintanswer);
$new_user = sql_query("INSERT INTO users (username, passhash, secret, passhint, hintanswer, editsecret, birthday, invitedby, email, ". (!$arr[0]?"class, ":"") ."added, last_access, last_login, time_offset, dst_in_use) VALUES (" .
implode(",", array_map("sqlesc", array($wantusername, $wantpasshash, $secret, $editsecret, $birthday, $passhint, $wanthintanswer, (int)$assoc['sender'], $email))).
", ". (!$arr[0]?UC_SYSOP.", ":""). "'".  time() ."','".  time() ."','".  time() ."', $time_offset, {$dst_in_use['tm_isdst']})");

$message = "Welcome New {$INSTALLER09['site_name']} Member : - " . htmlspecialchars($wantusername) . "";

if (!$new_user) {
if (mysql_errno() == 1062)
stderr("Error","Username already exists!");
stderr("Error","borked");
}

//===send PM to inviter
$sender = $assoc["sender"];
$added = sqlesc(time());
$msg = sqlesc("Hey there [you] ! :wave:\nIt seems that someone you invited to {$INSTALLER09['site_name']} has arrived ! :clap2: \n\n Please go to your [url={$INSTALLER09['baseurl']}/invite.php]Invite page[/url] to confirm them so they can log in.\n\ncheers\n");
$subject = sqlesc("Someone you invited has arrived!");
sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $sender, $msg, $added)") or sqlerr(__FILE__, __LINE__);
//////////////end/////////////////////
$id = mysql_insert_id();
sql_query('UPDATE invite_codes SET receiver = ' . sqlesc($id) . ', status = "Confirmed" WHERE sender = ' . sqlesc((int)$assoc['sender']). ' AND code = ' . sqlesc($invite)) or sqlerr(__FILE__, __LINE__);
$latestuser_cache['id'] =  (int)$id;
$latestuser_cache['username'] = $wantusername;
/** OOP **/
$mc1->cache_value('latestuser', $latestuser_cache, 0, $INSTALLER09['expires']['latestuser']);
write_log('User account '.htmlspecialchars($wantusername).' was created!');
autoshout($message);
stderr('Success','Signup successfull, Your inviter needs to confirm your account now before you can use your account !');
?>