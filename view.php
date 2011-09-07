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
dbconn();
loggedinorreturn();

$lang = array_merge( load_language('global') );

$HTMLOUT='';

//== Crude block access lol

if (isset($_GET["file"]) && $_GET["file"] == 'include/config.php'){
stderr("Error",  "Files with configuration data are not accessible");
}

if (isset($_GET["file"]) && $_GET["file"] == '/include/config.php'){
stderr("Error",  "Files with configuration data are not accessible");
}

if (isset($_GET["file"]) && $_GET["file"] == '/include/mysql.class.php'){
stderr("Error",  "Files with configuration data are not accessible");
}

if (isset($_GET["file"]) && $_GET["file"] == 'announce.php'){
stderr("Error",  "Files with configuration data are not accessible");
}

if (isset($_GET["file"]) && $_GET["file"] == '/announce.php'){
stderr("Error",  "Files with configuration data are not accessible");
}

//== ID list - Add individual user IDs to this list for access to this script

require_once(CLASS_DIR.'class_check.php');
//class_check(UC_SYSOP, true, true);
class_check(UC_SYSOP);
/*
$allowed_ids = array(1,10,14); //== 1 Is Sysop - add userids you want access
if (!in_array($CURUSER['id'], $allowed_ids))
    stderr('Error', 'Access Denied!');
*/
if ($INSTALLER09['staff_viewcode_on'] == true) {

 $THIS_FILE = "view.php"; 
 
  if (isset($_GET["file"])) {
    $file = $_GET["file"];
  } else {
    $file = $THIS_FILE;
  }

  if (!preg_match('/\.\.\//', $file)) {
    $fullFilename = "C:\AppServ/www/$file"; /** CHANGE THIS LINE TO YOUR PATH **/
  }

  $path_parts = pathinfo("$fullFilename");
  if (isset($fullFilename) &&
      is_file($fullFilename) &&
      is_readable($fullFilename) &&
      $path_parts["extension"] == "php") {

    $HTMLOUT .= '<div class="source">';
    show_source($fullFilename);
    $HTMLOUT .=  '</div>';
  } elseif (!isset($fullFilename)) {
    $HTMLOUT .= '<p>Hey, wise guy!  What do you think youre doing?  Tampering with the filename is not allowed.  Go hack somebody elses web server, and leave me alone.  Punk.</p>';
  } elseif ($path_parts["extension"] != "php") {
    $HTMLOUT .= '<p>Whoops!  You can only view the source of files with a .php extension.  It wouldnt make sense to view the source of, say, an image, now would it?</p>';
  } 
    } else {
    $HTMLOUT='';
    $HTMLOUT .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
    <html xmlns='http://www.w3.org/1999/xhtml'>
    <head>
    <title>Sorry</title>
    </head>
    <body><div style='font-size:33px;color:white;background-color:red;text-align:center;'>View source code option disabled currently !!</div></body></html>";
    print $HTMLOUT;
    exit();
    }
print $HTMLOUT;
?>