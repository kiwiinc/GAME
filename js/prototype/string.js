"use strict";

/**

 * Removes all whitespace characters

 */

String.prototype.clip = function () {

	return this.toLowerCase().replace(/\s+/g, "");

};



/**

 * Reduces string to a single line 

 */

String.prototype.condense = function () {

	return this.replace( /\s+/g, " " ).trim();

};



/**

 * Check if string is empty.

 */

String.prototype.isEmpty = function () {

	return (this.length === 0 || !this.trim());

};