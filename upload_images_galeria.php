<?php 
	//require_once("upload/phpuploader/include_phpuploader.php");
	include_once("includes/class.Resize.php");
	
	//USER CODE:
	$sDirDest = "../fotos_album/".str_pad($_POST['id'], 5, "0", STR_PAD_LEFT);
	
	if(!is_dir($sDirDest)){
		mkdir($sDirDest, 0755);
	}
	
	$oImage = new SimpleImage();
	for($i=0; $i<count($_FILES['imagenes']['name']); $i++){
		//$ext = explode('.', basename( $_FILES['imagenes']['name'][$i]));
		//$targetfilepath= $sDirDest ."/". basename( $_FILES['imagenes']['name'][$i]);
		//$result = preg_replace('([^A-Za-z0-9.])', '', basename( $_FILES['imagenes']['name'][$i]));
		$vExt = explode(".",$_FILES['imagenes']['name'][$i]);
		$result = date("YmdHis") . "." . $vExt[count($vExt)-1];
		$targetfilepath= $sDirDest ."/". $result;
		
		if( is_file ($targetfilepath) )
			unlink($targetfilepath);
		
		//$target_path = $target_path . md5(uniqid()) . "." . $ext[count($ext)-1]; 

		if(move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $targetfilepath)) {
			//echo "Imagen Subida ".$targetfilepath."<br />";
		} else{
			echo "ERROR al subir ".basename( $_FILES['imagenes']['name'][$i])." <br />";
		}
		$oImage->load($targetfilepath);
		$oImage->resizeToWidth(800);
		$oImage->save($targetfilepath);
	}	

	header("location: admin_galeria_images.php?id=".$_POST['id']);

?>
