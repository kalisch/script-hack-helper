<html><head><title>Check scrpit</title></head><body>
<?php 

  $current_dir = __DIR__;
	if(isset($_GET['dir'])&&$_GET['dir']) $current_dir .= '/'.$_GET['dir'];
	
	find_files($current_dir);


	function find_files($seed) {
		
		$ssw = 0;
		
		if(! is_dir($seed)) return false;
		$files = array();
		$dirs = array($seed);
	  
		echo('<br><br><table style="font-family:Verdana; font-size:10px;">');
	  
		while(NULL !== ($dir = array_pop($dirs))) {   
		   if($dh = opendir($dir)) {
				while( false !== ($file = readdir($dh))) {
					if($file == '.' || $file == '..') continue;
					  $path = $dir . '/' . $file;
					  if(is_dir($path)) {    $dirs[] = $path; }                
					  else { if(preg_match('/^.*\.(php[\d]?|js|txt)$/i', $path)) { check_files($path, &$ssw); }} 
				}
				closedir($dh);
			}}
	
		echo('</table ><br><br>Total: '.$ssw.' registros<br><br>');
	}
	
	function check_files($this_file, &$ssw) {
		
		// .................. string to find in files, array
		$str_to_find[]='base64_decode';
		
		// .................. common know files, array
		$common[]='com_content/controller.php';
		$common[]='com_mailto/controller.php';
		$common[]='com_user/controller.php';
		$common[]='com_content/controllers/article.php';
		$common[]='com_content/models/form.php';
		$common[]='com_users/controllers/user.php';
		$common[]='com_users/models/login.php';
		$common[]='administrator/components/com_templates/controllers/source.php';
		$common[]='administrator/components/com_templates/models/source.php';
		$common[]='administrator/components/com_phocadownload/helpers/fileupload.php';
		$common[]='geshi/php.php';
		$common[]='geshi/php-brief.php';
		$common[]='/com_weblinks/controllers/weblink.php';
		$common[]='/com_weblinks/models/form.php';
		$common[]='/libraries/simplepie/simplepie.php';
		$common[]='/libraries/phpxmlrpc/xmlrpc.php';
		$common[]='/administrator/components/com_media/controllers/file.php';
		$common[]='/administrator/components/com_login/models/login.php';
		$common[]='/administrator/components/com_media/controllers/file.php';
		$common[]='/administrator/components/com_joomlaupdate/restore.php';
		$common[]='/administrator/components/com_menus/controllers/item.php';
		$common[]='/administrator/components/com_users/models/users.php';
		$common[]='/administrator/components/com_phocamaps/helpers/phocamapsmap.php';
		$common[]='/administrator/components/com_phocamaps/helpers/phocamaps.php';
		$common[]='plugins/system/highlight/highlight.php';
		$common[]='/check.php';
		$common[]='components/com_ajaxregistration/controller.php';
		
		
		// .................. delete files true/false GET['delete']=1/0 / default = 0
		$delete_files = 0;
		if(isset($_GET['delete'])&&$_GET['delete']=='1') $delete_files = 1;
		
		// .................. show commons files true/false GET['commons']=1/0 / default = 1
		$show_commons = 1;
		if(isset($_GET['commons'])&&$_GET['commons']=='0') $show_commons = 0;
		
		// .................. filter by days diff 1/0 / default = 0
		// .................. GET['days]=1 shows files with update diff <= 1
		$filter_by_days = 0;
		if(isset($_GET['days'])&&$_GET['days']) {
			$days = (int) $_GET['days'];
			$filter_by_days = 1;
		}
		
		// .................. show check manual files true/false GET['manual']=1/0 / default = 1
		$manual = 1;
		if(isset($_GET['manual'])&&$_GET['manual']=='0') $manual = 0;
		
		// .................. calculate days diff between today and file date
		$alertday = 0;
		$fecha =  date("F d Y", filemtime($this_file));
		$ts1 = strtotime(date ("F d Y H:i:s.", filemtime($this_file)));
		$ts2 = strtotime(date ("F d Y H:i:s."));
		$seconds_diff3 = $ts2 - $ts1;
		$seconds_diff = floor($seconds_diff3/3600/24);
		$seconds_diff2 = (int) floor($seconds_diff3/3600/24);
		if($seconds_diff<7)  $alertday = 1;
		 
		if($filter_by_days) { // we are filtering by update file date, difference between today
			if($days>=$seconds_diff2)  { //if get param days is lower than update date diff > show file
				print_record_table($this_file, &$ssw, $seconds_diff, 'recent', $alertday, '', $delete_files);	
			}
		} 
		else { // we are looking for strings into flie content
			
			if(!($content = file_get_contents($this_file))&&$manual) {   // if is not posible to look into the file content
				print_record_table($this_file, &$ssw, $seconds_diff, 'manual', $alertday, '', $delete_files);	
			}
			else {  // if is posible to look into the file content
				
				while(list(,$value)=each($str_to_find)) {
					
					if(stripos($content, $value) !== false) {
						$type = 'danger';
						while(list(,$valuecommon)=each($common)) {  // looking for commons files
							if (stripos($this_file, $valuecommon) !== false) {
								$type = 'common';
								if(!$show_commons) return; //return if we don´t show commom files
								break;
							}
						}
						print_record_table($this_file, &$ssw, $seconds_diff, $type, $alertday, $value, $delete_files);
					}	
				}
			}
		}
	  	unset($content);
	}


	function print_record_table($this_file, &$ssw, $seconds_diff, $type, $alertday, $value, $delete_files) {

		switch ($type) {
			case 'deleted':
				$style= 'style="color:black; border:red 1px solid; background-color:yellow;"';
				$text = 'Deleted file';
				break;
			case 'recent':
				$style= 'style="color:white; border:#333 1px solid; background-color:red;"';
				$text = 'Recent update';
				break;
			case 'manual':
				$style= 'style="color:black; border:#333 1px solid; background-color:#e2e2e2;"';
				$text = 'Manual check';
				break;
			case 'danger':
				$style= 'style="color:yellow; border:#333 1px solid; background-color:red;"';
				$text = 'Contains '.$value;
				break;	
			case 'common':
				$style= 'style="color:#006699; border:#333 1px solid; background-color:#white;"';
				$text = 'Contains '.$value;
				break;								
		}
		
		$style2 = $style;
		
		if($alertday) $style2 = 'style="color:yellow; border:#000 1px solid; background-color:black;"';
		
		$ssw++;
		
		
		echo('
			<tr>
				<td '.$style.'>'.$type.'</td>
				<td '.$style2.'>'.$seconds_diff.'</td>
				<td '.$style.'>' .date ("F d Y H:i:s.", filemtime($this_file)).'</td>
				<td '.$style.'>'.$this_file.'</td>
				<td '.$style.'>'.$text.'</td>
				<td '.$style.'>'.substr(decoct(fileperms($this_file)),3).'</td>
				
				
			
			</tr>');
		
		if($delete_files) delete_the_file($this_file, &$ssw, $seconds_diff, $type, $alertday, $value, 0);
		
		return;

	}
	
	
	function delete_the_file($this_file, &$ssw, $seconds_diff, $type, $alertday, $value, $delete_files) {
		
		// .................. delete the files with this strings, array 
		$deletefiles[] = '/Auth/OpenID/';
		$deletefiles[] = '/js/tokenizephp.js';
		$deletefiles[] = '/beez/';
		$deletefiles[] = '/w.php';
		$deletefiles[] = '/pp1.php';
		$deletefiles[] = '/x.php';
		$deletefiles[] = 'mod_feed/session';
		$deletefiles[] = 'jce/libraries/classes/manager.php';
		$deletefiles[] = 'images/stories/docpdf/doc.php';
		$deletefiles[] = 'images/stories/ll.php';
		$deletefiles[] = '/cache/';
		
		while(list(,$valuedelete)=each($deletefiles)) {
			if (stripos($this_file, $valuedelete) !== false) {
				unlink($this_file);
				print_record_table($this_file, &$ssw, $seconds_diff, 'deleted', $alertday, '', 0);
				return;
			}
		}
		return;
	}



?>
</body></html>
