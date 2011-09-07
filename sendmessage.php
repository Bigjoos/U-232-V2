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
require_once(CLASS_DIR.'page_verify.php');
dbconn(false);
loggedinorreturn();
$lang = array_merge( load_language('global'), load_language('sendmessage') );
$newpage = new page_verify(); 
$newpage->create('tkmg');
$stdfoot = array(/** include js **/'js' => array('shout'));

flood_limit('messages');

parked();

if ($CURUSER["sendpmpos"] == 0|| $CURUSER["sendpmpos"] > 1 )
   stderr($lang['sendmessage_sorry'], $lang['sendmessage_no_auth']);

// Standard Administrative PM Replies
$pm_std_reply[1] = sprintf( $lang['sendmessage_std_reply1'], $INSTALLER09['baseurl'] );
$pm_std_reply[2] = "{$lang['sendmessage_std_reply2']}";

// Standard Administrative PMs
$pm_template[1] = array( $lang['sendmessage_template1_sub'], sprintf($lang['sendmessage_template1_body'], $INSTALLER09['site_name']) );
$pm_template[2] = array( $lang['sendmessage_template2_sub'], sprintf($lang['sendmessage_template2_body'], $INSTALLER09['baseurl']) );

// Standard Administrative MMs
$mm_template[1] = array( $lang['sendmessage_mm_template1_sub'], sprintf($lang['sendmessage_mm_template1_body'], $INSTALLER09['site_name']) );
$mm_template[2] = array( $lang['sendmessage_mm_template2_sub'], $lang['sendmessage_mm_template2_body'] );
$mm_template[3] = array( $lang['sendmessage_mm_template3_sub'], $lang['sendmessage_mm_template3_body'] );
    
    $HTMLOUT = '';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {						          ////////  MM  //
      if ($CURUSER['class'] < UC_MODERATOR)
        stderr("{$lang['sendmessage_error']}", "{$lang['sendmessage_denied']}");

      $n_pms = htmlentities($_POST['n_pms']);
      $pmees = htmlentities($_POST['pmees']);
      $this_subject = '';
      $this_body = '';
      $auto = isset($_POST['auto']) ? $_POST['auto'] : FALSE;

      if ($auto)
      {
        $this_subject = htmlentities($mm_template[$auto][0], ENT_QUOTES);
        $this_body = htmlentities($mm_template[$auto][1], ENT_QUOTES);
      }
      
      $mass_msg_pm_to = sprintf( $lang['sendmessage_mass_msg_to'], $n_pms, ($n_pms>1?"s":"") );
      
      $HTMLOUT .= "<h1>{$mass_msg_pm_to}</h1>
      <form name='compose' method='post' action='takemessage.php'>";
      
      if (isset($_SERVER['HTTP_REFERER']) && htmlspecialchars($_SERVER['HTTP_REFERER']) == $INSTALLER09['baseurl']."/shoutbox.php")
      { 
      $HTMLOUT .= "<input type='hidden' name='returnto' value='./index.php' />";
      } else {
      $HTMLOUT .= "<input type='hidden' name='returnto' value='".(isset($_GET["returnto"]) ? $_GET["returnto"]:$_SERVER["HTTP_REFERER"])."' />";
      }
      
      $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='5'>
      <tr>
        <td align='right'><b>{$lang['sendmessage_subject']}</b></td>
        <td><input style='width: 650px;' name='subject' type='text' value='$this_subject' size='50' /></td>
      </tr>
      <tr>
        <td align='right'><b>{$lang['sendmessage_subject']}</b></td>
        <td>". textbbcode("compose","body","$this_body")."</td>
      </tr>
      <tr>
        <td align='right'><b>{$lang['sendmessage_comment']}</b></td>
        <td><input style='width: 650px;' name='comment' type='text' size='70' /></td>
      </tr>
      <tr>
        <td colspan='2'><div align='center'><b>{$lang['sendmessage_from']}</b>
      {$CURUSER['username']}
        <input name='sender' type='radio' value='self' checked='checked' />&nbsp; System
        <input name='sender' type='radio' value='system' />
        </div>
        <div align='center'><b>{$lang['sendmessage_snapshot']}</b>&nbsp;
        <input name='snap' type='checkbox' value='1' />
        </div></td>
      </tr>
      <tr>
        <td colspan='2' align='center'><input type='submit' value='{$lang['sendmessage_send_it']}' class='btn' />
        </td>
      </tr>
      </table>
      <input type='hidden' name='pmees' value='{$pmees}' />
      <input type='hidden' name='n_pms' value='{$n_pms}' />
      </form><br /><br />
      
      
      <form method='post' action='sendmessage.php'>
      <table border='1' cellspacing='0' cellpadding='5'>
      <tr><td>
      <b>{$lang['sendmessage_templates']}</b>
      <select name='auto'>";
      

      for ($i = 1; $i <= count($mm_template); $i++)	
     {
        $HTMLOUT .= "<option value='$i' ".($auto == $i?"selected='selected'":"").
            ">{$mm_template[$i][0]}</option>\n";
     }

      $HTMLOUT .= "</select>
      <input type='submit' value='{$lang['sendmessage_use']}' class='btn' />
      <input type='hidden' name='pmees' value='{$pmees}' />
      <input type='hidden' name='n_pms' value='{$n_pms}' />
      </td></tr></table></form>";

    } 
    else 
    {                                                        
    ////////  PM  //
    $receiver = 0+$_GET["receiver"];
    if (!is_valid_id($receiver))
     die;

      $replyto = isset($_GET["replyto"]) ? (int)$_GET["replyto"] : 0;
      if ($replyto && !is_valid_id($replyto))
        stderr("{$lang['sendmessage_system_error']}", "{$lang['sendmessage_it_broke']}");

      $auto = isset($_GET["auto"]) ? $_GET["auto"] : false;
      $std = isset($_GET["std"]) ? $_GET["std"] : false;

      if (($auto || $std ) && $CURUSER['class'] < UC_MODERATOR)
        stderr("{$lang['sendmessage_user_error']}", "{$lang['sendmessage_denied']}");

      $res = mysql_query("SELECT * FROM users WHERE id=$receiver") or die(mysql_error());
      
      $user = mysql_fetch_assoc($res);
      
      if (!$user)
        stderr("{$lang['sendmessage_user_error']}", "{$lang['sendmessage_no_id']}");

      if ($auto)
        $body = $pm_std_reply[$auto];
      if ($std)
        $body = $pm_template[$std][1];
      
      
      if ($replyto)
      {
        $res = sql_query("SELECT * FROM messages WHERE id=$replyto") or sqlerr();
        $msga = mysql_fetch_assoc($res);
        if ($msga['receiver'] != $CURUSER['id'])
          die;
        $res = sql_query("SELECT username FROM users WHERE id={$msga['sender']}") or sqlerr();
        $usra = mysql_fetch_assoc($res);
        $body = sprintf( $lang['sendmessage_user_wrote'], $usra['username'], $msga['msg'] );
        $subject = "{$lang['sendmessage_re']}" . htmlspecialchars($msga['subject']);
      }


      $HTMLOUT .= "<table class='main' width='750' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
      <div align='center'>
      <h1>Message to <a href='userdetails.php?id={$receiver}'>{$user["username"]}</a></h1>
      <form name='compose' method='post' action='takemessage.php'>";
      
      if (isset($_SERVER['HTTP_REFERER']) && htmlspecialchars($_SERVER['HTTP_REFERER']) == $INSTALLER09['baseurl']."/shoutbox.php")
      { 
      $HTMLOUT .= "<input type='hidden' name='returnto' value='./index.php' />";
      } else {
      $HTMLOUT .= "<input type='hidden' name='returnto' value='".(isset($_GET["returnto"]) ? $_GET["returnto"]:$_SERVER["HTTP_REFERER"])."' />";
      }
      
      $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='5'>
      <tr>
        <td colspan='2'><b>{$lang['sendmessage_subject']}</b>
      <input name='subject' type='text' size='76' value='".(isset($subject) ? htmlentities($subject, ENT_QUOTES) : '')."' /></td>
      </tr>
      <tr>
        <td".($replyto ? " colspan='2'":'').">".textbbcode("compose", "body", isset($body) ? htmlspecialchars($body) : '')."</td></tr>
      <tr>";
      
      if ($replyto) 
      { 
        $HTMLOUT .= "<td align='center'><input type='checkbox' name='delete' value='yes' ".($CURUSER['deletepms'] == 'yes' ? "checked='checked'":'')." />{$lang['sendmessage_delete']}
        <input type='hidden' name='origmsg' value='$replyto' /></td>";
      }
      
      $HTMLOUT .= "<td align='center'><input type='checkbox' name='save' value='yes' ".($CURUSER['savepms'] == 'yes' ? "checked='checked'":'')." />{$lang['sendmessage_save_sent']}</td></tr>
      <tr><td".($replyto ? " colspan='2'":'')." align='center'><input type='submit' value='{$lang['sendmessage_send_it']}' class='btn' /></td></tr>
      </table>
      <input type='hidden' name='receiver' value='$receiver' />
      </form>
      <!--";

      if ($CURUSER['class'] >= UC_MODERATOR)
      {

        $HTMLOUT .= "<br /><br />
        <form method='get' action='sendmessage.php'>
        <table border='1' cellspacing='0' cellpadding='5'>
        <tr><td>
        <b>{$lang['sendmessage_pm_templates']}</b>
        <select name='std'>";
        
        for ($i = 1; $i <= count($pm_template); $i++)
        {
          $HTMLOUT .= "<option value='$i' ".($std == $i?"selected='selected'":"").
            ">".$pm_template[$i][0]."</option>\n";
        }
        
        $HTMLOUT .= "</select>";
        
        if (isset($_SERVER['HTTP_REFERER']) && htmlspecialchars($_SERVER['HTTP_REFERER']) == $INSTALLER09['baseurl']."/shoutbox.php")
        { 
        $HTMLOUT .= "<input type='hidden' name='returnto' value='./index.php' />";
        } else {
        $HTMLOUT .= "<input type='hidden' name='returnto' value='".(isset($_GET["returnto"]) ? $_GET["returnto"]:$_SERVER["HTTP_REFERER"])."' />";
        }
        
        $HTMLOUT .= "<input type='hidden' name='receiver' value='$receiver' />
        <input type='hidden' name='replyto' value='$replyto' />
        <input type='submit' value='{$lang['sendmessage_use']}' class='btn' />
        </td></tr></table></form>";

      }

      $HTMLOUT .= "-->
      </div></td></tr></table>";

    }


    echo stdhead("{$lang['sendmessage_send_msg']}", false) . $HTMLOUT . stdfoot($stdfoot);
?>