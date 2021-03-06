<?php
	/**
	 * Plugin Name: Thrixty Player 2.2.1
	 * Plugin URI:
	 * Description: Wordpress Plugin, that is building a Player for 360° photography.
	 *   It uses Shortcodes to generate HTML-Code, ready to be used as the Players base.
	 *   The versionnumber of this plugin reflects the version of the used ThrixtyPlayer.
	 * Author: F.Heitmann @ Fuchs EDV
	 * Author URI:
	 * Version: 2.2.1
	 *
	 * @package Wordpress
	 * @subpackage Thrixty Player
	 * @since 4.1.0
	 * @version 2.2.1
	 */



	/**
	 * Plugin Installation Hook
	 *
	 * This hook ensures the existance of the option thrixty_options.
	 *
	 * @name thrixty_activation
	 *
	 * @since 4.1.0
	 *
	 * @param /
	 * @return /
	 *
	 */
	register_activation_hook(__FILE__, "thrixty_activation");
	function thrixty_activation(){
		// if not in the database, add the base settings
		if( !get_option("thrixty_options") ){
			add_option(
				"thrixty_options",
				array(
					"basepath" => "__SITE__/360shots_objekte/", // player standard: ""
					"filelist_path_small" => "", // player standard: "small/Filelist.txt"
					"filelist_path_large" => "", // player standard: "large/Filelist.txt"
					"zoom_control" => "", // player standard: progressive
					"zoom_mode" => "", // player standard: inbox
					"zoom_pointer" => "", // player standard: minimap
					"outbox_position" => "", // player standard: right
					"reversion" => "", // player standard: false
					"cycle_duration" => "", // player standard: 5
					"sensitivity_x" => "", // player standard: 20
					"autoplay" => "", //  // player standard: -1 / infinite
					"autoload" => "", // player standard: on
				)
			);
		}
	}



	/**
	 * Plugin Deinstallation Hook
	 *
	 * This hook removes the option thrixty_options.
	 *
	 * @name thrixty_uninstall
	 *
	 * @since 4.1.0
	 *
	 * @param /
	 * @return /
	 *
	 */
	register_uninstall_hook(__FILE__, "thrixty_uninstall");
	function thrixty_uninstall(){
		delete_option("thrixty_options");
	}



	/**
	 * Frontend Scripts and Styles
	 *
	 * This hook hooks in ressources needed for the Player.
	 *
	 * @name thrixty_register_ressources
	 *
	 * @since 4.1.0
	 *
	 * @param /
	 * @return /
	 *
	 */
	add_action("init", "thrixty_register_ressources");
	function thrixty_register_ressources(){
		global $player_counter;
		// This global is counting the number of initialized Players.
		$player_counter = 0;
		// the following script loads all dependencies automatically
		wp_enqueue_script('thrixty_init_js', plugins_url("thrixty_base/thrixty.js", __FILE__));
		wp_enqueue_style('thrixty_init_css', plugins_url("thrixty_base/thrixty.css", __FILE__));
	}



	/**
	 * Shortcode Generator Button for TinyMCE
	 *
	 * This hook includes the thrixty tinymce plugin into tinymce.
	 *
	 * @name thrixty_shortcode_handler
	 *
	 * @since 4.1.0
	 *
	 * @param /
	 * @return /
	 *
	 */
	add_action('init', 'add_thrixty_gen_button');
	function add_thrixty_gen_button() {
		global $pagenow;
		// check for current page being an editor page || TODO: verbessern
		if( 'post.php' == $pagenow || 'post-new.php' == $pagenow ){
			// also check for user capabilities | (split for readability)
			if( current_user_can('edit_posts') || current_user_can('edit_pages') ){
				if( "true" == get_user_option('rich_editing') ) {
					// Add button functionality in form of a javascript plugin file.
					add_filter('mce_external_plugins', 'integrate_thrixty_mce_plugin');
					function integrate_thrixty_mce_plugin($plugin_array) {
						$plugin_array['thrixty'] = plugins_url('tinymce_plugin/thrixty_shortcode_generator.js' , __FILE__ );
						return $plugin_array;
					}
					// Add the button itself.
					add_filter('mce_buttons', 'show_shortcode_generator_button');
					function show_shortcode_generator_button($buttons) {
						array_push($buttons, "|", "thrixty");
						return $buttons;
					}
					// Build a JS global, which is needed by the generator for the user dialog.
					add_action('wp_print_scripts', 'give_thrixty_options_to_js');
					function give_thrixty_options_to_js(){
						$thrixty_options = get_option("thrixty_options");
						$upload_dir = wp_upload_dir();
						$to_sc_gen = array(
							"__PLUGIN__" => plugins_url("", __FILE__),
							"__UPLOAD__" => $upload_dir["baseurl"],
							"__SITE__" => get_site_url(),
							"basepath" => $thrixty_options["basepath"],
							// "object_name" => "",
							"filelist_path_small" => $thrixty_options["filelist_path_small"] ? $thrixty_options["filelist_path_small"] : "small/Filelist.txt",
							"filelist_path_large" => $thrixty_options["filelist_path_large"] ? $thrixty_options["filelist_path_large"] : "large/Filelist.txt",
							// "zoom_control" => $thrixty_options["zoom_control"],
							// "zoom_mode" => $thrixty_options["zoom_mode"],
							// "zoom_pointer" => $thrixty_options["zoom_pointer"],
							// "outbox_position" => $thrixty_options["outbox_position"],
							// "reversion" => $thrixty_options["reversion"],
							// "cycle_duration" => $thrixty_options["cycle_duration"],
							// "sensitivity_x" => $thrixty_options["sensitivity_x"],
							// "autoplay" => $thrixty_options["autoplay"],
							// "autoload" => $thrixty_options["autoload"],
						);
						// JSON en- and de-coding to translate PHP hash into JS object
						?><script>
							var thrixty_sc_gen_var = '<?php echo json_encode($to_sc_gen); ?>';
							thrixty_sc_gen_var = JSON.parse(thrixty_sc_gen_var);
						</script><?php
					}
				}
			}
		}
	}



	/**
	 * Shortcode Handler for Posts
	 *
	 * This hook registers the Shortcode "thrixty".
	 *
	 * @name thrixty_shortcode_handler
	 *
	 * @since 4.1.0
	 *
	 * @param /
	 * @return /
	 *
	 */
	add_shortcode("thrixty", "thrixty_shortcode_handler");
	function thrixty_shortcode_handler($atts){
		global $player_counter; // This global is counting the number of initialized Players.

		$atts = $atts;
		$thrixty_options = get_option("thrixty_options");
		$div_attrs = array();
		/* basepath */
			if( !!$atts["basepath"] ){
				$basepath = $atts["basepath"];
			} else if( !!$thrixty_options["basepath"] ){
				$basepath = $thrixty_options["basepath"];
			} else {
				$basepath = "";
			}
			$basepath = str_replace("__PLUGIN__", plugins_url("", __FILE__), $basepath);
			$upload_dir = wp_upload_dir();
			$basepath = str_replace("__UPLOAD__", $upload_dir["baseurl"], $basepath);
			$basepath = str_replace("__SITE__", get_site_url(), $basepath);

			/* $atts["object_name"] only exists as a shortcode attribute !!! */
			$basepath = trailingslashit(trailingslashit($basepath).$atts["object_name"]);
			$div_attrs["basepath"] = $basepath;
		/* filelist_path_small */
			if( !!$atts["filelist_path_small"] ){
				$div_attrs["filelist_path_small"] = $atts["filelist_path_small"];
			} else if( !!$thrixty_options["filelist_path_small"] ){
				$div_attrs["filelist_path_small"] = $thrixty_options["filelist_path_small"];
			}
		/* filelist_path_large */
			if( !!$atts["filelist_path_large"] ){
				$div_attrs["filelist_path_large"] = $atts["filelist_path_large"];
			} else if( !!$thrixty_options["filelist_path_large"] ){
				$div_attrs["filelist_path_large"] = $thrixty_options["filelist_path_large"];
			}
		/* zoom_control */
			if( !!$atts["zoom_control"] ){
				$div_attrs["zoom_control"] = $atts["zoom_control"];
			} else if( !!$thrixty_options["zoom_control"] ){
				$div_attrs["zoom_control"] = $thrixty_options["zoom_control"];
			}
		/* zoom_mode */
			if( !!$atts["zoom_mode"] ){
				$div_attrs["zoom_mode"] = $atts["zoom_mode"];
			} else if( !!$thrixty_options["zoom_mode"] ){
				$div_attrs["zoom_mode"] = $thrixty_options["zoom_mode"];
			}
		/* zoom_pointer */
			if( !!$atts["zoom_pointer"] ){
				$div_attrs["zoom_pointer"] = $atts["zoom_pointer"];
			} else if( !!$thrixty_options["zoom_pointer"] ){
				$div_attrs["zoom_pointer"] = $thrixty_options["zoom_pointer"];
			}
		/* outbox_position */
			if( !!$atts["outbox_position"] ){
				$div_attrs["outbox_position"] = $atts["outbox_position"];
			} else if( !!$thrixty_options["outbox_position"] ){
				$div_attrs["outbox_position"] = $thrixty_options["outbox_position"];
			}
		/* reversion */
			if( !!$atts["reversion"] ){
				$div_attrs["reversion"] = $atts["reversion"];
			} else if( !!$thrixty_options["reversion"] ){
				$div_attrs["reversion"] = $thrixty_options["reversion"];
			}
		/* cycle_duration */
			if( !!$atts["cycle_duration"] ){
				$div_attrs["cycle_duration"] = $atts["cycle_duration"];
			} else if( !!$thrixty_options["cycle_duration"] ){
				$div_attrs["cycle_duration"] = $thrixty_options["cycle_duration"];
			}
		/* sensitivity_x */
			if( !!$atts["sensitivity_x"] ){
				$div_attrs["sensitivity_x"] = $atts["sensitivity_x"];
			} else if( !!$thrixty_options["sensitivity_x"] ){
				$div_attrs["sensitivity_x"] = $thrixty_options["sensitivity_x"];
			}
		/* autoplay */
			if( !!$atts["autoplay"] ){
				$div_attrs["autoplay"] = $atts["autoplay"];
			} else if( !!$thrixty_options["autoplay"] ){
				$div_attrs["autoplay"] = $thrixty_options["autoplay"];
			}
		/* autoload */
			if( !!$atts["autoload"] ){
				$div_attrs["autoload"] = $atts["autoload"];
			} else if( !!$thrixty_options["autoload"] ){
				$div_attrs["autoload"] = $thrixty_options["autoload"];
			}
		/* Build the Thrixty Div. */
			$returning = "<div ";
				$returning .= "id=\"thrixty_box_$player_counter\" "; /* this is, what the global counter is for */
				$returning .= "class=\"thrixty\" ";
				$returning .= "tabindex=\"$player_counter\" "; /* this is, what the global counter is for */
				/* Convert attributes array to actual HTML-attributes on the div. */
				foreach( $div_attrs as $key => $value ){
					/* Wordpress's shortcodes cant stand hyphens... */
					/* So we use underscores and translate them back into hypens later. */
					$returning .= "thrixty-".str_replace("_", "-", $key)."=\"$value\" ";
				}
			$returning .= "></div>";
		$player_counter += 1;
		return $returning;
	}



	/**
	 * Admin Initialisation
	 *
	 * This hook introduces a settings section.
	 *
	 * @name thrixty_admin_init
	 *
	 * @since 4.1.0
	 *
	 * @param /
	 * @return /
	 *
	 */
	add_action('admin_init', 'thrixty_admin_init');
	function thrixty_admin_init(){
		register_setting( 'thrixty_options', 'thrixty_options');
	}



	/**
	 * Thrixty Player Settings Page for Admin Backend
	 *
	 * This hook adds a settings page to the admin backend.
	 *
	 * @name thrixty_settings_site
	 *
	 * @since 4.1.0
	 *
	 * @param /
	 * @return /
	 *
	 */
	add_action('admin_menu', 'thrixty_settings_site');
	function thrixty_settings_site() {
		/* capability 'manage_options' needed */
		add_options_page('Thrixty Player', 'Thrixty Player', 'manage_options', 'thrixty_options_page', 'thrixty_options_page_recieve');
	}
	// These functions belong to the thrixty settings page.
		function thrixty_options_page_recieve(){
			// if this POST var was set, the button for box3d to thrixty conversion was pressed.
			if( isset($_POST['post_ids']) ){
				thrixty_convert_box3d_shortcodes($_POST['post_ids']);
			}
			thrixty_options_page_html();
		}
		/**
		 * Thrixty Player Settings Page HTML
		 *
		 * This function builds the HTML for the settings page.
		 *
		 * @name thrixty_options_page_html
		 *
		 * @param /
		 * @return /
		 */
		function thrixty_options_page_html() {
			$thrixty_options = get_option('thrixty_options');
			$box3d_options = get_option("box3d_options");

			?>
			<div class="wrap">
				<h2>Thrixty Player - Allgemeine Einstellungen</h2>
				<hr>
				<h3>Bitte lesen Sie diese <u>Seiteninformationen</u>, bevor Sie hier Einstellungen vornehmen!</h3>
				<p>
					Dieses Plugin arbeitet mit <b><i>Wordpress-Shortcodes</i></b> und generiert daraus Instanzen des Thrixtys.<br>
					<br>
					Der Thrixty selbst hat eigene <b><i>Standardeinstellungen</i></b>.<br>
					Diese können <b><i>von den Plugineinstellungen &uuml;berschrieben</i></b> werden, sofern sie gesetzt wurden.<br>
					Alle speziell <b><i>im Shortcode angebenen Parameter &uuml;berschreiben alle anderen Einstellungen</i></b>.<br>
					<br>
					Die allgemeinen Einstellungen auf dieser Seite sind daf&uuml;r gedacht, alle Objekte auf Ihrer Seite gleich darzustellen.<br>
					Sie sollten dabei darauf achten, <b><i>so wenige Einstellungen wie m&ouml;glich zu setzen</i></b>!<br>
					<br>
					Die Generierung der Shortcodes geschieht &uuml;ber das 360Shots Logo im Beitragseditor (visuell).<br>
					<br>
					Wir raten Ihnen dringend, dass Sie Ihre <b><i>Animationen in einem zentralen Ordner</i></b> ablegen, auf den Sie dann in der Einstellung "Basepath" verweisen!<br>
					Hier wurde als Standardeinstellung angenommen, dass Sie sie in <b>"[root]/360shots_objekte/"</b> ablegen.<br>
					<br>
				</p>
				<hr>
				<form action="options.php" method="post">
					<?php settings_fields('thrixty_options'); ?>
					<style>
						#thrixty_settings_table th,
						#thrixty_settings_table td{
							vertical-align: top;
							padding: 5px 3px;
							border: 1px solid lightgray;
							border-collapse: collapse;
							margin: 0;
						}
						#thrixty_settings_table td input,
						#thrixty_settings_table td select{
							width: 100%;
							line-height: 1.5em;
							border: 1px solid #cccccc;
							margin: 0;
						}
					</style>
					<table id="thrixty_settings_table">
						<tr>
							<th>Option</th>
							<th>Value</th>
							<th>Description</th>
						</tr>
						<tr>
							<td>Basepath</td>
							<td>
								<input id='plugin_basepath' name='thrixty_options[basepath]' size='40' type='text' placeholder='[!MANDATORY!]' value='<?php echo $thrixty_options['basepath']; ?>' />
							</td>
							<td rowspan="4">
								Dies ist der Grundpfad, von dem aus nach den beiden Filelists gesucht wird.<br>
								Format: <b>[Basepath/][object]/[filelist-path]</b><br>
								Beispiel: <b>http://example.com/360_pictures/ cake / small/Filelist.txt</b><br>
								F&uuml;r diese Option gibt es Shortcuts, die einen Teil des Pfades herleiten:<br>
								<b>"__SITE__"</b>: Verweist auf die URL der Startseite.<br>
								<b>"__PLUGIN__"</b>: Verweist auf Hauptordner dieses Plugins, wie er auch f&uuml;r die Ressourcen benutzt wird.<br>
								<b>"__UPLOAD__"</b>: Verweist auf den Uploadordner, der in Wordpress verwendet wird.<br>
								<?php if( isset($box3d_options["path"]) ){ ?>
									Der alte Basepath Wert aus Box3D: <b><?php echo $box3d_options["path"]; ?></b><br>
								<? } ?>
							</td>
						</tr>
						<tr>
							<td>Object</td>
							<td>
								<input size='40' type='text' placeholder='[immer Shortcode-spezifisch]' value='[immer Shortcode-spezifisch]' disabled />
							</td>
							<!--<td>object_name</td>-->
						</tr>
						<tr>
							<td>Filelist Path Small</td>
							<td>
								<input id='plugin_filelist_path_small' name='thrixty_options[filelist_path_small]' size='40' type='text' placeholder='[Thrixty Standard] small/Filelist.txt' value='<?php echo $thrixty_options['filelist_path_small']; ?>' />
							</td>
							<!--<td>filelist_path_small</td>-->
						</tr>
						<tr>
							<td>Filelist Path Large</td>
							<td>
								<input id='plugin_filelist_path_large' name='thrixty_options[filelist_path_large]' size='40' type='text' placeholder='[Thrixty Standard] large/Filelist.txt' value='<?php echo $thrixty_options['filelist_path_large']; ?>' />
							</td>
							<!--<td>filelist_path_large</td>-->
						</tr>
						<tr>
							<td>Zoom Control</td>
							<td>
								<?php $zc = $thrixty_options['zoom_control']; ?>
								<select id='plugin_zoom_control' name='thrixty_options[zoom_control]'>
									<option <?php if( $zc == ""            ){ echo "selected "; } ?>value=""           >[Thrixty Standard] progressive</option>
									<option <?php if( $zc == "progressive" ){ echo "selected "; } ?>value="progressive">Progressive</option>
									<option <?php if( $zc == "classic"     ){ echo "selected "; } ?>value="classic"    >Classic</option>
								</select>
							</td>
							<td>
								Hier wird eingestellt, wie der Kunde sich in dem vergr&ouml;&szlig;erten Bild bewegen kann.<br>
								Im Progressiven Modus wird die Mausposition genutzt, um den Bildausschnitt laufend zu verschieben.<br>
								Im Klassischen Modus wird der Zoom Pointer benutzt, um den Bildausschnitt zu verschieben.<br>
								M&ouml;gliche Werte: <i><b>progressive</b>, classic</i>
							</td>
						</tr>
						<tr>
							<td>Zoom Mode</td>
							<td>
								<?php $zm = $thrixty_options['zoom_mode']; ?>
								<select id='plugin_zoom_mode' name='thrixty_options[zoom_mode]'>
									<option <?php if( $zm == ""       ){ echo "selected "; } ?>value=""      >[Thrixty Standard] inbox</option>
									<option <?php if( $zm == "inbox"  ){ echo "selected "; } ?>value="inbox" >Inbox</option>
									<option <?php if( $zm == "outbox" ){ echo "selected "; } ?>value="outbox">Outbox</option>
									<option <?php if( $zm == "none"   ){ echo "selected "; } ?>value="none"  >None</option>
								</select>
							</td>
							<td>
								Hier kann die Zoom Art gew&auml;hlt werden.<br>
								Der Inbox-Zoom zoomt das Bild direkt gr&ouml;&szlig;er.<br>
								Der Outbox-Zoom erzeugt dagegen ein extra Fenster.<br>
								(Im Fullscreen wird aus dem Outbox vorr&uuml;bergehend in den normalen Inbox-Zoom gewechselt!)<br>
								M&ouml;gliche Werte: <i><b>inbox</b>, outbox, none</i>
							</td>
						</tr>
						<tr>
							<td>Zoom Pointer</td>
							<td>
								<?php $zp = $thrixty_options['zoom_pointer']; ?>
								<select id='plugin_zoom_pointer' name='thrixty_options[zoom_pointer]'>
									<option <?php if( $zp == ""        ){ echo "selected "; } ?>value=""       >[Thrixty Standard] minimap</option>
									<option <?php if( $zp == "minimap" ){ echo "selected "; } ?>value="minimap">minimap</option>
									<option <?php if( $zp == "marker"  ){ echo "selected "; } ?>value="marker" >marker</option>
									<option <?php if( $zp == "none"    ){ echo "selected "; } ?>value="none"   >none</option>
								</select>
							</td>
							<td>
								Damit man sich in dem vergr&ouml;&szlig;erten Bild zurechtfindet, kann man dazu einen Markierer anzeigen.<br>
								Die Minimap ist ein stark verkleinertes Bild und beschreibt daran den momentanen Ausschnitt.<br>
								Der Marker ist ein Rechteck innerhalb des Bildes, das den momentanen Ausschnitt markiert.<br>
								M&ouml;gliche Werte: <i><b>minimap</b>, marker, none</i>
							</td>
						</tr>
						<tr>
							<td>Outbox Position</td>
							<td>
								<?php $op = $thrixty_options['outbox_position']; ?>
								<select id='plugin_outbox_position' name='thrixty_options[outbox_position]'>
									<option <?php if( $op == ""       ){ echo "selected "; } ?>value=""      >[Thrixty Standard] right</option>
									<option <?php if( $op == "right"  ){ echo "selected "; } ?>value="right" >right</option>
									<option <?php if( $op == "bottom" ){ echo "selected "; } ?>value="bottom">bottom</option>
									<option <?php if( $op == "left"   ){ echo "selected "; } ?>value="left"  >left</option>
									<option <?php if( $op == "top"    ){ echo "selected "; } ?>value="top"   >top</option>
								</select>
							</td>
							<td>
								Wenn der Outbox Zoom verwendet wird, kann hier gew&auml;hlt werden, wo das Fenster auftauchen soll.<br>
								M&ouml;gliche Werte: <i><b>right</b>, bottom, left, top</i>
							</td>
						</tr>
						<tr>
							<td>Reversion</td>
							<td>
								<?php $rev = $thrixty_options['reversion']; ?>
								<select id='plugin_reversion' name='thrixty_options[reversion]'>
									<option <?php if( $rev == ""      ){ echo "selected "; } ?>value=""     >[Thrixty Standard] false</option>
									<option <?php if( $rev == "false" ){ echo "selected "; } ?>value="false">false</option>
									<option <?php if( $rev == "true"  ){ echo "selected "; } ?>value="true" >true</option>
								</select>
							</td>
							<td>
								Diese Option ermöglicht Ihnen, die Animation an die Maus/Berührungs-Steuerung anzupassen.<br>
								<b>Sie sollten Ihre Aufnahmen immer im Uhrzeigersinn drehend aufnehmen!</b><br>
								Falls Sie Ihre Aufnahmen bereits gegen den Uhrzeigersinn aufgenommen haben, stellen Sie diese Option um auf "true".<br>
								<?php if( isset($box3d_options["reversion"]) ){ ?>
									Der alte Wert aus Box3D: <b><?php echo $box3d_options["reversion"]; ?></b><br>
								<? } ?>
							</td>
						</tr>
						<tr>
							<td>Cycle Duration</td>
							<td>
								<input id='plugin_cycle_duration' name='thrixty_options[cycle_duration]' size='40' type='text' placeholder='[Thrixty Standard] 5' value='<?php echo $thrixty_options['cycle_duration']; ?>' />
							</td>
							<td>
								Dies ist die Zeit, die eine ganze Umdrehung dauern soll. Dies sollte f&uuml;r alle Objekte gleich sein, um Gleichm&auml;&szlig;igkeit &uuml;ber die ganze Seite zu gew&auml;hrleisten.<br>
								Die (empfohlene) Standardeinstellung: <b>5</b> Sekunden f&uuml;r eine komplette Drehung<br>
								<?php if( isset($box3d_options["framerate"]) ){ ?>
									<td><?php echo (72 / $box3d_options["framerate"]); ?></td>
								<? } ?>
							</td>
						</tr>
						<tr>
							<td>Sensitivity X</td>
							<td>
								<input id='plugin_sensitivity_x' name='thrixty_options[sensitivity_x]' size='40' type='text' placeholder='[Thrixty Standard] 20' value='<?php echo $thrixty_options['sensitivity_x']; ?>' />
							</td>
							<td>
								Dies ist die Anzahl an Pixeln, ab welcher Distanz ein angefangener Klick als Geste z&auml;hlt.<br>
								Dies ist wichtig, um das "Wurstfinger-Problem" zu umgehen.<br>
								Die (empfohlene) Standardeinstellung: <b>20</b> Pixel<br>
							</td>
						</tr>
						<tr>
							<td>Autoplay</td>
							<td>
								<input id='plugin_autoplay' name='thrixty_options[autoplay]' size='40' type='text' placeholder='[Thrixty Standard] infinite' value='<?php echo $thrixty_options['autoplay']; ?>' />
							</td>
							<td>
								Dies gibt an, ob Player Instanzen ihre Animation automatisch abspielen sollen.<br>
								M&ouml;gliche Werte: <i><b>-1, on (infinite)</b>, [integer] (finite), 0 / off</i>
							</td>
						</tr>
						<tr>
							<td>Autoload</td>
							<td>
								<?php $al = $thrixty_options['autoload']; ?>
								<select id='plugin_autoload' name='thrixty_options[autoload]'>
									<option <?php if( $al == ""    ){ echo "selected "; } ?>value=""   >[Thrixty Standard] on</option>
									<option <?php if( $al == "on"  ){ echo "selected "; } ?>value="on" >On</option>
									<option <?php if( $al == "off" ){ echo "selected "; } ?>value="off">Off</option>
								</select>
							</td>
							<td>
								Diese Option gibt an, ob die Player automatisch ihre Bilder laden sollen.<br>
								Auf Mobil-geräten wird diese Option vom Thrixty ignoriert und niemals Bilder automatisch geladen.<br>
								M&ouml;gliche Werte: <i><b>on</b>, off</i>
							</td>
						</tr>
					</table>
					<br>
					<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
				</form>
				<br>
				<hr>
				<h3 id="converter">Box3D zu Thrixty konvertieren</h3>
				<form name="test" action="options-general.php?page=thrixty_options_page" method="post">
					<p>
						Fuer den Fall, dass Sie vorher Box3D verwendet haben, k&ouml;nnen Sie hier automatisch Box3D Shortcodes in Thrixty Shortcodes &uuml;bersetzen lassen.<br>
						Bitte bedenken Sie dabei, dass es einige Funktionen nicht mehr gibt oder auch neue dazu gekommen sind.<br>
						Die Filelists selber werden NICHT kontrolliert!<br>
						<br>
						<b>Bitte setzen Sie ihre Einstellungen, <i>BEVOR</i></b> Sie diese Funktion nutzen!<br>
						Insbesondere <b>Basepath</b> sollte gesetzt sein!<br>
						<br>
						Geben Sie entweder "all" an, um alle Posts und Pages nach dem alten Shortcode durchsuchen zu lassen, oder eine komma-getrennte Liste mit den entsprechenden IDs.<br>
						<input name="post_ids" type="text" placeholder="Post IDs or 'all'" />
						<input name="Submit" type="submit" value="Start Box3D to Thrixty Conversion" /><br>
						<i><b>ACHTUNG! Dies kann nur per Hand r&uuml;ckg&auml;ngig gemacht werden!</b></i><br>
					</p>
				</form>
			</div><?php
		}



		/**
		 * Thrixty Player Settings Shortcode Converter
		 *
		 * This function converts box3d shortcodes into thrixty ones.
		 *
		 * @name thrixty_convert_box3d_shortcodes
		 *
		 * @param String post_ids of posts to be converted (comma seperated)
		 * @return /
		 */
		function thrixty_convert_box3d_shortcodes($post_ids_string){
			global $wpdb;

			$post_ids = array();

			if( $post_ids_string == "all" ){
				// select post ids
				$query = "SELECT id
					FROM wp_posts
					WHERE post_content LIKE '%[box3d%';";
				$results = $wpdb->get_results($query, OBJECT_K);

				foreach( $results as $i => $v ){
					$post_ids []= $i;
				} unset( $i, $v );

			} else {
				// parse the string as a list of post ids
				$tmp_arr = explode(",", $post_ids_string);
				foreach( $tmp_arr as $i => $v ){
					// remove everything, that is not a digit
					$v = preg_replace( "/[^0-9]/", "", $v );
					// add to query, if there is a number left
					if( $v != "" ){
						$post_ids []= (integer) $v;
					}
				} unset( $i, $v );

				unset( $tmp_arr );
			}

			// next select all posts with these ids
			$query = "SELECT id, post_content FROM ".$wpdb->prefix."posts WHERE id IN (".implode(", ", $post_ids).")";
			$posts = $wpdb->get_results($query, OBJECT_K);

			// The typical Shortcode RegEx which gets returned by "get_shortcode_regex()".
			//   The function delivers it with all registered Shortcodes though,
			//   so this is aiming for box3d shortcodes only.
			$shortcode_regex = '/\[(\[?)(box3d)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s';

			foreach( $posts as $cur_post ){

				// get single post text
				$cur_post_content = $cur_post->post_content;

				// use the regex to search for Shortcodes
				$hits = array();
				preg_match_all($shortcode_regex, $cur_post_content, $hits, PREG_OFFSET_CAPTURE); // found things will get stored in $hits

				// replace the last shortcodes first!!!
				$hits = array_reverse($hits[0]);

				// loop through all found shortcodes
				foreach( $hits as $hit ){
					// shortcode_parse_atts gets confused, when
					//   the space before the closing bracket is missing...
					$old_shortcode = substr_replace( $hit[0], " ", -1, 0 );

					$position = $hit[1];

					// parse shortcode attributes to array
					$old_sc_atts = shortcode_parse_atts($old_shortcode);

					//// generate thrixty shortcode
					$new_shortcode = "[thrixty";
					/// basepath
						// check for old shortcode attribute path
						if( isset($old_sc_atts["path"]) && $old_sc_atts["path"] != "" ){
							// if it was set, set basepath to that old value
							$new_shortcode .= " basepath='".$old_sc_atts["path"]."'";
						// if not, check for different old and new basepath options
						} else if( isset($box3d_options["path"]) && isset($thrixty_options["basepath"]) && $box3d_options["path"] != $thrixty_options["basepath"] ){
							// if they are indeed different, the new shortcode with the old path and object can only work with a basepath attribute
							$new_shortcode .= " basepath='".$box3d_options["path"]."'";
						}
					/// filelist small and large paths
						if( isset($old_sc_atts["object"]) && $old_sc_atts["object"] != "" ){
							$new_shortcode .= " object_name='".$old_sc_atts["object"]."'";
						}
					/// direction
						if( isset($old_sc_atts["direction"]) && $old_sc_atts["direction"] != "" ){
							$new_shortcode .= " reversion='".$old_sc_atts["direction"]."'";
						}
					$new_shortcode .= "]";

					// write new shortcode in place of the old
					$cur_post_content = substr_replace($cur_post_content, $new_shortcode, $position, strlen($old_shortcode)-1 );
				}

				$query = "UPDATE ".$wpdb->prefix."posts SET post_content = '".addslashes($cur_post_content)."' WHERE id = ".$cur_post->id.";";
				$wpdb->query( $query );

			} unset( $cur_post );

		}

	// /thrixty settings page





	// Development Stuff
	// just ignore :)

	//// disables automatic saving of drafts
	// add_action( 'wp_print_scripts', 'disableAutoSave' );
	// function disableAutoSave(){
	// 	wp_deregister_script('autosave');
	// }

	//// shows all posts with a box3d shortcode
	// [shortcodefinder find="box3d"]
	// add_shortcode('shortcodefinder', 'wpb_find_shortcode');
	// function wpb_find_shortcode($atts, $content=null) {
	// 	ob_start();
	// 	extract(
	// 		shortcode_atts(
	// 			array(
	// 				'find' => '',
	// 			),
	// 			$atts
	// 		)
	// 	);
	// 	$string = $atts['find'];
	//
	// 	$args = array(
	// 		's' => '['.$string,
	// 	);
	//
	// 	$the_query = new WP_Query( $args );
	//
	// 	if ( $the_query->have_posts() ) {
	// 		echo '<ul>';
	// 		while ( $the_query->have_posts() ) {
	// 			$the_query->the_post();
	// 			echo "<li><a href='".the_permalink()."'>";
	// 				the_title();
	// 			echo "</a></li>";
	// 		}
	// 		echo '</ul>';
	// 	} else {
	// 		echo "Sorry no posts using Shortcode \"[".$string." ]\" found";
	// 	}
	//
	// 	wp_reset_postdata();
	// 	return ob_get_clean();
	// }

?>