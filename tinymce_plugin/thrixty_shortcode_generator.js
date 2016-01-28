"use strict";
jQuery(document).ready(function(){
	//// integrate the Generator Button.
		tinymce.create("tinymce.plugins.thrixty_gen", {
			init: function(ed, url){
				if( generate_dialog(ed) ){
					ed.addButton("thrixty", {
						title: "Thrixty Player Shortcode Generator",
						image: url+"/generator_icon.png",
						onclick: function(){
							overlay.show();
						}
					});
				} else {
					throw new Error("The ThrixtyPlugin failed to deliver a proper JSON parameter string. Please check your Browsers JSON decode Compatibility and the option \"thrixty_options\".", "", "");
				}
			},
			createControl: function(b,a){
				return null
			}
		});
		tinymce.PluginManager.add(
			"thrixty",
			tinymce.plugins.thrixty_gen
		);
	//// /

	// the overlay as root object needs to be known to all functions -> document-wide scope
	var overlay;
	function generate_dialog(tinymce_obj){
		// check for variable set by plugin
		if( typeof(thrixty_sc_gen_var) !== 'undefined' ){
			// create a gray overlay over the entire page
			overlay = jQuery('<div id="generator_dialog" style="display:none; box-sizing:border-box; position:fixed; top:0; right:0; bottom:0; left:0; background:rgba(0,0,0,0.5); z-index:9999;       padding:50px 0;"></div>');
			jQuery("body").append(overlay);
				// generator dialog container - in the middle of the dialog
				// todo: vertical center -> use three divs "outer", "middle" and "inner" -> middle one gets "vertical-align: center;"
				var gen_container = jQuery('<div style="box-sizing:border-box; margin:auto; width:600px; background:white; border:3px solid #555555; padding: 15px;"></div>');
				overlay.append(gen_container);
				var styles_text = "<style>";
					styles_text += "#generator_dialog input, #generator_dialog select{width:100%;}";
					styles_text += "#generator_dialog td:nth-child(1){text-align:left; width:0;}";
					styles_text += "#generator_dialog td:nth-child(2){text-align:center;}";
					styles_text += "#generator_dialog button{width:100%; height: 40px;}";
					styles_text += "#generator_dialog input:disabled{border: transparent; box-shadow: none;}";
				styles_text += "</style>";
				gen_container.append(jQuery(styles_text));
				var stuff = "<form id='thrixty_generator_form' name='thrixty_generator_form'>";
					stuff += "<table style='width:100%; height:100%; text-align:center;'>";
						stuff += "<tr>";
							stuff += "<th>Label</th>";
							stuff += "<th>Field</th>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='basepath'>basepath</label></td>";
							stuff += "<td><input id='basepath' class='path_observe' name='basepath' type='text' placeholder='"+thrixty_sc_gen_var.basepath+"' /></td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='object_name'>object_name</label></td>";
							stuff += "<td><input id='object_name' class='path_observe' name='object_name' type='text'/></td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='filelist_path_small'>filelist_path_small</label></td>";
							stuff += "<td><input id='filelist_path_small' class='path_observe' name='filelist_path_small' type='text' placeholder='"+thrixty_sc_gen_var.filelist_path_small+"'/></td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='filelist_path_large'>filelist_path_large</label></td>";
							stuff += "<td><input id='filelist_path_large' class='path_observe' name='filelist_path_large' type='text' placeholder='"+thrixty_sc_gen_var.filelist_path_large+"' /></td>";
						stuff += "</tr>";
						stuff += "<tr><td colspan='2'><hr></td></tr>";
						stuff += "<tr>";
							stuff += "<td><label for='cur_small_path'>current small path</label></td>";
							stuff += "<td id='cur_small_path'>"+thrixty_sc_gen_var.basepath_replaced+thrixty_sc_gen_var.filelist_path_small+"</td>";
						stuff += "</tr>";
							stuff += "<td><label for='cur_large_path'>current large path</label></td>";
							stuff += "<td id='cur_large_path'>"+thrixty_sc_gen_var.basepath_replaced+thrixty_sc_gen_var.filelist_path_large+"</td>";
						stuff += "</tr>";
						stuff += "<tr><td colspan='2'><hr></td></tr>";
						stuff += "<tr>";
							stuff += "<td><label for='zoom_mode'>zoom_mode</label></td>";
							stuff += "<td>";
								stuff += "<select id='zoom_mode' name='zoom_mode'>";
									stuff += "<option value='' selected>[Standard]</option>";
									stuff += "<option value='inbox'>Inbox</option>";
									stuff += "<option value='outbox'>Outbox</option>";
								stuff += "</select>";
							stuff += "</td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='outbox_position'>outbox_position</label></td>";
							stuff += "<td>";
								stuff += "<select id='outbox_position' name='outbox_position'>";
									stuff += "<option value='' selected>[Standard]</option>";
									stuff += "<option value='right'>Right</option>";
									stuff += "<option value='bottom'>Bottom</option>";
									stuff += "<option value='left'>Left</option>";
									stuff += "<option value='top'>Top</option>";
								stuff += "</select>";
							stuff += "</td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='position_indicator'>position_indicator</label></td>";
							stuff += "<td>";
								stuff += "<select id='position_indicator' name='position_indicator'>";
									stuff += "<option value='' selected>[Standard]</option>";
									stuff += "<option value='minimap'>Minimap</option>";
									stuff += "<option value='marker'>Marker</option>";
								stuff += "</select>";
							stuff += "</td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='zoom_control'>zoom_control</label></td>";
							stuff += "<td>";
								stuff += "<select id='zoom_control' name='zoom_control'>";
									stuff += "<option value='' selected>[Standard]</option>";
									stuff += "<option value='progressive' >Progressive</option>";
									stuff += "<option value='classic'>Classic</option>";
								stuff += "</select>";
							stuff += "</td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='direction'>direction</label></td>";
							stuff += "<td>";
								stuff += "<select id='direction' name='direction'>";
									stuff += "<option value='' selected>[Standard]</option>";
									stuff += "<option value='forward'>Forward</option>";
									stuff += "<option value='backward'>Backward</option>";
								stuff += "</select>";
							stuff += "</td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='cycle_duration'>cycle_duration</label></td>";
							stuff += "<td><input id='cycle_duration' name='cycle_duration' type='number' min='1' placeholder='[Standard]' /></td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='sensitivity_x'>sensitivity_x</label></td>";
							stuff += "<td><input id='sensitivity_x' name='sensitivity_x' type='number' min='0' placeholder='[Standard]' /></td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='autoload'>autoload</label></td>";
							stuff += "<td>";
								stuff += "<select id='autoload' name='autoload'>";
									stuff += "<option value='' selected>[Standard]</option>";
									stuff += "<option value='on'>On</option>";
									stuff += "<option value='off'>Off</option>";
								stuff += "</select>";
							stuff += "</td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='autoplay'>autoplay</label></td>";
							stuff += "<td><input id='autoplay' name='autoplay' type='number' min='-1' placeholder='[Standard]' /></td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><button type='button' id='close' style='background:red;'>Cancel</button></td>";
							stuff += "<td><button type='button' id='ok' style='background:green;'>Generate</button></td>";
						stuff += "</tr>";
					stuff += "</table>";
				stuff += "</form>";
				// gen_container.html(stuff);
				gen_container.append(jQuery(stuff));
			gen_container.find("#ok").on(
				"click",
				function(e){
					// do not submit this form
					e.preventDefault();

					// send
					var elem = jQuery('#thrixty_generator_form')[0];

					// generate shortcode
					var count = elem.length;
					var content = "[thrixty ";
					for( var i=0; i<count; i++ ){
						var current_elem = elem[i];
						switch( current_elem.name ){
							case "basepath":
								if( current_elem.value != "" ){
									content += "basepath=\""+current_elem.value+"\" ";
								}
								break;
							case "object_name":
								if( current_elem.value != "" ){
									content += "object_name=\""+current_elem.value+"\" ";
								}
								break;
							case "filelist_path_small":
								if( current_elem.value != "" ){
									content += "filelist_path_small=\""+current_elem.value+"\" ";
								}
								break;
							case "filelist_path_large":
								if( current_elem.value != "" ){
									content += "filelist_path_large=\""+current_elem.value+"\" ";
								}
								break;
							case "zoom_mode":
								if( current_elem.value != "" ){
									content += "zoom_mode=\""+current_elem.value+"\" ";
								}
								break;
							case "outbox_position":
								if( current_elem.value != "" ){
									content += "outbox_position=\""+current_elem.value+"\" ";
								}
								break;
							case "position_indicator":
								if( current_elem.value != "" ){
									content += "position_indicator=\""+current_elem.value+"\" ";
								}
								break;
							case "zoom_control":
								if( current_elem.value != "" ){
									content += "zoom_control=\""+current_elem.value+"\" ";
								}
								break;
							case "direction":
								if( current_elem.value != "" ){
									content += "direction=\""+current_elem.value+"\" ";
								}
								break;
							case "cycle_duration":
								if( current_elem.value != "" ){
									content += "cycle_duration=\""+current_elem.value+"\" ";
								}
								break;
							case "sensitivity_x":
								if( current_elem.value != "" ){
									content += "sensitivity_x=\""+current_elem.value+"\" ";
								}
								break;
							case "autoload":
								if( current_elem.value != "" ){
									content += "autoload=\""+current_elem.value+"\" ";
								}
								break;
							case "autoplay":
								if( current_elem.value != "" ){
									content += "autoplay=\""+current_elem.value+"\" ";
								}
								break;
							default:
								// buttons -> do nothing
								break;
						}
					}
					content += "]";
					// write shortcode into the editor area
					tinymce_obj.selection.setContent(content);
					overlay.hide();
				}
			);
			gen_container.find("#close").on(
				"click",
				function(){
					overlay.hide();
				}
			);



			var bp_field = gen_container.find("#basepath")[0];
			var on_field = gen_container.find("#object_name")[0];
			var fs_field = gen_container.find("#filelist_path_small")[0];
			var fl_field = gen_container.find("#filelist_path_large")[0];
			var cur_sm_p = gen_container.find("#cur_small_path");
			var cur_la_p = gen_container.find("#cur_large_path");
			gen_container.find(".path_observe").on(
				// "keyup change input",
				"input",
				debounce(
					function(e){
						var object_path = thrixty_sc_gen_var.basepath_replaced + on_field.value;
						object_path += object_path.charAt(object_path.length-1) === "/" ? "" : "/";
						/* preview paths */
						var small_path = fs_field.value || fs_field.placeholder;
						var large_path = fl_field.value || fl_field.placeholder;
						small_path = object_path + small_path;
						large_path = object_path + large_path;
						cur_sm_p.html(small_path);
						cur_la_p.html(large_path);
						// check the file actually being
						jQuery.ajax({
							url: small_path,
							success: function(){
								cur_sm_p.css("color", "green");
							},
							error: function(){
								cur_sm_p.css("color","red");
							},
						});
						jQuery.ajax({
							url: large_path,
							success: function(){
								cur_la_p.css("color", "green");
							},
							error: function(){
								cur_la_p.css("color","red");
							},
						});


					},
					500
				)
			);
			// alles erfolgreich?
			return true;
		} /*endif*/ else {
			// error?
			return false;
		}
	};


	function debounce(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	};

});