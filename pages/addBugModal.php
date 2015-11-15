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
<div class="modal-body">
	<div class="header-modal">
		<div class="modal-close" ng-click="closeAddBugModal()"><i class="fa fa-close fa-3x"></i></div>
		<div class='modal-container'>
			<h2 id="modal-title"><?php echo $LANG['Title_add_bug']; ?></h2>
		</div>
	</div>
	<div class="addbug">
		<fieldset>
			<legend><?php echo $LANG['Add_bug_infos']; ?></legend>
			<label for="bTitle"><?php echo $LANG['Title']; ?> *</label><br />
			<input type="text" id="bTitle" ng-model="bug.title" autofocus /><br />
			<label for="appV"><?php echo $LANG['Software_version']; ?></label><br />
			<input type="text" id="appV" ng-model="bug.app_url" /><br />
			<label for="appUrl"><?php echo $LANG['Software_URL']; ?></label><br />
			<input type="text" id="appUrl" ng-model="bug.app_version" /><br />

			<div class="select-add">
				<label for="priority"><?php echo $LANG['Priority']; ?></label>
				<div class="triangle-down"></div>
				<select class="sl-priority sl-middle" id="sl-mod-priority" ng-style="{'background-color': priorities[bug.priority].color}" ng-model="bug.priority">
					<option ng-repeat="prio in priorities" ng-style="{'background-color': prio.color}" ng-value="{{prio.priority}}">{{prio.priority}}</option>
				</select>
			</div>
			<div class="select-add">
				<label for="name_label"><?php echo $LANG['Label']; ?></label>
				<select ng-style="{'background-color': getLabelColor(bug.FK_label_ID)}" ng-model="bug.FK_label_ID"
						ng-options="label.id as label.name for label in labels">
				</select>
			</div>
			<div class="select-add">
				<label for="assignee"><?php echo $LANG['Assign_dev']; ?></label>
				<select ng-style="{'background-color': (bug.FK_dev_ID == 0) ?'#DDD':'#FFF'}" ng-model="bug.FK_dev_ID"
						ng-options="dev.id as dev.pseudo for dev in devs">
				</select>
			</div>
			<br /><br />
			<label for="descr"><?php echo $LANG['Description']; ?> *</label><br />
			<textarea id="descr" rows="6" placeholder="<?php echo $LANG['Add_bug_descr']; ?>" ng-model="bug.description"></textarea>
			<br />
			<button class="btn-success" style="width: 100%;" ng-click="submitNewBug()"><?php echo $LANG['Btn_submit']; ?></button>
			<div class="text-center text-danger" style="padding-top: 5px;"><b>{{ajaxMsg}}</b>&nbsp;</div>
		</fieldset>

		<fieldset>
			<legend><?php echo $LANG['Add_bug_screens']; ?></legend>
			<div class="addBugUpload onDrag" nv-file-drop uploader="uploader">
				<div class="fileupload-buttonbar">
					<div class="pull-right" ng-show="uploader.queue.length > 0">
						<button class="btn-success btn-xs" ng-click="uploader.uploadAll()"><i class="fa fa-upload"></i> <?php echo $LANG['Btn_upload_all']; ?></button>
						<button class="btn-warning btn-xs" ng-click="uploader.cancelAll()"><i class="fa fa-ban"></i> <?php echo $LANG['Btn_cancel_all']; ?></button>
					</div>
					<button class="btn-action" onClick="$('#uploadInputAB').click()">
						<i class="fa fa-plus"></i> <span><?php echo $LANG['Btn_add_images']; ?></span>
					</button>
					<span class="text-muted">&nbsp;&nbsp;<?php echo $LANG['Drag_drop']; ?> <span id="imageFileWarning"><?php echo $LANG['Images_files']; ?></span> <?php echo $LANG['Here']; ?></span>
					<input type="file" class="hide" multiple nv-file-select uploader="uploader" id="uploadInputAB" />
				</div>
				<div class="progress" ng-show="uploader.queue.length > 0" style="margin: 10px 0;">
					<div class="progress-bar" role="progressbar" ng-style="{'width': uploader.progress + '%'}"></div>
				</div>
				<table role="presentation" class="table table-striped">
					<thead ng-show="uploader.queue.length > 0">
						<tr>
							<th class="text-center"><?php echo $LANG['Name']; ?></th>
							<th class="text-center">Size / Progress / Status</th>
							<th class="text-center"><?php echo $LANG['Action']; ?></th>
						</tr>
					</thead>
					<tbody class="files">
						<tr ng-repeat="item in uploader.queue">
							<td class="text-center">
								<div ng-thumb="{file: item._file, height: 100}"></div>
							</td>
							<td class="text-center" style="vertical-align: middle;">
								<strong>{{item.file.name}}</strong><br />
								{{item.file.size/1024/1024|number:2}} MB
								<div>
									<div class="progress" style="margin-bottom: 0;">
										<div class="progress-bar" role="progressbar" ng-style="{'width': item.progress + '%'}"></div>
									</div>
								</div>
								<div>
									<span ng-show="item.isUploading"><i class="fa fa-spinner fa-spin"></i> {{item.progress}}%</span>
									<span ng-show="item.isSuccess"><i class="fa fa-check"></i></span>
									<span ng-show="item.isCancel"><i class="fa fa-ban"></i></span>
									<span ng-show="item.isError"><i class="fa fa-remove"></i></span>
								</div>
							<td class="text-center" style="vertical-align: middle;">
								<button class="btn-success" ng-click="item.upload()" ng-disabled="item.isReady || item.isUploading || item.isSuccess" title="Upload">
									<i class="fa fa-upload"></i>
								</button>
								<button class="btn-warning" ng-click="item.cancel()" ng-disabled="!item.isUploading" title="Cancel">
									<i class="fa fa-ban"></i>
								</button>
								<button class="btn-delete" ng-click="item.remove()" title="Remove">
									<i class="fa fa-trash"></i>
								</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</fieldset>
	</div>
</div>

<script>
	$(function(){
		$('.onDrag')
			.off('dragenter dragleave drop')
			.on('dragenter', function(){ $(this).addClass('dragTarget'); })
			.on('dragleave drop', function(){ $(this).removeClass('dragTarget'); });
	});
</script>