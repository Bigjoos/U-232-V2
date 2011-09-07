<?php
//==pdq crazyhour
	 if (isset($CURUSER)) {
   $transfer_filename  = $INSTALLER09['cache'].'/transfer_crazyhour.txt';
   $crazyhour_filename = $INSTALLER09['cache'].'/crazy_hour.txt';
   $crazyhour_cache = fopen($crazyhour_filename,'r+');
   $crazyhour_var = fread($crazyhour_cache, filesize($INSTALLER09['cache'].'/crazy_hour.txt'));
   fclose($crazyhour_cache);
   $cimg = '<img src=\''.$INSTALLER09["pic_base_url"].'cat_free.gif\' alt=\'FREE!\' />';
   if ($crazyhour_var >= TIME_NOW && $crazyhour_var < TIME_NOW + 3600) { // is crazyhour
       $htmlout .="
       <li>
       <a class='tooltip' href='#'><b>CrazyHour ON</b><span class='custom info'><img src='./templates/1/images/Info.png' alt='CrazyHours' height='48' width='48' /><em>CrazyHour</em>
       ".$INSTALLER09['crazy_title']." Ends in ".mkprettytime($crazyhour_var - TIME_NOW)."<br />
       ". $INSTALLER09['crazy_message']."</span></a></li>";
        if (is_file($transfer_filename))
            unlink($transfer_filename);
    }
    elseif ($crazyhour_var < TIME_NOW + 3600 && !is_file($transfer_filename)) { //== crazyhour over
        $transfer_file_created = fopen($transfer_filename, 'w') or die('no perms?');
        fclose($transfer_file_created);
        $crazyhour['crazyhour_new']       = mktime(23, 59, 59, date('m'), date('d'), date('y'));
        $crazyhour['crazyhour']['var']    = mt_rand($crazyhour['crazyhour_new'], ($crazyhour['crazyhour_new'] + 86400));
        $fp = fopen($crazyhour_filename, 'w');
        fwrite($fp, $crazyhour['crazyhour']['var']);
        fclose($fp); 
        write_log('Next Crazyhour is at '.date('F j, g:i a T', $crazyhour['crazyhour'] ['var'])); 
        
        $htmlout .="
        <li>
         <a class='tooltip' href='#'>CrazyHour<span class='custom info'><img src='./templates/1/images/Info.png' alt='CrazyHours' height='48' width='48' /><em>CrazyHour</em>
         "." Crazyhour will be ".get_date($crazyhour['crazyhour']['var'], '')."  
         ".mkprettytime($crazyhour['crazyhour']['var'] - TIME_NOW)." remaining till Crazyhour
        </span></a></li>";
        }
        else // make date look prettier with countdown etc even :]
        $htmlout .="
        <li>
         <a class='tooltip' href='#'>CrazyHour<span class='custom info'><img src='./templates/1/images/Info.png' alt='CrazyHours' height='48' width='48' /><em>CrazyHour</em>
         "." Crazyhour will be ".get_date($crazyhour_var, '')."  
         ".mkprettytime($crazyhour_var - TIME_NOW)." remaining till Crazyhour
        </span></a></li>";
        }
	      // crazyhour end
?>