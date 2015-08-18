<?php
/* ********************************************* */
/* Original script by WebFooL on Untangle Forums */
/* Modifications by fsSnowboard                  */
/*                                               */
/* Use at your own risk                          */
/* ********************************************* */

date_default_timezone_set('UTC');

$remote_filter_list = "https://easylist-downloads.adblockplus.org/easylist_noelemhide.txt";
$timezone_adjust = 60 * 60 * 7;  //Currently set to EST

$Content = "./easylist_noelemhide.txt";

if (file_exists($Content)) {
    $lines = file($Content);
}

$last_modified =  preg_replace('/! Last modified: /', '', $lines[3]);
$last_modified_time = strtotime($last_modified);
echo "File last modified: ". $last_modified ." (".$last_modified_time.")</br>\n";

$date_match_time = time() - (60 * 60 * 24 * 5) + $timezone_adjust;
echo "Date Match time (only used for matching if last modified is less than current date): ". $download_date_match ."(".$date_match_time.")</br>\n";

$download_match_time = $last_modified_time + (60 * 60 * 24 * 5) + $timezone_adjust;
$download_date_match = date("d M Y H:i T", $date_match_time);

echo "New download on: ". date("d M Y H:i", $download_match_time) ." (".$download_match_time.")</br>\n";

if($last_modified_time <= $date_match_time || !file_exists($Content)) {
	echo "File is 5 days old or not exists, downloading latest.";
	//if file is 5 days old, download new one
	$new_easylist = file_get_contents($remote_filter_list);
	file_put_contents("easylist_noelemhide.txt", $new_easylist);
	
	//read new file
	$Content = "./easylist_noelemhide.txt";  
	$lines = file($Content);
}



$badcharacters = array("#", '"', "'", "[", "]", "^", "\n", "\t", "\r", "||");


unset($lines[0]);  //Line 0 is [Adblock Plus 2.0] and not needed

foreach ($lines as $key => $value) {
    // no keep lines start with ! and @@, and lines with |http and |https
    if(!empty($value) && ($value[0] !== "!") && ($value[0] !== "@" && $value[1] !== "@") && $value !== "|http:" && $value !== "|https:") {
        // strip everything to the right of $,
        if (strpos($value, '$') !== false) {
            $value = substr($value, 0, strpos($value, '$'));
        }
        //strip out bad characters
        $cleanedList[] = str_replace($badcharacters, '', $value);
    }
}

//foreach ($lines as $key => $value) {
	
    //strip everything to the right and including ^
    //$pos = strpos($value, "^");
	//if ($pos !== FALSE) {
	//    //echo $key." ".$pos."\n";
	//	//echo "part:". substr($value, 0, $pos)."\n";
	//	//echo "full:".$value."\n";
    //	$lines[$key] = substr($value, 0, $pos);
    //}
//}

echo "Filter Items: ". count($cleanedList) ."<br />\n";

$linesSplit = array_chunk( $cleanedList, 2000 );
//print_r($linesSplit);


$i = 1; 
foreach ($linesSplit as $inner_array) {
    $currentJson = 'ABimport'.$i.'.json';
    $store = '[';
    foreach ($inner_array as $value) {
        $store .= '{"enabled":true,"string":"'.$value.'","javaClass":"com.untangle.uvm.node.GenericRule"},';
    }
    $store .= "]";
    file_put_contents($currentJson, $store);
    $i++;
}
?>