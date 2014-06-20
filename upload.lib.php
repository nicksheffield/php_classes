<?php

/**
*	
*	File Upload class
*
*	@version 1.1
*	@author  Nick Sheffield
*
*	@todo    Finish docblocks
*
*	@example
*
*	if($_FILES){
*		$upload_result = Upload::to_folder('uploads/');
*
*		foreach($upload_result as $file){
*			if($file['error_message']){
*				echo '<p class="error">'.$file['error_message'].'</p>';
*			}else if($file['filepath']){
*				echo '<a href="'.$file['filepath'].'">Download file</a>';
*			}
*		}
*	}
*
*/

class Upload{

	public static $optional = false;

	/*
		Upload files into the specified folder.

		$folder  String  The destination folder to put the uploaded files.

		Return   Array   An array of files and/or any errors that are occured.

		eg.

		array(
			[0] => array(
				'error_message' => 'The file you are attempting to upload is too large'
			),
			[1] => array(
				'error_message' => false,
				'filepath' => 'uploads/spongebob.png'
			),
			[2] => array(
				'error_message' => false,
				'filepath' => 'uploads/lasercat.gif'
			)
		)
	*/
	public static function to_folder($folder){

		$files_array = array();

		for($i = 0; $i < count($_FILES['file']['name']); $i++){
			$file  = $_FILES['file']['name'][$i];
			$tmp   = $_FILES['file']['tmp_name'][$i];
			$error = $_FILES['file']['error'][$i];

			$files_array[] = self::upload_file($tmp, $folder.$file, $error);
		}

		return $files_array;
	}



	/*

		Upload a single file.

		Private

		$tmp    String    The filepath of the temporary file that was uploaded
		$file   String    The filepath of the destination file. eg, uploads/newfile.png
		$error  Int       The error number provided by the $_FILES array

		Return  Array     An array providing an error message or a filepath

		eg.

		array(
			'error_message' => 'false',
			'filepath' => 'uploads/spongebob.png'
		)


	*/
	private static function upload_file($tmp, $file, $error){
		if($error == 0){
			move_uploaded_file($tmp, $file);
			return self::file($file);

		}else if($error == 1 || $error == 2){
			return self::error('The file you are attempting to upload is too large');

		}else if($error == 3){
			return self::error('The file you are attempting to upload is incomplete');

		}else if($error == 4){
			if(self::$optional) return false;
			else                return self::error('No file was uploaded');

		}else{
			return self::error('Unknown error');
		}
	}



	/*

		Generate an error array.

		Private

	*/
	private static function error($text){
		$arr = array(
			'error_message' => $text
		);

		return $arr;
	}



	/*

		Generate an array with no error, and a filepath.

		Private

	*/
	private static function file($filepath){
		$arr = array(
			'error_message' => false,
			'filepath' => $filepath
		);

		return $arr;
	}




}