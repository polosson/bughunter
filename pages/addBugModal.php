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
<div class="modal-body">
	<div class="header-modal">
		<div class="modal-close" ng-click="closeAddBugModal()"><i class="fa fa-close fa-3x"></i></div>
		<div class='modal-container'>
			<h2 id="modal-title">Add a new bug</h2>
		</div>
	</div>
	<div class="addbug">
		<fieldset>
			<legend>Bug's infos</legend>
			<label for="bTitle">Title *</label><br />
			<input type="text" id="bTitle" ng-model="bug.title" autofocus /><br />
			<label for="appV">Software version</label><br />
			<input type="text" id="appV" ng-model="bug.app_url" /><br />
			<label for="appUrl">Software URL</label><br />
			<input type="text" id="appUrl" ng-model="bug.app_version" /><br />

			<div class="select-add">
				<label for="priority">Priority</label>
				<div class="triangle-down"></div>
				<select class="sl-priority sl-middle" id="sl-mod-priority" ng-style="{'background-color': priorities[bug.priority].color}" ng-model="bug.priority">
					<option ng-repeat="prio in priorities" ng-style="{'background-color': prio.color}" ng-value="{{prio.priority}}">{{prio.priority}}</option>
				</select>
			</div>
			<div class="select-add">
				<label for="name_label">Label</label>
				<select ng-style="{'background-color': getLabelColor(bug.FK_label_ID)}" ng-model="bug.FK_label_ID"
						ng-options="label.id as label.name for label in labels">
				</select>
			</div>
			<div class="select-add">
				<label for="assignee">Assign. dev.</label>
				<select ng-style="{'background-color': (bug.FK_dev_ID == 0) ?'#DDD':'#FFF'}" ng-model="bug.FK_dev_ID"
						ng-options="dev.id as dev.pseudo for dev in devs">
				</select>
			</div>
			<br /><br />
			<label for="descr">Description *</label><br />
			<textarea id="descr" rows="6" placeholder="Describe the bug you found... Please be precise!" ng-model="bug.description"></textarea>
			<br />
			<button class="btn-success" style="width: 100%;" ng-click="submitNewBug()">SUBMIT</button>
			<div class="text-center text-danger" style="padding-top: 5px;"><b>{{ajaxMsg}}</b>&nbsp;</div>
		</fieldset>

		<fieldset>
			<legend>Bug's screenshots</legend>
			<div id="fileupload">
				<div class="fileupload-buttonbar">
					<button class="btn-action">
						<i class="fa fa-plus"></i> <span>Add images</span>
					</button>
					<span class="text-muted">&nbsp;&nbsp;or drag & drop images files here</span>
					<input type="file" name="files[]" multiple accept="image/*" class="hidden" />
				</div>
				<div class="fileupload-progress fade">
					<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
						<div class="progress-bar progress-bar-success" style="width:0%;"></div>
					</div>
					<div class="progress-extended">&nbsp;</div>
				</div>
				<table role="presentation" class="table table-striped">
					<tbody class="files"></tbody>
				</table>
			</div>
		</fieldset>
	</div>
</div>