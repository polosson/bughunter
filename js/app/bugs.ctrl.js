// alive.ctrl.js
'use strict';

/**
 * Controleur de la liste des bugs et des filtres
 */
bughunter.controller("bugsCtrl", function($scope, $rootScope, $http, $modal){
	$scope.modeAdmin	= true;
	$scope.bugsList		= [];
	$scope.priorities	= [];
	$scope.labels		= [];
	$scope.devs			= [];
	$scope.listKilled	= false;
	$scope.search	 = {'title':"", 'priority':"", 'FK_label_ID':"", 'FK_dev_ID':""};
	$scope.orderProp = 'priority';
	$scope.orderRev  = true;
	getBugsList();

	$scope.resetFilter = function(){
		$scope.search = {'title':"", 'priority':"", 'FK_label_ID':"", 'FK_dev_ID':""};
	};

	function getBugsList (type) {
		if (!type) type = 0;
		$http({
			'url': 'actions/getBugsList.php?type='+type
		}).then(
			function(R){
				$scope.bugsList		= R.data.bugsList;
				$scope.priorities	= R.data.priorities;
				$scope.labels		= R.data.labels;
				$scope.devs			= R.data.devs;
				$rootScope.$broadcast('updateBugCount', {'type':type, 'count':R.data.bugsList.length});
			},
			function(errMsg) { console.log("error", errMsg); $('#msg').html(errMsg).addClass('msg_error'); }
		);
	}

	$scope.$on('showbugsAlive', function(){
		$scope.bugsList		= [];
		getBugsList(0);
		$scope.listKilled	= false;
	});
	$scope.$on('showbugsKilled', function(){
		$scope.bugsList		= [];
		getBugsList(1);
		$scope.listKilled	= true;
	});

	$scope.openBug = function(bug){
		var modalInstance = $modal.open({
			templateUrl: 'pages/bugModal.php?v='+ new Date().getTime(),
			controller: 'bugModalCtrl',
			backdrop: 'static',
			windowClass: '',
			resolve: {
				priorities: function() { return $scope.priorities; },
				labels:		function() { return $scope.labels; },
				devs:		function() { return $scope.devs; },
				bug:		function() { return bug; }
			}
		});
		modalInstance.result.then(function (R) {
			console.log(R);
		});
	};

	$scope.getLabelColor = function(labelID){
		var zeLabel = $.grep($scope.labels, function(e){ return e.id === labelID; });
		return zeLabel[0].color;
	};

});


/**
 * Controleur de la modale de bug
 */
bughunter.controller('bugModalCtrl', function($scope, $modalInstance, priorities, labels, devs, bug){
	$scope.modeAdmin  = true;
	$scope.editInfos  = false;
	$scope.editDescr  = false;
	$scope.editComment= false;
	$scope.priorities = angular.copy(priorities);
	$scope.labels	  = angular.copy(labels);
	$scope.devs		  = angular.copy(devs);
	$scope.bug		  = angular.copy(bug);
	$scope.bug.descriptionHtml = angular.copy(nl2br(bug.description));
	console.log(bug);

	$scope.closeBugModal = function(){
		$modalInstance.dismiss();
	};

	$scope.getLabelColor = function(labelID){
		var zeLabel = $.grep($scope.labels, function(e){ return e.id === labelID; });
		return zeLabel[0].color;
	};

	$scope.initEdit = function(){
		$scope.editInfos  = true;
	};
	$scope.saveEdit = function(){
		$scope.editInfos  = false;
	};
	$scope.cancelEdit = function(){
		$scope.editInfos  = false;
	};

	$scope.initUpdDescr = function(){
		$scope.editDescr  = true;
	};
	$scope.saveUpdDescr  = function(){
		$scope.editDescr  = false;
	};
	$scope.cancelUpdDescr  = function(){
		$scope.editDescr  = false;
	};

	$scope.initUpdComment = function(idComm){
		$scope.editComment  = idComm;
	};
	$scope.saveUpdComment = function(){
		$scope.editComment  = false;
	};
	$scope.cancelUpdComment = function(){
		$scope.editComment  = false;
	};
	$scope.deleteComment = function(idComm){
		if (!confirm("Delete this comment? Sure?"))
			return;
	};
});