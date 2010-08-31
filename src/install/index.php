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
?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="application/xhtml+xml;charset=utf-8" />
		<title>Installation SWFM - PHP Backend</title>		
		<script type="text/javascript" src="jquery.js"></script>
		<script type="text/javascript" src="install.js"></script>
		<link rel="stylesheet" media="all" href="style.css" />
		<script type="text/javascript">
		<!--		
		
		
		function save() {}
		//-->
		</script>
	</head>
<body onload="init()">
	<center>
	<div id="wrapper">
	
	<h1>Installation SWFM - PHP Backend</h1>
	<img src="images/ajax-loader.gif" title="Loading..." id="loading"/>
	<div id="notify"></div>
	
	
	<div id="settings">
		<form action="save.php" method="post">
			<p id="basepath">
				<label for="basepath">basepath of SWFM installation - access only under this directory</label><br />
				<input name="basepath" type="text" size="50" onchange="checkBasePath()" />
				<img id="basepath-check" src="images/false.png"/>
			</p>
			<p>
				<label for="commandspath">path to commands directory</label><br />
				<input name="commandspath" type="text" size="50" onchange="checkCommandsPath()" />
				<img id="commandspath-check" src="images/false.png"/>
			</p>
			<p id="commands">
			</p>
			<p>
				<label for="mimetype_detection_mode">mimetype_detection_mode</label><br />
				<select name="mimetype_detection_mode" size="1">
					<option value="internal">internal</option>
					<option value="cmd_file">cmd_file</option>
					<option value="file">file</option>
				</select>
			</p>
			<p>
				<label for="filesystem_type">type of filesystem</label><br />
				<select name="filesystem_type" size="1">
					<option value="local">local</option>
					<option value="afs">afs</option>
				</select>
			</p>
			<!--<p>
				<label for="use_x_sendfile">use_x_sendfile</label><br />
				<input name="use_x_sendfile" type="checkbox" value="true" />
			</p>-->
			<input type="button" value="save config" onclick="save()" />
		
		</form>
	</div>
	
	
	</div>
	</center>
</body>
</html>