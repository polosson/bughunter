<?php
require('init.php');
$l = new Liste();
$l->getListe('t_bugs', "id", "priority", "DESC", "closed", "=", 0);
$countAlive = $l->countResults();
$l->getListe('t_bugs', "id", "priority", "DESC", "closed", "=", 1);
$countKilled = $l->countResults();
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
	<link rel="stylesheet" href="css/normalize.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
	<link rel="stylesheet" href="css/font-awesome.min.css" type="text/css"/>
	<!-- JAVASCRIPT-->
	<script src="js/Jquery-2.0.1.min.js"></script>
	<script src="js/Angular.min.js"></script>
	<script src="js/Angular-bootstrap.min.js"></script>
	<script src="js/app/Utils.js"></script>
	<script src="js/app/bughunter.app.js"></script>
	<script src="js/app/bugs.ctrl.js"></script>
	<script>
		var startCountAlive  = <?php echo $countAlive; ?>;
		var startCountKilled = <?php echo $countKilled; ?>;
	</script>
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
				<li>
					<a href="#" ng-click="disconnect()">disconnect</a>
				</li>
				<li>
					<a href="#">login</a>
				</li>
				<li ng-class="{'on': page === 'settings'}">
					<a href="#" ng-click="showPage('settings')">settings</a>
				</li>
				<li ng-class="{'on': page === 'killed'}">
					<a href="#" ng-click="showPage('killed')"><span class="killed circle">{{countKilled}}</span> killed bugs</a>
				</li>
				<li ng-class="{'on': page === 'alive'}">
					<a href="#" ng-click="showPage('alive')"><span class="alive circle">{{countAlive}}</span> bugs alive</a>
				</li>
			</ul>
		</nav>
	</header>
	<div id="msg" class=""></div>

	<main id="content" ng-controller="bugsCtrl">
		<?php include('pages/bugsList.php'); ?>
	</main>

</body>
</html>