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
include('../init.php');
?>
<div id="modal-settings">
	<div class="modal-header">
		<div class="modal-close" ng-click="closeSettingsModal()"><i class="fa fa-close fa-3x"></i></div>
		<div class="settings-message text-danger">{{ajaxMsg}}</div>
		<h1><?php echo $LANG['Title_settings']; ?></h1>
	</div>
	<div class="modal-body wrapper-settings">
		<div class="settings-section effect2">
			<div class="settings-content">
				<h2><?php echo $LANG['Title_labels']; ?></h2>
				<div class="settings-form">
					<label for="name"><?php echo $LANG['Name']; ?></label> <input id="name" name="name" type="text" ng-model="newLabel.name" />
					<label for="picker"><?php echo $LANG['Color']; ?></label> <input class="picker" id="picker" name="color" type="text" colorpicker="hex" ng-model="newLabel.color" />
					<button class="btn-success" ng-click="addLabel()"><?php echo $LANG['Btn_add']; ?></button>
				</div>

				<table>
					<thead>	<tr> <th><?php echo $LANG['Name']; ?></th> <th><?php echo $LANG['Color']; ?></th> <th></th> </tr> </thead>
					<tbody>
						<tr ng-repeat="label in config.labels">
							<td>
								<span ng-hide="editLabel === label.id" title="#{{label.id}}">{{label.name}}</span>
								<input type="text" ng-show="editLabel === label.id" ng-model="label.name" /></td>
							<td>
								<div ng-style="{'background-color': label.color}" class="color-sample"></div>
								<span ng-hide="editLabel === label.id">{{label.color}}</span>
								<input type="text" ng-show="editLabel === label.id" class="picker" colorpicker="hex" ng-model="label.color" />
								<div style="clear: both;"></div>
							</td>
							<td>
								<button ng-hide="editLabel" ng-click="initEdit('labels', label.id)" class="btn-action">Edit</button>
								<button ng-hide="editLabel || label.name === 'none'" ng-click="deleteItem('labels', $index)" class="btn-delete"><?php echo $LANG['Btn_delete']; ?></button>
								<button ng-show="editLabel === label.id" class="btn-success" ng-click="saveEdit('labels', label.id)"><?php echo $LANG['Btn_update']; ?></button>
								<button ng-show="editLabel === label.id" class="btn-warning" ng-click="cancelEdit('labels', label.id)"><?php echo $LANG['Btn_cancel']; ?></button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="settings-section effect2">
			<div class="settings-content">
				<h2><?php echo $LANG['Title_devs']; ?></h2>
				<div class="settings-form">
					<label for="dev_input"><?php echo $LANG['Pseudo']; ?></label> <input id="dev_input" name="pseudo" type="text" ng-model="newDev.pseudo" />
					<label for="email"><?php echo $LANG['Email']; ?></label> <input id="email" name="mail" type="email" ng-model="newDev.mail" />
					<button class="btn-success" ng-click="addDev()"><?php echo $LANG['Btn_add']; ?></button>
				</div>

				<table>
					<thead>
						<tr> <th><?php echo $LANG['Pseudo']; ?></th> <th><?php echo $LANG['Email']; ?></th> <th></th> </tr>
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
								<button ng-hide="editDev || dev.id === '0'" ng-click="deleteItem('devs', $index)" class="btn-delete">Delete</button>
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
				<h2><?php echo $LANG['Title_project_infos']; ?></h2>
				<div class="settings-form">
					<label for="project_name" style="width: 120px;"><?php echo $LANG['Name']; ?></label>
					<input placeholder="project name" type="text" id="project_name" ng-model="projInfo.project_name" />
					<br />
					<label for="project_type" style="width: 120px;"><?php echo $LANG['Type']; ?></label>
					<select ng-options="type as type for type in projTypes" ng-model="projInfo.project_type"></select>
					<br />
					<label for="project_git" style="width: 120px;"><?php echo $LANG['Git_repo']; ?></label>
					<input placeholder="git repository url" style="width: 57%;" type="text" id="project_git" ng-model="projInfo.git_repo" />
					<br />
					<label for="enable_notify" style="width: 120px;"><?php echo $LANG['Notify_enable']; ?></label>
					<div class="btn-group" id="enable_notify" title="<?php echo $LANG['Notify_help']; ?>">
						<button class="btn" ng-class="{'btn-primary': projInfo.enable_notify,  'btn-default': !projInfo.enable_notify}" ng-click="projInfo.enable_notify = true">
							<?php echo $LANG['Enabled']; ?>
						</button>
						<button class="btn" ng-class="{'btn-primary': !projInfo.enable_notify, 'btn-default': projInfo.enable_notify}" ng-click="projInfo.enable_notify = false">
							<?php echo $LANG['Disabled']; ?>
						</button>
					</div>
				</div>
				<div class="text-left" style="margin: 15px 0 0 130px;">
					<button class="btn-success" ng-show="config.authAdmin" ng-click="saveProject()"><?php echo $LANG['Btn_save']; ?></button>
					<button class="btn-warning" ng-show="config.authAdmin" ng-click="cancelProject()"><?php echo $LANG['Btn_cancel']; ?></button>
				</div>
			</div>
		</div>

		<div class="settings-section effect2">
			<div class="settings-content">
				<h2><?php echo $LANG['Title_admin']; ?></h2>
				<div class="settings-form">
					<h4><?php echo $LANG['Title_password_change']; ?></h4>
					<input placeholder="<?php echo $LANG['Password_change']; ?>" type="password" class="passwInput">
					<input placeholder="<?php echo $LANG['Password_confirm']; ?>" type="password" class="passwInput">
					<button class="btn-success" ng-click="changePassword()" style="max-width: 220px;"><?php echo $LANG['Btn_update_password']; ?></button>
				</div>
				<div class="settings-form">
					<h4><?php echo $LANG['Title_language_choice']; ?></h4>
					<select ng-options="langue as langue for langue in config.av_lang" ng-model="current_lang" style="margin-right: 20px;"></select>
					<button class="btn-success" ng-show="config.authAdmin" ng-click="saveLanguage()"><?php echo $LANG['Btn_save']; ?></button>
				</div>
				<div class="settings-form text-center" style="margin-top: 20px;">
					<h4 class="text-left"><?php echo $LANG['Title_service']; ?></h4>
					<button class="btn-warning" style="max-width: none; width: 45%; padding: 3px;" ng-click="getBackup()"
							title="<?php echo $LANG['Help_backup_all']; ?>"><?php echo $LANG['Btn_backup_all']; ?></button>
					<button class="btn-delete" style="max-width: none; width: 45%; padding: 3px;" ng-click="resetBughunter()"
							title="<?php echo $LANG['Help_reset']; ?>"><?php echo $LANG['Btn_reset_BH']; ?></button>
				</div>
			</div>
		</div>

	</div>
</div>