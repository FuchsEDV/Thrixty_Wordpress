/* thrixty_initialize.js */
/**
 *  @fileOverview
 *  @author F.Heitmann @ Fuchs EDV Germany
 *  @version 1.0
 *  @license GPLv3
 *  @module ThrixtyPlayer.MainClass
 */

(function(){
	// 1.: get this scripts path || This must be executed at file-load!!!
	var basepath = (function(){
		var scripts = document.getElementsByTagName('script');
		var path = scripts[scripts.length - 1].src;
		return path.substring(0, path.lastIndexOf("/")+1);
	})();
	console.log(basepath);


	// 2.: define ThrixtyPlayer Namespace
	/**
	 *  @description ThrixtyPlayer Application
	 *  @name ThrixtyPlayer
	 *
	 *  @namespace ThrixtyPlayer The Thrixty Player is a 360 degree Product Presentation designed in HTML 5.
	 *  @property {array} initialized_players Container for all Players initialized.
	 */
	ThrixtyPlayer = {
		initialized_players: [],
	};


	// 3.: load jQuery 2.1.3 and assign new global variable jQuery_2_1_3
	load_script(basepath+"jquery-2.1.3.js", function(){
		window.jQuery_2_1_3 = jQuery.noConflict(true);
		load_includes(basepath);
	});


	// 4.: load_includes
	function load_includes(basepath){
		var includes = [
			basepath+"thrixty_main_class.js",
			basepath+"thrixty_eventhandler_class.js",
			basepath+"thrixty_drawinghandler_class.js",
		];
		var includes_count = includes.length;
		var loaded_count = 0;
		for( var i=0; i<includes_count; i++ ){
			var class_load = function(){
				loaded_count += 1;
				if( loaded_count == includes_count ){
					// when all includes are loaded, go to 5th step
					initialize_ThrixtyPlayer();
				}
			};
			load_script(includes[i], class_load);
		}
	}


	// 5.: init ThrixtyPlayer
	function initialize_ThrixtyPlayer(){
		jQuery_2_1_3(document).ready(function(){
			ThrixtyPlayer.initialized_players = (function(){
				// start with selecting all boxes
				var selector = jQuery_2_1_3("div.thrixty-player");

				// first validate the selector
				// check for being an object AND not being a plain js obj
				if( (typeof selector == "object") && (!jQuery_2_1_3.isPlainObject(selector)) ){
					// valid Selector
					// continue
				} else {
					// INVALID Selector
					throw new Error("no proper box found");
				}

				// init a player for all boxes
				var all_players = [];
				selector.each(function(index, element){
					// new ThrixtyPlayer
					var new_player = new ThrixtyPlayer.MainClass(jQuery_2_1_3(element));
					new_player.setup();

					all_players.push(new_player);
				});
				return all_players;
			})();
			log("Collection of Players:");
			log(ThrixtyPlayer.initialized_players);
		});
	}
})()





// general functions
function log(a){
	console.log(a);
	return a;
}

// function detect_mobile() {
// 	if( navigator.userAgent.match(/Android/i)
// 		|| navigator.userAgent.match(/webOS/i)
// 		|| navigator.userAgent.match(/iPhone/i)
// 		|| navigator.userAgent.match(/iPad/i)
// 		|| navigator.userAgent.match(/iPod/i)
// 		|| navigator.userAgent.match(/BlackBerry/i)
// 		|| navigator.userAgent.match(/Windows Phone/i)
// 	){
// 		return true;
// 	} else {
// 		return false;
// 	}
// }

function load_script( url, callback ){
    // Add the script tag to the head
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;

    // Bind the event to the callback function.
    // Several events for cross browser compatibility.
    script.onreadystatechange = callback;
    script.onload = callback;
    script.onerror = function(){
    	throw new Error(url+" not found!", 0, 0);
    }

    // Start the loading
    head.appendChild(script);
}
/* thrixty_initialize.js */