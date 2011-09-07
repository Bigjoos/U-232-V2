<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
$radio = array('host'=>'09source.kicks-ass.net',
			   'port'=>8000,'password'=>'richmond1');

$langs = array('CURRENTLISTENERS'=>'Current listeners: <b>%d</b>',
              'SERVERTITLE'=>'Server: <b>%s</b>',
              'SERVERURL'=>'Server url: <b>%s:'.$radio['port'].'</b>',
              'SONGTITLE'=>'Current song: <b>%s</b>',
              'BITRATE'=>'Bitrate: <b>%s kb</b>',
              'BITRATE'=>'Bitrate: <b>%s kb</b>',
              'PEAKLISTENERS'=>'Peak listeners: <b>%d</b>',
            );

  function radioinfo($radio) {
    global $langs, $INSTALLER09, $mc1, $CURUSER;    
    $xml = $html = $history = '';        
    if($hand = @fsockopen($radio['host'],$radio['port'],$errno,$errstr,30)) {
        fputs($hand, "GET /admin.cgi?pass=".$radio['password']."&mode=viewxml HTTP/1.1\nUser-Agent:Mozilla/5.0 ".
        "(Windows; U; Windows NT 6.1; en-GB; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6\n\n");
        while(!feof($hand))
                $xml .= fgets($hand,1024);
        preg_match_all('/\<(SERVERTITLE|SERVERURL|SONGTITLE|STREAMSTATUS|BITRATE|CURRENTLISTENERS|PEAKLISTENERS)\>(.*?)<\/\\1\>/iU',$xml,$tempdata,PREG_SET_ORDER);
        foreach($tempdata as $t2)
                $data[$t2[1]] = isset($langs[$t2[1]]) ? sprintf($langs[$t2[1]],$t2[2]) : $t2[2];
        unset($tempdata);
        preg_match_all('/\<SONG>(.*?)<\/SONG\>/',$xml,$temph);
        unset($temph[0][0],$temph[1]);
        $history = array();
        foreach($temph[0] as $temph2) {
                preg_match_all('/\<(TITLE|PLAYEDAT)>(.*?)<\/\\1\>/i',$temph2,$temph3,PREG_PATTERN_ORDER);
                $history[] = '<b>&nbsp;'.$temph3[2][1].'</b> <sub>('.get_date(time(), 'DATE' ,$temph3[2][0]).')</sub>';
        }
        
        if($data['STREAMSTATUS'] == 0)
                return 'Sorry '.$CURUSER['username'].'... : Server '.$radio['host'].' is online but there is no stream';
        else {
                unset($data['STREAMSTATUS']);
                $md5_current_song = md5($data['SONGTITLE']);
                $current_song = $mc1->get('current_radio_song');
                if($current_song === false || $current_song != $md5_current_song) {
                   autoshout(str_replace(array('<','>'),array('[',']'),$data['SONGTITLE'].' playing on '.strtolower($data['SERVERTITLE']).' - '.strtolower($data['SERVERURL'])));
                  $mc1->cache_value('current_radio_song',$md5_current_song,0);
                }
                $html = '<fieldset>
                <legend>'.$INSTALLER09['site_name'].' radio</legend><ul>';
                foreach($data as $d)
                        $html .= '<li><b>'.$d.'</b></li>';
                        $html .= '<li>Playlist history: '.(count($history) > 0 ? join(', ',$history) : 'No playlist history'); 
                $html .= '</li></ul></fieldset>';
                return $html;
        }
        } 
    else 
    $html .='<fieldset><legend>'.$INSTALLER09['site_name'].' radio</legend>
    <font size="3" color="red"><img src="'.$INSTALLER09['pic_base_url'].'off1.gif" alt="Off" title="Off" border="0" /><br />
    <b>Sorry '.$CURUSER['username'].' Radio is currently Offline</b></font></fieldset><br />';
    return $html;
    }
?>