<?php
require_once('init.php');

$l = new Liste();
$labels = $l->getListe('t_labels');
$devs	= $l->getListe('t_devs');

?>
<table class="list-bugs">
	<thead>
		<tr>
			<th class="row-id">id</th>
			<th class="row-priority">priority</th>
			<th class="row-title">title</th>
			<th class="row-comments">comments</th>
			<th class="row-description">description</th>
			<th class="row-label">label</th>
			<th class="row-assignee">assignee</th>
			<th class="row-action">action</th>
		</tr>
	</thead>
	<tbody>
		<tr ng-repeat="bug in bugsList">
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