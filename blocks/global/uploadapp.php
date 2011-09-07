<?php
//==Memcached Big red uploadapp thingy box:
   if($INSTALLER09['uploadapp_alert'] && $CURUSER['class'] >= UC_MODERATOR) {
   $newapp = $mc1->get_value('new_uploadapp_');
   if ($newapp === false) {
   $res_newapps = sql_query("SELECT count(id) FROM uploadapp WHERE status = 'pending'");
   list($newapp) = mysql_fetch_row($res_newapps);
   $mc1->cache_value('new_uploadapp_', $newapp, $INSTALLER09['expires']['alerts']);
   }
   if ($newapp > 0){
   $htmlout.="
   <li>
   <a class='tooltip' href='uploadapps.php'><b>New Uploader App Waiting</b><span class='custom info'><img src='./templates/1/images/Info.png' alt='Upload App' height='48' width='48' /><em>New Uploader App Waiting</em>
   Hey {$CURUSER['username']}! $newapp uploader application" . ($newapp > 1 ? "s" : "") . " to be dealt with 
   click at the headling above here to view the application</span></a></li>";
   }
   }
   //==End
?>