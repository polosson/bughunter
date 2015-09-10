/**
 *	Copyright (C) 2015  Azuk & Polosson
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as
 *	published by the Free Software Foundation, either version 3 of the
 *	License, or (at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
// Utils.js
'use strict';

/**
 * Useful to check email adress validity
 *
 * @param {String} email Email address to check
 * @returns {Boolean} True if email address is valid
 */
function check_email (email) {
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
};
/**
 * Useful to test if a string is a hex RGB color
 */
function checkColor (str) {
	var regCol = /^#[0-9A-F]{6}$/i;
	return regCol.test(str);
}
/**
 *  Useful to get the file name from a path
 *
 *  @param: {String} path The path in which we want to extract the file name
 */
function basename(path) {
    return path.replace(/\\/g, '/').replace( /.*\//, '');
}
/**
 *  Useful to remove the file name from a path
 *
 *  @param: {String} path The path in which we want to remove the file name
 */
function dirname(path) {
    return path.replace(/\\/g,'/').replace(/\/[^\/]*$/, '');;
}
/**
 * Useful to transform "\n" in 'br'
 *
 * @param: {String} str The string in which to replace \n with br
 */
function nl2br (str) {
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br />' + '$2');
}