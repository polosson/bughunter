<?php

?>
<div id="modal-settings">
	<div class="modal-header">
		<div class="settings-message text-danger">{{ajaxMsg}}</div>
		<div class="pull-right" style="margin-top: 20px;">
			<button class="btn-success" ng-show="config.authAdmin">SAVE</button>
			<button class="btn-warning" ng-click="closeSettingsModal()">CANCEL</button>
		</div>
		<h1>SETTINGS</h1>
	</div>
	<div class="modal-body wrapper-settings">

		<div class="settings-section effect2">
			<div class="settings-content">
				<h2>Labels</h2>
				<div class="settings-form">
					<label for="name">Name</label><input id="name" name="name" type="text">
					<label for="picker">Color #</label><input class="picker" id="picker" name="color" type="text">
					<button class="btn-success">Add</button>
				</div>

				<table>
					<thead>	<tr> <th>Name</th> <th>Color</th> <th></th> </tr> </thead>
					<tbody>
						<tr ng-repeat="label in config.labels">
							<td>
								<span ng-hide="editLabel === label.id" title="#{{label.id}}">{{label.name}}</span>
								<input type="text" ng-show="editLabel === label.id" ng-model="label.name" /></td>
							<td>
								<div ng-style="{'background-color': label.color}" class="color-sample"></div>
								<span ng-hide="editLabel === label.id">{{label.color}}</span>
								<input type="text" ng-show="editLabel === label.id" class="picker" ng-model="label.color" />
								<div style="clear: both;"></div>
							</td>
							<td>
								<button ng-hide="editLabel" ng-click="initEdit('labels', label.id)" class="btn-action">Edit</button>
								<button ng-hide="editLabel || label.name === 'none'" ng-click="deleteItem('labels', label.id)" class="btn-delete">Delete</button>
								<button ng-show="editLabel === label.id" class="btn-success" ng-click="saveEdit('labels', label.id)">Update</button>
								<button ng-show="editLabel === label.id" class="btn-warning" ng-click="cancelEdit('labels', label.id)">Cancel</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="settings-section effect2">
			<div class="settings-content">
				<h2>Devs</h2>
				<div class="settings-form">
					<label for="dev_input">Pseudo </label> <input id="dev_input" name="pseudo" type="text" />
					<label for="email">E-mail</label> <input id="email" name="mail" type="email" />
					<button class="btn-success">Add</button>
				</div>

				<table>
					<thead>
						<tr> <th>Pseudo</th> <th>E-mail</th> <th></th> </tr>
					</thead>
					<tbody>
						<tr ng-repeat="dev in config.devs">
							<td>
								<span ng-hide="editDev === dev.id" title="#{{dev.id}}">{{dev.pseudo}}</span>
								<input type="text" ng-show="editDev === dev.id" ng-model="dev.pseudo" />
							</td>
							<td ng-class="{'stripedBG': dev.id === '0'}">
								<span ng-hide="editDev === dev.id">{{dev.mail}}</span>
								<input type="email" ng-show="editDev === dev.id" ng-model="dev.mail" />
							</td>
							<td>
								<button ng-hide="editDev" ng-click="initEdit('devs', dev.id)" class="btn-action">Edit</button>
								<button ng-hide="editDev || dev.id === '0'" ng-click="deleteItem('devs', dev.id)" class="btn-delete">Delete</button>
								<button ng-show="editDev === dev.id" class="btn-success" ng-click="saveEdit('devs', dev.id)">Update</button>
								<button ng-show="editDev === dev.id" class="btn-warning" ng-click="cancelEdit('devs', dev.id)">Cancel</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="settings-section effect2">
			<div class="settings-content">
				<h2>Project infos</h2>
				<div class="settings-form text-center">
					<label for="project_name">Name</label> <input placeholder="project name" type="text" id="project_name" ng-model="config.globalConf.project_name.value" />
					<label for="project_type">Type</label> <select ng-options="type as type for type in projTypes" ng-model="config.globalConf.project_type.value"></select>
					<br />
					<label for="project_git">Git repo</label>
					<input placeholder="git repository url" style="width: 57%;" type="text" id="project_git" ng-model="config.globalConf.git_repo.value" />
				</div>
			</div>
		</div>

		<div class="settings-section effect2">
			<div class="settings-content">
				<h2>Password change</h2>
				<div class="settings-form text-center">
					<input placeholder="new password" type="password" class="passwInput">
					<input placeholder="confirm password" type="password" class="passwInput">
					<button class="btn-success" ng-click="changePassword()">Update PW</button>
				</div>
			</div>
		</div>

	</div>
</div>