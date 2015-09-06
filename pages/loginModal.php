
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