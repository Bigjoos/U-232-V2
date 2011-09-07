<?php
//=== free addon start
    if ($CURUSER) { 
    if (isset($free))
    {
    foreach ($free as $fl)
    {
        switch ($fl['modifier'])
        {
            case 1:
                $mode = 'All Torrents Free';
                break;

            case 2:
                $mode = 'All Double Upload';
                break;

            case 3:
                $mode = 'All Torrents Free and Double Upload';
                break;

            default:
                $mode = 0;
        }
        
     $htmlout .= ($fl['modifier'] != 0 && $fl['expires'] > TIME_NOW ? '
     <li>
     <a class="tooltip" href="#"><b>FreeLeech ON</b><span class="custom info"><img src="./templates/1/images/Info.png" alt="Freeleech" height="48" width="48" />
     <em>'.$fl['title'].'</em>
     '.$mode.'<br />
     '.$fl['message'].' set by '.$fl['setby'].'<br />'.($fl['expires'] != 1 ? 
     'Until '.get_date($fl['expires'], 'DATE').' ('.mkprettytime($fl['expires'] - time()).' to go)' : '').'  
     </span></a></li>' : '');
}
}
}
//=== free addon end
?>