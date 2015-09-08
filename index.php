<?php
/**
	Copyright (C) 2015  Azuk & Polosson

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU Affero General Public License as
	published by the Free Software Foundation, either version 3 of the
	License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Affero General Public License for more details.

	You should have received a copy of the GNU Affero General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<!DOCTYPE html>
<html lang="en" ng-app="bughunter">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>BUGHUNTER</title>
	<!-- FAVICON -->
	<link rel="icon" type="image/png" href="gfx/favicon.png" />
	<!-- CSS STYLES-->
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/normalize.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
	<link rel="stylesheet" href="css/font-awesome.min.css" type="text/css"/>
	<!-- JAVASCRIPT-->
	<script src="js/Jquery-2.0.1.min.js"></script>
	<script src="js/Angular.min.js"></script>
	<script src="js/Angular-sanitize.min.js"></script>
	<script src="js/Angular-bootstrap.min.js"></script>
	<script src="js/app/Utils.js"></script>
	<script src="js/app/bughunter.app.js"></script><?php
	if (!is_file('config/config.php')){
		include('install.php');
		die();
	} ?>
	<script src="js/app/menu.ctrl.js"></script>
	<script src="js/app/bugModal.ctrl.js"></script>
	<script src="js/app/bugsList.ctrl.js"></script>
	<script src="js/app/settings.ctrl.js"></script>
</head>
<body>
	<header class="clearfix">

		<div id="loadAjax">
			<i class="fa fa-spinner fa-4x fa-spin"></i>
		</div>

		<nav class="cl-effect-13" ng-controller="menuCtrl">
			<div id="logo">
				<img src="gfx/logo.png" alt="logo_bughunter"/>
			</div>
			<ul>
				<li ng-show="config.authAdmin">
					<a href="#" ng-click="disconnect()">disconnect</a>
				</li>
				<li ng-hide="config.authAdmin">
					<a href="#" ng-click="connectModal()">Admin</a>
				</li>
				<li ng-class="{'on': page === 'settings'}" ng-show="config.authAdmin">
					<a href="#" ng-click="showPage('settings')">settings</a>
				</li>
				<li ng-class="{'on': page === 'killed'}">
					<a href="#" ng-click="showPage('killed')"><span class="killed circle">{{count.count.killed}}</span> killed bugs</a>
				</li>
				<li ng-class="{'on': page === 'alive'}">
					<a href="#" ng-click="showPage('alive')"><span class="alive circle">{{count.count.alive}}</span> bugs alive</a>
				</li>
			</ul>
		</nav>
	</header>
	<div id="msg"></div>

	<main id="content" ng-controller="bugsCtrl">
		<?php include('pages/bugsList.php'); ?>
	</main>

</body>
</html>