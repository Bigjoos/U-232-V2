-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 19, 2011 at 12:57 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `09source`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `announcement_main`
-- 

CREATE TABLE `announcement_main` (
  `main_id` int(10) unsigned NOT NULL auto_increment,
  `owner_id` int(10) unsigned NOT NULL default '0',
  `created` int(11) NOT NULL default '0',
  `expires` int(11) NOT NULL default '0',
  `sql_query` text NOT NULL,
  `subject` text NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `announcement_main`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `announcement_process`
-- 

CREATE TABLE `announcement_process` (
  `process_id` int(10) unsigned NOT NULL auto_increment,
  `main_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `status` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`process_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `announcement_process`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `attachments`
-- 

CREATE TABLE `attachments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `post_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `file_name` varchar(255) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  `added` int(11) NOT NULL default '0',
  `extension` enum('zip','rar') NOT NULL default 'zip',
  `size` bigint(20) unsigned NOT NULL default '0',
  `times_downloaded` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `attachments`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `avps`
-- 

CREATE TABLE `avps` (
  `arg` varchar(20) collate utf8_unicode_ci NOT NULL,
  `value_s` text collate utf8_unicode_ci NOT NULL,
  `value_i` int(11) NOT NULL default '0',
  `value_u` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`arg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `avps`
-- 

INSERT INTO `avps` VALUES ('lastcleantime', '', 0, 0);
INSERT INTO `avps` VALUES ('lastslowcleantime', '', 0, 0);
INSERT INTO `avps` VALUES ('loadlimit', '2.115-1298280129', 0, 0);
INSERT INTO `avps` VALUES ('inactivemail', '0', 0, 0);
INSERT INTO `avps` VALUES ('lastoptimizedbtime', '', 0, 0);
INSERT INTO `avps` VALUES ('sitepot', '0', 0, 0);
INSERT INTO `avps` VALUES ('lastslowcleantime2', '', 0, 0);
INSERT INTO `avps` VALUES ('lastlottocleantime', '', 0, 0);
INSERT INTO `avps` VALUES ('lastbackuptime', '', 0, 0);
INSERT INTO `avps` VALUES ('last24', '0', 0, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `bans`
-- 

CREATE TABLE `bans` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` int(11) NOT NULL,
  `addedby` int(10) unsigned NOT NULL default '0',
  `comment` varchar(255) collate utf8_unicode_ci NOT NULL,
  `first` int(11) default NULL,
  `last` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `first_last` (`first`,`last`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `bans`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `blackjack`
-- 

CREATE TABLE `blackjack` (
  `userid` int(11) NOT NULL default '0',
  `points` int(11) NOT NULL default '0',
  `status` enum('playing','waiting') collate utf8_bin NOT NULL default 'playing',
  `cards` text collate utf8_bin NOT NULL,
  `date` int(11) default '0',
  `gameover` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `blackjack`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `blocks`
-- 

CREATE TABLE `blocks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `blockid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`blockid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `blocks`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `bonus`
-- 

CREATE TABLE `bonus` (
  `id` int(5) NOT NULL auto_increment,
  `bonusname` varchar(50) NOT NULL default '',
  `points` decimal(10,1) NOT NULL default '0.0',
  `description` text NOT NULL,
  `art` varchar(10) NOT NULL default 'traffic',
  `menge` bigint(20) unsigned NOT NULL default '0',
  `pointspool` decimal(10,1) NOT NULL default '1.0',
  `enabled` enum('yes','no') NOT NULL default 'yes' COMMENT 'This will determined a switch if the bonus is enabled or not! enabled by default',
  `minpoints` decimal(10,1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `bonus`
-- 

INSERT INTO `bonus` VALUES (1, '1.0GB Uploaded', 275.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 1073741824, 1.0, 'yes', 275.0);
INSERT INTO `bonus` VALUES (2, '2.5GB Uploaded', 350.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 2684354560, 1.0, 'yes', 350.0);
INSERT INTO `bonus` VALUES (3, '5GB Uploaded', 550.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 5368709120, 1.0, 'yes', 550.0);
INSERT INTO `bonus` VALUES (4, '3 Invites', 650.0, 'With enough bonus points acquired, you are able to exchange them for a few invites. The points are then removed from your Bonus Bank and the invitations are added to your invites amount.', 'invite', 3, 1.0, 'yes', 650.0);
INSERT INTO `bonus` VALUES (5, 'Custom Title!', 50.0, 'For only 50.0 Karma Bonus Points you can buy yourself a custom title. the only restrictions are no foul or offensive language or userclass can be entered. The points are then removed from your Bonus Bank and your special title is changed to the title of your choice', 'title', 1, 1.0, 'yes', 50.0);
INSERT INTO `bonus` VALUES (6, 'VIP Status', 5000.0, 'With enough bonus points acquired, you can buy yourself VIP status for one month. The points are then removed from your Bonus Bank and your status is changed.', 'class', 1, 1.0, 'yes', 5000.0);
INSERT INTO `bonus` VALUES (7, 'Give A Karma Gift', 100.0, 'Well perhaps you dont need the upload credit, but you know somebody that could use the Karma boost! You are now able to give your Karma credits as a gift! The points are then removed from your Bonus Bank and added to the account of a user of your choice!\r\n\r\nAnd they recieve a PM with all the info as well as who it came from...', 'gift_1', 1073741824, 1.0, 'yes', 100.0);
INSERT INTO `bonus` VALUES (8, 'Custom Smilies', 300.0, 'With enough bonus points acquired, you can buy yourself a set of custom smilies for one month! The points are then removed from your Bonus Bank and with a click of a link, your new smilies are available whenever you post or comment!', 'smile', 1, 1.0, 'yes', 300.0);
INSERT INTO `bonus` VALUES (9, 'Remove Warning', 1000.0, 'With enough bonus points acquired... So you have been naughty... tsk tsk :P Yep now for the Low Low price of only 1000 points you can have that warning taken away lol.!', 'warning', 1, 1.0, 'yes', 1000.0);
INSERT INTO `bonus` VALUES (10, 'Ratio Fix', 500.0, 'With enough bonus points acquired, you can bring the ratio of one torrent to a 1 to 1 ratio! The points are then removed from your Bonus Bank and your status is changed.', 'ratio', 1, 1.0, 'yes', 500.0);
INSERT INTO `bonus` VALUES (11, 'FreeLeech', 30000.0, 'The Ultimate exchange if you have over 30000 Points - Make the tracker freeleech for everyone for 3 days: Upload will count but no download.\r\nIf you dont have enough points you can donate certain amount of your points until it accumulates. Everybodys karma counts!', 'freeleech', 1, 50.0, 'yes', 1.0);
INSERT INTO `bonus` VALUES (12, 'Doubleupload', 30000.0, 'The ultimate exchange if you have over 30000 points - Make the tracker double upload for everyone for 3 days: Upload will count double.\r\nIf you dont have enough points you can donate certain amount of your points until it accumulates. Everybodys karma counts!', 'doubleup', 1, 26100.0, 'yes', 1.0);
INSERT INTO `bonus` VALUES (13, 'Halfdownload', 30000.0, 'The ultimate exchange if you have over 30000 points - Make the tracker Half Download for everyone for 3 days: Download will count only half.\r\nIf you dont have enough points you can donate certain amount of your points until it accumulates. Everybodys karma counts!', 'halfdown', 1, 25001.0, 'yes', 1.0);
INSERT INTO `bonus` VALUES (14, '1.0GB Download Removal', 150.0, 'With enough bonus points acquired, you are able to exchange them for a Download Credit Removal. The points are then removed from your Bonus Bank and the download credit is removed from your total downloaded amount.', 'traffic2', 1073741824, 1.0, 'yes', 150.0);
INSERT INTO `bonus` VALUES (15, '2.5GB Download Removal', 300.0, 'With enough bonus points acquired, you are able to exchange them for a Download Credit Removal. The points are then removed from your Bonus Bank and the download credit is removed from your total downloaded amount.', 'traffic2', 2684354560, 1.0, 'yes', 300.0);
INSERT INTO `bonus` VALUES (16, '5GB Download Removal', 500.0, 'With enough bonus points acquired, you are able to exchange them for a Download Credit Removal. The points are then removed from your Bonus Bank and the download credit is removed from your total downloaded amount.', 'traffic2', 5368709120, 1.0, 'yes', 500.0);
INSERT INTO `bonus` VALUES (17, 'Anonymous Profile', 750.0, 'With enough bonus points acquired, you are able to exchange them for Anonymous profile for 14 days. The points are then removed from your Bonus Bank and the Anonymous switch will show on your profile.', 'anonymous', 1, 1.0, 'yes', 750.0);
INSERT INTO `bonus` VALUES (18, 'Freeleech for 1 Year', 80000.0, 'With enough bonus points acquired, you are able to exchange them for Freelech for one year for yourself. The points are then removed from your Bonus Bank and the freeleech will be enabled on your account.', 'freeyear', 1, 1.0, 'yes', 80000.0);
INSERT INTO `bonus` VALUES (19, '3 Freeleech Slots', 1000.0, 'With enough bonus points acquired, you are able to exchange them for some Freeleech Slots. The points are then removed from your Bonus Bank and the slots are added to your free slots amount.', 'freeslots', 3, 0.0, 'yes', 1000.0);
INSERT INTO `bonus` VALUES (20, '200 Bonus Points - Invite trade-in', 1.0, 'If you have 1 invite and dont use them click the button to trade them in for 200 Bonus Points.', 'itrade', 200, 0.0, 'yes', 0.0);
INSERT INTO `bonus` VALUES (21, 'Freeslots - Invite trade-in', 1.0, 'If you have 1 invite and dont use them click the button to trade them in for 2 Free Slots.', 'itrade2', 2, 0.0, 'yes', 0.0);
INSERT INTO `bonus` VALUES (22, 'Pirate Rank for 2 weeks', 50000.0, 'With enough bonus points acquired, you are able to exchange them for Pirates status and Freeleech for 2 weeks. The points are then removed from your Bonus Bank and the Pirate icon will be displayed throughout, freeleech will then be enabled on your account.', 'pirate', 1, 1.0, 'yes', 50000.0);
INSERT INTO `bonus` VALUES (23, 'King Rank for 1 month', 70000.0, 'With enough bonus points acquired, you are able to exchange them for Kings status and Freeleech for 1 month. The points are then removed from your Bonus Bank and the King icon will be displayed throughout,  freeleech will then be enabled on your account.', 'king', 1, 1.0, 'yes', 70000.0);
INSERT INTO `bonus` VALUES (24, '10GB Uploaded', 1000.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 10737418240, 0.0, 'yes', 1000.0);
INSERT INTO `bonus` VALUES (25, '25GB Uploaded', 2000.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 26843545600, 0.0, 'yes', 2000.0);
INSERT INTO `bonus` VALUES (26, '50GB Uploaded', 4000.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 53687091200, 0.0, 'yes', 4000.0);
INSERT INTO `bonus` VALUES (27, '100GB Uploaded', 8000.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 107374182400, 0.0, 'yes', 8000.0);
INSERT INTO `bonus` VALUES (28, '520GB Uploaded', 40000.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 558345748480, 0.0, 'yes', 40000.0);
INSERT INTO `bonus` VALUES (29, '1TB Uploaded', 80000.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 1099511627776, 0.0, 'yes', 80000.0);
INSERT INTO `bonus` VALUES (30, 'Parked Profile', 75000.0, 'With enough bonus points acquired, you are able to unlock the parked option within your profile which will ensure your account will be safe. The points are then removed from your Bonus Bank and the parked switch will show on your profile.', 'parked', 1, 1.0, 'yes', 75000.0);
INSERT INTO `bonus` VALUES (31, 'Pirates bounty', 50000.0, 'With enough bonus points acquired, you are able to exchange them for Pirates bounty which will select random users and deduct random amount of reputation points from them. The points are removed from your Bonus Bank and the reputation points will be deducted from the selected users then credited to you.', 'bounty', 1, 1.0, 'yes', 50000.0);
INSERT INTO `bonus` VALUES (32, '100 Reputation points', 40000.0, 'With enough bonus points acquired, you are able to exchange them for some reputation points. The points are then removed from your Bonus Bank and the rep is added to your total reputation amount.', 'reputation', 100, 0.0, 'yes', 40000.0);
INSERT INTO `bonus` VALUES (33, 'Userblocks', 50000.0, 'With enough bonus points acquired and a minimum of 50 reputation points, you are able to exchange them for userblocks access. The points are then removed from your Bonus Bank and the userblock configuration page will appear on your menu.', 'userblocks', 0, 0.0, 'yes', 50000.0);

-- --------------------------------------------------------

-- 
-- Table structure for table `bonuslog`
-- 

CREATE TABLE `bonuslog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `donation` decimal(10,1) NOT NULL,
  `type` varchar(44) collate utf8_unicode_ci NOT NULL,
  `added_at` int(11) NOT NULL,
  KEY `id` (`id`),
  KEY `added_at` (`added_at`),
  FULLTEXT KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='log of contributors towards freeleech etc...';

-- 
-- Dumping data for table `bonuslog`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `bookmarks`
-- 

CREATE TABLE `bookmarks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `torrentid` int(10) unsigned NOT NULL default '0',
  `private` enum('yes','no') character set utf8 NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `bookmarks`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `cards`
-- 

CREATE TABLE `cards` (
  `id` int(11) NOT NULL auto_increment,
  `points` int(11) NOT NULL default '0',
  `pic` text collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `cards`
-- 

INSERT INTO `cards` VALUES (1, 2, '2p.bmp');
INSERT INTO `cards` VALUES (2, 3, '3p.bmp');
INSERT INTO `cards` VALUES (3, 4, '4p.bmp');
INSERT INTO `cards` VALUES (4, 5, '5p.bmp');
INSERT INTO `cards` VALUES (5, 6, '6p.bmp');
INSERT INTO `cards` VALUES (6, 7, '7p.bmp');
INSERT INTO `cards` VALUES (7, 8, '8p.bmp');
INSERT INTO `cards` VALUES (8, 9, '9p.bmp');
INSERT INTO `cards` VALUES (9, 10, '10p.bmp');
INSERT INTO `cards` VALUES (10, 10, 'vp.bmp');
INSERT INTO `cards` VALUES (11, 10, 'dp.bmp');
INSERT INTO `cards` VALUES (12, 10, 'kp.bmp');
INSERT INTO `cards` VALUES (13, 1, 'tp.bmp');
INSERT INTO `cards` VALUES (14, 2, '2b.bmp');
INSERT INTO `cards` VALUES (15, 3, '3b.bmp');
INSERT INTO `cards` VALUES (16, 4, '4b.bmp');
INSERT INTO `cards` VALUES (17, 5, '5b.bmp');
INSERT INTO `cards` VALUES (18, 6, '6b.bmp');
INSERT INTO `cards` VALUES (19, 7, '7b.bmp');
INSERT INTO `cards` VALUES (20, 8, '8b.bmp');
INSERT INTO `cards` VALUES (21, 9, '9b.bmp');
INSERT INTO `cards` VALUES (22, 10, '10b.bmp');
INSERT INTO `cards` VALUES (23, 10, 'vb.bmp');
INSERT INTO `cards` VALUES (24, 10, 'db.bmp');
INSERT INTO `cards` VALUES (25, 10, 'kb.bmp');
INSERT INTO `cards` VALUES (26, 1, 'tb.bmp');
INSERT INTO `cards` VALUES (27, 2, '2k.bmp');
INSERT INTO `cards` VALUES (28, 3, '3k.bmp');
INSERT INTO `cards` VALUES (29, 4, '4k.bmp');
INSERT INTO `cards` VALUES (30, 5, '5k.bmp');
INSERT INTO `cards` VALUES (31, 6, '6k.bmp');
INSERT INTO `cards` VALUES (32, 7, '7k.bmp');
INSERT INTO `cards` VALUES (33, 8, '8k.bmp');
INSERT INTO `cards` VALUES (34, 9, '9k.bmp');
INSERT INTO `cards` VALUES (35, 10, '10k.bmp');
INSERT INTO `cards` VALUES (36, 10, 'vk.bmp');
INSERT INTO `cards` VALUES (37, 10, 'dk.bmp');
INSERT INTO `cards` VALUES (38, 10, 'kk.bmp');
INSERT INTO `cards` VALUES (39, 1, 'tk.bmp');
INSERT INTO `cards` VALUES (40, 2, '2c.bmp');
INSERT INTO `cards` VALUES (41, 3, '3c.bmp');
INSERT INTO `cards` VALUES (42, 4, '4c.bmp');
INSERT INTO `cards` VALUES (43, 5, '5c.bmp');
INSERT INTO `cards` VALUES (44, 6, '6c.bmp');
INSERT INTO `cards` VALUES (45, 7, '7c.bmp');
INSERT INTO `cards` VALUES (46, 8, '8c.bmp');
INSERT INTO `cards` VALUES (47, 9, '9c.bmp');
INSERT INTO `cards` VALUES (48, 10, '10c.bmp');
INSERT INTO `cards` VALUES (49, 10, 'vc.bmp');
INSERT INTO `cards` VALUES (50, 10, 'dc.bmp');
INSERT INTO `cards` VALUES (51, 10, 'kc.bmp');
INSERT INTO `cards` VALUES (52, 1, 'tc.bmp');

-- --------------------------------------------------------

-- 
-- Table structure for table `casino`
-- 

CREATE TABLE `casino` (
  `userid` int(10) NOT NULL default '0',
  `win` bigint(20) NOT NULL default '0',
  `lost` bigint(20) NOT NULL default '0',
  `trys` int(11) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `enableplay` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `deposit` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `casino`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `casino_bets`
-- 

CREATE TABLE `casino_bets` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) NOT NULL default '0',
  `proposed` varchar(40) collate utf8_unicode_ci NOT NULL,
  `challenged` varchar(40) collate utf8_unicode_ci NOT NULL,
  `amount` bigint(20) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `winner` varchar(25) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`,`proposed`,`challenged`,`amount`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `casino_bets`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `categories`
-- 

CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `image` varchar(255) collate utf8_unicode_ci NOT NULL,
  `cat_desc` varchar(255) collate utf8_unicode_ci NOT NULL default 'No Description',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `categories`
-- 

INSERT INTO `categories` VALUES (2, 'Games/PC ISO', 'cat_games.gif', 'No Description');
INSERT INTO `categories` VALUES (3, 'Movies/SVCD', 'cat_screeners.gif', 'No Description');
INSERT INTO `categories` VALUES (4, 'Music', 'cat_mp3.gif', 'No Description');
INSERT INTO `categories` VALUES (5, 'Episodes', 'cat_episode.gif', 'No Description');
INSERT INTO `categories` VALUES (6, 'XXX', 'cat_xxx.gif', 'No Description');
INSERT INTO `categories` VALUES (7, 'Games/GBA', 'cat_games.gif', 'No Description');
INSERT INTO `categories` VALUES (8, 'Games/PS2', 'cat_games.gif', 'No Description');
INSERT INTO `categories` VALUES (9, 'Anime', 'cat_anime.gif', 'No Description');
INSERT INTO `categories` VALUES (10, 'Movies/XviD', 'cat_xvid.gif', 'No Description');
INSERT INTO `categories` VALUES (11, 'Movies/DVD-R', 'cat_dvdr.gif', 'No Description');
INSERT INTO `categories` VALUES (12, 'Games/PC Rips', 'cat_games.gif', 'No Description');
INSERT INTO `categories` VALUES (13, 'Appz/misc', 'cat_misc.gif', 'No Description');
INSERT INTO `categories` VALUES (1, 'Apps', 'cat_misc.gif', 'No Description');

-- --------------------------------------------------------

-- 
-- Table structure for table `cheatdetect`
-- 

CREATE TABLE `cheatdetect` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(11) unsigned NOT NULL default '0',
  `torrentid` int(11) unsigned NOT NULL default '0',
  `detectedclient` varchar(35) collate utf8_unicode_ci NOT NULL,
  `suspicion` varchar(30) collate utf8_unicode_ci NOT NULL,
  `time` int(11) NOT NULL default '0',
  `data` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `suspicion` (`suspicion`(1))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `cheatdetect`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `cheaters`
-- 

CREATE TABLE `cheaters` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` int(11) NOT NULL,
  `userid` int(10) NOT NULL default '0',
  `torrentid` int(10) NOT NULL default '0',
  `client` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `rate` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `beforeup` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `upthis` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `timediff` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `userip` varchar(15) collate utf8_unicode_ci NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `cheaters`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `coins`
-- 

CREATE TABLE `coins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `torrentid` int(10) unsigned NOT NULL default '0',
  `points` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `torrentid` (`torrentid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `coins`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `comments`
-- 

CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` int(10) unsigned NOT NULL default '0',
  `torrent` int(10) unsigned NOT NULL default '0',
  `added` int(11) NOT NULL,
  `text` text collate utf8_unicode_ci NOT NULL,
  `ori_text` text collate utf8_unicode_ci NOT NULL,
  `editedby` int(10) unsigned NOT NULL default '0',
  `editedat` int(11) NOT NULL,
  `anonymous` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `request` int(10) unsigned NOT NULL default '0',
  `offer` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `torrent` (`torrent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `comments`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `countries`
-- 

CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci default NULL,
  `flagpic` varchar(50) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `countries`
-- 

INSERT INTO `countries` VALUES (1, 'Sweden', 'sweden.gif');
INSERT INTO `countries` VALUES (2, 'United States of America', 'usa.gif');
INSERT INTO `countries` VALUES (3, 'Russia', 'russia.gif');
INSERT INTO `countries` VALUES (4, 'Finland', 'finland.gif');
INSERT INTO `countries` VALUES (5, 'Canada', 'canada.gif');
INSERT INTO `countries` VALUES (6, 'France', 'france.gif');
INSERT INTO `countries` VALUES (7, 'Germany', 'germany.gif');
INSERT INTO `countries` VALUES (8, 'China', 'china.gif');
INSERT INTO `countries` VALUES (9, 'Italy', 'italy.gif');
INSERT INTO `countries` VALUES (10, 'Denmark', 'denmark.gif');
INSERT INTO `countries` VALUES (11, 'Norway', 'norway.gif');
INSERT INTO `countries` VALUES (12, 'United Kingdom', 'uk.gif');
INSERT INTO `countries` VALUES (13, 'Ireland', 'ireland.gif');
INSERT INTO `countries` VALUES (14, 'Poland', 'poland.gif');
INSERT INTO `countries` VALUES (15, 'Netherlands', 'netherlands.gif');
INSERT INTO `countries` VALUES (16, 'Belgium', 'belgium.gif');
INSERT INTO `countries` VALUES (17, 'Japan', 'japan.gif');
INSERT INTO `countries` VALUES (18, 'Brazil', 'brazil.gif');
INSERT INTO `countries` VALUES (19, 'Argentina', 'argentina.gif');
INSERT INTO `countries` VALUES (20, 'Australia', 'australia.gif');
INSERT INTO `countries` VALUES (21, 'New Zealand', 'newzealand.gif');
INSERT INTO `countries` VALUES (22, 'Spain', 'spain.gif');
INSERT INTO `countries` VALUES (23, 'Portugal', 'portugal.gif');
INSERT INTO `countries` VALUES (24, 'Mexico', 'mexico.gif');
INSERT INTO `countries` VALUES (25, 'Singapore', 'singapore.gif');
INSERT INTO `countries` VALUES (67, 'India', 'india.gif');
INSERT INTO `countries` VALUES (62, 'Albania', 'albania.gif');
INSERT INTO `countries` VALUES (26, 'South Africa', 'southafrica.gif');
INSERT INTO `countries` VALUES (27, 'South Korea', 'southkorea.gif');
INSERT INTO `countries` VALUES (28, 'Jamaica', 'jamaica.gif');
INSERT INTO `countries` VALUES (29, 'Luxembourg', 'luxembourg.gif');
INSERT INTO `countries` VALUES (30, 'Hong Kong', 'hongkong.gif');
INSERT INTO `countries` VALUES (31, 'Belize', 'belize.gif');
INSERT INTO `countries` VALUES (32, 'Algeria', 'algeria.gif');
INSERT INTO `countries` VALUES (33, 'Angola', 'angola.gif');
INSERT INTO `countries` VALUES (34, 'Austria', 'austria.gif');
INSERT INTO `countries` VALUES (35, 'Yugoslavia', 'yugoslavia.gif');
INSERT INTO `countries` VALUES (36, 'Western Samoa', 'westernsamoa.gif');
INSERT INTO `countries` VALUES (37, 'Malaysia', 'malaysia.gif');
INSERT INTO `countries` VALUES (38, 'Dominican Republic', 'dominicanrep.gif');
INSERT INTO `countries` VALUES (39, 'Greece', 'greece.gif');
INSERT INTO `countries` VALUES (40, 'Guatemala', 'guatemala.gif');
INSERT INTO `countries` VALUES (41, 'Israel', 'israel.gif');
INSERT INTO `countries` VALUES (42, 'Pakistan', 'pakistan.gif');
INSERT INTO `countries` VALUES (43, 'Czech Republic', 'czechrep.gif');
INSERT INTO `countries` VALUES (44, 'Serbia', 'serbia.gif');
INSERT INTO `countries` VALUES (45, 'Seychelles', 'seychelles.gif');
INSERT INTO `countries` VALUES (46, 'Taiwan', 'taiwan.gif');
INSERT INTO `countries` VALUES (47, 'Puerto Rico', 'puertorico.gif');
INSERT INTO `countries` VALUES (48, 'Chile', 'chile.gif');
INSERT INTO `countries` VALUES (49, 'Cuba', 'cuba.gif');
INSERT INTO `countries` VALUES (50, 'Congo', 'congo.gif');
INSERT INTO `countries` VALUES (51, 'Afghanistan', 'afghanistan.gif');
INSERT INTO `countries` VALUES (52, 'Turkey', 'turkey.gif');
INSERT INTO `countries` VALUES (53, 'Uzbekistan', 'uzbekistan.gif');
INSERT INTO `countries` VALUES (54, 'Switzerland', 'switzerland.gif');
INSERT INTO `countries` VALUES (55, 'Kiribati', 'kiribati.gif');
INSERT INTO `countries` VALUES (56, 'Philippines', 'philippines.gif');
INSERT INTO `countries` VALUES (57, 'Burkina Faso', 'burkinafaso.gif');
INSERT INTO `countries` VALUES (58, 'Nigeria', 'nigeria.gif');
INSERT INTO `countries` VALUES (59, 'Iceland', 'iceland.gif');
INSERT INTO `countries` VALUES (60, 'Nauru', 'nauru.gif');
INSERT INTO `countries` VALUES (61, 'Slovenia', 'slovenia.gif');
INSERT INTO `countries` VALUES (63, 'Turkmenistan', 'turkmenistan.gif');
INSERT INTO `countries` VALUES (64, 'Bosnia Herzegovina', 'bosniaherzegovina.gif');
INSERT INTO `countries` VALUES (65, 'Andorra', 'andorra.gif');
INSERT INTO `countries` VALUES (66, 'Lithuania', 'lithuania.gif');
INSERT INTO `countries` VALUES (68, 'Netherlands Antilles', 'nethantilles.gif');
INSERT INTO `countries` VALUES (69, 'Ukraine', 'ukraine.gif');
INSERT INTO `countries` VALUES (70, 'Venezuela', 'venezuela.gif');
INSERT INTO `countries` VALUES (71, 'Hungary', 'hungary.gif');
INSERT INTO `countries` VALUES (72, 'Romania', 'romania.gif');
INSERT INTO `countries` VALUES (73, 'Vanuatu', 'vanuatu.gif');
INSERT INTO `countries` VALUES (74, 'Vietnam', 'vietnam.gif');
INSERT INTO `countries` VALUES (75, 'Trinidad & Tobago', 'trinidadandtobago.gif');
INSERT INTO `countries` VALUES (76, 'Honduras', 'honduras.gif');
INSERT INTO `countries` VALUES (77, 'Kyrgyzstan', 'kyrgyzstan.gif');
INSERT INTO `countries` VALUES (78, 'Ecuador', 'ecuador.gif');
INSERT INTO `countries` VALUES (79, 'Bahamas', 'bahamas.gif');
INSERT INTO `countries` VALUES (80, 'Peru', 'peru.gif');
INSERT INTO `countries` VALUES (81, 'Cambodia', 'cambodia.gif');
INSERT INTO `countries` VALUES (82, 'Barbados', 'barbados.gif');
INSERT INTO `countries` VALUES (83, 'Bangladesh', 'bangladesh.gif');
INSERT INTO `countries` VALUES (84, 'Laos', 'laos.gif');
INSERT INTO `countries` VALUES (85, 'Uruguay', 'uruguay.gif');
INSERT INTO `countries` VALUES (86, 'Antigua Barbuda', 'antiguabarbuda.gif');
INSERT INTO `countries` VALUES (87, 'Paraguay', 'paraguay.gif');
INSERT INTO `countries` VALUES (89, 'Thailand', 'thailand.gif');
INSERT INTO `countries` VALUES (88, 'Union of Soviet Socialist Republics', 'ussr.gif');
INSERT INTO `countries` VALUES (90, 'Senegal', 'senegal.gif');
INSERT INTO `countries` VALUES (91, 'Togo', 'togo.gif');
INSERT INTO `countries` VALUES (92, 'North Korea', 'northkorea.gif');
INSERT INTO `countries` VALUES (93, 'Croatia', 'croatia.gif');
INSERT INTO `countries` VALUES (94, 'Estonia', 'estonia.gif');
INSERT INTO `countries` VALUES (95, 'Colombia', 'colombia.gif');
INSERT INTO `countries` VALUES (96, 'Lebanon', 'lebanon.gif');
INSERT INTO `countries` VALUES (97, 'Latvia', 'latvia.gif');
INSERT INTO `countries` VALUES (98, 'Costa Rica', 'costarica.gif');
INSERT INTO `countries` VALUES (99, 'Egypt', 'egypt.gif');
INSERT INTO `countries` VALUES (100, 'Bulgaria', 'bulgaria.gif');
INSERT INTO `countries` VALUES (101, 'Scotland', 'scotland.gif');
INSERT INTO `countries` VALUES (102, 'United Arab Emirates', 'uae.gif');

-- --------------------------------------------------------

-- 
-- Table structure for table `dbbackup`
-- 

CREATE TABLE `dbbackup` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL,
  `added` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `dbbackup`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `events`
-- 

CREATE TABLE `events` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `startTime` int(11) NOT NULL,
  `endTime` int(11) NOT NULL,
  `overlayText` text collate utf8_unicode_ci NOT NULL,
  `displayDates` tinyint(1) NOT NULL,
  `freeleechEnabled` tinyint(1) NOT NULL,
  `duploadEnabled` tinyint(1) NOT NULL,
  `hdownEnabled` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `startTime` (`startTime`,`endTime`),
  FULLTEXT KEY `overlayText` (`overlayText`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `events`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `failedlogins`
-- 

CREATE TABLE `failedlogins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `added` int(11) NOT NULL,
  `banned` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `attempts` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `failedlogins`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `files`
-- 

CREATE TABLE `files` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrent` int(10) unsigned NOT NULL default '0',
  `filename` varchar(255) collate utf8_unicode_ci NOT NULL,
  `size` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `torrent` (`torrent`),
  FULLTEXT KEY `filename` (`filename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `files`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `forums`
-- 

CREATE TABLE `forums` (
  `sort` tinyint(3) unsigned NOT NULL default '0',
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `description` varchar(200) default NULL,
  `min_class_read` tinyint(3) unsigned NOT NULL default '0',
  `min_class_write` tinyint(3) unsigned NOT NULL default '0',
  `post_count` int(10) unsigned NOT NULL default '0',
  `topic_count` int(10) unsigned NOT NULL default '0',
  `min_class_create` tinyint(3) unsigned NOT NULL default '0',
  `parent_forum` tinyint(4) NOT NULL default '0',
  `forum_id` tinyint(4) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `forums`
-- 

INSERT INTO `forums` VALUES (0, 2, 'Testing Bunny Forums', '', 0, 0, 5, 1, 4, 0, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `forum_config`
-- 

CREATE TABLE `forum_config` (
  `id` smallint(1) NOT NULL default '1',
  `delete_for_real` smallint(6) NOT NULL default '0',
  `min_delete_view_class` smallint(2) unsigned NOT NULL default '7',
  `readpost_expiry` smallint(3) NOT NULL default '14',
  `min_upload_class` smallint(2) unsigned NOT NULL default '2',
  `accepted_file_extension` varchar(80) NOT NULL,
  `accepted_file_types` varchar(280) NOT NULL,
  `max_file_size` int(10) unsigned NOT NULL default '2097152',
  `upload_folder` varchar(80) NOT NULL default 'uploads/',
  PRIMARY KEY  (`readpost_expiry`),
  KEY `delete_for_real` (`delete_for_real`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `forum_config`
-- 

INSERT INTO `forum_config` VALUES (13, 0, 4, 7, 0, 'a:5:{i:0;s:3:"zip";i:1;s:3:"rar";i:2;s:3:"gif";i:3;s:3:"png";i:4;s:0:"";}', 'a:3:{i:0;s:15:"application/zip";i:1;s:15:"application/rar";i:2;s:0:"";}', 2097152, 'uploads/');

-- --------------------------------------------------------

-- 
-- Table structure for table `forum_poll`
-- 

CREATE TABLE `forum_poll` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `question` varchar(280) NOT NULL,
  `poll_answers` text,
  `number_of_options` smallint(2) unsigned NOT NULL default '0',
  `poll_starts` int(11) NOT NULL default '0',
  `poll_ends` int(11) NOT NULL default '0',
  `change_vote` enum('yes','no') NOT NULL default 'no',
  `multi_options` smallint(2) unsigned NOT NULL default '1',
  `poll_closed` enum('yes','no') default 'no',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `forum_poll`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `forum_poll_votes`
-- 

CREATE TABLE `forum_poll_votes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `poll_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `option` tinyint(3) unsigned NOT NULL default '0',
  `ip` varchar(15) NOT NULL default '',
  `added` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `forum_poll_votes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `freeslots`
-- 

CREATE TABLE `freeslots` (
  `tid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `double` int(10) unsigned NOT NULL default '0',
  `free` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `tid_uid` (`tid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `freeslots`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `friends`
-- 

CREATE TABLE `friends` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `friendid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`friendid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `friends`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `funds`
-- 

CREATE TABLE `funds` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cash` decimal(8,2) NOT NULL default '0.00',
  `user` int(10) unsigned NOT NULL default '0',
  `added` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `funds`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `happyhour`
-- 

CREATE TABLE `happyhour` (
  `id` int(10) NOT NULL auto_increment,
  `userid` int(10) NOT NULL default '0',
  `torrentid` int(10) NOT NULL default '0',
  `multiplier` float NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`,`torrentid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `happyhour`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `happylog`
-- 

CREATE TABLE `happylog` (
  `id` int(10) NOT NULL auto_increment,
  `userid` int(10) NOT NULL default '0',
  `torrentid` int(10) NOT NULL default '0',
  `multi` float NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`,`torrentid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `happylog`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `infolog`
-- 

CREATE TABLE `infolog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` int(11) default '0',
  `txt` text character set latin1,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `infolog`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `invite_codes`
-- 

CREATE TABLE `invite_codes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender` int(10) unsigned NOT NULL default '0',
  `receiver` varchar(32) NOT NULL default '0',
  `code` varchar(32) NOT NULL default '',
  `invite_added` int(10) NOT NULL,
  `status` enum('Pending','Confirmed') NOT NULL default 'Pending',
  PRIMARY KEY  (`id`),
  KEY `sender` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `invite_codes`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `ips`
-- 

CREATE TABLE `ips` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip` varchar(15) character set latin1 collate latin1_bin default NULL,
  `userid` int(10) default NULL,
  `type` enum('login','announce','browse') NOT NULL,
  `seedbox` tinyint(1) NOT NULL default '0',
  `lastbrowse` int(11) NOT NULL default '0',
  `lastlogin` int(11) NOT NULL default '0',
  `lastannounce` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `ips`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `lottery_config`
-- 

CREATE TABLE `lottery_config` (
  `name` varchar(255) character set latin1 NOT NULL default '',
  `value` varchar(255) character set latin1 NOT NULL default '',
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `lottery_config`
-- 

INSERT INTO `lottery_config` VALUES ('ticket_amount', '10000');
INSERT INTO `lottery_config` VALUES ('ticket_amount_type', 'seedbonus');
INSERT INTO `lottery_config` VALUES ('user_tickets', '5');
INSERT INTO `lottery_config` VALUES ('class_allowed', '0|1|2|3|4|5|6');
INSERT INTO `lottery_config` VALUES ('total_winners', '1');
INSERT INTO `lottery_config` VALUES ('prize_fund', '10000000');
INSERT INTO `lottery_config` VALUES ('start_date', '1300268213');
INSERT INTO `lottery_config` VALUES ('end_date', '1300860413');
INSERT INTO `lottery_config` VALUES ('use_prize_fund', '0');
INSERT INTO `lottery_config` VALUES ('enable', '1');
INSERT INTO `lottery_config` VALUES ('lottery_winners', '1');
INSERT INTO `lottery_config` VALUES ('lottery_winners_amount', '333.33');
INSERT INTO `lottery_config` VALUES ('lottery_winners_time', '1285586662');

-- --------------------------------------------------------

-- 
-- Table structure for table `messages`
-- 

CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender` int(10) unsigned NOT NULL default '0',
  `receiver` int(10) unsigned NOT NULL default '0',
  `added` int(11) NOT NULL,
  `subject` varchar(30) collate utf8_unicode_ci NOT NULL default 'No Subject',
  `msg` text collate utf8_unicode_ci,
  `unread` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `poster` bigint(20) unsigned NOT NULL default '0',
  `location` smallint(6) NOT NULL default '1',
  `saved` enum('no','yes') collate utf8_unicode_ci NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `receiver` (`receiver`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `messages`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `news`
-- 

CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `added` int(11) NOT NULL default '0',
  `body` text character set utf8 collate utf8_bin NOT NULL,
  `title` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `sticky` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `news`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `notconnectablepmlog`
-- 

CREATE TABLE `notconnectablepmlog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` int(10) unsigned NOT NULL default '0',
  `date` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `notconnectablepmlog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `now_viewing`
-- 

CREATE TABLE `now_viewing` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `topic_id` int(10) unsigned NOT NULL default '0',
  `added` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `now_viewing`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `offers`
-- 

CREATE TABLE `offers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `offer` varchar(225) collate utf8_unicode_ci NOT NULL default '',
  `descr` text collate utf8_unicode_ci NOT NULL,
  `added` int(11) unsigned NOT NULL default '0',
  `comments` int(11) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `cat` int(10) unsigned NOT NULL default '0',
  `acceptedby` int(10) unsigned NOT NULL default '0',
  `torrentid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `id_added` (`id`,`added`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `offers`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `over_forums`
-- 

CREATE TABLE `over_forums` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `description` varchar(200) default NULL,
  `min_class_view` tinyint(3) unsigned NOT NULL default '0',
  `sort` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `over_forums`
-- 

INSERT INTO `over_forums` VALUES (2, 'Testing Bunny Forums', '', 0, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `peers`
-- 

CREATE TABLE `peers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrent` int(10) unsigned NOT NULL default '0',
  `passkey` varchar(32) collate utf8_unicode_ci NOT NULL,
  `peer_id` varchar(20) character set utf8 collate utf8_bin NOT NULL,
  `ip` varchar(64) collate utf8_unicode_ci NOT NULL,
  `port` smallint(5) unsigned NOT NULL default '0',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `to_go` bigint(20) unsigned NOT NULL default '0',
  `seeder` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `started` int(11) NOT NULL,
  `last_action` int(11) NOT NULL,
  `connectable` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `userid` int(10) unsigned NOT NULL default '0',
  `agent` varchar(60) collate utf8_unicode_ci NOT NULL,
  `finishedat` int(10) unsigned NOT NULL default '0',
  `downloadoffset` bigint(20) unsigned NOT NULL default '0',
  `uploadoffset` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `torrent_peer_id` (`torrent`,`peer_id`),
  KEY `torrent` (`torrent`),
  KEY `torrent_seeder` (`torrent`,`seeder`),
  KEY `last_action` (`last_action`),
  KEY `connectable` (`connectable`),
  KEY `userid` (`userid`),
  KEY `passkey` (`passkey`),
  KEY `torrent_connect` (`torrent`,`connectable`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `peers`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `pmboxes`
-- 

CREATE TABLE `pmboxes` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `boxnumber` tinyint(4) NOT NULL default '2',
  `name` varchar(15) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `pmboxes`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `polls`
-- 

CREATE TABLE `polls` (
  `pid` mediumint(8) NOT NULL auto_increment,
  `start_date` int(10) default NULL,
  `choices` mediumtext character set utf8 collate utf8_unicode_ci,
  `starter_id` mediumint(8) NOT NULL default '0',
  `starter_name` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `votes` smallint(5) NOT NULL default '0',
  `poll_question` varchar(255) character set utf8 collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `polls`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `poll_voters`
-- 

CREATE TABLE `poll_voters` (
  `vid` int(10) NOT NULL auto_increment,
  `ip_address` varchar(16) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `vote_date` int(10) NOT NULL default '0',
  `poll_id` int(10) NOT NULL default '0',
  `user_id` varchar(32) character set utf8 collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`vid`),
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `poll_voters`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `posts`
-- 

CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `topic_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `added` int(11) NOT NULL default '0',
  `body` text,
  `edited_by` int(10) unsigned NOT NULL default '0',
  `edit_date` int(11) NOT NULL default '0',
  `icon` varchar(80) default NULL,
  `post_title` varchar(120) default NULL,
  `bbcode` enum('yes','no') NOT NULL default 'yes',
  `post_history` text NOT NULL,
  `edit_reason` varchar(60) default NULL,
  `ip` varchar(15) NOT NULL default '',
  `status` enum('deleted','recycled','ok') NOT NULL default 'ok',
  PRIMARY KEY  (`id`),
  KEY `topicid` (`topic_id`),
  KEY `userid` (`user_id`),
  FULLTEXT KEY `body` (`post_title`,`body`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `posts`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `promo`
-- 

CREATE TABLE `promo` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(120) character set utf8 NOT NULL,
  `added` int(10) NOT NULL default '0',
  `days_valid` int(2) NOT NULL default '0',
  `accounts_made` int(3) NOT NULL default '0',
  `max_users` int(3) NOT NULL default '0',
  `link` varchar(32) character set utf8 NOT NULL,
  `creator` int(10) NOT NULL default '0',
  `users` text character set utf8 NOT NULL,
  `bonus_upload` bigint(10) NOT NULL default '0',
  `bonus_invites` int(2) NOT NULL default '0',
  `bonus_karma` int(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `promo`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `ratings`
-- 

CREATE TABLE `ratings` (
  `torrent` int(10) unsigned NOT NULL default '0',
  `user` int(10) unsigned NOT NULL default '0',
  `rating` tinyint(3) unsigned NOT NULL default '0',
  `added` int(11) NOT NULL default '0',
  `topic` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`torrent`,`user`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `ratings`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `read_posts`
-- 

CREATE TABLE `read_posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `topic_id` int(10) unsigned NOT NULL default '0',
  `last_post_read` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `topic_id` (`topic_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `read_posts`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `reports`
-- 

CREATE TABLE `reports` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `reported_by` int(10) unsigned NOT NULL default '0',
  `reporting_what` int(10) unsigned NOT NULL default '0',
  `reporting_type` enum('User','Comment','Request_Comment','Offer_Comment','Request','Offer','Torrent','Hit_And_Run','Post') character set utf8 NOT NULL default 'Torrent',
  `reason` text character set utf8 NOT NULL,
  `who_delt_with_it` int(10) unsigned NOT NULL default '0',
  `delt_with` tinyint(1) NOT NULL default '0',
  `added` int(11) NOT NULL default '0',
  `how_delt_with` text character set utf8 NOT NULL,
  `2nd_value` int(10) unsigned NOT NULL default '0',
  `when_delt_with` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `delt_with` (`delt_with`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `reports`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `reputation`
-- 

CREATE TABLE `reputation` (
  `reputationid` int(11) unsigned NOT NULL auto_increment,
  `reputation` int(10) NOT NULL default '0',
  `whoadded` int(10) NOT NULL default '0',
  `reason` varchar(250) collate utf8_unicode_ci default NULL,
  `dateadd` int(10) NOT NULL default '0',
  `locale` enum('posts','comments','torrents','users') collate utf8_unicode_ci NOT NULL default 'posts',
  `postid` int(10) NOT NULL default '0',
  `userid` mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (`reputationid`),
  KEY `userid` (`userid`),
  KEY `whoadded` (`whoadded`),
  KEY `multi` (`postid`,`userid`),
  KEY `dateadd` (`dateadd`),
  KEY `locale` (`locale`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `reputation`
-- 

INSERT INTO `reputation` VALUES (1, 5, 26, ':)', 1272357798, 'torrents', 11, 19);
INSERT INTO `reputation` VALUES (2, 5, 1, 'test', 1272875083, 'torrents', 13, 26);
INSERT INTO `reputation` VALUES (3, 5, 26, 'test', 1273518600, 'posts', 42, 1);
INSERT INTO `reputation` VALUES (4, 5, 26, ':)', 1274218450, 'torrents', 28, 1);
INSERT INTO `reputation` VALUES (5, 5, 21, '', 1274716502, 'posts', 67, 2);
INSERT INTO `reputation` VALUES (6, 0, 51, '', 1276008176, 'users', 1, 1);
INSERT INTO `reputation` VALUES (7, 5, 1, 'test', 1277864693, 'users', 2, 2);
INSERT INTO `reputation` VALUES (8, 5, 15, '10', 1279198290, 'posts', 84, 2);
INSERT INTO `reputation` VALUES (9, 5, 34, '', 1279558360, 'torrents', 16, 1);
INSERT INTO `reputation` VALUES (10, 5, 7, '', 1279788406, 'posts', 102, 1);
INSERT INTO `reputation` VALUES (11, 5, 7, 'test', 1279788437, 'users', 1, 1);
INSERT INTO `reputation` VALUES (12, 5, 2, 'test', 1279828697, 'torrents', 17, 1);
INSERT INTO `reputation` VALUES (13, 5, 15, '', 1279895327, 'torrents', 17, 1);
INSERT INTO `reputation` VALUES (14, 5, 1, 'Test', 1280411374, 'users', 97, 97);
INSERT INTO `reputation` VALUES (15, 5, 1, 'Test', 1280784342, 'users', 6, 6);
INSERT INTO `reputation` VALUES (16, 5, 49, 'good', 1283499532, 'torrents', 21, 1);
INSERT INTO `reputation` VALUES (17, 5, 116, '', 1283724913, 'users', 1, 1);
INSERT INTO `reputation` VALUES (18, 5, 92, '', 1283997335, 'posts', 161, 1);
INSERT INTO `reputation` VALUES (19, 5, 1, 'Testings', 1288697470, 'users', 80, 80);
INSERT INTO `reputation` VALUES (20, 5, 87, '', 1290899590, 'torrents', 9, 7);
INSERT INTO `reputation` VALUES (21, 5, 10, '', 1293445706, 'torrents', 27, 7);
INSERT INTO `reputation` VALUES (22, 0, 186, 'coz i love u :P', 1296937735, 'users', 183, 183);
INSERT INTO `reputation` VALUES (23, 5, 56, 'thx', 1297351792, 'torrents', 99, 94);
INSERT INTO `reputation` VALUES (24, 5, 99, '', 1297443775, 'users', 94, 94);
INSERT INTO `reputation` VALUES (25, 5, 1, 'Test', 1298672015, 'torrents', 106, 31);
INSERT INTO `reputation` VALUES (26, 0, 208, 'Test', 1299632812, 'users', 229, 229);

-- --------------------------------------------------------

-- 
-- Table structure for table `reputationlevel`
-- 

CREATE TABLE `reputationlevel` (
  `reputationlevelid` int(11) unsigned NOT NULL auto_increment,
  `minimumreputation` int(10) NOT NULL default '0',
  `level` varchar(250) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`reputationlevelid`),
  KEY `reputationlevel` (`minimumreputation`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `reputationlevel`
-- 

INSERT INTO `reputationlevel` VALUES (1, -999999, 'is infamous around these parts');
INSERT INTO `reputationlevel` VALUES (2, -50, 'can only hope to improve');
INSERT INTO `reputationlevel` VALUES (3, -10, 'has a little shameless behaviour in the past');
INSERT INTO `reputationlevel` VALUES (4, 0, 'is an unknown quantity at this point');
INSERT INTO `reputationlevel` VALUES (5, 15, 'is on a distinguished road');
INSERT INTO `reputationlevel` VALUES (6, 50, 'will become famous soon enough');
INSERT INTO `reputationlevel` VALUES (7, 150, 'has a spectacular aura about');
INSERT INTO `reputationlevel` VALUES (8, 250, 'is a jewel in the rough');
INSERT INTO `reputationlevel` VALUES (9, 350, 'is just really nice');
INSERT INTO `reputationlevel` VALUES (10, 450, 'is a glorious beacon of light');
INSERT INTO `reputationlevel` VALUES (11, 550, 'is a name known to all');
INSERT INTO `reputationlevel` VALUES (12, 650, 'is a splendid one to behold');
INSERT INTO `reputationlevel` VALUES (13, 1000, 'has much to be proud of');
INSERT INTO `reputationlevel` VALUES (14, 1500, 'has a brilliant future');
INSERT INTO `reputationlevel` VALUES (15, 2000, 'has a reputation beyond repute');

-- --------------------------------------------------------

-- 
-- Table structure for table `requests`
-- 

CREATE TABLE `requests` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `request` varchar(225) default NULL,
  `descr` text NOT NULL,
  `added` int(11) unsigned NOT NULL default '0',
  `comments` int(11) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `cat` int(10) unsigned NOT NULL default '0',
  `filledby` int(10) unsigned NOT NULL,
  `torrentid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `id_added` (`id`,`added`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `requests`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `searchcloud`
-- 

CREATE TABLE `searchcloud` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `searchedfor` varchar(50) collate utf8_unicode_ci NOT NULL,
  `howmuch` int(10) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `searchedfor` (`searchedfor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `searchcloud`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `shit_list`
-- 

CREATE TABLE `shit_list` (
  `userid` int(10) unsigned NOT NULL default '0',
  `suspect` int(10) unsigned NOT NULL default '0',
  `shittyness` int(2) unsigned NOT NULL default '0',
  `added` int(11) NOT NULL default '0',
  `text` text collate utf8_unicode_ci,
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `shit_list`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `shoutbox`
-- 

CREATE TABLE `shoutbox` (
  `id` bigint(10) NOT NULL auto_increment,
  `userid` bigint(6) NOT NULL default '0',
  `to_user` int(10) NOT NULL default '0',
  `username` varchar(25) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `text` text NOT NULL,
  `text_parsed` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `for` (`to_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `shoutbox`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `sitelog`
-- 

CREATE TABLE `sitelog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` int(11) NOT NULL,
  `txt` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `sitelog`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `snatched`
-- 

CREATE TABLE `snatched` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `torrentid` int(10) unsigned NOT NULL default '0',
  `ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `port` smallint(5) unsigned NOT NULL default '0',
  `connectable` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `agent` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `peer_id` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `upspeed` bigint(20) NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `downspeed` bigint(20) NOT NULL default '0',
  `to_go` bigint(20) unsigned NOT NULL default '0',
  `seeder` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `seedtime` int(11) unsigned NOT NULL default '0',
  `leechtime` int(11) unsigned NOT NULL default '0',
  `start_date` int(11) NOT NULL,
  `last_action` int(11) NOT NULL,
  `complete_date` int(11) NOT NULL,
  `timesann` int(10) unsigned NOT NULL default '0',
  `hit_and_run` int(11) NOT NULL,
  `mark_of_cain` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `finished` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `tr_usr` (`torrentid`,`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `snatched`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `staffmessages`
-- 

CREATE TABLE `staffmessages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender` int(10) unsigned NOT NULL default '0',
  `added` int(11) default '0',
  `msg` text collate utf8_unicode_ci,
  `subject` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `answeredby` int(10) unsigned NOT NULL default '0',
  `answered` int(1) NOT NULL default '0',
  `answer` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  KEY `answeredby` (`answeredby`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `staffmessages`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `staffpanel`
-- 

CREATE TABLE `staffpanel` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `page_name` varchar(80) collate utf8_unicode_ci NOT NULL,
  `file_name` varchar(80) collate utf8_unicode_ci NOT NULL,
  `description` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `av_class` tinyint(3) unsigned NOT NULL default '0',
  `added_by` int(10) unsigned NOT NULL default '0',
  `added` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `file_name` (`file_name`),
  KEY `av_class` (`av_class`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `staffpanel`
-- 

INSERT INTO `staffpanel` VALUES (1, 'Flood Control', 'staffpanel.php?tool=floodlimit', 'Manage flood limits', 5, 1, 1277910147);
INSERT INTO `staffpanel` VALUES (2, 'Coders Log', 'staffpanel.php?tool=editlog', 'Coders site file edit log', 6, 1, 1277909868);
INSERT INTO `staffpanel` VALUES (3, 'Bonus Manager', 'staffpanel.php?tool=bonusmanage', 'Site karma bonus manager', 5, 1, 1277910813);
INSERT INTO `staffpanel` VALUES (4, 'High Speeds', 'staffpanel.php?tool=cheaters', 'Detect possible ratio cheats', 4, 1, 1277911147);
INSERT INTO `staffpanel` VALUES (5, 'Non Connectables', 'staffpanel.php?tool=findnotconnectable', 'Find - Pm non-connectable users', 4, 1, 1277911274);
INSERT INTO `staffpanel` VALUES (6, 'Manual Cleanup', 'staffpanel.php?tool=docleanup', 'Manually run site cleanup cycles', 6, 1, 1277911477);
INSERT INTO `staffpanel` VALUES (7, 'Edit Events', 'staffpanel.php?tool=events', 'Edit - Add Freeleech/doubleseed/halfdownload events', 6, 1, 1277911847);
INSERT INTO `staffpanel` VALUES (8, 'Site Log', 'staffpanel.php?tool=log', 'View site log', 4, 1, 1277912694);
INSERT INTO `staffpanel` VALUES (9, 'Poll Manager', 'staffpanel.php?tool=polls_manager', 'Add - Edit site polls', 4, 1, 1277912814);
INSERT INTO `staffpanel` VALUES (10, 'Ban Ips', 'staffpanel.php?tool=bans', 'Cached ip ban manager', 4, 1, 1277912935);
INSERT INTO `staffpanel` VALUES (11, 'Add user', 'staffpanel.php?tool=adduser', 'Add new users from site', 5, 1, 1277912999);
INSERT INTO `staffpanel` VALUES (12, 'Extra Stats', 'staffpanel.php?tool=stats_extra', 'View graphs of site stats', 5, 1, 1277913051);
INSERT INTO `staffpanel` VALUES (13, 'Templates', 'staffpanel.php?tool=themes', 'Site template manager', 6, 1, 1277913213);
INSERT INTO `staffpanel` VALUES (14, 'Tracker Stats', 'staffpanel.php?tool=stats', 'View uploader and category activity', 4, 1, 1277913435);
INSERT INTO `staffpanel` VALUES (15, 'Shoutbox History', 'staffpanel.php?tool=shistory', 'View shout history', 4, 1, 1277913521);
INSERT INTO `staffpanel` VALUES (16, 'Backup Db', 'staffpanel.php?tool=backup', 'Mysql Database Back Up', 6, 1, 1277913720);
INSERT INTO `staffpanel` VALUES (17, 'Usersearch', 'staffpanel.php?tool=usersearch', 'Mass pm and Mass announcement system', 5, 1, 1277913916);
INSERT INTO `staffpanel` VALUES (18, 'Manual optimize', 'staffpanel.php?tool=mysql_overview', 'Mysql overview', 6, 1, 1277914491);
INSERT INTO `staffpanel` VALUES (19, 'Mysql Stats', 'staffpanel.php?tool=mysql_stats', 'Mysql server stats', 6, 1, 1277914654);
INSERT INTO `staffpanel` VALUES (20, 'Failed Logins', 'staffpanel.php?tool=failedlogins', 'Clear Failed Logins', 4, 1, 1277914881);
INSERT INTO `staffpanel` VALUES (21, 'Invite Manager', 'staffpanel.php?tool=inviteadd', 'Manage site invites', 5, 1, 1277915658);
INSERT INTO `staffpanel` VALUES (22, 'Inactive Users', 'staffpanel.php?tool=inactive', 'Manage inactive users', 4, 1, 1277915991);
INSERT INTO `staffpanel` VALUES (23, 'Reset Passwords', 'staffpanel.php?tool=reset', 'Reset lost passwords', 4, 1, 1277916104);
INSERT INTO `staffpanel` VALUES (24, 'Forum Manager', 'staffpanel.php?tool=forum_manage', 'Forum admin and management', 5, 1, 1277916172);
INSERT INTO `staffpanel` VALUES (25, 'Overforum Manager', 'staffpanel.php?tool=over_forums', 'Over Forum admin and management', 5, 1, 1277916240);
INSERT INTO `staffpanel` VALUES (26, 'Edit Categories', 'staffpanel.php?tool=categories', 'Manage site categories', 6, 1, 1277916351);
INSERT INTO `staffpanel` VALUES (27, 'Reputation Admin', 'reputation_ad.php', 'Reputation system admin', 6, 1, 1277916398);
INSERT INTO `staffpanel` VALUES (28, 'Reputation Settings', 'reputation_settings.php', 'Manage reputation settings', 6, 1, 1277916443);
INSERT INTO `staffpanel` VALUES (29, 'News Admin', 'staffpanel.php?tool=news', 'Add - Edit site news', 4, 1, 1277916501);
INSERT INTO `staffpanel` VALUES (30, 'Freeslot Manage', 'staffpanel.php?tool=slotmanage', 'Manage site freeslots', 5, 1, 1277916560);
INSERT INTO `staffpanel` VALUES (31, 'Freeleech Manage', 'staffpanel.php?tool=freeleech', 'Manage site wide freeleech', 5, 1, 1277916603);
INSERT INTO `staffpanel` VALUES (32, 'Freeleech Users', 'staffpanel.php?tool=freeusers', 'View freeleech users', 5, 1, 1277916636);
INSERT INTO `staffpanel` VALUES (33, 'Site Donations', 'staffpanel.php?tool=donations', 'View all/current site donations', 6, 1, 1277916690);
INSERT INTO `staffpanel` VALUES (34, 'View Reports', 'staffpanel.php?tool=reports', 'Respond to site reports', 4, 1, 1278323407);
INSERT INTO `staffpanel` VALUES (35, 'Delete', 'staffpanel.php?tool=delacct', 'Delete user accounts', 4, 1, 1278456787);
INSERT INTO `staffpanel` VALUES (36, 'Username change', 'staffpanel.php?tool=namechanger', 'Change usernames here.', 6, 1, 1278886954);
INSERT INTO `staffpanel` VALUES (37, 'Blacklist', 'staffpanel.php?tool=nameblacklist', 'Control username blacklist.', 4, 1, 1279054005);
INSERT INTO `staffpanel` VALUES (38, 'System Overview', 'staffpanel.php?tool=system_view', 'Monitor load averages and view phpinfo', 6, 1, 1277910147);
INSERT INTO `staffpanel` VALUES (39, 'Snatched Overview', 'staffpanel.php?tool=snatched_torrents', 'View all snatched torrents', 4, 1, 1277910147);
INSERT INTO `staffpanel` VALUES (40, 'Pm Overview', 'staffpanel.php?tool=pmview', 'Pm overview - For monitoring only !!!', 6, 1, 1277910147);
INSERT INTO `staffpanel` VALUES (41, 'Data Reset', 'staffpanel.php?tool=datareset', 'Reset download stats for nuked torrents', 5, 1, 1277910147);
INSERT INTO `staffpanel` VALUES (42, 'Dupe Ip Check', 'staffpanel.php?tool=ipcheck', 'Check duplicate ips', 4, 1, 1277910147);
INSERT INTO `staffpanel` VALUES (43, 'Lottery', 'lottery.php', 'Configure lottery', 4, 1, 1282824272);
INSERT INTO `staffpanel` VALUES (44, 'Group Pm', 'staffpanel.php?tool=grouppm', 'Send grouped pms', 4, 1, 1282838663);
INSERT INTO `staffpanel` VALUES (45, 'Client Ids', 'staffpanel.php?tool=allagents', 'View all client id', 6, 1, 1283592994);
INSERT INTO `staffpanel` VALUES (46, 'Forum Config', 'staffpanel.php?tool=forum_config', 'Configure forums', 5, 1, 1284303053);
INSERT INTO `staffpanel` VALUES (47, 'Sysop log', 'staffpanel.php?tool=sysoplog', 'View staff actions', 6, 1, 1284686084);
INSERT INTO `staffpanel` VALUES (48, 'Server Load', 'staffpanel.php?tool=load', 'View current server load', 6, 1, 1284900585);
INSERT INTO `staffpanel` VALUES (49, 'Promotions', 'promo.php', 'Add new signup promotions', 4, 1, 1286231384);
INSERT INTO `staffpanel` VALUES (50, 'Account Manage', 'staffpanel.php?tool=acpmanage', 'Account manager - Conifrm pending users', 4, 1, 1289950651);
INSERT INTO `staffpanel` VALUES (51, 'Block Manager', 'staffpanel.php?tool=block.settings', 'Manage Global site block settings', 6, 1, 1292185077);
INSERT INTO `staffpanel` VALUES (52, 'Cheat Detection', 'staffpanel.php?tool=cheat', 'Dislay Zero Report Users And Cheats', 4, 1, 1294108427);
INSERT INTO `staffpanel` VALUES (53, 'Warnings', 'staffpanel.php?tool=warn', 'Warning Management', 4, 1, 1294788655);
INSERT INTO `staffpanel` VALUES (54, 'Leech Warnings', 'staffpanel.php?tool=leechwarn', 'Leech Warning Management', 4, 1, 1294794876);
INSERT INTO `staffpanel` VALUES (55, 'Hnr Warnings', 'staffpanel.php?tool=hnrwarn', 'Hit And Run Warning Management', 4, 1, 1294794904);
INSERT INTO `staffpanel` VALUES (56, 'Site Peers', 'staffpanel.php?tool=view_peers', 'Site Peers Overview', 4, 1, 1296099600);
INSERT INTO `staffpanel` VALUES (57, 'Mass Seed Bonus', 'staffpanel.php?tool=massbonus', 'Manage seed bonus for all users', 5, 1, 1297378719);
INSERT INTO `staffpanel` VALUES (58, 'Top Uploaders', 'staffpanel.php?tool=uploader_info', 'View site top uploaders', 4, 1, 1297907345);

-- --------------------------------------------------------

-- 
-- Table structure for table `stats`
-- 

CREATE TABLE `stats` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `regusers` int(10) unsigned NOT NULL default '0',
  `unconusers` int(10) unsigned NOT NULL default '0',
  `torrents` int(10) unsigned NOT NULL default '0',
  `seeders` int(10) unsigned NOT NULL default '0',
  `leechers` int(10) unsigned NOT NULL default '0',
  `torrentstoday` int(10) unsigned NOT NULL default '0',
  `donors` int(10) unsigned NOT NULL default '0',
  `unconnectables` int(10) unsigned NOT NULL default '0',
  `forumtopics` int(10) unsigned NOT NULL default '0',
  `forumposts` int(10) unsigned NOT NULL default '0',
  `numactive` int(10) unsigned NOT NULL default '0',
  `torrentsmonth` int(10) unsigned NOT NULL default '0',
  `gender_na` int(10) unsigned NOT NULL default '1',
  `gender_male` int(10) unsigned NOT NULL default '1',
  `gender_female` int(10) unsigned NOT NULL default '1',
  `powerusers` int(10) unsigned NOT NULL default '1',
  `disabled` int(10) unsigned NOT NULL default '1',
  `uploaders` int(10) unsigned NOT NULL default '1',
  `moderators` int(10) unsigned NOT NULL default '1',
  `administrators` int(10) unsigned NOT NULL default '1',
  `sysops` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `stats`
-- 

INSERT INTO `stats` VALUES (1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `stylesheets`
-- 

CREATE TABLE `stylesheets` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uri` varchar(255) collate utf8_unicode_ci NOT NULL,
  `name` varchar(64) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `stylesheets`
-- 

INSERT INTO `stylesheets` VALUES (1, '1.css', 'Default v.2 Skin');

-- --------------------------------------------------------

-- 
-- Table structure for table `subscriptions`
-- 

CREATE TABLE `subscriptions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `topic_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `subscriptions`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `subtitles`
-- 

CREATE TABLE `subtitles` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(200) collate utf8_unicode_ci NOT NULL,
  `filename` varchar(36) collate utf8_unicode_ci NOT NULL,
  `imdb` varchar(50) collate utf8_unicode_ci NOT NULL,
  `lang` varchar(3) collate utf8_unicode_ci NOT NULL,
  `comment` text collate utf8_unicode_ci NOT NULL,
  `fps` varchar(6) collate utf8_unicode_ci NOT NULL,
  `poster` varchar(120) collate utf8_unicode_ci NOT NULL,
  `cds` int(3) NOT NULL default '0',
  `hits` int(10) NOT NULL default '0',
  `added` int(11) NOT NULL default '0',
  `owner` int(10) NOT NULL default '0',
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `subtitles`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `thanks`
-- 

CREATE TABLE `thanks` (
  `id` int(11) NOT NULL auto_increment,
  `torrentid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `thanks`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `tickets`
-- 

CREATE TABLE `tickets` (
  `id` int(4) NOT NULL auto_increment,
  `user` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `tickets`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `topics`
-- 

CREATE TABLE `topics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `topic_name` varchar(120) default NULL,
  `locked` enum('yes','no') NOT NULL default 'no',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `last_post` int(10) unsigned NOT NULL default '0',
  `sticky` enum('yes','no') NOT NULL default 'no',
  `views` int(10) unsigned NOT NULL default '0',
  `poll_id` int(10) unsigned NOT NULL default '0',
  `num_ratings` int(10) unsigned NOT NULL default '0',
  `rating_sum` int(10) unsigned NOT NULL default '0',
  `topic_desc` varchar(120) NOT NULL default '',
  `post_count` int(10) unsigned NOT NULL default '0',
  `first_post` int(10) unsigned NOT NULL default '0',
  `status` enum('deleted','recycled','ok') NOT NULL default 'ok',
  `main_forum_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`user_id`),
  KEY `subject` (`topic_name`),
  KEY `lastpost` (`last_post`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `topics`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `torrents`
-- 

CREATE TABLE `torrents` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `info_hash` varchar(40) collate utf8_unicode_ci NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `filename` varchar(255) collate utf8_unicode_ci NOT NULL,
  `save_as` varchar(255) collate utf8_unicode_ci NOT NULL,
  `search_text` text collate utf8_unicode_ci NOT NULL,
  `descr` text collate utf8_unicode_ci NOT NULL,
  `ori_descr` text collate utf8_unicode_ci NOT NULL,
  `category` int(10) unsigned NOT NULL default '0',
  `size` bigint(20) unsigned NOT NULL default '0',
  `added` int(11) NOT NULL,
  `type` enum('single','multi') collate utf8_unicode_ci NOT NULL default 'single',
  `numfiles` int(10) unsigned NOT NULL default '0',
  `comments` int(10) unsigned NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `times_completed` int(10) unsigned NOT NULL default '0',
  `leechers` int(10) unsigned NOT NULL default '0',
  `seeders` int(10) unsigned NOT NULL default '0',
  `last_action` int(11) NOT NULL,
  `visible` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `banned` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `owner` int(10) unsigned NOT NULL default '0',
  `numratings` int(10) unsigned NOT NULL default '0',
  `ratingsum` int(10) unsigned NOT NULL default '0',
  `nfo` text collate utf8_unicode_ci NOT NULL,
  `client_created_by` char(50) collate utf8_unicode_ci NOT NULL default 'unknown',
  `free` int(11) unsigned NOT NULL default '0',
  `sticky` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `anonymous` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `url` varchar(80) collate utf8_unicode_ci default NULL,
  `checked_by` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `points` int(10) NOT NULL default '0',
  `allow_comments` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `poster` varchar(255) character set utf8 collate utf8_bin NOT NULL default 'pic/noposter.png',
  `nuked` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `nukereason` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `last_reseed` int(11) NOT NULL default '0',
  `release_group` enum('scene','p2p','none') collate utf8_unicode_ci NOT NULL default 'none',
  `subs` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `vip` enum('1','0') collate utf8_unicode_ci NOT NULL default '0',
  `newgenre` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `pretime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `info_hash` (`info_hash`),
  KEY `owner` (`owner`),
  KEY `visible` (`visible`),
  KEY `category_visible` (`category`,`visible`),
  FULLTEXT KEY `ft_search` (`search_text`,`ori_descr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `torrents`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `uploadapp`
-- 

CREATE TABLE `uploadapp` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) NOT NULL default '0',
  `applied` int(11) NOT NULL default '0',
  `speed` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `offer` longtext collate utf8_unicode_ci NOT NULL,
  `reason` longtext collate utf8_unicode_ci NOT NULL,
  `sites` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `sitenames` varchar(150) collate utf8_unicode_ci NOT NULL default '',
  `scene` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `creating` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `seeding` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `connectable` enum('yes','no','pending') collate utf8_unicode_ci NOT NULL default 'pending',
  `status` enum('accepted','rejected','pending') collate utf8_unicode_ci NOT NULL default 'pending',
  `moderator` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `comment` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `users` (`userid`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `uploadapp`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(40) collate utf8_unicode_ci NOT NULL,
  `passhash` varchar(32) collate utf8_unicode_ci NOT NULL,
  `secret` varchar(20) collate utf8_unicode_ci NOT NULL,
  `passkey` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `email` varchar(80) collate utf8_unicode_ci NOT NULL,
  `status` enum('pending','confirmed') collate utf8_unicode_ci NOT NULL default 'pending',
  `added` int(11) NOT NULL,
  `last_login` int(11) NOT NULL,
  `last_access` int(11) NOT NULL,
  `curr_ann_last_check` int(10) unsigned NOT NULL default '0',
  `curr_ann_id` int(10) unsigned NOT NULL default '0',
  `editsecret` varchar(32) collate utf8_unicode_ci NOT NULL,
  `privacy` enum('strong','normal','low') collate utf8_unicode_ci NOT NULL default 'normal',
  `stylesheet` int(10) default '1',
  `info` text collate utf8_unicode_ci,
  `acceptpms` enum('yes','friends','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `ip` varchar(15) collate utf8_unicode_ci NOT NULL,
  `class` tinyint(3) unsigned NOT NULL default '0',
  `override_class` tinyint(3) unsigned NOT NULL default '255',
  `language` varchar(32) collate utf8_unicode_ci NOT NULL default 'en',
  `avatar` varchar(100) collate utf8_unicode_ci NOT NULL,
  `av_w` smallint(3) unsigned NOT NULL default '0',
  `av_h` smallint(3) unsigned NOT NULL default '0',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `title` varchar(30) collate utf8_unicode_ci NOT NULL,
  `country` int(10) unsigned NOT NULL default '0',
  `notifs` varchar(300) collate utf8_unicode_ci NOT NULL,
  `modcomment` text collate utf8_unicode_ci NOT NULL,
  `enabled` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `donor` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `warned` int(11) NOT NULL default '0',
  `torrentsperpage` int(3) unsigned NOT NULL default '0',
  `topicsperpage` int(3) unsigned NOT NULL default '0',
  `postsperpage` int(3) unsigned NOT NULL default '0',
  `deletepms` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `savepms` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `reputation` int(10) NOT NULL default '10',
  `time_offset` varchar(5) collate utf8_unicode_ci NOT NULL default '0',
  `dst_in_use` tinyint(1) NOT NULL default '0',
  `auto_correct_dst` tinyint(1) NOT NULL default '1',
  `show_shout` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'yes',
  `shoutboxbg` enum('1','2','3') character set utf8 collate utf8_bin NOT NULL default '1',
  `chatpost` int(11) NOT NULL default '1',
  `smile_until` int(10) NOT NULL default '0',
  `seedbonus` decimal(10,1) NOT NULL default '200.0',
  `bonuscomment` text collate utf8_unicode_ci,
  `vip_added` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `vip_until` int(10) NOT NULL default '0',
  `freeslots` int(11) unsigned NOT NULL default '5',
  `free_switch` int(11) unsigned NOT NULL default '0',
  `invites` int(10) unsigned NOT NULL default '1',
  `invitedby` int(10) unsigned NOT NULL default '0',
  `invite_rights` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `anonymous` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `uploadpos` int(11) NOT NULL default '1',
  `forumpost` int(11) NOT NULL default '1',
  `downloadpos` int(11) NOT NULL default '1',
  `immunity` int(11) NOT NULL default '0',
  `leechwarn` int(11) NOT NULL default '0',
  `disable_reason` text character set utf8 collate utf8_bin NOT NULL,
  `clear_new_tag_manually` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `last_browse` int(11) NOT NULL default '0',
  `sig_w` smallint(3) unsigned NOT NULL default '0',
  `sig_h` smallint(3) unsigned NOT NULL default '0',
  `signatures` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `signature` varchar(225) collate utf8_unicode_ci NOT NULL default '',
  `forum_access` int(11) NOT NULL default '0',
  `highspeed` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `hnrwarn` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `hit_and_run_total` int(9) default '0',
  `donoruntil` int(11) unsigned NOT NULL default '0',
  `donated` int(3) NOT NULL default '0',
  `total_donated` decimal(8,2) NOT NULL default '0.00',
  `vipclass_before` int(10) NOT NULL default '0',
  `parked` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `passhint` int(10) unsigned NOT NULL,
  `hintanswer` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `avatarpos` int(11) NOT NULL default '1',
  `support` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `supportfor` text collate utf8_unicode_ci NOT NULL,
  `sendpmpos` int(11) NOT NULL default '1',
  `invitedate` int(11) NOT NULL default '0',
  `invitees` varchar(100) character set utf8 collate utf8_bin NOT NULL default '',
  `invite_on` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'yes',
  `subscription_pm` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'no',
  `gender` enum('Male','Female','N/A') collate utf8_unicode_ci NOT NULL default 'N/A',
  `anonymous_until` int(10) NOT NULL default '0',
  `viewscloud` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'yes',
  `tenpercent` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `avatars` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `offavatar` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `pirate` int(11) unsigned NOT NULL default '0',
  `king` int(11) unsigned NOT NULL default '0',
  `hidecur` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `ssluse` int(1) NOT NULL default '1',
  `signature_post` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `forum_post` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `avatar_rights` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `offensive_avatar` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `view_offensive_avatar` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `paranoia` tinyint(3) unsigned NOT NULL default '0',
  `google_talk` varchar(255) collate utf8_unicode_ci NOT NULL,
  `msn` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `aim` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `yahoo` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `website` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `icq` varchar(255) collate utf8_unicode_ci NOT NULL,
  `show_email` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `parked_until` int(10) NOT NULL default '0',
  `gotgift` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `hash1` varchar(96) collate utf8_unicode_ci NOT NULL default '',
  `suspended` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `bjwins` int(10) NOT NULL default '0',
  `bjlosses` int(10) NOT NULL default '0',
  `warn_reason` text character set utf8 collate utf8_bin NOT NULL,
  `onirc` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `irctotal` bigint(20) unsigned NOT NULL default '0',
  `birthday` date default '0000-00-00',
  `got_blocks` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `last_access_numb` bigint(30) NOT NULL default '0',
  `onlinetime` bigint(30) NOT NULL default '0',
  `pm_on_delete` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'no',
  `commentpm` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `ip` (`ip`),
  KEY `uploaded` (`uploaded`),
  KEY `downloaded` (`downloaded`),
  KEY `country` (`country`),
  KEY `last_access` (`last_access`),
  KEY `enabled` (`enabled`),
  KEY `warned` (`warned`),
  KEY `pkey` (`passkey`),
  KEY `free_switch` (`free_switch`),
  KEY `iphistory` (`ip`,`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `users`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `user_blocks`
-- 

CREATE TABLE `user_blocks` (
  `userid` int(10) unsigned NOT NULL,
  `index_page` int(10) unsigned NOT NULL default '585727',
  `global_stdhead` int(10) unsigned NOT NULL default '255',
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `user_blocks`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `ustatus`
-- 

CREATE TABLE `ustatus` (
  `id` int(10) NOT NULL auto_increment,
  `userid` int(10) NOT NULL default '0',
  `last_status` varchar(140) NOT NULL,
  `last_update` int(11) NOT NULL default '0',
  `archive` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `ustatus`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `voted_offers`
-- 

CREATE TABLE `voted_offers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `offerid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`id`),
  KEY `userid` (`userid`),
  KEY `offerid_userid` (`offerid`,`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `voted_offers`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `voted_requests`
-- 

CREATE TABLE `voted_requests` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `requestid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`id`),
  KEY `userid` (`userid`),
  KEY `requestid_userid` (`requestid`,`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `voted_requests`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `wiki`
-- 

CREATE TABLE `wiki` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) character set latin1 NOT NULL,
  `body` longtext character set latin1,
  `userid` int(10) unsigned default '0',
  `time` int(11) NOT NULL,
  `lastedit` int(10) unsigned default NULL,
  `lastedituser` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `wiki`
-- 

INSERT INTO `wiki` VALUES (1, 'index', '[align=center][size=6]Welcome to the [b]Wiki[/b][/size][/align]', 0, 1228076412, 1281610709, 1);