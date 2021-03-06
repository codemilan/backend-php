<?php
###############################################################################
# This file is a part of the SmartWFM PHP-Backend                             #
# Copyright (C) 2010 Morris Jobke <kabum@users.sourceforge.net>               #
#                                                                             #
# SmartWFM PHP-Backend is free software; you can redestribute it and/or modify#
# it under terms of GNU General Public License by Free Software Foundation.   #
#                                                                             #
# This program is distributed in the hope that it will be useful, but         #
# WITHOUT ANY WARRANTY. See GPLv3 for more details.                           #
###############################################################################

require_once('lib/SmartWFM/tools.php');

if( !defined( 'ERROR_PERMISSION_DENIED' ) )
	define( 'ERROR_PERMISSION_DENIED', 1 );
if( !defined( 'ERROR_NO_SUCH_FILE_OR_DIRECTORY' ) )
	define( 'ERROR_NO_SUCH_FILE_OR_DIRECTORY', 2 );

class search {
	/**
	  * constructor
	  */
	public function search( $config = NULL ) {
		$this->path = $config['path'];
		$this->name = explode(' ', $config['options']['name']);
	}

	protected function getCmd() {
		$cmd = 'find';
		$cmd .= ' '.Path::join(
			SmartWFM_Registry::get( 'basepath', '/' ),
			$this->path
		);
		foreach($this->name as $v) {
			$cmd .= ' -iname \'*'.$v.'*\'';
		}
		$cmd .= ' ! -iwholename \'*/.*\'';
		$cmd .= ' 2>&1';
		return $cmd;
	}

	public function getResult() {
		$cmd = $this->getCmd();
		exec($cmd, $output, $ret);
		@syslog($ret ? LOG_ERR : LOG_INFO, '[' . $_SERVER['REMOTE_USER'] . '] Search - cmd: ' . $cmd);
		if(!$ret) {
			$results = array();
			foreach($output as $f) {
				$basePath = SmartWFM_Registry::get( 'basepath', '/' );
				$dir = @is_dir($f) ? true : false;
				if(substr($f, 0, strlen($basePath)) == $basePath)
					$f = substr($f, strlen($basePath)+1);		// eliminate basePath and leading /
				$results[] = array(
					basename($f),
					dirname($f),
					$dir
				);
			}
			return $results;
		} else {
			if(count($output) and isset($output[0])){
				if(strpos($output[0], 'Permission denied.'))
					return ERROR_PERMISSION_DENIED;
				if(strpos($output[0], 'No such file or directory.'))
					return ERROR_NO_SUCH_FILE_OR_DIRECTORY;
			}
		}
	}
}
?>
