<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/

	function tag_info() {
		
		$result = sql_query("SELECT searchedfor, howmuch FROM searchcloud ORDER BY id DESC LIMIT 50");
  
		while($row = mysql_fetch_assoc($result)) {
			// suck into array
			$arr[$row['searchedfor']] = $row['howmuch'];
		}
		//sort array by key
		if (isset($arr)) {
		ksort($arr);
		
		return $arr;
		}
	}

	function cloud() {
		//min / max font sizes
		$small = 10;
		$big = 35;
		//get tag info from worker function
		$tags = tag_info();
		//amounts
		if (isset($tags)) {
		$minimum_count = min(array_values($tags));
		$maximum_count = max(array_values($tags));
		$spread = $maximum_count - $minimum_count;
      
		if($spread == 0) {$spread = 1;}
		
		$cloud_html = '';

		$cloud_tags = array();
		
		foreach ($tags as $tag => $count) {

			$size = $small + ($count - $minimum_count) * ($big - $small) / $spread;
			//set up colour array for font colours.
			$colour_array = array('yellow', 'green', 'blue', 'purple', 'orange', '#0099FF');
			//spew out some html malarky!
			$cloud_tags[] = '<a style="color:'.$colour_array[mt_rand(0, 5)].'; font-size: '. floor($size) . 'px'
    . '" class="tag_cloud" href="browse.php?search=' . urlencode($tag) . '&amp;cat=0&amp;incldead=1'
    . '" title="\'' . htmlentities($tag)  . '\' returned a count of ' . $count . '">'
    . htmlentities(stripslashes($tag)) . '</a>';
		}
		
		$cloud_html = join("\n", $cloud_tags) . "\n";
		
		return $cloud_html;
		}
	}
?>