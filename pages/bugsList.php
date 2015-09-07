
<div id="filter-bug" class="clearfix">
	<div class="search-box">
		<input type="text" placeholder="search bug title..." ng-model="search.title" />
		<button class="btn-action" id="reset" ng-click="search.title = ''">reset</button>
    </div>

    <div class="all-filter">
        <h2 class="filter">filter by:</h2>

		<div class="select-filter">
			Priority<br />
			<select filter="priority" class="filtrage" ng-model="search.priority">
				<option value="">all</option>
				<option ng-repeat="prio in config.priorities" ng-style="{'background-color': prio.color}" ng-value="{{prio.priority}}">{{prio.priority}}</option>
			</select>
		</div>

		<div class="select-filter">
			Label<br />
			<select filter="label" class="filtrage" ng-model="search.FK_label_ID">
				<option value="">all</option>
				<option ng-repeat="label in config.labels" ng-style="{'background-color': label.color}" ng-value="{{label.id}}">{{label.name}}</option>
			</select>
		</div>

		<div class="select-filter">
			Assignee<br />
			<select filter="assignee" class="filtrage" ng-model="search.FK_dev_ID">
				<option value="" style="background-color: #CCC;">all</option>
				<option ng-repeat="dev in config.devs" style="background-color: #FFF;" ng-value="{{dev.id}}">{{dev.pseudo}}</option>
			</select>
		</div>

		<button class="btn-action" id="reset-filter" ng-click="resetFilter()">reset</button>
		<button class='btn-success add_newbug'>add bug</button>
    </div>
</div>

<table class="list-bugs">
	<thead>
		<tr>
			<th class="row-id"			ng-click="orderProp='id';			orderRev=!orderRev;">
				 id &nbsp;<i class="fa fa-sort"></i>
			</th>
			<th class="row-priority"	ng-click="orderProp='priority';		orderRev=!orderRev;">
				priority &nbsp;<i class="fa fa-sort"></i>
			</th>
			<th class="row-title">
				<div class="pull-right" ng-click="orderProp='date'; orderRev=!orderRev;">
					<i class="fa fa-calendar-o"></i> &nbsp;<i class="fa fa-sort"></i> &nbsp;&nbsp;
				</div>
				<div ng-click="orderProp='title'; orderRev=!orderRev;">
					title &nbsp;<i class="fa fa-sort"></i>
				</div>
			</th>
			<th class="row-comments"	ng-click="orderProp='FK_comment_ID';orderRev=!orderRev;">
				comments &nbsp;<i class="fa fa-sort"></i>
			</th>
			<th class="row-description" ng-click="orderProp='description';	orderRev=!orderRev;">
				description &nbsp;<i class="fa fa-sort"></i>
			</th>
			<th class="row-label"		ng-click="orderProp='FK_label_ID';	orderRev=!orderRev;">
				label &nbsp;<i class="fa fa-sort"></i>
			</th>
			<th class="row-assignee"	ng-click="orderProp='FK_dev_ID';	orderRev=!orderRev;">
				assignee &nbsp;<i class="fa fa-sort"></i>
			</th>
			<th class="row-action"		ng-show="config.authAdmin">
				action
			</th>
		</tr>
	</thead>
	<tbody>
		<tr ng-repeat="bug in bugsList | filter:search | orderBy:orderProp : orderRev" ng-hide="(bugsType === 0 && bug.closed === '1') || bug.removed">
			<td style="text-align: left;">#{{bug.id}}</td>
			<td>
				<div class="wrapper-sl" ng-show="config.authAdmin && bug.closed === '0'">
					<div class="triangle-down"></div>
					<select class="sl-priority" ng-style="{'background-color': config.priorities[bug.priority].color}" ng-model="bug.priority">
						<option ng-repeat="prio in config.priorities" ng-style="{'background-color': prio.color}" ng-value="{{prio.priority}}">{{prio.priority}}</option>
					</select>
				</div>
				<span ng-class="{'highest':bug.priority == '4', 'high':bug.priority == '3', 'middle':bug.priority == '2', 'low':bug.priority == '1'}" ng-hide="config.authAdmin && bug.closed === '0'">
					{{bug.priority}}
				</span>
			</td>
			<td ng-click="openBug(bug)">
				{{bug.title}}<br/>
				<span>By <em>{{bug.author}}</em> | {{bug.date | date: 'yyyy, MMM dd, HH:mm'}}</span>
			</td>
			<td>
				<span class="nbr-com">{{bug.comment.length || "0"}}</span>
			</td>
			<td>{{bug.description}}</td>
			<td>
				<div class="wrapper-sl-label" ng-show="config.authAdmin && bug.closed === '0'">
					<div class="triangle-down"></div>
					<select class="sl-label" ng-style="{'background-color': getLabelColor(bug.FK_label_ID)}" ng-model="bug.FK_label_ID"
							ng-options="label.id as label.name for label in config.labels">
					</select>
				</div>
				<span ng-style="{'background-color': getLabelColor(bug.FK_label_ID)}" ng-hide="config.authAdmin && bug.closed === '0'">{{bug.label.name}}</span>
			</td>
			<td>
				<div class="wrapper-sl-label" ng-show="config.authAdmin && bug.closed === '0'">
					<div class="triangle-down"></div>
					<select class="sl-assignee" ng-style="{'background-color': (bug.FK_dev_ID == 0) ?'#DDD':'#FFF'}" ng-model="bug.FK_dev_ID"
							ng-options="dev.id as dev.pseudo for dev in config.devs">
					</select>
				</div>
				<div style="display: inline-block; width: 80%; padding: 4px 2px;"
					 ng-style="{'background-color': (bug.dev.id > 0)? '#FFF' : '#DDD'}"
					 ng-hide="config.authAdmin && bug.closed === '0'">{{bug.dev.pseudo}}</div>
			</td>
			<td ng-show="config.authAdmin">
				<span class="btn-action" ng-hide="bug.closed === '1'" ng-click="killBug(bug.id)">Kill bug</span>
				<span class="btn-delete" ng-show="bug.closed === '1'" ng-click="deleteBug(bug.id)">Remove</span>
			</td>
		</tr>
	</tbody>
</table>