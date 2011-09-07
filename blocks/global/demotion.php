<?php
//==Temp demotion
   if ($CURUSER['override_class'] != 255 && $CURUSER) // Second condition needed so that this box isn't displayed for non members/logged out members.
   {
   $htmlout .= "<li>
   <a class='tooltip' href='./restoreclass.php'><b>Temp. Demotion</b><span class='custom info'><img src='./templates/1/images/Info.png' alt='Demotion' height='48' width='48' /><em>Temp. Demoted</em>   
   To reset your Temp. status, simply just click at the Temp. Demotion above here.</span></a></li>
   ";
   }
   //==End
?>