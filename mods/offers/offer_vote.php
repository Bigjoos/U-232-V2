<?php if (!defined('IN_OFFERS')) exit('No direct script access allowed');
	
$res = sql_query('SELECT * FROM voted_offers WHERE offerid = '.$id.' and userid = '.$CURUSER['id']) or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_assoc($res);

if ($arr) {
    $HTMLOUT .= "
<h3>You've Already Voted</h3>
<p style='text-decoration:underline;'>1 vote per offer is allowed</p>
<p><a class='altlink' href='viewoffers.php?id=$id&amp;offer_details'><b>offer details</b></a> | 
<a class='altlink' href='viewoffers.php'><b>all offers</b></a></p>
<br /><br />";
}
else {
    sql_query('UPDATE offers SET hits = hits+1 WHERE id='.$id) or sqlerr(__FILE__,__LINE__);
    if (mysql_affected_rows()) {
        sql_query('INSERT INTO voted_offers VALUES(0, '.$id.', '.$CURUSER['id'].')') or sqlerr(__FILE__,__LINE__);
        $HTMLOUT .=  "
<h3>Vote accepted</h3>
<p style='text-decoration:underline;'>Successfully voted for offer $id</p>
<p><a class='altlink' href='viewoffers.php?id=$id&amp;offer_details'><b>offer details</b></a> |
<a class='altlink' href='viewoffers.php'><b>all offers</b></a></p>
<br /><br />";
    } else {
        $HTMLOUT .=  "
<h3>Error</h3>
<p style='text-decoration:underline;'>No such ID $id</p>
<p><a class='altlink' href='viewoffers.php?id=$id&amp;offer_details'><b>offer details</b></a> |
<a class='altlink' href='viewoffers.php'><b>all offers</b></a></p>
<br /><br />"; 
    }
}

/////////////////////// HTML OUTPUT //////////////////////////////
print stdhead('Vote').$HTMLOUT.stdfoot();
?>