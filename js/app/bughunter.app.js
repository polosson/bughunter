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
// bughunter.app.js
'use strict';

/**
 * BUGHUNTER APPLICATION DECLARATION
 */
var bughunter = angular.module('bughunter', ['ui.bootstrap', 'ngSanitize']);


/* * * * * * * * * * * * *  DIRECTIVES ET SERVICES GÉNÉRIQUES, ET CONFIGURATION * * * * * * * * * * * * * * */

/**
 * AJAX request/responses interceptor, to display loading icon
 */
bughunter.config(function($httpProvider) {
	$httpProvider.interceptors.push(function($q) {
		return {
			'request': function(config) {
				$('#loadAjax').show();
				return config;
			},
			'response': function(response) {
				$('#loadAjax').hide();
				return response;
			}
		};
	});
});

/**
 * GLOBAL CONFIGURATION factory
 */
bughunter.factory('config', function(){
	return {
		data: {
			authAdmin: false,
			priorities: [],
			labels: [],
			devs: [],
			globalConf: {}
		}
	};
});

/**
 * MESSAGES service
 */
bughunter.service('msgSrv', function($timeout){
	return {
		showMsg: showMsg,
		hideMsg: hideMsg
	};
	/**
	 * Display message (hides automatically after 2 sec when success, 10 sec when error)
	 * @param {String} msg The message to display
	 * @param {String} type The message type ("error", or "success") for color
	 */
	function showMsg (msg,type) {
		$('#msg').html(msg).removeClass('msg_error msg_success').addClass('msg_'+type).show();
		var time = 2000;
		if (type === "error")
			time = 10000;
		$timeout(function(){
			hideMsg();
		}, time);
	}
	/**
	 * Hides the message (fade out 0.8 sec)
	 */
	function hideMsg () {
		$('#msg').fadeOut(800);
	}
});

/**
 * BUG COUNT service
 */
bughunter.service('countBugs', function(){
	var countBugs = {alive: 0, killed: 0};

	return {
		count: countBugs,
		updateCount:	 updateCount,
		updateCountType: updateCountType,
		bugWasKilled:	 bugWasKilled,
		bugWasRemoved:	 bugWasRemoved
	};

	function updateCount (count) {
		countBugs.alive  = count.alive;
		countBugs.killed = count.killed;
	};

	function updateCountType(type, count) {
		if (type === 0)
			countBugs.alive  = count;
		if (type === 1)
			countBugs.killed  = count;
	}

	function bugWasKilled() {
		countBugs.alive  -= 1;
		countBugs.killed += 1;
	}

	function bugWasRemoved() {
		countBugs.killed -= 1;
	}
});


bughunter.service('ajaxBug', function($http, $q){

	// Ajax Configuration
	var aCnf = {
		url: "",
		method: "POST",
		data: {}
	};
	// Public methods
	return {
		saveModBug:  saveModBug,
		killBug:	 killBug,
		removeBug:	 removeBug,
		addComment:  addComment,
		saveComment: saveComment,
		delComment:  delComment,
		addLabel:	 addLabel,
		addDev:		 addDev,
		updateSetting:updateSetting,
		removeSetting:removeSetting,
		updatePW:	 updatePW
	};
	// Save existing bug's informations
	function saveModBug (bug) {
		aCnf.url = "actions/adminBug.php";
		aCnf.data = {action: 'modBug', bugID: bug.id, bugInfos: bug};
		return callAjax();
	}
	// Set bug as closed
	function killBug (bugId) {
		aCnf.url = "actions/adminBug.php";
		aCnf.data = {action:'killBug', bugID: bugId};
		return callAjax();
	}
	// Delete bug
	function removeBug (bugId) {
		aCnf.url = "actions/adminBug.php";
		aCnf.data = {action:'removeBug', bugID: bugId};
		return callAjax();
	}
	// Save new comment
	function addComment (bugId, commentText) {
		aCnf.url = "actions/adminBug.php";
		aCnf.data = {action: 'addComm', bugID: bugId, commentText: commentText};
		return callAjax();
	}
	// Save comment text (update existing)
	function saveComment (bugId, comment) {
		aCnf.url = "actions/adminBug.php";
		aCnf.data = {action: 'modComm', bugID: bugId, comment: comment};
		return callAjax();
	}
	// Remove comment from bug
	function delComment (bugId, commentId) {
		aCnf.url = "actions/adminBug.php";
		aCnf.data = {action: 'delComm', bugID: bugId, commID: commentId};
		return callAjax();
	}
	function addLabel (label) {
		aCnf.url = "actions/adminSettings.php";
		aCnf.data = {action: 'addLabel', label: label};
		return callAjax();
	}
	function addDev (dev) {
		aCnf.url = "actions/adminSettings.php";
		aCnf.data = {action: 'addDev', dev: dev};
		return callAjax();
	}
	function updateSetting (type, item) {
		aCnf.url = "actions/adminSettings.php";
		aCnf.data = {action: 'updateSetting', type: type, item: item};
		return callAjax();
	}
	function removeSetting (type, itemId) {
		aCnf.url = "actions/adminSettings.php";
		aCnf.data = {action: 'deleteSetting', type: type, itemID: itemId};
		return callAjax();
	}
	function updatePW (newPW) {
		aCnf.url = "actions/adminSettings.php";
		aCnf.data = {action: 'updatePW', newPW: newPW};
		return callAjax();
	}

	// --- PRIVATE METHODS --- //
	function callAjax(){
		var r = $http(aCnf);
		return r.then(handleSuccess, handleError);
	};
	function handleSuccess(response) {
		if (response.data.error !== "OK")
			return $q.reject("ERROR : "+response.data.message);
		return response.data;
	}
	function handleError(response) {
		console.log(response);
		return $q.reject(""+response.status+" - "+response.statusText+" ("+aCnf.url+")");
	}
});

/**
 * "Hit enter" directive : To allow execution of a function with Enter key.
 */
bughunter.directive('hitenter', function() {
	return function(scope, element, attrs) {
		element.bind("keydown keypress", function(event) {
			if (event.keyCode === 13) {
				scope.$apply(function(){
					scope.$eval(attrs.hitenter, {'event': event});
				});
				event.preventDefault();
			}
		});
	};
});

/**
 * Custom filter to remove string "http://" from the beginning of an URL
 */
bughunter.filter('formaturl', function(){
	return function(url){
		var reghttp = /^http\:\/\//i;
		return url.replace(reghttp, '');
	};
});