<?php 
/*
Plugin Name: Banner Wizz
Plugin URI: 
Description: This plugin helps you add multiple banners at the top or bottom of the post. Banners are displayed based on post keywords. 
Version: 1.1
Author: wpwizz
Author URI: http://www.wpwizz.com/
Programed by: Marius Moiceanu (marius81@gmail.com) 
*/

error_reporting(0); // Disable error reporting... For debug mode please comment this line.
$blog_url = get_bloginfo('wpurl');
$base_url_m = "../wp-content/plugins/banner-wizz/";
add_action('admin_menu', 'show_config_page_m');
array( $this, 'link' );

include("core.php"); // Some basic functions that i need

// Add admin config page under Settings tab
 function show_config_page_m() {
			global $wpdb;
			if ( function_exists('add_options_page') ) {
				add_options_page('Banner Wizz Configuration', 'Banner Wizz', 9, basename(__FILE__), 'show_page_m');
			}
			}

			

			
function show_page_m() {

global $base_url_m, $wpdb;

$action = $_REQUEST['action'];
$db = $_REQUEST['db'];


// I set default action if none specified

if(!isset($action)) {
$action = "viewbanners";
}

$File = new File_Wizz_banner ; 

// i check if db is installed

$inst_db = 0;
$inst_db = count($wpdb->get_results("SELECT * FROM information_schema.tables WHERE table_name = 'banner_wizz'"));
if (!$inst_db > 0 && !$db =="2"){
$action = "install";
} 





// begin action switch
 switch ($action)
{
case "viewbanners": 

$content .= $File->FFileRead($base_url_m . "templates/viewbanners.html");
$count = 0;
$results = $wpdb -> get_results("SELECT * FROM banner_wizz");
foreach($results as $result)
{	
	if ($result->status ==0 ) {
	$active_m = "<font color='red'>Activate</font>";
	} 
	if ($result->status ==1 ) {
	$active_m = "<font color='green'>Deactivate</font>";
	} 
	$count++;
	
	$content_table .= "  <tr>
    <td>".$count."</td>
    <td>".$result->name."</td>
    <td>".$result->keywords."</td>
    <td><a href='options-general.php?page=post_banner_wizz.php&action=status&id=".$result->id."'>".$active_m."</a></td>
    <td><a href='options-general.php?page=post_banner_wizz.php&action=editbanner&id=".$result->id."'>Edit</a></td>
    <td><a href='options-general.php?page=post_banner_wizz.php&action=delete&id=".$result->id."' onclick=\"return confirm('Are you sure you want to delete this entry??')\">Delete</a></td>
  </tr>";

}
$content = str_replace("{table_content}",$content_table,$content);

break;

case "addbanner":
$content = $File->FFileRead($base_url_m . "templates/addbanner.html");
$up_title_m = "Add new banner";
$action_m = "options-general.php?page=post_banner_wizz.php&action=addbanner_db";
$position ="<option value='0'>Random</option>
          <option value='1'>Post top</option>
          <option value='2'>Post bottom</option>";

$align = "<option value='0'>Left</option>
          <option value='1'>Center</option>
          <option value='2'>Right</option>";		  

break;

case "addbanner_db":

check_latest_db_format(); // With this function I check if I have database format ready for version 1.1. If not I update the table.

$content = "Ieee merge ";
$keywords = $_REQUEST['keywords'];
$title = $_REQUEST['title'];
$bannerlink = $_REQUEST['bannerlink'];
$position = $_REQUEST['position'];
$align = $_REQUEST['align'];
//$file = $_REQUEST['file'];

// Banner upload routine.
$target_path = "../wp-content/plugins/banner-wizz/uploads/";
$db_path = "/wp-content/plugins/banner-wizz/uploads/" . basename( $_FILES['file']['name']);
$target_path = $target_path . basename( $_FILES['file']['name']); 
if(!move_uploaded_file($_FILES['file']['tmp_name'], $target_path) === FALSE) {
$content = "There was an error uploading banner file! Check upload directory permission!<br>Upload path is: /wp-content/plugins/wizz_banner/uploads/";
}

 $wpdb->query("INSERT INTO banner_wizz(`id`, `name`, `keywords`, `link`, `pic`, `status`, `position`, `align`)
        VALUES(NULL, '".$title."', '".$keywords."', '".$bannerlink."','".$db_path."','0','".$position."','".$align."')") or die(mysql_error());
	if(mysql_affected_rows()>0) {
	$content = "New banner was added to database!";
	} else $content = "There was an error adding banner to database!";
	
break;

case "editbanner":


$id = $_REQUEST['id'];
$content = $File->FFileRead($base_url_m . "templates/addbanner.html");
$results = $wpdb -> get_results("SELECT * FROM banner_wizz WHERE id='".$id."'");
foreach($results as $result) {
$keywords_m = $result->keywords;
$title_m = $result->name;
$link_m = $result->link;
$banner_m = "<img src='..".$result->pic."'>";
$position_no = $result->position;
$align_no = $result->align;
}


if ($position_no==1) {
$selected0 = "";
$selected1 = "selected='selected'";
$selected2 = "";
}

if ($position_no==2) {
$selected0 = "";
$selected1 = "";
$selected2 = "selected='selected'";

}


$position ="<option value='0' ".$selected0.">Random</option>
          <option value='1' ".$selected1.">Post top</option>
          <option value='2' ".$selected2.">Post bottom</option>";
		  
if ($align_no==1) {
$align0 = "";
$align1 = "selected='selected'";
$align2 = "";
}

if ($align_no==2) {
$align0 = "";
$align1 = "";
$align2 = "selected='selected'";
}

$align = "<option value='0' ".$align0." >Left</option>
          <option value='1' ".$align1.">Center</option>
          <option value='2' ".$align2.">Right</option>";

$up_title_m = "Edit banner settings";
$action_m = "options-general.php?page=post_banner_wizz.php&action=editbanner_db&id=".$id."";
break;

case "editbanner_db":

check_latest_db_format();

$id = $_REQUEST['id'];
$keywords = $_REQUEST['keywords'];
$title = $_REQUEST['title'];
$bannerlink = $_REQUEST['bannerlink'];
$position = $_REQUEST['position'];
$align = $_REQUEST['align'];

$target_path = "../wp-content/plugins/banner-wizz/uploads/";
$db_path = "/wp-content/plugins/banner-wizz/uploads/";

$file_name_m = basename($_FILES['file']['name']);

if(!empty($file_name_m)){
$target_path = $target_path . $file_name_m; 

$results = $wpdb -> get_results("SELECT * FROM banner_wizz WHERE id='".$id."'");
foreach($results as $result) {
$pic_m = $result->pic;
}
unlink("..".$pic_m);

if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path) === FALSE) {
$content = "There was an error uploading banner file! Check upload directory permission!<br>Upload path is: /wp-content/plugins/wizz_banner/uploads/";
}

$db_var = ",`pic` = '" . $db_path . $file_name_m ."'";
} else $db_var="";

	$wpdb->query("UPDATE banner_wizz SET `name` = '".$title."', `keywords` = '".$keywords."', `position` = '".$position."', `align` = '".$align."', `link` = '".$bannerlink."' ".$db_var." WHERE `id`='".$id."'"); 

	if(mysql_affected_rows()>0) {
	$content = "Banner was modified!";
	} else $content = "There was an error modifying banner!";

break;

case "about":
$content = "<br><br><br><br>Banner Wizz v1.1<br>Programed by: Marius Moiceanu <br>Visit <a href='www.wpwizz.com' target='_blank'>www.wpwizz.com</a> for latest version.";
break;


case "delete":

$id = $_REQUEST['id'];
$results = $wpdb -> get_results("SELECT * FROM banner_wizz WHERE id='".$id."'");
foreach($results as $result) {
$pic_m = $result->pic;
}
unlink("..".$pic_m);
$wpdb -> get_results("DELETE FROM banner_wizz WHERE id='".$id."'");

	if(mysql_affected_rows()>0) {
	$content = "The entry was deleted";
	} else $content = "There was an error deleting entry";

break;

case "status":
$id = $_REQUEST['id'];
$results = $wpdb -> get_row("SELECT * FROM banner_wizz WHERE id='".$id."'");
$status_m = $results->status;

if ($status_m == 0) { $update_status_m =1; } 
if ($status_m == 1) { $update_status_m =0; } 

$wpdb -> get_results("UPDATE banner_wizz SET status='".$update_status_m."' WHERE id='".$id."'");
	if(mysql_affected_rows()>0) {
	$content = "The entry status was modified  <meta http-equiv=Refresh content=0;url=options-general.php?page=post_banner_wizz.php&action=viewbanners>";
	} else $content = "There was an error modifying entry status";
break;

case "install":
$content = $File->FFileRead($base_url_m . "templates/install.html");
break;

case "install_db";

    $structure = "CREATE TABLE IF NOT EXISTS `banner_wizz` (
  `id` int(10) unsigned NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `keywords` text NOT NULL,
  `link` text NOT NULL,
  `pic` text NOT NULL,
  `status` int(2) NOT NULL,
  `position` int(2) NOT NULL,
   `align` int(2) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;";
    $wpdb->query($structure);
	
$inst_db2 = count($wpdb->get_results("SELECT * FROM information_schema.tables WHERE table_name = 'banner_wizz'"));
if (!$inst_db2 > 0 ){
$content = "There was an error during install!";
} else $content = "The setup was successful! Enjoy Banner Wizz! <meta http-equiv=Refresh content=2;url=options-general.php?page=post_banner_wizz.php&action=viewbanners>";
	
	
break;
} // End switch  




