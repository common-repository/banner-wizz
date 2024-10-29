<?php 

class File_Wizz_banner {
 
function FFileRead($file)

{   
	error_reporting(0);
	$fp = fopen ($file, "r");
	$buffer = fread($fp, filesize($file));
	fclose ($fp);
	return $buffer;
}

function FFileWrite($file,$what)

{
	$fh=fopen($file,"a+"); 
	fwrite($fh,$what); 
	fclose($fh);
}

function ReadURL($url) {
error_reporting(0);
$base_url_m = "../wp-content/plugins/banner-wizz/";

if (fopen($url, "r")) {
$content_url = file_get_contents($url); 
} else  $content_url = $this -> FFileRead($base_url_m .'/templates/toolbar.html');
return $content_url;
}

}

function max_key($array) {
foreach ($array as $key => $val) {
    if ($val == max($array)) return $key;
    }
}

function check_latest_db_format() {
// I check if i have latest database format

$fields = mysql_list_fields(DB_NAME, 'banner_wizz');
$columns = mysql_num_fields($fields);
for ($i = 0; $i < $columns; $i++) {$field_array[] = mysql_field_name($fields, $i);}

if (!in_array('position', $field_array))
{
 mysql_query("ALTER TABLE `banner_wizz` ADD position INT(2) NOT NULL DEFAULT '2'") or die(mysql_error());
}

if (!in_array('align', $field_array))
{
 mysql_query("ALTER TABLE `banner_wizz` ADD align INT(2) NOT NULL DEFAULT '1'") or die(mysql_error());
}
}



?>