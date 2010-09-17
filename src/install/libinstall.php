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

/**
  *	@author Morris Jobke
  *	@since	0.4
  *
  *	This is an abstract class for other options saved by this script.
  */
abstract class BaseOption {
	protected $defaultValue;
	protected $value;
	protected $name;	
	protected $type;	
	protected $possibleValues;
	protected $errorCode = NULL;	
	protected $errorMessage = NULL;	
	protected $enabled = True;
	protected $title;
	protected $description;

	protected function boolIt($v) {
		$bool = array('False', 'False', '0');
		if(in_array($v, $bool))
			return False;
		else
			return (boolean) $v;
	}
	
	public function __construct($n, $t, $d, $title, $description, $p = NULL) {
		$this->name = $n;
		$this->type = $t;
		$this->defaultValue = ($t == 'boolean') ? $this->boolIt($d) : $d;
		$this->possibleValues = $p;
		if(method_exists($this, 'checkAvailablity'))
			$this->checkAvailablity();
		$this->title = $title;
		$this->description = $description;
	}
	
	public function getDefault() {
		return $this->defaultValue;
	}
	
	public function getError() {
		return array(
			'code' 		=> $this->errorCode,
			'message' 	=> $this->errorMessage
		);
	}
		
	public function getName() {
		return $this->name;
	}
	
	protected function setValue($v) {
		if($this->type == 'boolean')
			$this->value = $this->boolIt($v);
		else
			$this->value = $v;
	}
	
	public function getValue() {
		if(!$this->enabled)
			return $this->defaultValue;
			
		return empty($this->value) ? $this->defaultValue : $this->value;
	}
	
	public function hasError() {
		return ($this->errorCode == NULL) ? False : True;
	}
	
	/**
	  * @return	value in PHP-syntax
	  */
	public function getPHPValue() {
		switch($this->type){
			case 'string':
				$value = "'".$this->getValue()."'";
				break;
			case 'array':
				$value = 'array(';
				foreach($this->getValue() as $v){
					$value .= "'".$v."', ";
				}
				$value .= ')';
				break;
			case 'boolean':
				$value = $this->getValue() ? 'True' : 'False';
				break;
			default:
				$value = $this->getValue();
				break;
		}
		return $value;
	}
	
	abstract public function check($v);
};

/**
  *	@author Morris Jobke
  *	@since	0.4
  *
  *	class to handle 'basepath' option
  *
  *	errorCodes:
  *		1 	path-string is empty
  *		2	path is file  
  *		3	path doesn't exists
  */
class BasePathOption extends BaseOption {
	public function check($v) {
		$this->setValue($v);
		
		if($this->value == '') {
			$this->errorCode = 1;
			$this->errorMessage = 'path-string is empty';
		} else {
			if(file_exists($this->value)) {
				if(is_dir($this->value))
					return True;
				else {
					$this->errorCode = 2;
					$this->errorMessage = 'path is file';				
				}
			} else {
				$this->errorCode = 3;
				$this->errorMessage = 'path doesn\'t exists';				
			}
		}
		return False;
	}
};

/**
  *	@author Morris Jobke
  *	@since	0.4
  *
  *	class to handle 'setting_filename' option
  *
  *	errorCodes:
  *		1 	path-string is empty
  *		2	path is directory  
  *		3	path doesn't exists
  */
class SettingFilenameOption extends BaseOption {
	public function check($v) {
		$this->setValue($v);
		
		if($this->value == '') {
			$this->errorCode = 1;
			$this->errorMessage = 'path-string is empty';
		} else {
			if(file_exists($this->value)) {
				if(is_dir($this->value)) {
					$this->errorCode = 2;
					$this->errorMessage = 'path is directory';	
				} else	
					return True;	
			} else {
				if(!file_exists(dirname($this->value))) {					
					$this->errorCode = 3;
					$this->errorMessage = 'path doesn\'t exists';	
				} else 
					return True;
								
			}
		}
		return False;
	}
};

/**
  *	@author Morris Jobke
  *	@since	0.4
  *
  *	class to handle 'mimetype_detection_mode' option
  *
  *	errorCodes:
  *		1	value isn't correct
  */
class MimetypeDetectionModeOption extends BaseOption {
	public function check($v) {
		$this->setValue($v);
		
		if(in_array($this->value, $this->possibleValues))
			return True;
		else {
			$this->errorCode = 1;
			$this->errorMessage = 'value isn\'t correct';
			return False;
		}
	}
};

/**
  *	@author Morris Jobke
  *	@since	0.4
  *
  *	class to handle 'use_x_sendfile' option
  */
class UseXSendfileOption extends BaseOption {
	public function check($v) {
		if(!$this->enabled) {
			$this->value = $this->defaultValue;
			return True;
		}
		$bool = array('False', 'False', '0');
		$this->value = in_array($v, $bool) ? False : (boolean) $v;
		
		return True;
	}

	public function checkAvailablity() {
		$this->enabled = False;
		//TODO
	}
};

/**
  *	@author Morris Jobke
  *	@since	0.4
  *
  *	class to handle 'commands_path' option
  *
  *	errorCodes:
  *		1 	path-string is empty
  *		2	path is file  
  *		3	path doesn't exists
  */
