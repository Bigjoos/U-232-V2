<?php
//==Memcached message query
    if ($INSTALLER09['msg_alert'] && $CURUSER)
    {
      $unread = $mc1->get_value('inbox_new_'.$CURUSER['id']);
      if ($unread === false) {
      $res = sql_query('SELECT count(id) FROM messages WHERE receiver='.$CURUSER['id'].' && unread="yes" AND location = "1"') or sqlerr(__FILE__,__LINE__);
      $arr = mysql_fetch_row($res);
      $unread = (int)$arr[0];
      $mc1->cache_value('inbox_new_'.$CURUSER['id'], $unread, $INSTALLER09['expires']['unread']);
    }
    }
    //==End
//== big red message box
    if ($INSTALLER09['msg_alert'] && isset($unread) && !empty($unread))
    {
      $htmlout .= "
      <li>
      <a class='tooltip' href='messages.php'><b>New Private Message(s)</b><span class='custom info'><img src='./templates/1/images/Info.png' alt='New Pm' height='48' width='48' /><em>New Private Message(s)</em>
      ".sprintf($lang['gl_msg_alert'], $unread) . ($unread > 1 ? "s" : "") . "
      </span></a></li>";
    }
?>