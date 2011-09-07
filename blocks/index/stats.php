<?php
//==Stats Begin
    $stats_cache = $mc1->get_value('site_stats_');
    if ($stats_cache === false) {
    $stats_cache = mysql_fetch_assoc(sql_query("SELECT *, seeders + leechers AS peers, seeders / leechers AS ratio, unconnectables / (seeders + leechers) AS ratiounconn FROM stats WHERE id = '1' LIMIT 1"))/* or sqlerr(__FILE__, __LINE__)*/;
    $stats_cache['seeders'] = (int) $stats_cache['seeders'];
    $stats_cache['leechers'] = (int) $stats_cache['leechers'];
    $stats_cache['regusers'] = (int) $stats_cache['regusers'];
    $stats_cache['unconusers'] = (int) $stats_cache['unconusers'];
    $stats_cache['torrents'] =  (int) $stats_cache['torrents'];
    $stats_cache['torrentstoday'] = (int) $stats_cache['torrentstoday'];
    $stats_cache['ratiounconn'] = (int) $stats_cache['ratiounconn'];
    $stats_cache['unconnectables'] = (int) $stats_cache['unconnectables'];
    $stats_cache['ratio'] = (int)$stats_cache['ratio'];
    $stats_cache['peers'] = (int) $stats_cache['peers'];
    $stats_cache['numactive'] = (int) $stats_cache['numactive'];
    $stats_cache['donors'] = (int) $stats_cache['donors'];
    $stats_cache['forumposts'] = (int) $stats_cache['forumposts'];
    $stats_cache['forumtopics'] = (int) $stats_cache['forumtopics'];
    $stats_cache['torrentsmonth'] = (int) $stats_cache['torrentsmonth'];
    $stats_cache['gender_na'] = (int) $stats_cache['gender_na'];
    $stats_cache['gender_male'] = (int) $stats_cache['gender_male'];
    $stats_cache['gender_female'] = (int) $stats_cache['gender_female'];
    $stats_cache['powerusers'] = (int) $stats_cache['powerusers'];
    $stats_cache['disabled'] = (int) $stats_cache['disabled'];
    $stats_cache['uploaders'] = (int) $stats_cache['uploaders'];
    $stats_cache['moderators'] = (int) $stats_cache['moderators'];
    $stats_cache['administrators'] = (int) $stats_cache['administrators'];
    $stats_cache['sysops'] = (int) $stats_cache['sysops'];
    $mc1->cache_value('site_stats_', $stats_cache, $INSTALLER09['expires']['site_stats']);
    }
    //==End
    //==Installer 09 stats
        /*
        $HTMLOUT .="<div class='headline'>{$lang['index_stats_title']}</div>
        <div class='headbody'>
        <!--<a href=\"javascript: klappe_news('a3')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica3\" alt=\"[Hide/Show]\" /></a><div id=\"ka3\" style=\"display: none;\">-->
         <div class='statsa'>{$lang['index_stats_regged']}</div><div class='statsb'>{$stats_cache['regusers']}/{$INSTALLER09['maxusers']}</div> 
         <div class='statsa'>{$lang['index_stats_online']}</div><div class='statsb'>{$stats_cache['numactive']}</div> 
         <div class='statsa'>{$lang['index_stats_uncon']}</div><div class='statsb'>{$stats_cache['unconusers']}</div>
         <div class='statsa'>{$lang['index_stats_donor']}</div><div class='statsb'>{$stats_cache['donors']}</div> 
         <div class='statsa'>{$lang['index_stats_topics']}</div><div class='statsb'>{$stats_cache['forumtopics']}</div> 
         <div class='statsa'>{$lang['index_stats_torrents']}</div><div class='statsb'>{$stats_cache['torrents']}</div> 
         <div class='statsa'>{$lang['index_stats_posts']}</div><div class='statsb'>{$stats_cache['forumposts']}</div> 
         <div class='statsa'>{$lang['index_stats_newtor']}</div><div class='statsb'>{$stats_cache['torrentstoday']}</div> 
         <div class='statsa'>{$lang['index_stats_peers']}</div><div class='statsb'>{$stats_cache['peers']}</div> 
         <div class='statsa'>{$lang['index_stats_unconpeer']}</div><div class='statsb'>{$stats_cache['unconnectables']}</div> 
         <div class='statsa'>{$lang['index_stats_seeders']}</div><div class='statsb'>{$stats_cache['seeders']}</div> 
         <div class='statsa'>{$lang['index_stats_unconratio']}</div><div class='statsb'>".round($stats_cache['ratiounconn'] * 100)."</div> 
         <div class='statsa'>{$lang['index_stats_leechers']}</div><div class='statsb'>{$stats_cache['leechers']}</div> 
         <div class='statsa'>{$lang['index_stats_slratio']}</div><div class='statsb'>".round($stats_cache['ratio'] * 100)."</div> 
        </div><!--</div>--><br />";
        */
        $HTMLOUT .="<div class='headline'>{$lang['index_stats_title']}</div>
        <div class='headbody'>
        <!--<a href=\"javascript: klappe_news('a3')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica3\" alt=\"[Hide/Show]\" /></a><div id=\"ka3\" style=\"display: none;\">-->    
        <table width='100%' border='1' cellspacing='0' cellpadding='10'>
        <tr>
        <td align='center'>
        <table align='center' class='main' border='1' cellspacing='0' cellpadding='5'>
        <tr>
	      <td class='rowhead'>{$lang['index_stats_regged']}</td><td align='right'>{$stats_cache['regusers']}/{$INSTALLER09['maxusers']}</td>
	      <td class='rowhead'>{$lang['index_stats_online']}</td><td align='right'>{$stats_cache['numactive']}</td>
        </tr>
        <tr>
	      <td class='rowhead'>{$lang['index_stats_uncon']}</td><td align='right'>{$stats_cache['unconusers']}</td>
	      <td class='rowhead'>{$lang['index_stats_donor']}</td><td align='right'>{$stats_cache['donors']}</td>
        </tr>
        <tr>
        <td colspan='4'> </td>
        </tr>
        <tr>
        <td class='rowhead'>{$lang['index_stats_newtor_month']}</td><td align='right'>{$stats_cache['torrentsmonth']}</td>
        <td class='rowhead'>{$lang['index_stats_gender_na']}</td><td align='right'>{$stats_cache['gender_na']}</td>
        </tr>
        <tr>
        <td class='rowhead'>{$lang['index_stats_gender_male']}</td><td align='right'>{$stats_cache['gender_male']}</td>
        <td class='rowhead'>{$lang['index_stats_gender_female']}</td><td align='right'>{$stats_cache['gender_female']}</td>
        </tr>    
        <tr>
        <td colspan='4'> </td>
        </tr>       
        <tr>
        <td class='rowhead'>{$lang['index_stats_powerusers']}</td><td align='right'>{$stats_cache['powerusers']}</td>
        <td class='rowhead'>{$lang['index_stats_banned']}</td><td align='right'>{$stats_cache['disabled']}</td>
        </tr>  
        <tr>
        <td class='rowhead'>{$lang['index_stats_uploaders']}</td><td align='right'>{$stats_cache['uploaders']}</td>
        <td class='rowhead'>{$lang['index_stats_moderators']}</td><td align='right'>{$stats_cache['moderators']}</td>
        </tr>
        <tr>
        <td class='rowhead'>{$lang['index_stats_admin']}</td><td align='right'>{$stats_cache['administrators']}</td>
        <td class='rowhead'>{$lang['index_stats_sysops']}</td><td align='right'>{$stats_cache['sysops']}</td>
        </tr>
        <tr>
	      <td colspan='4'> </td>
        </tr>
        <tr>
	      <td class='rowhead'>{$lang['index_stats_topics']}</td><td align='right'>{$stats_cache['forumtopics']}</td>
	      <td class='rowhead'>{$lang['index_stats_torrents']}</td><td align='right'>{$stats_cache['torrents']}</td>
        </tr>
        <tr>
        <td class='rowhead'>{$lang['index_stats_posts']}</td><td align='right'>{$stats_cache['forumposts']}</td>
	      <td class='rowhead'>{$lang['index_stats_newtor']}</td><td align='right'>{$stats_cache['torrentstoday']}</td>
        </tr>       
        <tr>
        <td colspan='4'> </td>
        </tr>
        <tr>
	      <td class='rowhead'>{$lang['index_stats_peers']}</td><td align='right'>{$stats_cache['peers']}</td>
	      <td class='rowhead'>{$lang['index_stats_unconpeer']}</td><td align='right'>{$stats_cache['unconnectables']}</td>
        </tr>
        <tr>
	      <td class='rowhead'>{$lang['index_stats_seeders']}</td><td align='right'>{$stats_cache['seeders']}</td>
	      <td class='rowhead' align='right'><b>{$lang['index_stats_unconratio']}</b></td><td align='right'><b>".round($stats_cache['ratiounconn'] * 100)."</b></td>
        </tr>
        <tr>
	      <td class='rowhead'>{$lang['index_stats_leechers']}</td><td align='right'>{$stats_cache['leechers']}</td>
	      <td class='rowhead'>{$lang['index_stats_slratio']}</td><td align='right'>".round($stats_cache['ratio'] * 100)."</td>
        </tr></table></td></tr></table></div><!--</div>-->";
        //==End 09 stats
?>