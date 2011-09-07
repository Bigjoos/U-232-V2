<?php
// 09 poster mod
    $query = "SELECT id, seeders, leechers, name, poster FROM torrents WHERE poster <> '' ORDER BY added DESC LIMIT {$INSTALLER09['latest_torrents_limit']}" or sqlerr(__FILE__, __LINE__);
    $result = mysql_query( $query );
    $num = mysql_num_rows( $result );
    // count rows
    $HTMLOUT .="<script type='text/javascript' src='{$INSTALLER09['baseurl']}/scripts/scroll.js'></script>";
    $HTMLOUT .= "<div class='headline'>{$lang['index_latest']}</div>
    <div class='headbody'>
    <div style=\"overflow:hidden\">
    <div id=\"marqueecontainer\" onmouseover=\"copyspeed=pausespeed\" onmouseout=\"copyspeed=marqueespeed\"> 
    <span id=\"vmarquee\" style=\"position: absolute; width: 98%;\"><span style=\"white-space: nowrap;\">";
    $i = $INSTALLER09['latest_torrents_limit'];
    while ( $row = mysql_fetch_assoc( $result ) ) {
        $id = (int) $row['id'];
        $name = htmlspecialchars( $row['name'] );
        $poster = ($row['poster'] == '' ? ''.$INSTALLER09['pic_base_url'].'no_poster.png' : htmlspecialchars( $row['poster'] ));
        $seeders = number_format($row['seeders']);
        $leechers = number_format($row['leechers']);
        $name = str_replace( '_', ' ' , $name );
        $name = str_replace( '.', ' ' , $name );
        $name = substr( $name, 0, 50 );
        if ( $i == 0 )
        $HTMLOUT .= "</span></span><span id=\"vmarquee2\" style=\"position: absolute; width: 98%;\"></span></div></div><div style=\"overflow:hidden\">
        <div id=\"marqueecontainer\" onmouseover=\"copyspeed=pausespeed\" onmouseout=\"copyspeed=marqueespeed\"> <span id=\"vmarquee\" style=\"position: absolute; width: 98%;\"><span style=\"white-space: nowrap;\">";
        $HTMLOUT .= "<a href='{$INSTALLER09['baseurl']}/details.php?id=$id'><img src='" . htmlspecialchars( $poster ) . "' alt='{$name}' title='{$name} - Seeders : {$seeders} - Leechers : {$leechers}' width='100' height='120' border='0' /></a>&nbsp;&nbsp;&nbsp;";
        $i++;
    }
    $HTMLOUT .= "</span></span><span id=\"vmarquee2\" style=\"position: absolute; width: 98%;\"></span></div></div></div><br />\n";
    //== end 09 poster mod
?>