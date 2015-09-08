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