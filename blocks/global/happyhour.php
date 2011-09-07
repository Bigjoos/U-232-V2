<?php
// happy hour
    if ( $CURUSER ) {
    if ( happyHour( "check" ) ) {
        $htmlout.="
        <li>
         <a class='tooltip' href='browse.php?cat=" . happyCheck( "check" ) . "'><b>HappyHour</b><span class='custom info'><img src='./templates/1/images/Info.png' alt='Happy Hour' height='48' width='48' /><em>HappyHour</em>
         Hey its now happy hour ! " . ( ( happyCheck( "check" ) == 255 ) ? "Every torrent downloaded in the happy hour is free" : "Only in the selected Category, click on HappyHour above here to go to it" ) . "<br /><font color='red'><b> " . happyHour( "time" ) . " </b></font> remaining from this happy hour!
        </span></a></li>";
    }
   }
?>