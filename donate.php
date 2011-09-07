<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
require_once "include/bittorrent.php";
require_once "include/html_functions.php";
require_once "include/user_functions.php";

dbconn();
    
    $lang = array_merge( load_language('global'), load_language('donate') );
    
    $HTMLOUT = '';

    $HTMLOUT .= "<b>{$lang['donate_click']}</b>


    <form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
<input type='hidden' name='cmd' value='_s-xclick' />
<input type='hidden' name='encrypted' value='-----BEGIN PKCS7-----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-----END PKCS7-----
' /><br />
<input type='image' src='{$INSTALLER09['pic_base_url']}makedonation.gif' name='submit' alt='PayPal - The safer, easier way to pay online.' />
<img alt='' border='0' src='https://www.paypal.com/en_GB/i/scr/pixel.gif' width='1' height='1' />
</form>

    <br />

    <br />";
    $HTMLOUT .= begin_main_frame(); 
    $HTMLOUT .= begin_frame(); 
    
    $HTMLOUT .= "<table border='0' cellspacing='0' cellpadding='0'>
    <tr valign='top'>
      <td class='embedded'>
        <img src='pic/flag/uk.gif' style='margin-right: 10px' alt='' />
      </td>
      <td class='embedded'>
        <p>{$lang['donate_donating']}</p>
<p>{$lang['donate_thanks']}</p>
      </td>
    </tr>
    </table>";
    
    $HTMLOUT .= end_frame(); 
    $HTMLOUT .= begin_frame("{$lang['donate_other']}");
    
    $HTMLOUT .= "{$lang['donate_no_other']}";
    $HTMLOUT .= end_frame(); 
    $HTMLOUT .= end_main_frame();


    $HTMLOUT .= "<b>{$lang['donate_after']}<a href='sendmessage.php?receiver=1'>{$lang['donate_send']}</a>{$lang['donate_the']}<font color='red'>{$lang['donate_transaction']}</font>{$lang['donate_credit']}</b>";

    print stdhead() . $HTMLOUT . stdfoot();
?>