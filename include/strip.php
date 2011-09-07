<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/

function trim_ml (&$descr) {
    $lines = array();
    foreach( explode( "\n", $descr ) as  $line ) {
        $lines[] = trim( $line, "\x00..\x1F.,-+=\t ~" );
    }
    $descr = implode( "\n", $lines );
}
function trim_regex( $pattern, $replacement, $subject ) {
    trim_ml( $subject );
    return preg_replace( $pattern, $replacement, $subject );
}
function strip(&$desc){
	$desc=preg_replace('`[\x00-\x08\x0b-\x0c\x0e-\x1f\x7f-\xff]`','',$desc);
	return;
}
 
?>
