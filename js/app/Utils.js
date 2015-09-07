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
 *  Useful to only accept numbers while keyboard in use
 *
 * @param {Object} evt Event object (keydown, keyup...) to test
 * @param {Boolean} float True to allow dot (.) key
 * @returns {Boolean} True if key is a number
 */
function checkNum (evt, float) {
	var keyCode = evt.which ? evt.which : evt.keyCode;
	if ((float && keyCode == 190) || (float && keyCode == 110) )
		return true;
	if ((keyCode < 48 || keyCode > 57) && (keyCode < 96 || keyCode > 105 ))
		return false;
	return true;
};

/**
 * Useful to disallow special characters while keyboard in use
 *
 * @param: {Boolean} accents TRUE to allow accents (&éèàù)
 * @param: {Boolean} space	 TRUE to allow spaces ( )
 * @param: {Boolean} punct	 TRUE to allow dot, comma, and quote (.,')
 * @param: {Boolean} email	 TRUE to allow hyphen, underscore, and arobase (-_@)
 */
function checkChar (evt, accents, space, punct, email) {
	var keyCode = evt.which ? evt.which : evt.keyCode;
	if ((accents && keyCode == 48) || (accents && keyCode == 49) || (accents && keyCode == 50) || (accents && keyCode == 55) || (accents && keyCode == 165))
		return true;
	if (space && keyCode == 32)
		return true;
	if ((punct && keyCode == 110) || (punct && keyCode == 190) || ((keyCode == 59) && evt.shiftKey === true) || (punct && keyCode == 188) || (punct && keyCode == 52))
		return true;
	if ((email && keyCode == 54) || (email && keyCode == 56) || (email && keyCode == 109) || (email && keyCode == 225) || (email && keyCode == 48))
		return true;
	if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 96 && keyCode <= 105 ) || ((keyCode >= 48 && keyCode <= 57) && evt.shiftKey === true))
		return true;
	return false;
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