
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
					<span ng-class="{'highest':bug.priority == '4', 'high':bug.priority == '3', 'middle':bug.priority == '2', 'low':bug.priority == '1'}">{{bug.priority}}</span>
				</li>
				<li id="label">
					<span>Label</span>
					<span ng-style="{'background-color': bug.label.color}">{{bug.label.name}}</span>
				</li>
				<li id="assignee">
					<span>Assignee</span>
					<span style="background-color: #FFF;">{{bug.dev.pseudo}}</span>
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