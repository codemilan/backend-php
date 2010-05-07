<?php
###############################################################################
# This file is a part of the SmartWFM PHP-Backend                             #
# Copyright (C) 2009-2010 Philipp Seidel <phibo@oss.dinotools.de>             #
#                    2010 Morris Jobke <kabum@users.sourceforge.net           #
#                                                                             #
# SmartWFM PHP-Backend is free software; you can redestribute it and/or modify#
# it under terms of GNU General Public License by Free Software Foundation.   #
#                                                                             #
# This program is distributed in the hope that it will be useful, but         #
# WITHOUT ANY WARRANTY. See GPLv3 for more details.                           #
###############################################################################

if(SmartWFM_Registry::get('filesystem_type') == 'afs') {
	require_once('lib/AFS/libafs.php');
}

class BaseDirectCommand_Download extends SmartWFM_Command {
	function process($params) {
		$fs_type = SmartWFM_Registry::get('filesystem_type');

		$BASE_PATH = SmartWFM_Registry::get('basepath','/');
		$path = Path::join(
			$BASE_PATH,
			$params['path']
		);

		$file = Path::join(
			$path,
			$params['name']
		);

		if(Path::validate($BASE_PATH, $path) != true || Path::validate($BASE_PATH, $file) != true) {
			print "error";
			return;
		}

		if($fs_type == 'afs') {
			$afs = new afs($path);

			if( !$afs->allowed( AFS_READ ) ) {
				print 'Permission denied.';
				return;
			}
		} else if($fs_type == 'local') {
			if(!is_readable($file)) {
				print 'Permission denied.';
				return;
			}
		}

		if (file_exists($file)) {
			$mime = @MimeType::get($file);

			$fp = fopen($file, 'r');
			if($fp === False) {
				print('Error reading the file');
				return;
			}

			#Content-Description: File Transfer
			#Content-Type: application/octet-stream
			header('Content-Type: ' . $mime);
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();

			while(($content = fread($fp, 4096)) != '') {
				print($content);
			}
			exit();
		}

	}	
}

SmartWFM_DirectCommandManager::register('download', new BaseDirectCommand_Download());

class BaseDirectCommand_Upload extends SmartWFM_Command {
	function process($params) {
		$fs_type = SmartWFM_Registry::get('filesystem_type');

		$BASE_PATH = SmartWFM_Registry::get('basepath','/');

		$path = Path::join(
			$BASE_PATH,
			$params['path']
		);

		$file = Path::join(
			$path,
			$_FILES['file']['name']
		);

		if(Path::validate($BASE_PATH, $path) != true || Path::validate($BASE_PATH, $file) != true) {
			print "error";
			return;
		}

		if($fs_type == 'afs') {
			$afs = new afs($path);

			if(!$afs->allowed(AFS_INSERT)) {
				print 'Permission denied.';
				return;
			}
		} else if($fs_type == 'local') {
			if(!is_writable($path)) {
				print 'Permission denied.';
				return;
			}
		}
		//ToDo: check if file exists

		move_uploaded_file($_FILES['file']['tmp_name'], $file);

	}
}

SmartWFM_DirectCommandManager::register('upload', new BaseDirectCommand_Upload());

?>
