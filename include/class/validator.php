<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
function validator($context){
       global $CURUSER;
       $timestamp=time();
       $hash=hash_hmac("sha1", $CURUSER['secret'], $context.$timestamp);
       return substr($hash, 0, 20).dechex($timestamp);
}
function validatorForm($context){
       return "<input type=\"hidden\" name=\"validator\" value=\"".validator($context)."\"/>";
}

function validate($validator, $context, $seconds=0){
       global $CURUSER;
       $timestamp=hexdec(substr($validator, 20));
       if($seconds && time() > $timestamp + $seconds)
               return False;
       $hash=substr(hash_hmac("sha1", $CURUSER['secret'], $context.$timestamp), 0, 20);
       if (substr($validator, 0, 20) != $hash)
               return False;
       return True;
}
?>