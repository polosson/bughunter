	<script src="js/app/install.ctrl.js"></script>
	<style>
		#msg { top: 65px !important; }
		.settings-content { padding: 5px 5px 20px 20px; }
		label { width: 200px; }
		input { width: 300px !important; }
	</style>
</head>
<body>
	<header class="clearfix">

		<div id="loadAjax">
			<i class="fa fa-spinner fa-4x fa-spin"></i>
		</div>

		<nav class="cl-effect-13">
			<div id="logo">
				<img src="gfx/logo.png" alt="logo_bughunter"/>
			</div>
		</nav>
	</header>
	<div id="msg"></div>

	<main id="content" ng-controller="installCtrl">
		<h1>FIRST INSTALLATION</h1>
		<p>
			It seems that the configuration file of Bughunter is <b>missing or unreadable</b>.<br />
			If you <b>already have installed</b> the Bughunter, there is a problem with configuration file <code>config/config.php</code>.<br />
			Please check its existence, and/or accessibility for Apache user.
		</p>
		<p>
			If you run Bughunter <b>for the first time</b>, you can fill the informations below, and click on <b>"install"</b> button.<br />
			This will create the configuration file for you.
		</p>
		<div class="settings-section effect2">
			<div class="settings-content">
				<h3>MySQL DATABASE CREDENTIALS</h3>
				<label for="host">MySQL host:</label>	  <input type="text" id="host" ng-model="sql.host" /><br />
				<label for="user">MySQL user:</label>	  <input type="text" id="user" ng-model="sql.user" autofocus /><br />
				<label for="pass">MySQL password:</label> <input type="password" id="pass" ng-model="sql.pass" /><br />
				<label for="dbnm">MySQL DB name:</label>  <input type="text" id="dbnm" ng-model="sql.dbnm" />
				<button class="btn-success" ng-click="launchInstall()">INSTALL</button>
			</div>
		</div>
	</main>
</body>
</html>