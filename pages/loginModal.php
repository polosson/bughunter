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
<div id="modal-login">
	<div class="modal-close" ng-click="closeLoginModal()"><i class="fa fa-close fa-3x"></i></div>
	<div class="modal-header">
		<h2>Bughunter login</h2>
	</div>
	<div class="modal-body">
		<label for="password">Enter password :</label>
		<input autofocus type="password" name="password" style="margin-left:30px;" hitenter="connect()" ng-model="password">
		<button class="btn-action" ng-click="connect()">connect</button>
	</div>
	<div class="modal-footer text-danger">{{message}}</div>
</div>