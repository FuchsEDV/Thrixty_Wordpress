jQuery(document).ready(function(){
	// integrate the Generator Button.
	tinymce.create("tinymce.plugins.thrixty_gen", {
		init: function(ed, url){
			if( generate_dialog(ed) ){
				ed.addButton("thrixty", {
					title: "Thrixty Player Shortcode Generator",
					image: url+"/generator_icon.png",
					onclick: function(){
						button_click_event(ed);
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

	var overlay;
	function generate_dialog(tinymce_obj){
		// check for variable set by plugin
		if( typeof(thrixty_sc_gen_var) !== 'undefined' ){
			overlay = jQuery('<div id="generator_dialog" style="display:none; box-sizing:border-box; position:fixed; top:0; right:0; bottom:0; left:0; background:rgba(0,0,0,0.5); z-index:9999;       padding:50px 0;"></div>');
			jQuery("body").append(overlay);
				var gen_container = jQuery('<div style="box-sizing:border-box; margin:auto; width:600px; background:white; border:3px solid #555555; padding: 15px;"></div>');
				overlay.append(gen_container);
				var styles_text = "<style>";
					styles_text += "#generator_dialog input, #generator_dialog select{width:100%;}";
					styles_text += "#generator_dialog td:nth-child(1){text-align:left; width:0;}";
					styles_text += "#generator_dialog td:nth-child(2){text-align:center;}";
					styles_text += "#generator_dialog button{width:100%; height: 40px;}";
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
							stuff += "<td><input id='basepath' name='basepath' type='text' placeholder='"+thrixty_sc_gen_var.basepath+"' /></td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='filelist_path_small'>filelist_path_small</label></td>";
							stuff += "<td><input id='filelist_path_small' name='filelist_path_small' type='text' placeholder='' /></td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='filelist_path_large'>filelist_path_large</label></td>";
							stuff += "<td><input id='filelist_path_large' name='filelist_path_large' type='text' placeholder='' /></td>";
						stuff += "</tr>";
						stuff += "<tr><td colspan='2'><hr></td></tr>";
						stuff += "<tr>";
							stuff += "<td><label for='zoom_mode'>zoom_mode</label></td>";
							stuff += "<td>";
								stuff += "<select id='zoom_mode' name='zoom_mode'>";
									// Possible Inbox Zoom
									stuff += "<option value='inbox' ";
										if( "inbox" === thrixty_sc_gen_var.zoom_mode ){
											stuff += "selected ";
										}
										stuff += "selected>Inbox</option>";
									// Possible Inbox Zoom with Minimap
									stuff += "<option value='inbox_minimap' ";
										if( "inbox_minimap" === thrixty_sc_gen_var.zoom_mode ){
											stuff += "selected";
										}
										stuff += ">Inbox mit Minimap</option>";
									// Possible Outbox Zoom on the Right
									stuff += "<option value='outbox_right' ";
										if( "outbox_right" === thrixty_sc_gen_var.zoom_mode ){
											stuff += "selected";
										}
										stuff += ">Outbox Rechts</option>";
									// Possible Outbox Zoom on the Left
									stuff += "<option value='outbox_left' ";
										if( "outbox_left" === thrixty_sc_gen_var.zoom_mode ){
											stuff += "selected";
										}
										stuff += ">Outbox links</option>";
									// Possible Outbox Zoom at the Top
									stuff += "<option value='outbox_top' ";
										if( "outbox_top" === thrixty_sc_gen_var.zoom_mode ){
											stuff += "selected";
										}
										stuff += ">Outbox Oben</option>";
									// Possible Outbox Zoom at the Bottom
									stuff += "<option value='outbox_bottom' ";
										if( "outbox_bottom" === thrixty_sc_gen_var.zoom_mode ){
											stuff += "selected";
										}
										stuff += ">Outbox Unten</option>";
								stuff += "</select>";
							stuff += "</td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='seconds_per_turn'>seconds_per_turn</label></td>";
							stuff += "<td><input id='seconds_per_turn' name='seconds_per_turn' type='number' placeholder='"+thrixty_sc_gen_var.seconds_per_turn+"' /></td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='sensitivity_x'>sensitivity_x</label></td>";
							stuff += "<td><input id='sensitivity_x' name='sensitivity_x' type='number' placeholder='"+thrixty_sc_gen_var.sensitivity_x+"' /></td>";
						stuff += "</tr>";
						stuff += "<tr>";
							stuff += "<td><label for='direction'>direction</label></td>";
							stuff += "<td>";
								stuff += "<select id='direction' name='direction'>";
									if( "forward" === thrixty_sc_gen_var.direction ){
										stuff += "<option value='forward' selected>Vorwaerts</option>";
									} else {
										stuff += "<option value='forward'>Vorwaerts</option>";
									}
									if( "backward" === thrixty_sc_gen_var.direction ){
										stuff += "<option value='backward' selected>Rueckwaerts</option>";
									} else {
										stuff += "<option value='backward'>Rueckwaerts</option>";
									}
								stuff += "</select>";
							stuff += "</td>";
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
				function(){
					// send
					var elem = jQuery('#thrixty_generator_form')[0];

					var count = elem.length;
					var content = "[thrixty ";
					for( var i=0; i<count; i++ ){
						var current_elem = elem[i];
						switch( current_elem.name ){
							case "basepath":
								if( "" != current_elem.value ){
									content += "basepath=\""+current_elem.value+"\" ";
								} else if( "" == thrixty_sc_gen_var.basepath ){
									content += "basepath=\"|BASEPATH|\" ";
								}
								break;
							case "filelist_path_small":
								if( "" != current_elem.value ){
									content += "filelist_path_small=\""+current_elem.value+"\" ";
								} else {
									content += "filelist_path_small=\"|SMALL FILELIST|\" ";
								}
								break;
							case "filelist_path_large":
								if( "" != current_elem.value ){
									content += "filelist_path_large=\""+current_elem.value+"\" ";
								} else {
									content += "filelist_path_large=\"|LARGE FILELIST|\" ";
								}
								break;
							case "zoom_mode":
								var cur_val = current_elem[current_elem.selectedIndex].value;
								if( cur_val != thrixty_sc_gen_var.zoom_mode ){
									content += "zoom_mode=\""+current_elem.value+"\" ";
								}
								break;
							case "seconds_per_turn":
							console.log("seconds per turn execute");
								if( "" != current_elem.value ){
									content += "seconds_per_turn=\""+current_elem.value+"\" ";
								} else if( "" == thrixty_sc_gen_var.seconds_per_turn ){
									content += "seconds_per_turn=\"[SECONDS PER TURN]\" ";
								}
								break;
							case "sensitivity_x":
								if( "" != current_elem.value ){
									content += "sensitivity_x=\""+current_elem.value+"\" ";
								} else if( "" == thrixty_sc_gen_var.sensitivity_x ){
									content += "sensitivity_x=\"[SENSITIVITY X]\" ";
								}
								break;
							case "direction":
								var cur_val = current_elem[current_elem.selectedIndex].value;
								if( cur_val != thrixty_sc_gen_var.direction ){
									content += "direction=\""+current_elem.value+"\" ";
								}
								break;
							default:
								// buttons -> do nothing
								break;
						}
					}
					content += "]";
					tinymce_obj.selection.setContent(content);
					close_dialog();
				}
			);
			gen_container.find("#close").on(
				"click",
				function(){
					close_dialog();
				}
			);
			// alles erfolgreich?
			return true;
		} /*endif*/ else {
			// error?
			return false;
		}
	}
	function open_dialog(){
		overlay.show();
	}
	function close_dialog(){
		overlay.hide();
	}
	function button_click_event(tinymce_obj){
		open_dialog();
	}
});