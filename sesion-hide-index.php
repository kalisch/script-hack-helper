<?php



  $ip = '';
	if(isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];

	$sw = 0;
	if($ip!='xxx.xxx.xxx.xxx')  {
		
		if(isset($_GET['acceso'])&&$_GET['acceso']=='tuusuario') {
			setcookie("access_nombrecookie", 'tupasswordinventado');
			$sw = 1;										
		}
		
		//echo('sesion: '. $_COOKIE['access_ecodes']);
		
		if(isset($_COOKIE['access_nombrecookie'])&&$_COOKIE['access_nombrecookie'] == 'tupasswordinventado') $sw = 1;	
		
	} else { $sw = 1; }
		
	if(!$sw) {
		header("HTTP/1.0 404 Not Found");
		die("<h1>404 File not found</h1>");  	
	}


?>
