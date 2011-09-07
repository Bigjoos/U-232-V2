<?php if (!defined('IN_OFFERS')) exit('No direct script access allowed');

$offer = (isset($_POST['offertitle']) ? $_POST['offertitle'] : '');

if ($offer == '')
    stderr('Error', 'You must enter a title!');	
	   
$cat = (isset($_POST['category']) ? (int)$_POST['category'] : 0);
if (!is_valid_id($cat))
    stderr('Error', 'You must select a category to put the request in!');	   

$descrmain =  (isset($_POST['body']) ? $_POST['body'] : '');
if (!$descrmain)
    stderr('Error', 'You must enter a description!');	

$pic = '';
if (!empty($_POST['picture'])) {
    if (!preg_match('/^https?:\/\/([a-zA-Z0-9\-\_]+\.)+([a-zA-Z]{1,5}[^\.])(\/[^<>]+)+\.(jpg|jpeg|gif|png|tif|tiff|bmp)$/i', $_POST['picture']))
        stderr('Error', "Picture MUST be in jpg, gif or png format. Make sure you include http:// in the URL.");

    $picture  = $_POST['picture'];
//    $picture2 = trim(urldecode($picture));
//    $headers  = get_headers($picture2);
//    if (strpos($headers[0], '200') === false)
//        $picture = $INSTALLER09['baseurl'].'/pic/notfound.png';
    $pic = "[img]".$picture."[/img]\n";
}

$descr    = "$pic";
$descr   .= "$descrmain";
$offer2 = sqlesc($offer);
$descr    = sqlesc($descr);

sql_query("INSERT INTO offers (hits, userid, cat, offer, descr, added) VALUES(1,$CURUSER[id], $cat, $offer2, $descr, ".TIME_NOW.")") or sqlerr(__FILE__,__LINE__);
$id = mysql_insert_id();

sql_query("INSERT INTO voted_offers VALUES(0, $id, $CURUSER[id])") or sqlerr();

if ($INSTALLER09['karma'] && isset($CURUSER['seedbonus']))
    mysql_query("UPDATE users SET seedbonus = seedbonus-".$INSTALLER09['offer_cost_bonus']." WHERE id = $CURUSER[id]") or sqlerr(__FILE__, __LINE__);

write_log("Offer (".$offer.") was added to the Offer section by $CURUSER[username]");

$message = " [b][color=blue]New Offer[/color][/b]  [url={$INSTALLER09['baseurl']}/viewoffers.php?id=$id&offer_details] ".$offer."[/url]  ";
autoshout($message);

/** IRC announce **/
//$message = " [b][color=blue]New request[/color][/b]  [url=viewrequests.php?id=$id&req_details] ".$request."[/url]  ";
//autoshout($message);

header("Refresh: 0; url=viewoffers.php?id=$id&offer_details");

?>