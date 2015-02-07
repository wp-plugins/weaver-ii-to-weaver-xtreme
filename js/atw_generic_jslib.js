/* *********************************************************************************
 * Aspen Plus JavaScript support Library
 *
 * Author: WeaverTheme - www.weavertheme.com
 * @version 1.0
 * @license GNU Lesser General Public License, http://www.gnu.org/copyleft/lesser.html
 * @author  Bruce Wampler
 *
 * Notes - this library requires jQuery to be loaded
 *  this library was cobbled together over a long period of time, so it contains a
 *  bit of a jumble of straight JavaScript and jQuery calls. So it goes. It works.
 *
 *
 ************************************************************************************* */


function wii2wx_plus_winWidth() {
    var myWidth = 0;
    if( typeof( window.innerWidth ) == 'number' ) {
        myWidth = window.innerWidth;    //Non-IE
    } else if( document.documentElement &&
            ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
         myWidth = document.documentElement.clientWidth; //IE 6+ in 'standards compliant mode'
    } else if ( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
        myWidth = document.body.clientWidth;    //IE 4 compatible
    }

    /* document.innerWidth does not work the same on all devices, partly depending on how "viewport"
	is set. This breaks things when switching to Full View in smart mode. So we will manually
	override the width when we are in FUll View mode which can be determined by the value
	of the viewport meta tag.
    */
    var metas = document.getElementsByTagName('meta');
    var i;
    for (i=0; i<metas.length; i++) {
	if (metas[i].name == "viewport") {
	    if (metas[i].content.indexOf('device-width') < 0) { // have specified theme width
		myWidth = weaverThemeWidth;
		break;
	    }
	}
    }
    return myWidth;
}

function wii2wx_plus_onResize() {

}

jQuery(document).ready(function($) {		// self-defining function
    wii2wx_plus_RunOnReady();			// non jQuery stuff

    // jQuery code allowed here

});

//Initial load of page
jQuery(window).load(wii2wx_plus_RunOnLoad);

//Every resize of window
jQuery(window).resize(wii2wx_plus_RunOnResize);

function wii2wx_plus_RunOnReady() {
   wii2wx_plus_onResize();
}

function wii2wx_plus_RunOnLoad() {

}

function wii2wx_plus_RunOnResize() {
   wii2wx_plus_onResize();
}
