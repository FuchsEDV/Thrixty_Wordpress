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
		// its pointless to generate a dialogue, when 'thrixty_sc_gen_var' wasnt set...
		if( typeof(thrixty_sc_gen_var) !== 'undefined' ){
			overlay = jQuery('<div id="overlay"></div>'); jQuery("body").append(overlay);
				var gen_dialog = jQuery('<div id="generator_dialog"></div>'); overlay.append(gen_dialog);
				var styles_text = "<style>";
					styles_text += "#overlay{ display: none; box-sizing: border-box; position: fixed; top: 0; right: 0; bottom: 0; left: 0; background: rgba(0,0,0,0.5); z-index: 9999; padding: 50px 0; }";
					styles_text += "#generator_dialog{ box-sizing: border-box; margin: auto; width: 600px; background: white; border: 3px solid #555555; padding: 15px; max-width: 100%; max-height: 100%; overflow-y: scroll; }";
					styles_text += "#generator_dialog #reuse_basepath{font-size: 10px; height:initial; width:initial;}";
					styles_text += "#generator_dialog input, #generator_dialog select{width:100%;}";
					styles_text += "#generator_dialog td:nth-child(1){text-align:left; width:0;}";
					styles_text += "#generator_dialog td:nth-child(2){text-align:center;}";
					styles_text += "#generator_dialog button{width:100%; height: 40px;}";
					styles_text += "#generator_dialog input:disabled{border: transparent; box-shadow: none;}";
				styles_text += "</style>";
				gen_dialog.append(jQuery(styles_text));
				var stuff = "<form id='thrixty_generator_form' name='thrixty_generator_form'>";
					stuff += "<table style='width:100%; height:100%; text-align:center;'>";
						stuff += "<tr>";
							stuff += "<th>Label</th>";
							stuff += "<th>Field</th>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='basepath'>basepath</label></td>";
							stuff += "<td><input id='basepath' class='path_observe' name='basepath' type='text' placeholder='"+thrixty_sc_gen_var.basepath+"' /><br></td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td></td>";
							stuff += "<td><button id='reuse_basepath' type='button'>Plugineinstellung &uuml;bernehmen</button></td>";
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
							stuff += "<td id='cur_small_path'></td>";
						stuff += "</tr>";
							stuff += "<td><label for='cur_large_path'>current large path</label></td>";
							stuff += "<td id='cur_large_path'></td>";
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
							stuff += "<td><button type='submit' id='ok' style='background:green;'>Generate</button></td>";
						stuff += "</tr>";
					stuff += "</table>";
				stuff += "</form>";
				// gen_dialog.html(stuff);
				gen_dialog.append(jQuery(stuff));



			var gen_form = gen_dialog.find("#thrixty_generator_form");
			var bp_field = gen_dialog.find("#basepath");
			var on_field = gen_dialog.find("#object_name");
			var fs_field = gen_dialog.find("#filelist_path_small");
			var fl_field = gen_dialog.find("#filelist_path_large");
			var cur_sm_p = gen_dialog.find("#cur_small_path");
			var cur_la_p = gen_dialog.find("#cur_large_path");
			var reuse_bp_btn = gen_dialog.find("#reuse_basepath")
			var close_btn = gen_dialog.find("#close");
			var ok_btn = gen_dialog.find("#ok");



			reuse_bp_btn.on(
				"click",
				function(e){
					var basepath_elem = bp_field[0];
					basepath_elem.value = basepath_elem.placeholder;
					// TODO: TRIGGER PATH OBSERVE
					bp_field.trigger("input");
				}
			);
			close_btn.on(
				"click",
				function(){
					overlay.hide();
				}
			);
			ok_btn.on(
				"click",
				function(e){
					overlay.hide();
				}
			);
			gen_form.on(
				"submit",
				function(e){
					e.preventDefault();

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
								content += "object_name=\""+current_elem.value+"\" ";
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

					/* clear the input fields */
					gen_form[0].reset();
				}
			);



			gen_dialog.find(".path_observe").on(
				// "keyup change input",
				"input",
				debounce(
					function(e){
						var basepath = bp_field.val() != "" ? bp_field.val() : thrixty_sc_gen_var.basepath;
							basepath = basepath.replace("__SITE__",   thrixty_sc_gen_var.__SITE__);
							basepath = basepath.replace("__PLUGIN__", thrixty_sc_gen_var.__PLUGIN__);
							basepath = basepath.replace("__UPLOAD__", thrixty_sc_gen_var.__UPLOAD__);

						var object_name = on_field.val();
						var filelist_small = fs_field.val() != "" ? fs_field.val() : thrixty_sc_gen_var.filelist_path_small;
						var filelist_large = fl_field.val() != "" ? fl_field.val() : thrixty_sc_gen_var.filelist_path_large;

						var path_small = basepath;
						var path_large = basepath;
							path_small += path_small.charAt(path_small.length-1) === "/" ? "" : "/";
							path_large += path_large.charAt(path_large.length-1) === "/" ? "" : "/";
							path_small += object_name;
							path_large += object_name;
							path_small += path_small.charAt(path_small.length-1) === "/" ? "" : "/";
							path_large += path_large.charAt(path_large.length-1) === "/" ? "" : "/";
							path_small += filelist_small;
							path_large += filelist_large;

						cur_sm_p.html(path_small);
						cur_la_p.html(path_large);
						// check the file actually being
						jQuery.ajax({
							url: path_small,
							success: function(){
								cur_sm_p.css("color", "green");
							},
							error: function(){
								cur_sm_p.css("color","red");
							},
						});
						jQuery.ajax({
							url: path_large,
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
		} else {
			// error?
			return false;
		} /*endif*/
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