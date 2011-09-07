<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
error_reporting(E_ALL); //== turn off = 0 when live
define('PUBLIC_ACCESS', true);
define('SQL_DEBUG', 1);
//==charset
$INSTALLER09['char_set']='UTF-8';//also to be used site wide in meta tags
if (ini_get('default_charset') != $INSTALLER09['char_set']){
ini_set('default_charset',$INSTALLER09['char_set']);
}
/* Compare php version for date/time stuff etc! */
if (version_compare(PHP_VERSION, "5.1.0RC1", ">="))
date_default_timezone_set('Europe/London');
define('TIME_NOW', time());
$INSTALLER09['time_adjust'] =  0;
$INSTALLER09['time_offset'] = '0'; 
$INSTALLER09['time_use_relative'] = 1;
$INSTALLER09['time_use_relative_format'] = '{--}, h:i A';
$INSTALLER09['time_joined'] = 'j-F y';
$INSTALLER09['time_short'] = 'jS F Y - h:i A';
$INSTALLER09['time_long'] = 'M j Y, h:i A';
$INSTALLER09['time_tiny'] = '';
$INSTALLER09['time_date'] = '';
//== DB setup
$INSTALLER09['mysql_host'] = '#mysql_host';
$INSTALLER09['mysql_user'] = '#mysql_user';
$INSTALLER09['mysql_pass'] = '#mysql_pass';
$INSTALLER09['mysql_db']   = '#mysql_db';
//== Cookie setup
$INSTALLER09['cookie_prefix']  = '#cookie_prefix'; // This allows you to have multiple trackers, eg for demos, testing etc.
$INSTALLER09['cookie_path']    = '#cookie_path';   // ATTENTION: You should never need this unless the above applies eg: /tbdev
$INSTALLER09['cookie_domain']  = '#cookie_domain'; // set to eg: .somedomain.com or is subdomain set to: .sub.somedomain.com
$INSTALLER09['domain'] = '#domain';
$INSTALLER09['site_online'] = 1;
$INSTALLER09['tracker_post_key'] = 'lsdflksfda4545frwe35@kk';
$INSTALLER09['max_torrent_size'] = 3*1024*1024;
$INSTALLER09['announce_interval'] = 60 * 30;
$INSTALLER09['signup_timeout'] = 86400 * 3;
$INSTALLER09['autoclean_interval'] = 900;
$INSTALLER09['autoslowclean_interval'] = 28800;
$INSTALLER09['autoslowclean_interval2'] = 57600;
$INSTALLER09['lotteryclean_interval'] = 259200;
$INSTALLER09['autobackup_interval'] = 86400;
$INSTALLER09['optimizedb_interval'] = 172800;
$INSTALLER09['docleanup'] = 1;
$INSTALLER09['doslowcleanup'] = 1;
$INSTALLER09['doslowleanup2'] = 1;
$INSTALLER09['lotterycleanup'] = 0;
$INSTALLER09['optimizedb'] = 1;
$INSTALLER09['dobackup'] = 1;
$INSTALLER09['minvotes'] = 1;
$INSTALLER09['max_dead_torrent_time'] = 6 * 3600;
$INSTALLER09['language'] = 'en';
$INSTALLER09['user_ratios'] = 1;
$INSTALLER09['bot_id'] = 2;
$INSTALLER09['forums_online'] = 1;
$INSTALLER09['autoshout_on'] = 1;
$INSTALLER09['seedbonus_on'] = 1;
$INSTALLER09['maxsublength'] = 100;
$INSTALLER09['votesrequired'] = 15;
//== Memcache expires
$INSTALLER09['expires']['latestuser'] = 0; // 0 = infinite  
$INSTALLER09['expires']['MyPeers_'] = 120; // 60 = 60 seconds 
$INSTALLER09['expires']['unread'] = 86400; // 86400 = 1 day 
$INSTALLER09['expires']['alerts'] = 0;  // 0 = infinite
$INSTALLER09['expires']['user_cache'] = 900;  // 900 = 15 min
$INSTALLER09['expires']['forum_posts'] = 0;  // 900 = 15 min
$INSTALLER09['expires']['torrent_comments'] = 900;  // 900 = 15 min
$INSTALLER09['expires']['latestposts'] = 0;  // 900 = 15 min
$INSTALLER09['expires']['top5_torrents'] = 0; // 0 = infinite
$INSTALLER09['expires']['last5_torrents'] = 0; // 0 = infinite 
$INSTALLER09['expires']['iphistory'] = 900;  // 900 = 15 min
$INSTALLER09['expires']['newpoll'] = 0;  // 900 = 15 min
$INSTALLER09['expires']['curuser'] = 900;  // 900 = 15 min
$INSTALLER09['expires']['genrelist'] = 30*86400; // 30x86400 = 30 days
$INSTALLER09['expires']['poll_data'] = 0; // 300 = 5 min
$INSTALLER09['expires']['torrent_data'] = 900; // 900 = 15 min
$INSTALLER09['expires']['user_flag'] = 86400*28; // 900 = 15 min
$INSTALLER09['expires']['shit_list'] = 900; // 900 = 15 min
$INSTALLER09['expires']['port_data'] = 900; // 900 = 15 min
$INSTALLER09['expires']['user_peers'] = 900; // 900 = 15 min
$INSTALLER09['expires']['user_friends'] = 900; // 900 = 15 min
$INSTALLER09['expires']['user_hash'] = 900; // 900 = 15 min
$INSTALLER09['expires']['user_blocks'] = 900; // 900 = 15 min
$INSTALLER09['expires']['hnr_data'] = 300; // 900 = 15 min
$INSTALLER09['expires']['snatch_data'] = 300; // 900 = 15 min
$INSTALLER09['expires']['user_snatches_data'] = 300; // 900 = 15 min
$INSTALLER09['expires']['staff_snatches_data'] = 300; // 900 = 15 min
$INSTALLER09['expires']['user_snatches_complete'] = 300; // 900 = 15 min
$INSTALLER09['expires']['completed_torrents'] = 300; // 300 = 5 min
$INSTALLER09['expires']['activeusers'] = 60; // 60 = 1 minutes
$INSTALLER09['expires']['last24'] = 3600; // 3600 = 1 hours
$INSTALLER09['expires']['activeircusers'] = 300; // 900 = 15 min
$INSTALLER09['expires']['birthdayusers'] = 43200; //== 43200 = 12 hours
$INSTALLER09['expires']['news_users'] = 3600; // 3600 = 1 hours
$INSTALLER09['expires']['user_invitees'] = 900; // 900 = 15 min
$INSTALLER09['expires']['ip_data'] = 900; // 900 = 15 min
$INSTALLER09['expires']['latesttorrents'] = 0;  // 0 = infinite
$INSTALLER09['expires']['invited_by'] = 900; // 900 = 15 min
$INSTALLER09['expires']['user_torrents'] = 900; // 900 = 15 min
$INSTALLER09['expires']['user_seedleech'] = 900; // 900 = 15 min
$INSTALLER09['expires']['radio'] = 0; // 0 = infinite 
$INSTALLER09['expires']['total_funds'] = 0; // 0 = infinite
$INSTALLER09['expires']['latest_news'] = 0; // 0 = infinite
$INSTALLER09['expires']['site_stats'] = 300; // 300 = 5 min
$INSTALLER09['expires']['share_ratio'] = 900; // 900 = 15 min
$INSTALLER09['expires']['checked_by'] = 0; // 0 = infinite 
$INSTALLER09['expires']['latest_news_tpl'] = 0; // 0 = infinite
$INSTALLER09['expires']['latesttorrents_tpl'] = 0;  // 0 = infinite
$INSTALLER09['expires']['latestposts_tpl'] = 0;  // 0 = infinite
//== Latest posts limit
$INSTALLER09['latest_posts_limit'] = 5; //query limit for latest forum posts on index
//latest torrents limit
$INSTALLER09['latest_torrents_limit'] = 5;
/** Settings **/
$INSTALLER09['reports']      = 1;// 1/0 on/off
$INSTALLER09['karma']        = 1;// 1/0 on/off
$INSTALLER09['textbbcode']   = 1;// 1/0 on/off
//== Max users on site
$INSTALLER09['maxusers'] = 5000; // LoL Who we kiddin' here?
$INSTALLER09['invites'] = 3500; // LoL Who we kiddin' here?
$INSTALLER09['openreg'] = true; //==true=open, false = closed
$INSTALLER09['openreg_invites'] = true; //==true=open, false = closed
$INSTALLER09['failedlogins'] = 5; // Maximum failed logins before ip ban
$INSTALLER09['flood_time'] = 900; //comment/forum/pm flood limit
$INSTALLER09['readpost_expiry'] = 14*86400; // 14 days
$INSTALLER09['language'] = 'en';
/** define dirs **/
define('INCL_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('ROOT_DIR', realpath(INCL_DIR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
define('ADMIN_DIR', ROOT_DIR.'admin'.DIRECTORY_SEPARATOR);
define('FORUM_DIR', ROOT_DIR.'forums'.DIRECTORY_SEPARATOR);
define('CACHE_DIR', ROOT_DIR.'cache'.DIRECTORY_SEPARATOR);
define('MODS_DIR', ROOT_DIR.'mods'.DIRECTORY_SEPARATOR);
define('LANG_DIR', ROOT_DIR.'lang'.DIRECTORY_SEPARATOR.$INSTALLER09['language'].DIRECTORY_SEPARATOR);  
define('TEMPLATE_DIR', ROOT_DIR.'templates'.DIRECTORY_SEPARATOR);
define('BLOCK_DIR', ROOT_DIR.'blocks'.DIRECTORY_SEPARATOR);
define('IMDB_DIR', ROOT_DIR.'imdb'.DIRECTORY_SEPARATOR);
define('CLASS_DIR', INCL_DIR.'class'.DIRECTORY_SEPARATOR);
define('FLOGIN_DIR', ROOT_DIR.'fancy_login'.DIRECTORY_SEPARATOR);
$INSTALLER09['cache'] = ROOT_DIR.'cache';
$INSTALLER09['backup_dir'] = INCL_DIR.'backup';
$INSTALLER09['dictbreaker'] = ROOT_DIR.'dictbreaker';
$INSTALLER09['torrent_dir'] = ROOT_DIR.'torrents'; # must be writable for httpd user   
$INSTALLER09['bucket_dir'] = ROOT_DIR .'bitbucket'; # must be writable for httpd user 
$INSTALLER09['flood_file'] = INCL_DIR.'settings'.DIRECTORY_SEPARATOR.'limitfile.txt';
$INSTALLER09['nameblacklist'] = ROOT_DIR.'cache'.DIRECTORY_SEPARATOR.'nameblacklist.txt';
# the first one will be displayed on the pages
$INSTALLER09['announce_urls'] = array();
$INSTALLER09['announce_urls'][] = '#announce_urls';
$INSTALLER09['announce_urls'][] = '#announce_urls_https';
if ($_SERVER["HTTP_HOST"] == "")
$_SERVER["HTTP_HOST"] = $_SERVER["SERVER_NAME"];
$INSTALLER09['baseurl'] = 'http'.(isset($_SERVER['HTTPS']) && (bool)$_SERVER['HTTPS'] == true ? 's':'').'://'. $_SERVER['HTTP_HOST'];
//==Auto confirm no email
//== 0 and false = email off
define ('EMAIL_CONFIRM',1);
$INSTALLER09['send_email'] = true;
//== Email for sender/return path.
$INSTALLER09['site_email'] = '#site_email';
$INSTALLER09['site_name'] = '#site_name';
$INSTALLER09['xhtml_strict'] = 0;          // enable for all users
$INSTALLER09['xhtml_strict'] = 'Username'; // enable for one user
$INSTALLER09['msg_alert'] = 1; // saves a query when off
$INSTALLER09['report_alert'] = 1; // saves a query when off
$INSTALLER09['staffmsg_alert'] = 1; // saves a query when off
$INSTALLER09['uploadapp_alert'] = 1; // saves a query when off
$INSTALLER09['sql_error_log'] = ROOT_DIR.'logs'.DIRECTORY_SEPARATOR.'sql_err_'.date('M_D_Y').'.log';
$INSTALLER09['pic_base_url'] = "./pic/";
$INSTALLER09['stylesheet'] = "1";
//== set this to size of user avatars
$INSTALLER09['av_img_height'] = 100;
$INSTALLER09['av_img_width'] = 100;
//== set this to size of user signatures
$INSTALLER09['sig_img_height'] = 100;
$INSTALLER09['sig_img_width'] = 500;
$INSTALLER09['bucket_dir'] = ROOT_DIR . '/bitbucket'; # must be writable for httpd user  
$INSTALLER09['allowed_ext'] = array('image/gif', 'image/png', 'image/jpeg');
$INSTALLER09['bucket_maxsize'] = 2048*2048; #max size set to 500kb
$INSTALLER09['happyhour'] = CACHE_DIR.'happyhour'.DIRECTORY_SEPARATOR.'happyhour.txt';
$INSTALLER09['crazy_title'] ="w00t It's Crazyhour!";
$INSTALLER09['crazy_message'] ="All torrents are FREE and upload stats are TRIPLED!";
//==User class defines
define ('UC_USER', 0);
define ('UC_POWER_USER', 1);
define ('UC_VIP', 2);
define ('UC_UPLOADER', 3);
define ('UC_MODERATOR', 4);
define ('UC_ADMINISTRATOR', 5);
define ('UC_SYSOP', 6);
define('UC_MIN', 0);   // minimum class
define('UC_MAX', 6);   // maximum class
define('UC_STAFF', 4); // start of staff classes
//==View source code
$INSTALLER09['staff_viewcode_on'] = false;
//==Class check by pdq
$INSTALLER09['site']['owner'] = 1;
//== Salt - change this
$INSTALLER09['site']['salt2'] = 'jgutyshjsajk';
//= Change staff pin daily or weekly
$INSTALLER09['staff']['staff_pin'] = 'uFg40y3Iufqo99'; // should be mix of u/l case and min 12 chars length
//== Staff forum ID for autopost
$INSTALLER09['staff']['forumid'] = 2; // this forum ID should exist and be a staff forum
//==Important security settings below
//==Add all your Staff ids
$INSTALLER09['allowed_staff']['id'] = array(1,2);
//== Add ALL staff names before you promote them
$INSTALLER09['staff']['allowed'] = array( 'System'    => 1,
                                           'Admin'     => 1);
                                    
define ('TBVERSION','U-232_V2');
?>