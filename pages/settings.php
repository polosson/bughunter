<?php

?>
<div id="modal-settings">
	<div class="modal-header">
		<div class="settings-message">{{ajaxMsg}}</div>
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
					<label for="picker">Color #</label><input class="picker" id="picker" name="color" type="text">
					<label for="name">Name</label><input id="name" name="name" type="text">
					<button class="btn-success">Add</button>
				</div>

				<table>
					<thead>
						<tr>
							<th>Name</th>
							<th>Color</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								none
								<input style="display: none;" name="name" type="text"></td>
							<td>
								<div style="background-color:#adadad;" class="color-sample"></div>
								#adadad
								<input style="display: none;" class="picker" name="color" type="text">
								<div style="clear: both;"></div>
							</td>
							<td>
								<button class="btn-action edit_btn">Edit</button>
								<button style="display: none;" class="btn-success validate_btn">send</button>
								<button style="display: none;" class="btn-action cancel_btn">cancel</button>
							</td>
						</tr>
						<tr>
							<td>
								bug
								<input style="display: none;" name="name" type="text"></td>
							<td>
								<div style="background-color:#adadad;" class="color-sample"></div>
								#adadad
								<input style="display: none;" class="picker" name="color" type="text">
							</td>
							<td>
								<button class="btn-action edit_btn">Edit</button>
								<button class="btn-delete">Delete</button>
								<button style="display: none;" class="btn-success validate_btn">send</button>
								<button style="display: none;" class="btn-action cancel_btn">cancel</button>
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
					<label for="email">E-mail</label> <input id="email" name="mail" type="text" />
					<button class="btn-success">Add</button>
				</div>

				<table>
					<thead>
						<tr>
							<th>Pseudo</th>
							<th>E-mail</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								none
								<input style="display: none;" name="pseudo" type="text">
							</td>
							<td class="stripedBG"></td>
							<td>
								<button class="btn-action edit_btn">Edit</button>
								<button style="display: none;" class="btn-success validate_btn">send</button>
								<button style="display: none;" class="btn-action cancel_btn">cancel</button>
							</td>
						</tr>
						<tr>
							<td>
								Moutew
								<input style="display: none;" name="pseudo" type="text">
							</td>
							<td>
								moutew@d2mphotos.fr
								<input style="display: none;" name="mail" type="text">
							</td>
							<td>
								<button class="btn-action edit_btn">Edit</button>
								<button class="btn-delete delete-btn">Delete</button>
								<button style="display: none;" class="btn-success validate_btn">send</button>
								<button style="display: none;" class="btn-action cancel_btn">cancel</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="settings-section effect2">
			<div class="settings-content">
				<h2>Password change</h2>
				<div class="settings-form">
					<input placeholder="new password" id="new_password" name="new_password" type="password">
					<input placeholder="confirm password" id="confirm_password" name="confirm_password" type="password">
					<input class="btn-success reset-pass" value="update" type="submit">
				</div>
			</div>
		</div>

	</div>
</div>