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

if( !defined( 'ERROR_PERMISSION_DENIED' ) )
	define( 'ERROR_PERMISSION_DENIED', 1 );
if( !defined( 'ERROR_NO_SUCH_FILE_OR_DIRECTORY' ) )
	define( 'ERROR_NO_SUCH_FILE_OR_DIRECTORY', 2 );

class search {	
	/**
	  * constructor
	  */
	public function search( $config = NULL ) {
	}

	protected function getCmd() {
		$cmd = 'find';
		$cmd .= ' '.SmartWFM_Registry::get( 'basepath', '/' );
		$cmd .= ' -name ';
		$cmd .= ' \'*a*\'';
		$cmd .= ' 2>&1';
		return $cmd;
	}
	
	public function getResult() {
		$cmd = $this->getCmd();
		exec($cmd, $output, $ret);
		if(!$ret){
			//
		}else{
			if(count($output) and isset($output[0])){
				if(strpos($output[0], 'Permission denied'))
					return ERROR_PERMISSION_DENIED;
				if(strpos($output[0], 'No such file or directory'))
					return ERROR_NO_SUCH_FILE_OR_DIRECTORY;
			}
		}
		return array($cmd, $output);
	}
}
?>
