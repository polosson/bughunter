	<script src="js/app/install.ctrl.js"></script>
	<style>
		#msg { top: 65px !important; }
		h2 { color: #3A67A8 !important; text-transform: none !important; }
		li { color: #999999; }
		li.current { color: #FFFFFF; }
		li.success { color: #79AE3A; }
		.settings-content { padding: 5px 5px 20px 20px; }
		label { width: 200px; }
		input { width: 300px !important; }
	</style>
</head>
<body ng-controller="installCtrl">
	<header class="clearfix">

		<div id="loadAjax">
			<i class="fa fa-spinner fa-4x fa-spin"></i>
		</div>

		<nav class="cl-effect-13">
			<div id="logo">
				<img src="gfx/logo.png" alt="logo_bughunter"/>
			</div>
			<ul>
				<li ng-class="{'current':step===3, 'success':step>3}">3 - project</li>
				<li ng-class="{'current':step===2, 'success':step>2}">2 - database</li>
				<li ng-class="{'current':step===1, 'success':step>1}">1 - config</li>
			</ul>
		</nav>
	</header>
	<div id="msg"></div>

	<main id="content">
		<h1>INSTALLATION WIZARD</h1>
		<div class="row" ng-show="step === 1">
			<div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
				<h2>1 - Configuration file</h2>
				<p>
					It seems that the configuration file of Bughunter is <b>missing or unreadable</b>.
					If you <b>already have installed</b> the Bughunter, there is a problem with configuration
					file <code>config/config.php</code>.
					Please check its existence and/or accessibility for Apache user.
				</p>
				<p>
					If you run Bughunter for the <b>first time</b>, then it's normal and you just need to fill
					the informations below, and click on <b>"NEXT"</b> button.
					This will create the configuration file for you.
				</p>
				<div class="effect2">
					<div class="settings-content">
						<h3>MySQL DATABASE CONNECTION INFORMATIONS</h3>
						<label for="host">MySQL host:</label>	  <input type="text" id="host" ng-model="sql.host" /><br />
						<label for="user">MySQL user:</label>	  <input type="text" id="user" ng-model="sql.user" autofocus /><br />
						<label for="pass">MySQL password:</label> <input type="password" id="pass" ng-model="sql.pass" /><br />
						<label for="dbnm">MySQL DB name:</label>  <input type="text" id="dbnm" ng-model="sql.dbnm" />
						<button class="btn-success" ng-click="stepInstall()">NEXT</button>
					</div>
				</div>
			</div>
		</div>
		<div class="row" ng-show="step === 2">
			<div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
				<h2>2 - Database creation</h2>
				<p>
					OK, we have our config file. Now we must create the database if it does not exists yet. You can
					<b>skip</b> this step if you already have your database created on your MySQL host. You can also
					go to <b>previous</b> step to rename the database.
				</p>
				<div class="effect2">
					<div class="settings-content">
						<h3>MySQL infos:</h3>
						MySQL host : <b>{{sql.host}}</b><br />
						MySQL user : <b>{{sql.user}}</b><br />
						Database name : <b>{{sql.dbnm}}</b><br />
						<div class="text-center">
							<button class="btn-action" ng-click="prevStep()">PREV</button>
							<button class="btn-warning" ng-click="skipStep()">SKIP</button>
							<button class="btn-success" ng-click="stepInstall()" style="margin-left: 30px;">NEXT</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row" ng-show="step === 3">
			<div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
				<h2>3 - Project informations</h2>
				<p>
					One last optionnal step. Specify a <b>password</b> for admin login. You can also give some informations about
					the project you will hunt bugs for. You can <b>skip</b> this step and begin using Bughunter without
					these informations (which can be filled later, in "settings" modal).
				</p>
				<p>
					<b>Note:</b> If you skip this step, the <b>default password</b> will be used for admin login. This default
					password is <code>bhadmin</code>.
				</p>
				<div class="effect2">
					<div class="settings-content">
						<h3>Informations:</h3>
						<label for="admPassw">ADMIN password:</label>
						<input type="password" id="admPassw" ng-model="conf.password_access" />
						confirm:
						<input type="password" ng-model="admpwconfirm" />
						<br />
						<label for="projName">Project name:</label>	  <input type="text" id="projName" ng-model="conf.project_name" autofocus /><br />
						<label for="projType">Project type:</label>
						<select ng-options="type as type for type in ['open-source','private']" id='projType' ng-model="conf.project_type"></select><br />
						<label for="gitRepo">Git repository:</label>  <input type="text" id="gitRepo" ng-model="conf.git_repo" />
						<div class="text-center">
							<button class="btn-warning" ng-click="skipStep()">SKIP</button>
							<button class="btn-success" ng-click="stepInstall()" style="margin-left: 30px;">NEXT</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row" ng-show="step === 4">
			<div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
				<h2>DONE !</h2>
				<p>
					You can now <a href="./">refresh the page</a> and start using Bughunter!
				</p>
				<a href="./"><button class="btn-success">START BUG HUNTING</button></a>
			</div>
		</div>
	</main>
</body>
</html>