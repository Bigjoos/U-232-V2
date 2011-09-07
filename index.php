<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once CACHE_DIR.'block_settings_cache.php';
require_once CLASS_DIR.'class_blocks_index.php';
require_once INCL_DIR.'user_functions.php';
require_once INCL_DIR.'bbcode_functions.php';
require_once ROOT_DIR.'polls.php';
dbconn(true);
loggedinorreturn();

   $stdfoot = array(/** include js **/'js' => array('shout','java_klappe'));
   $lang = array_merge( load_language('global'), load_language('index') );
   $HTMLOUT = '';
   //==Global blocks by elephant2
   //==Curuser blocks by pdq
   if (curuser::$blocks['index_page'] & block_index::IE_ALERT && $BLOCKS['ie_user_alert']){
   require(BLOCK_DIR.'index/ie_user.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::ANNOUNCEMENT && $BLOCKS['announcement_on']){
   require(BLOCK_DIR.'index/announcement.php');
   }

   if (curuser::$blocks['index_page'] & block_index::SHOUTBOX && $BLOCKS['shoutbox_on']){
   require(BLOCK_DIR.'index/shoutbox.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::NEWS && $BLOCKS['news_on']){
   require(BLOCK_DIR.'index/news.php');
   }

   if (curuser::$blocks['index_page'] & block_index::ADVERTISEMENTS && $BLOCKS['ads_on']){
   require(BLOCK_DIR.'index/advertise.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::FORUMPOSTS && $BLOCKS['forum_posts_on']){
   require(BLOCK_DIR.'index/forum_posts.php');
   }

   if (curuser::$blocks['index_page'] & block_index::LATEST_TORRENTS && $BLOCKS['latest_torrents_on']){
   require(BLOCK_DIR.'index/latest_torrents.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::LATEST_TORRENTS_SCROLL && $BLOCKS['latest_torrents_scroll_on']){
   require(BLOCK_DIR.'index/latest_torrents_scroll.php');
   }
        
   if (curuser::$blocks['index_page'] & block_index::STATS && $BLOCKS['stats_on']){
   require(BLOCK_DIR.'index/stats.php');
   }

   if (curuser::$blocks['index_page'] & block_index::ACTIVE_USERS && $BLOCKS['active_users_on']){
   require(BLOCK_DIR.'index/active_users.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::IRC_ACTIVE_USERS && $BLOCKS['active_irc_users_on']){
   require(BLOCK_DIR.'index/active_irc_users.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::LAST_24_ACTIVE_USERS && $BLOCKS['active_24h_users_on']){
   require(BLOCK_DIR.'index/active_24h_users.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::BIRTHDAY_ACTIVE_USERS && $BLOCKS['active_birthday_users_on']){
   require(BLOCK_DIR.'index/active_birthday_users.php');
   }

   if (curuser::$blocks['index_page'] & block_index::LATEST_USER && $BLOCKS['latest_user_on']){
   require(BLOCK_DIR.'index/latest_user.php');
   }

   if (curuser::$blocks['index_page'] & block_index::ACTIVE_POLL && $BLOCKS['active_poll_on']){
   require(BLOCK_DIR.'index/poll.php');
   }

   if (curuser::$blocks['index_page'] & block_index::DONATION_PROGRESS && $BLOCKS['donation_progress_on']){
   require(BLOCK_DIR.'index/donations.php');
   }

   if (curuser::$blocks['index_page'] & block_index::XMAS_GIFT && $BLOCKS['xmas_gift_on']){
   require(BLOCK_DIR.'index/gift.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::RADIO OR $BLOCKS['radio_on']){
   require(BLOCK_DIR.'index/radio.php');
   }

   if (curuser::$blocks['index_page'] & block_index::TORRENTFREAK && $BLOCKS['torrentfreak_on']){
   require(BLOCK_DIR.'index/torrentfreak.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::DISCLAIMER && $BLOCKS['disclaimer_on']){
   require(BLOCK_DIR.'index/disclaimer.php');
   }
echo stdhead('Home') . $HTMLOUT . stdfoot($stdfoot);
?>