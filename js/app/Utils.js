// js/common.js
'use strict';

// Utile pour vérifier la validité d'une adresse email
// @params:
//		email : STRING, l'adresse email à vérifier
function check_email (email) {
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}


// Utile pour n'autoriser que les nombres
// @params:
//		float: TRUE pour autoriser les nombres décimaux
function checkNum (evt, float) {
	var keyCode = evt.which ? evt.which : evt.keyCode;
	if ((float && keyCode == 190) || (float && keyCode == 110) )
		return true;
	if ((keyCode < 48 || keyCode > 57) && (keyCode < 96 || keyCode > 105 ))
		return false;
	return true;
}


// Utile pour interdire les caractères spéciaux
// @params:
//		accents : TRUE pour autoriser les accents (&éèàù)
//		espace  : TRUE pour autoriser les espaces ( )
//		point   : TRUE pour autoriser les points, les virgules et les apostrophes (.,')
//		tirets  : TRUE pour autoriser les traits d'union, les underscore et le arobase (-_@)
function checkChar (evt, accents, espace, point, tiret) {
	var keyCode = evt.which ? evt.which : evt.keyCode;
	if ((accents && keyCode == 48) || (accents && keyCode == 49) || (accents && keyCode == 50) || (accents && keyCode == 55) || (accents && keyCode == 165))
		return true;
	if (espace && keyCode == 32)
		return true;
	if ((point && keyCode == 110) || (point && keyCode == 190) || ((keyCode == 59) && evt.shiftKey === true) || (point && keyCode == 188) || (point && keyCode == 52))
		return true;
	if ((tiret && keyCode == 54) || (tiret && keyCode == 56) || (tiret && keyCode == 109) || (tiret && keyCode == 225) || (tiret && keyCode == 48))
		return true;
	if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 96 && keyCode <= 105 ) || ((keyCode >= 48 && keyCode <= 57) && evt.shiftKey === true))
		return true;
	return false;
}

// Utile pour ne récupérer que le nom d'un fichier dans un path
// @params:
//		path : STRING, le chemin à basenamer
function basename(path) {
    return path.replace(/\\/g, '/').replace( /.*\//, '');
}
// Utile pour ne récupérer que le chemin d'un fichier
// @params:
//		path : STRING, le chemin à dirnamer
function dirname(path) {
    return path.replace(/\\/g,'/').replace(/\/[^\/]*$/, '');;
}
// Utile pour transformer des \n en <br />
function nl2br (str) {
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br />' + '$2');
}