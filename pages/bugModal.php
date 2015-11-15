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
		<div class="modal-close" ng-click="closeBugModal()"><i class="fa fa-close fa-3x"></i></div>
		<div class='modal-container'>
			<h2 id="modal-title">
				<span id="id_bug"># {{bug.id}}</span> :
				<span ng-hide="editInfos">{{bug.title}}</span>
				<input id="input-bug-title" name="input-bug-title" type="text" ng-show="editInfos" hitenter="saveBug()" ng-model="bug.title" />
			</h2>
			<div id="app-version">
				<span class='info'><?php echo $LANG['Software_version']; ?>:</span>
				<span ng-hide="editInfos">{{bug.app_version}}</span>
				<div ng-show="editInfos">
					<input id="input-app-v" name="input-app-v" type="text" hitenter="saveBug()" ng-model="bug.app_version" />
				</div>
			</div>
			<div id="app-url">
				<span class='info'><?php echo $LANG['Software_URL']; ?>: </span>
				<span ng-hide="editInfos"><a ng-href="{{bug.app_url}}">{{bug.app_url | formaturl}}</a></span>
				<div ng-show="editInfos">
					<input id="input-app-url" name="input-app-url" type="text" hitenter="saveBug()" ng-model="bug.app_url" />
				</div>
			</div>
			<ul>
				<li id="priority">
					<span><?php echo $LANG['Priority']; ?></span>
					<span ng-class="{'highest':bug.priority == '4', 'high':bug.priority == '3', 'middle':bug.priority == '2', 'low':bug.priority == '1'}" ng-hide="modeAdmin">{{bug.priority}}</span>

					<div class="triangle-down" ng-show="modeAdmin"></div>
					<select class="sl-priority sl-middle" id="sl-mod-priority" ng-style="{'background-color': priorities[bug.priority].color}" ng-model="bug.priority" ng-show="modeAdmin"
							ng-change="saveBug()">
						<option ng-repeat="prio in priorities" ng-style="{'background-color': prio.color}" ng-value="{{prio.priority}}">{{prio.priority}}</option>
					</select>
				</li>
				<li id="label">
					<span><?php echo $LANG['Label']; ?></span>
					<span ng-style="{'background-color': getLabelColor(bug.FK_label_ID)}" ng-hide="modeAdmin">{{bug.label.name}}</span>

					<div class="triangle-down" ng-show="modeAdmin"></div>
					<select class="sl-label" id="sl-mod-label" ng-style="{'background-color': getLabelColor(bug.FK_label_ID)}" ng-model="bug.FK_label_ID" ng-show="modeAdmin"
							ng-options="label.id as label.name for label in labels" ng-change="saveBug()">
					</select>
				</li>
				<li id="assignee">
					<span><?php echo $LANG['Assignee']; ?></span>
					<span ng-style="{'background-color': (bug.dev.id == 0) ?'#DDD':'#FFF'}" ng-hide="modeAdmin">{{bug.dev.pseudo}}</span>

					<div class="triangle-down" ng-show="modeAdmin"></div>
					<select class="sl-assignee" id="sl-mod-assignee" ng-style="{'background-color': (bug.FK_dev_ID == 0) ?'#DDD':'#FFF'}" ng-model="bug.FK_dev_ID" ng-show="modeAdmin"
							ng-options="dev.id as dev.pseudo for dev in devs" ng-change="saveBug()">
					</select>
				</li>
				<li id="killing">
					<button class="btn-action" style="margin-top: 30px;" ng-show="modeAdmin" ng-click="killBug()"><?php echo $LANG['Btn_kill_bug']; ?></button>
					<div class="text-danger" style="margin-top: 30px;" ng-show="bug.closed === '1'"><i class="fa fa-bug fa-2x fa-spin"></i><?php echo $LANG['Killed']; ?></div>
				</li>
			</ul>
		</div>
		<div class="edit-info" ng-show="modeAdmin">
			<button class="btn-action" ng-hide="editInfos || editDescr || editComment" ng-click="initEdit()"><?php echo $LANG['Btn_edit_info']; ?></button>
			<button class="btn-warning" ng-show="editInfos" ng-click="cancelEdit()"><?php echo $LANG['Btn_cancel']; ?></button>
			<button class="btn-success" ng-show="editInfos" ng-click="saveBug()"><?php echo $LANG['Btn_save']; ?></button>
		</div>
		<div id="ajaxBugMsg"></div>
	</div>
	<div class="modal-container-text" style="width: 66%;">
		<div class="info-post">
			<span class="pull-right text-muted">{{bug.date | date: 'dd/MM/yyyy - HH:mm'}}</span>
			<span class="group-btn" ng-show="modeAdmin">
				<button class="btn-action"  ng-hide="editInfos || editDescr || editComment"  ng-click="initUpdDescr()"><?php echo $LANG['Btn_edit']; ?></button>
				<button class="btn-warning" ng-show="editDescr" ng-click="cancelEdit()"><?php echo $LANG['Btn_cancel']; ?></button>
				<button class="btn-success" ng-show="editDescr" ng-click="saveBug()"><?php echo $LANG['Btn_save']; ?></button>
			</span>
			<div class="author">
				<?php echo $LANG['Opened_by']; ?> <span id="bug-author">{{bug.author}}</span>
			</div>
			<p id="modal-desc" class="clearfix" ng-bind-html="nl2br(bug.description)" ng-hide="editDescr"></p>
			<textarea ng-model="bug.description" ng-show="editDescr"></textarea>
		</div>
		<div class="info-post" ng-repeat="comment in bug.comment">
			<span class="pull-right text-muted">{{comment.date | date: 'dd/MM/yyyy - HH:mm'}}</span>
			<span class="group-btn" ng-show="modeAdmin">
				<button class="btn-action"  ng-hide="editInfos || editDescr || editComment" ng-click="initUpdComment(comment.id)"><?php echo $LANG['Btn_edit']; ?></button>
				<button class="btn-delete"  ng-hide="editComment" ng-click="deleteComment($index)"><?php echo $LANG['Btn_delete']; ?></button>
				<button class="btn-warning" ng-show="editComment == comment.id" ng-click="cancelUpdComment($index)"><?php echo $LANG['Btn_cancel']; ?></button>
				<button class="btn-success" ng-show="editComment == comment.id" ng-click="saveUpdComment($index)"><?php echo $LANG['Btn_save']; ?></button>
			</span>
			<div class="author">
				<?php echo $LANG['Comment_by']; ?> <span class="dev">{{comment.dev.pseudo}}</span>
			</div>
			<p class="answer" ng-bind-html="nl2br(comment.message)" ng-hide="editComment == comment.id"></p>
			<textarea ng-model="comment.message" ng-show="editComment == comment.id"></textarea>
		</div>
	</div>
	<div class="wrapper-img onDrag" nv-file-drop uploader="uploader">
		<input type="file" class="hide" nv-file-select uploader="uploader" id="uploadInput" />
		<div class="prev-img" ng-show="modeAdmin">
			<button class="btn-action" onClick="$('#uploadInput').click()" title="<?php echo $LANG['Drag_drop'].' '.$LANG['Images_files'].' '.$LANG['Here']; ?>"><i class="fa fa-plus"></i> <?php echo $LANG['Btn_add_images']; ?></button>
		</div>
		<div class="prev-img" ng-repeat="img in bug.img">
			<span class="edit-img" ng-show="modeAdmin" ng-click="deleteImg(img)"><span class="btn-delete"><?php echo $LANG['Btn_delete']; ?></span></span>
			<img ng-src="data/screens/{{img}}" title="{{img}} | click to enlarge" ng-click="showImg(img)" />
		</div>
	</div>
	<div class="modal-send-answer" ng-show="modeAdmin">
		<textarea name="answer_dev" rows="5" ng-model="newComment" placeholder="<?php echo $LANG['Write_comment']; ?>"></textarea>
		<button class="btn-action" ng-click="addComment()" ng-hide="editInfos || editDescr || editComment"><?php echo $LANG['Btn_add_comment']; ?></button>
	</div>
	<div class="last_action">
		<span class="title_action"><?php echo $LANG['Last_action']; ?>:</span>
		<span ng-hide="bug.last_action" class="text-muted">??</span>
		{{bug.last_action | date: 'dd/MM/yyyy - HH:mm'}}
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