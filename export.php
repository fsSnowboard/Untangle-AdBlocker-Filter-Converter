<?php 
$Content = "./easylist.txt";  
$lines = file($Content);  
$linesSplit = array_chunk( $lines, 2000 ); 
$badcharacters = array("#",'"', "'", "[", "]", "\n", "\t", "\r");  

$i = 0; 
foreach ($linesSplit as $inner_array) { 
    $i++; 
    $fp = fopen('ABimport'.$i.'.json', 'w');     
    $filestart = "[";  
    fwrite($fp, $filestart); 
    while (list($key, $value) = each($inner_array)) 
    {     
        $cleanstr = str_replace($badcharacters, "", $value);     
    $store = '{"enabled":true,"string":"'.$cleanstr.'","javaClass":"com.untangle.uvm.node.GenericRule"},';  
    fwrite($fp, $store); 
    }  
    $fileend = "]";  
    fwrite($fp, $fileend); 
    fclose($fp); 
} 
?>