$main_content_m = $File->FFileRead($base_url_m . "templates/main.html");

$global_vars_template = array( 
"{content}" => $content,
"{base_url}" => $base_url_m,
"{keywords_m}" => $keywords_m,
"{title_m}" => $title_m,
"{link_m}" => $link_m,
"{up_title}" => $up_title_m,
"{action_m}" => $action_m,
"{banner_image_m}" => $banner_m,
"{position}" => $position,
"{align}" => $align,
"{toolbar}" => $File->ReadURL('http://www.wpwizz.com/toolbar/toolbar.html')
);

foreach ($global_vars_template as $string_to_replace => $string_m) {
$main_content_m = str_replace($string_to_replace,$string_m,$main_content_m);
}
echo $main_content_m;
unset($global_vars_template);
}



function search_and_replace_m($post_content) {

global $wpdb,$blog_url;
$occurance_m = array();

// I SEARCH FOR WORDS OCCURANCE IN POST

if (is_single()) {
$results = $wpdb -> get_results("SELECT * FROM banner_wizz WHERE status='1'");
foreach($results as $result) {
$keywords_exp = explode(",",$result->keywords);
$word_check_m = 0;
foreach ($keywords_exp as $key_m) {
$no_m = 0;
$no_m = count(explode($key_m,$post_content)) - 1;
$word_check_m = $word_check_m + $no_m;
}
$occurance_m[$result->id] = $word_check_m;

}
/// IF I DONT FIND A WORD IN POST FOR ANY BANNER I JUST PICK ONE RANDOM
$id_post = max_key($occurance_m);


if(!$occurance_m[$id_post] > 0) {
$id_post = array_rand($occurance_m);
}

// I DISPLAY THE CHOSEN BANNER IN POST 
$res_m = $wpdb -> get_results("SELECT * FROM banner_wizz WHERE id='".$id_post."' LIMIT 0,1") or die(mysql_error());
foreach ($res_m as $rez_m) {
$align_no = $rez_m->align;

if ($align_no == 0) { $align_wizz ="left"; }
if ($align_no == 1) { $align_wizz ="center"; }
if ($align_no == 2) { $align_wizz ="right"; }


$banner_m = "<br><div align='".$align_wizz."'><a href='".$rez_m->link."' target='_blank' class='link'><img src='".$blog_url.$rez_m->pic."' alt=".$rez_m->name." title=".$rez_m->name."></a></div><br>";
$banner_poz = $rez_m->position;
}

$before_wizz = $post_content . $banner_m;
$after_wizz = $banner_m . $post_content;

if ($banner_poz == 0) {
$r_wizz = rand(1,2);

if ($r_wizz==1) {
return $before_wizz;
}

if ($r_wizz==2) {
return $after_wizz;
}

}

if ($banner_poz == 2) {
return $before_wizz;
}

if ($banner_poz == 1) {
return $after_wizz;
}



//return $post_content . $banner_m;
//unset($keywords_exp,$occurance_m,$post_content, $wpbd);

} else return $post_content;

unset($keywords_exp,$occurance_m,$post_content, $wpbd);
}
add_filter('the_content','search_and_replace_m',1);

?>