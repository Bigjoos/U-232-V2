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
require_once INCL_DIR.'html_functions.php';
require_once INCL_DIR.'bbcode_functions.php';
require_once CLASS_DIR.'page_verify.php';
require_once(CACHE_DIR.'subs.php');
dbconn(false);

loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('upload') );
    
    $stdfoot = array(/** include js **/'js' => array('shout','FormManager'));
    
    if (function_exists('parked'))
    parked();
    
    $newpage = new page_verify(); 
    $newpage->create('taud');
    $HTMLOUT = '';
    
    if ($CURUSER['class'] < UC_UPLOADER OR $CURUSER["uploadpos"] == 0|| $CURUSER["uploadpos"] > 1 || $CURUSER['suspended'] == 'yes')
    stderr($lang['upload_sorry'], $lang['upload_no_auth']);
   
    $HTMLOUT .= "
    <script type='text/javascript'>
    window.onload = function() {
    setupDependencies('upload'); //name of form(s). Seperate each with a comma (ie: 'weboptions', 'myotherform' )
    };
    </script>
    <div align='center'>
    <form name='upload' enctype='multipart/form-data' action='./takeupload.php' method='post'>
    <input type='hidden' name='MAX_FILE_SIZE' value='{$INSTALLER09['max_torrent_size']}' />
    <p>{$lang['upload_announce_url']}<b><input type=\"text\" size=\"38\" readonly=\"readonly\" value=\"{$INSTALLER09['announce_urls'][0]}\" onclick=\"select()\" /></b></p>";

    $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='10'>
    <tr>
    <td class='heading' valign='top' align='right'>{$lang['upload_imdb_url']}</td>
    <td valign='top' align='left'><input type='text' name='url' size='80' /><br />{$lang['upload_imdb_tfi']}{$lang['upload_imdb_rfmo']}</td>
    </tr>
    <tr>
    <td class='heading' valign='top' align='right'>{$lang['upload_poster']}</td>
    <td valign='top' align='left'><input type='text' name='poster' size='80' /><br />{$lang['upload_poster1']}</td>
    </tr>
    <tr>
    <td class='heading' valign='top' align='right'>{$lang['upload_torrent']}</td>
    <td valign='top' align='left'><input type='file' name='file' size='80' /></td>
    </tr>
    <tr>
    <td class='heading' valign='top' align='right'>{$lang['upload_name']}</td>
    <td valign='top' align='left'><input type='text' name='name' size='80' /><br />({$lang['upload_filename']})</td>
    </tr>
    <tr>
    <td class='heading' valign='top' align='right'>{$lang['upload_nfo']}</td>
    <td valign='top' align='left'><input type='file' name='nfo' size='80' /><br />({$lang['upload_nfo_info']})</td>
    </tr>
    <tr>
    <td class='heading' valign='top' align='right'>{$lang['upload_description']}</td>
    <td valign='top' align='left' style='white-space: nowrap;'>". textbbcode("upload","descr")."
    <br />({$lang['upload_html_bbcode']})</td>
    </tr>";

    $s = "<select name='type'>\n<option value='0'>({$lang['upload_choose_one']})</option>\n";

    $cats = genrelist();
    
    foreach ($cats as $row)
    {
    $s .= "<option value='{$row["id"]}'>" . htmlspecialchars($row["name"]) . "</option>\n";
    }
    
    $s .= "</select>\n";
    $HTMLOUT .= "<tr>
    <td class='heading' valign='top' align='right'>{$lang['upload_type']}</td>
    <td valign='top' align='left'>$s</td>
    </tr>";
  
	  $subs_list='';
	  $subs_list .= "<table border=\"1\"><tr>\n";
	  $i = 0;
	  foreach($subs as $s)
	  { 
	  $subs_list .=  ($i && $i % 2 == 0) ? "</tr><tr>" : "";
	  $subs_list .= "<td style='padding-right: 5px'><input name=\"subs[]\" type=\"checkbox\" value=\"".$s["id"]."\" /> ".$s["name"]."</td>\n";
	  ++$i;
	  }
	  $subs_list .= "</tr></table>\n";
    $HTMLOUT .= tr("Subtitile",$subs_list,1);
    $rg = "<select name='release_group'>\n<option value='none'>None</option>\n<option value='p2p'>p2p</option>\n<option value='scene'>Scene</option>\n</select>\n";
    $HTMLOUT .= tr("Release Type", $rg, 1);   
    $HTMLOUT .= tr("{$lang['upload_anonymous']}", "<input type='checkbox' name='uplver' value='yes' />{$lang['upload_anonymous1']}", 1);
    if ($CURUSER['class'] >= UC_STAFF){
    $HTMLOUT .= tr("{$lang['upload_comment']}", "<input type='checkbox' name='allow_commentd' value='yes' />{$lang['upload_discom1']}", 1);
    }
    $HTMLOUT .= tr("Strip ASCII", "<input type='checkbox' name='strip' value='strip' checked='checked' /><a href='http://en.wikipedia.org/wiki/ASCII_art' target='_blank'>What is this ?</a>", 1);
    if ($CURUSER['class'] >= UC_UPLOADER){
    $HTMLOUT .= "<tr>
    <td class='heading' valign='top' align='right'>Free Leech</td>
    <td valign='top' align='left'>
    <select name='free_length'>
    <option value='0'>Not Free</option>
    <option value='42'>Free for 1 day</option>
    <option value='1'>Free for 1 week</option>
    <option value='2'>Free for 2 weeks</option>
    <option value='4'>Free for 4 weeks</option>
    <option value='8'>Free for 8 weeks</option>
    <option value='255'>Unlimited</option>
    </select></td>
    </tr>";
    }
    //== 09 Genre mod no mysql by Traffic
    $HTMLOUT .= "
    <tr>
    <td class='heading' align='right'><b>Genre</b></td>
    <td align='left'> 
    <table>
    <tr>
    
    <td style='border:none'><input type='radio' name='genre' value='movie' />Movie</td>
    <td style='border:none'><input type='radio' name='genre' value='music' />Music</td>
    <td style='border:none'><input type='radio' name='genre' value='game' />Game</td>
    <td style='border:none'><input type='radio' name='genre' value='apps' />Apps</td>
    <td style='border:none'><input type='radio' name='genre' value='' checked='checked' />None</td>
    </tr>
    </table> 
    <table> 
    <tr>
    <td colspan='4' style='border:none'>
    <label style='margin-bottom: 1em; padding-bottom: 1em; border-bottom: 3px silver groove;'>
    <input type='hidden' class='Depends on genre being movie or genre being music' /></label>";
    $movie = array ('Action', 'Comedy', 'Thriller', 'Adventure', 'Family', 'Adult', 'Sci-fi');
    for ($x = 0; $x < count ($movie); $x++) {
    $HTMLOUT .= "<label><input type=\"checkbox\" value=\"$movie[$x]\"  name=\"movie[]\" class=\"DEPENDS ON genre BEING movie\" />$movie[$x]</label>";
    }
    $music = array ('Hip Hop', 'Rock', 'Pop', 'House', 'Techno', 'Commercial');
    for ($x = 0; $x < count ($music); $x++) {
    $HTMLOUT .= "<label><input type=\"checkbox\" value=\"$music[$x]\" name=\"music[]\" class=\"DEPENDS ON genre BEING music\" />$music[$x]</label>";
    }
    $game = array ('Fps', 'Strategy', 'Adventure', '3rd Person', 'Acton');
    for ($x = 0; $x < count ($game); $x++) {
    $HTMLOUT .= "<label><input type=\"checkbox\" value=\"$game[$x]\" name=\"game[]\" class=\"DEPENDS ON genre BEING game\" />$game[$x]</label>";
    }
    $apps = array ('Burning', 'Encoding', 'Anti-Virus', 'Office', 'Os', 'Misc', 'Image');
    for ($x = 0; $x < count ($apps); $x++) {
    $HTMLOUT .= "<label><input type=\"checkbox\" value=\"$apps[$x]\" name=\"apps[]\" class=\"DEPENDS ON genre BEING apps\" />$apps[$x]</label>";
    }
    $HTMLOUT .= "</td></tr></table></td></tr>";
    //== End
     
    if ($CURUSER['class'] >= UC_UPLOADER){
    $HTMLOUT .= tr("Vip Torrent", "<input type='checkbox' name='vip' value='1' />If this one is checked, only Vip's can download this torrent", 1);
    }
    $HTMLOUT .= "<tr>
    <td align='center' colspan='2'><input type='submit' class='btn' value='{$lang['upload_submit']}' /></td>
    </tr>
    </table>
    </form>
    </div>";
  
////////////////////////// HTML OUTPUT //////////////////////////

    echo stdhead($lang['upload_stdhead']) . $HTMLOUT . stdfoot($stdfoot);

?>