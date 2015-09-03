<?php
require_once('init.php');

$l = new Liste();
$labels = $l->getListe('t_labels');
$devs	= $l->getListe('t_devs');

?>
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
				<option value="">all</option><?php
				foreach ($PRIORITIES as $prio) : ?>
					<option style="background-color:<?php echo $prio['color'] ?>" value="<?php echo $prio['priority']; ?>"><?php echo $prio['priority']; ?></option><?php
				endforeach; ?>
			</select>
		</div>

		<div class="select-filter">
			Label<br />
			<select filter="label" class="filtrage" ng-model="search.FK_label_ID">
				<option value="">all</option><?php
				foreach ($labels as $label): ?>
					<option value="<?php echo $label["id"]; ?>"><?php echo $label["name"]; ?></option><?php
				endforeach; ?>
			</select>
		</div>

		<div class="select-filter">
			Assignee<br />
			<select filter="assignee" class="filtrage" ng-model="search.FK_dev_ID">
				<option value="">all</option><?php
				foreach($devs as $dev) :
					if ($dev["id"] === '-1') continue; ?>
					<option value="<?php echo $dev["id"]; ?>"><?php echo $dev["pseudo"]; ?></option><?php
				endforeach; ?>
			</select>
		</div>

		<button class="btn-action" id="reset-filter" ng-click="resetFilter()">reset</button>
		<button class='btn-success add_newbug'>add bug</button>
    </div>
</div>

<table class="list-bugs">
	<thead>
		<tr>
			<th class="row-id"		 ng-click="orderProp='id';			  orderRev=!orderRev;">id</th>
			<th class="row-priority" ng-click="orderProp='priority';	  orderRev=!orderRev;">priority</th>
			<th class="row-title"	 ng-click="orderProp='title';		  orderRev=!orderRev;">title</th>
			<th class="row-comments" ng-click="orderProp='FK_comment_ID'; orderRev=!orderRev;">comments</th>
			<th class="row-description" ng-click="orderProp='description';orderRev=!orderRev;">description</th>
			<th class="row-label"	 ng-click="orderProp='FK_label_ID';	  orderRev=!orderRev;">label</th>
			<th class="row-assignee" ng-click="orderProp='FK_dev_ID';	  orderRev=!orderRev;">assignee</th>
			<th class="row-action">action</th>
		</tr>
	</thead>
	<tbody>
		<tr ng-repeat="bug in bugsList | filter:search | orderBy:orderProp : orderRev">
			<td>{{bug.id}}</td>
			<td>
				<div class="wrapper-sl">
					<div class="triangle-down"></div>
					<select class="sl-priority" ng-style="{'background-color': priorities[bug.priority].color}" ng-model="bug.priority"><?php
						foreach($PRIORITIES as $prio): ?>
							<option style="background-color: <?php echo $prio['color']; ?>" value="<?php echo $prio['priority']; ?>"><?php echo $prio['priority']; ?></option><?php
						endforeach; ?>
					</select>
				</div>
			</td>
			<td>
				{{bug.title}}<br/>
				<span>By <em>{{bug.author}}</em> | {{bug.date}}</span>
			</td>
			<td>
				<span class="nbr-com">{{bug.comment.length || "0"}}</span>
			</td>
			<td>{{bug.description}}</td>
			<td>
				<div class="wrapper-sl-label">
					<div class="triangle-down"></div>
					<select class="sl-label" ng-style="{'background-color': bug.label.color}" ng-model="bug.label.id"><?php
						foreach($labels as $label): ?>
							<option style="background-color: <?php echo $label['color']; ?>" value="<?php echo $label['id']; ?>"><?php echo $label['name']; ?></option><?php
						endforeach; ?>
					</select>
				</div>
			</td>
			<td>
				<div class="wrapper-sl-label">
					<div class="triangle-down"></div>
					<select class="sl-assignee" ng-style="{'background-color': '#FFFFFF'}" ng-model="bug.dev.id"><?php
						foreach($devs as $dev): ?>
							<option style="background-color: #FFFFFF;" value="<?php echo $dev['id']; ?>"><?php echo $dev['pseudo']; ?></option><?php
						endforeach; ?>
					</select>
				</div>
			</td>
			<td>
				<span class="btn-action" ng-show="listKilled">Remove</span>
				<span class="btn-action" ng-hide="listKilled">Kill bug</span>
			</td>
		</tr>
	</tbody>
</table>