class CommandsPathOption extends BaseOption {
	public function check($v) {
		$this->setValue('../'.$v);
		
		if($this->value == '') {
			$this->errorCode = 1;
			$this->errorMessage = 'path-string is empty';
		} else {
			if(file_exists($this->value)) {
				if(is_dir($this->value))
					return True;
				else {
					$this->errorCode = 2;
					$this->errorMessage = 'path is file';				
				}
			} else {
				$this->errorCode = 3;
				$this->errorMessage = 'path doesn\'t exists';				
			}
		}
		return False;
	}
};

/**
  *	@author Morris Jobke
  *	@since	0.4
  *
  *	class to handle 'commands' option
  *
  *	errorCodes:
  *		1	couldn't read commands_path dir
  *		2	incorrect command name or commands_path is incorrect
  */
class CommandsOption extends BaseOption {
	public function check($v) {
		$commands = $this->getCommands();
		if($commands == False) {			
			$this->errorCode = 1;
			$this->errorMessage = 'couldn\'t read commands_path dir';
			return False;
		}
		$checkedValues = array();
		foreach($v as $command) {
			if(in_array($command, $commands))
				$checkedValues[] = $command;
			else {
				$this->errorCode = 2;
				$this->errorMessage = 'incorrect command name or '.
					'commands_path is incorrect';
				return False;
			}
		}
		$this->setValue($checkedValues);
		return True;
	}
	
	/**
	  *	reads commands_path dir and extracts importable files
	  +	@return	array	list of filenames
	  */
	public function getCommands() {
		global $c;
		$path = $c->getValue('commands_path');
		if(!file_exists($path) || !is_dir($path))
			return False;
		else {
			$h = @opendir($path);
			$commands = array();
			if(is_resource($h)) {
				while( ($f = readdir($h)) !== false ) {
					if(preg_match('!^\.{1,2}$!', $f))
						continue;
					if(preg_match('!.*~$!', $f))
						continue;
					if(!is_dir($path.$f)) {
						if(strlen($f) >= 4 and substr($f, -4) == '.php') {
							$commands[] = substr($f,0,-4);
						}
					}
				}
				closedir($h);
			}
			return $commands;
		}
	}
};

/**
  *	@author Morris Jobke
  * @since	0.4
  *
  *	class to handle whole config file generation process
  */
class Config {
	protected $options = array();
	protected $errors = array();
	
	/**
	  *	adds option to config
	  */
	public function addOption($o) {
		$this->options[$o->getName()] = $o;
	}
	
	/**
	  *	generates PHP config file
	  *	@return	file content
	  */
	public function generate() {
		if($this->errors != array())
			return array('error' => True, 'result' => $this->errors);
		$output = '';
		$output .= "< ?php2\n";
		foreach($this->options as$k => $o) {	
			if(!$o->hasError()) {		
				$output .= "SmartWFM_Registry::set('".$o->getName();
				$output .= "', ".$o->getPHPValue().");\n";
			}
		}
		$output .= "?>\n";
		return array('error' => False, 'result' => $output);
	}
	
	/**
	  * parses input array
	  * @params	input	associative array (option name - value)
	  */
	public function parse($input) {
		foreach($input as $k => $v) {
			if(array_key_exists($k, $this->options)) {
				if(!$this->options[$k]->check($v)) {
					$this->errors[$k] = $this->options[$k]->getError();
				}
			} else {
				if(!array_key_exists('general', $this->errors))
					$this->errors['general'] = array('incorrectKey' => array());
				$this->errors['general']['incorrectKey'][] = $k;
			}				
		}
	}
	
	public function getValue($k) {
		if(array_key_exists($k, $this->options))
			return $this->options[$k]->getValue();
		return False;
	}
};


$c = new Config();
$c->addOption( new BasePathOption(
	'basepath', 
	'string', 
	'/var/www',
	'Basepath',
	'root path of SWFM' 
) );
$c->addOption( new SettingFilenameOption(
	'setting_filename', 
	'string', 
	'/tmp/.smartwfm.ini',
	'setting filename',
	'filename for settings file'
) );
$c->addOption( new MimetypeDetectionModeOption(
	'mimetype_detection_mode', 
	'string', 
	'internal', 
	'MIMETYPE detection mode',
	'kind of MIMETYPE detection',
	array('internal', 'cmd_file', 'file')
) );
$c->addOption( new UseXSendfileOption(
	'use_x_sendfile',
	'boolean',
	'False',
	'use x-sendfile',
	'enables x-sendfile mode'
) );
$c->addOption( new CommandsPathOption(
	'commands_path', 
	'string', 
	'commands/',
	'commands path',
	'path to commands directory'
) );
$c->addOption( new CommandsOption(
	'commands', 
	'array', 
	array(),
	'enabled commands',
	'commands ...'
) );

$c->parse($_GET);
// DEBUG
$r = $c->generate();
if(!$r['error'])
	echo '<pre>'.$r['result'].'</pre>';
else
	echo '<pre>'.print_r($r['result'],1).'</pre>';
#echo '<pre>'.print_r($c,1).'</pre>';
// DEBUG END


?>