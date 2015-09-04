
<div class="modal-body">
	<div class="header-modal">
		<div class="modal-close" ng-click="closeBugModal()"><i class="fa fa-close fa-3x"></i></div>
		<div class='modal-container'>
			<h2 id="modal-title"><span id="id_bug"># {{bug.id}}</span> : {{bug.title}}</h2>
			<div id="app-version">
				<span class='info'>software version: </span>
				{{bug.app_version}}
			</div>
			<div id="app-url">
				<span class='info'>link: </span>
				{{bug.app_url}}
			</div>
			<ul>
				<li id="priority">
					<span>Priority</span>
					<span ng-class="{'highest':bug.priority == '4', 'high':bug.priority == '3', 'middle':bug.priority == '2', 'low':bug.priority == '1'}" ng-hide="modeAdmin">{{bug.priority}}</span>

					<div class="triangle-down" ng-show="modeAdmin"></div>
					<select class="sl-priority sl-middle" id="sl-mod-priority" ng-style="{'background-color': priorities[bug.priority].color}" ng-model="bug.priority" ng-show="modeAdmin">
						<option ng-repeat="prio in priorities" ng-style="{'background-color': prio.color}" ng-value="{{prio.priority}}">{{prio.priority}}</option>
					</select>
				</li>
				<li id="label">
					<span>Label</span>
					<span ng-style="{'background-color': bug.label.color}" ng-hide="modeAdmin">{{bug.label.name}}</span>

					<div class="triangle-down" ng-show="modeAdmin"></div>
					<select class="sl-label" id="sl-mod-label" ng-style="{'background-color': getLabelColor(bug.FK_label_ID)}" ng-model="bug.FK_label_ID" ng-show="modeAdmin"
							ng-options="label.id as label.name for label in labels">
					</select>
				</li>
				<li id="assignee">
					<span>Assignee</span>
					<span style="background-color: #FFF;" ng-hide="modeAdmin">{{bug.dev.pseudo}}</span>

					<div class="triangle-down" ng-show="modeAdmin"></div>
					<select class="sl-assignee" id="sl-mod-assignee" ng-style="{'background-color': (bug.FK_dev_ID == 0) ?'#CCC':'#FFF'}" ng-model="bug.FK_dev_ID" ng-show="modeAdmin"
							ng-options="dev.id as dev.pseudo for dev in devs">
					</select>
				</li>
			</ul>
		</div>
	</div>
	<div class="modal-container-text">
		<div class="info-post">
			Opened by <span id="bug-author">{{bug.author}}</span> | <span class="text-muted">{{bug.date}}</span>
			<p id="modal-desc" class="clearfix" ng-bind-html="bug.description"></p>
		</div>
		<div class="info-post" ng-repeat="comment in bug.comment">
			<div class="author">
				Answer by <span class="dev">{{comment.dev.pseudo}}</span> | <span class="text-muted">{{comment.date}}</span>
			</div>
			<p class="answer" ng-bind-html="comment.message"></p>
		</div>
	</div>
	<div class="modal-send-answer">

	</div>
	<div class="last_action">
		<span class="title_action" ng-show="bug.last_action">Last action:</span>
		{{bug.last_action}}
	</div>
</div>