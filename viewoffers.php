<?php
/**  
     Offers Mod v4 for tbdev 09 by pdq, some ideas/inspiration by elephant2, langs by ???
     Mod Posted 24 March 2010
     
     Original Mod Posted 21 November 2006 
     the Request and Offer mod complete and working by Sir_SnuggleBunny 
     based on the work of EnzoF1 & Oink [requests mod] and misterb & S4ne [offers mod] 
**/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'pager_functions.php');
require_once(INCL_DIR.'torrenttable_functions.php');
dbconn(false);  
loggedinorreturn();	 
parked();

/** settings **/
$INSTALLER09['offer_comment_bonus'] = 10;      // amount karma received when fill offer, default: 10
$INSTALLER09['offer_cost_bonus']    = 5;       // amount karma to make an offer, default: 5
$INSTALLER09['offer_min_class']     = UC_USER; // minimum class needed to use offers, default: UC_USER
$INSTALLER09['offer_gigs_upped']    = 10;      // Upload amount in GB, default: 10
$INSTALLER09['offer_min_ratio']     = 0.5;     // min ratio needed to use requests, default: 0.5


$lang = array_merge(load_language('global'), load_language('offers'));

define('IN_OFFERS', TRUE);

/** start **/
$HTMLOUT     = $sort['link'] = $filter['sql'] = $filter['link'] = $edit = $delete = $reset = '';
$sort['sql'] = ' ORDER BY added DESC ';
$cats        = genrelist();
$categ       = (isset($_GET['category']) ? (int)$_GET['category'] : 0);
$offeredid = (isset($_GET['offeredid']) ? (int)$_GET['offeredid'] : 0); 
$id          = (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if (isset($_GET['id'])) {
     if ($id < 1)
         stderr('Error', 'Bad ID!');  
}

/** valid offer actions **/
$offer_array = array('add_offer', 
                   'new_offer', 
                   'offer_details', 
                   'edit_offer', 
                   'take_offer_edit',
                   'offer_filled', 
                   'offer_reset', 
                   'offer_vote', 
                   'votes_view', 
                   'del_offer', 
                   'staff_delete');
          
foreach ($offer_array as $key) {
    if (isset($_GET[$key])) {
        require_once 'mods/offers/'.$key.'.php'; // display action
        exit();
    }
}

// else view all offers
if (isset($_GET['sort']) && $_GET['sort'] != '') {
    $sort_options = array('votes'   => ' ORDER BY hits DESC ', 
                          'cat'     => ' ORDER BY cat ', 
                          'offer' => ' ORDER BY offer ASC ', 
                          'added'   => ' ORDER BY added DESC ');
                          
    if (isset($sort_options[$_GET['sort']])) {
        $sort['sql']  = $sort_options[$_GET['sort']];
        $sort['link'] = $_GET['sort'];
    }
}

if (isset($_GET['filter']) && $_GET['filter'] != '') {
    $filter_options = array('true'  => ' AND offers.torrentid  = 0 ', 
                            'false' => ' AND offers.torrentid  != 0 ');
                            
    if (isset($filter_options[$_GET['filter']])) {
        $filter['sql']  = $filter_options[$_GET['filter']];
        $filter['link'] = $_GET['filter'];
    }
}
//=== end :P

$HTMLOUT .= "
<h1>Offers Section</h1>
<p><a class='altlink' href='viewoffers.php?add_offer'>Make an offer</a> | 
<a class='altlink' href='viewoffers.php'>View all offers</a> | 
<a class='altlink' href='viewoffers.php?offeredid=$CURUSER[id]'>View my offers</a></p>
";

$search = (isset($_GET['search']) ? ' AND offers.offer like '.sqlesc('%'.$_GET['search'].'%').' ' : '');

if ($offeredid != 0)
    $category = ($categ != 0 ? 'WHERE offers.cat = '.$categ.' AND offers.userid = '.$offeredid : 'WHERE offers.userid = '.$offeredid);
else 
    $category = ($categ != 0 ? 'WHERE offers.cat = '.$categ :  'WHERE offers.cat != ""');
  
$res = sql_query('SELECT count(offers.id) AS c FROM offers 
                  inner join users on offers.userid = users.id 
                  '.$category.' '.$filter['sql'].$search) or sqlerr(__FILE__, __LINE__);

$row = mysql_fetch_assoc($res);

$count = $row['c'];
if ($count > 0) {
    $perpage = 25; // per page
    
    $pager = pager(25, $count, 'viewoffers.php?category='.$categ.'&amp;sort='.$sort['link'].'&amp;filter='.$filter['link'].'&amp;');
    
    $res = sql_query('SELECT
                     u1.downloaded, 
                     u1.uploaded, 
                     u1.class as u1class, 
                     u2.class as u2class, 
                     u1.donor as u1donor, 
                     u2.donor as u2donor, 
                     u1.enabled as u1enabled, 
                     u2.enabled as u2enabled, 
                     u1.warned as u1warned, 
                     u2.warned as u2warned, 
                     u1.leechwarn as u1leechwarn, 
                     u2.leechwarn as u2leechwarn,
                     u1.chatpost as u1chatpost,
                     u2.chatpost as u2chatpost,
                     u1.pirate as u1pirate,
                     u2.pirate as u2pirate,
                     u1.king as u1king,
                     u2.king as u2king,
                     u1.username as offered, 
                     u2.username as filler, 
                     offers.torrentid,
                     offers.acceptedby, 
                     offers.id, 
                     offers.userid,
                     offers.offer, 
                     offers.added,
                     offers.hits, 
                     offers.cat
                     FROM offers
                     LEFT JOIN users as u1 on (offers.userid = u1.id)  
                     LEFT JOIN users as u2 on (offers.acceptedby = u2.id)
                     LEFT JOIN users on (offers.userid = u2.id) 
                     '.$category.' '.$filter['sql'].$search.$sort['sql'].$pager['limit']) or sqlerr(__FILE__, __LINE__);
    
    //$num = mysql_num_rows($res);
    
    $HTMLOUT .= "
<form method='get' action='viewoffers.php'>
<b>Search offers: </b><input type='text' size='40' name='search' />

<select name='category'><option value='0'>(Show All)</option>";

    $catdropdown = '';
    foreach ($cats as $cat) {
       $catdropdown .= "<option value='".$cat['id']."'";
       $catdropdown .= ">".htmlspecialchars($cat['name'])."</option>\n";
    }
    
    $HTMLOUT .= "$catdropdown</select>";
    
    $HTMLOUT .= "
<input class='btn' type='submit' value='Search' />  
&nbsp;<a class='altlink' href='viewoffers.php?category=".$categ."&amp;sort=".$sort['link']."&amp;filter=true'>Hide accepted</a>
".($CURUSER['class'] >= UC_MODERATOR ?
" | <a class='altlink' href='viewoffers.php?category=".$categ."&amp;sort=".$sort['link']."&amp;filter=false'>Only accepted</a>" : '').'
</form>
';
    
    $HTMLOUT .= ''.$pager['pagertop'].'';
    
    $HTMLOUT .= '

<script type="text/javascript">
<!-- 
var form=\'viewoff\'
function SetChecked(val,chkName) {
dml=document.forms[form];
len = dml.elements.length;
var i=0;
for( i=0 ; i<len ; i++) {
if (dml.elements[i].name==chkName) {
dml.elements[i].checked=val;}}}
// -->
</script>
    ';
    
    $HTMLOUT .= "<form method='post' name='viewoff' action='viewoffers.php?staff_delete' onsubmit='return ValidateForm(this,\"deloff\")'>
<table width='95%' id='reqtable' border='1' cellspacing='0' cellpadding='5'><thead><tr>
<th align='center' class='colhead'><a class='altlink_white' href='viewoffers.php?category=".$categ."&amp;filter=".$filter['link']."&amp;sort=cat'>Type</a></th>
<th align='center' class='colhead'><a class='altlink_white' href='viewoffers.php?category=".$categ."&amp;filter=".$filter['link']."&amp;sort=offer'>Name</a></th>
<th align='center' class='colhead'><a class='altlink_white' href='viewoffers.php?category=".$categ."&amp;filter=".$filter['link']."&amp;sort=added'>Added</a></th>
<th align='center' class='colhead'>Offered By</th><th align='center' class='colhead'>Accepted?</th><th align='center' class='colhead'>Accepted By</th>
<th align='center' class='colhead'><a class='altlink_white' href='viewoffers.php?category=".$categ."&amp;filter=".$filter['link']."&amp;sort=votes'>Votes</a></th>
";
    
    if ($CURUSER['class'] >= UC_MODERATOR)
         $HTMLOUT .= "<th align='center' class='colhead'>Del</th>";
    
    $HTMLOUT .= '
</tr></thead>
';
    
  //for ($i = 0; $i < $num; ++$i) {
        while ($arr = mysql_fetch_assoc($res)) {
        
        $ratio          = member_ratio($arr['uploaded'], $arr['downloaded']);
        $u1['id']       = $arr['userid'];
        $u1['username'] = $arr['offered'];
        $u1['class']    = $arr['u1class'];
        $u1['enabled']  = $arr['u1enabled'];
        $u1['donor']    = $arr['u1donor'];
        $u1['warned']   = $arr['u1warned'];
        $u1['leechwarn']   = $arr['u1leechwarn'];
        $u1['chatpost']   = $arr['u1chatpost'];
        $u1['pirate']   = $arr['u1pirate'];
        $u1['king']   = $arr['u1king'];
        $u2['id']       = $arr['acceptedby'];
        $u2['username'] = $arr['filler'];
        $u2['class']    = $arr['u2class'];
        $u2['enabled']  = $arr['u2enabled'];
        $u2['donor']    = $arr['u2donor'];
        $u2['warned']   = $arr['u2warned'];
        $u1_username    = format_username($u1);
        $u2_username    = format_username($u2);
        
        $acceptedby = ($arr['torrentid'] != 0 ? ($arr['acceptedby'] != 0 ? $u2_username : 'System') : '<small>Not accepted</small>');
        $addedby  = '<strong>'.$u1_username.'</strong><br />Ratio: '.$ratio;
        $filled   = ($arr['torrentid'] != 0 ? '<a href="details.php?id='.$arr['torrentid'].'"><span style="color:green;"><b>Yes</b></span></a>' : '<a href="viewoffers.php?id='.$arr['id'].'&amp;offer_details"><span style="color:red;"><b>No</b></span></a>');
           
        foreach($cats as $key => $value)
            $change[$value['id']] = array('id'    => $value['id'], 
                                          'name'  => $value['name'], 
                                          'image' => $value['image']);
                  
        $catname = htmlspecialchars($change[$arr['cat']]['name']);
        $catpic  = htmlspecialchars($change[$arr['cat']]['image']);     	              	
        $catimage = "<img src='pic/caticons/".$catpic."' title='$catname' alt='$catname' />";
           
        $HTMLOUT .= '
<tr><td align="center">'.$catimage.'</td>
<td align="left"><a href="viewoffers.php?id='.$arr['id'].'&amp;offer_details"><b>'.htmlspecialchars($arr['offer']).'</b></a></td>
<td align="center">'.get_date($arr['added'], '').'</td>
<td align="center">'.$addedby.'</td>
<td align="center">'.$filled.'</td>
<td align="center"><strong>'.$acceptedby.'</strong></td>
<td align="center"><a href="viewoffers.php?id='.$arr['id'].'&amp;votes_view"><b>'.$arr['hits'].'</b></a></td>
';
         
         if ($CURUSER['class'] >= UC_MODERATOR)
             $HTMLOUT .= '<td><input type="checkbox" name="deloff[]" value="'.$arr['id'].'" /></td>';
             
         $HTMLOUT .= '
</tr>
';
    }
    
    if ($CURUSER['class'] >= UC_MODERATOR)
        $HTMLOUT .= "<tr><td colspan='8' align='right'>
<a class='altlink' href='javascript:SetChecked(1,\"deloff[]\")'>select all</a> | 
<a class='altlink' href='javascript:SetChecked(0,\"deloff[]\")'>un-select all</a> 
<input type='submit' value='Delete Selected' class='btn' /></td></tr>";
    
    $HTMLOUT .= '
</table>
</form>

'.$pager['pagerbottom'].'
';
}
else
    $HTMLOUT .= 'Nothing here!';


/////////////////////// HTML OUTPUT //////////////////////////////
print stdhead('View Offers').$HTMLOUT.stdfoot();
?>