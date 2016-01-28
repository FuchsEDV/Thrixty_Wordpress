<?php
	/**
	 * Plugin Name: Thrixty Player 1.6
	 * Plugin URI:
	 * Description: Wordpress Plugin, that is building a Player for 360° photography.
	 *   It uses Shortcodes to generate HTML-Code, ready to be used as the Players base.
	 *   The versionnumber of this plugin reflects the version of the used ThrixtyPlayer.
	 * Author: F.Heitmann @ Fuchs EDV
	 * Author URI:
	 * Version: 1.6
	 *
	 * @package Wordpress
	 * @subpackage Thrixty Player
	 * @since 4.1.0
	 * @version 1.6
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
					"basepath" => "__SITE__/360_objects/",
					"filelist_path_small" => "small/Filelist.txt",
					"filelist_path_large" => "large/Filelist.txt",
					"zoom_mode" => "", // player standard: inbox
					"outbox_position" => "", // player standard: right
					"position_indicator" => "", // player standard: minimap
					"zoom_control" => "", // player standard: progressive
					"direction" => "", // player standard: forward
					"cycle_duration" => "", // player standard: 5
					"sensitivity_x" => "", // player standard: 20
					"autoload" => "", // player standard: on
					"autoplay" => "", //  // player standard: -1 / infinite
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
		wp_enqueue_script('thrixty_init', plugins_url("thrixty_base/thrixty_init.js", __FILE__));
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

						$basepath = $thrixty_options["basepath"];
						$basepath_replaced = str_replace("__PLUGIN__", plugins_url("", __FILE__), $basepath);
						$upload_dir = wp_upload_dir();
						$basepath_replaced = str_replace("__UPLOAD__", $upload_dir["baseurl"], $basepath_replaced);
						$basepath_replaced = str_replace("__SITE__", get_site_url(), $basepath_replaced);
						$basepath_replaced = trailingslashit($basepath_replaced);

						$to_sc_gen = array(
							"basepath" => $basepath,
							"basepath_replaced" => $basepath_replaced,
							"object_name" => "",
							"filelist_path_small" => "small/Filelist.txt",
							"filelist_path_large" => "large/Filelist.txt",
							// "zoom_mode" => $thrixty_options["zoom_mode"],
							// "outbox_position" => $thrixty_options["outbox_position"],
							// "position_indicator" => $thrixty_options["position_indicator"],
							// "zoom_control" => $thrixty_options["zoom_control"],
							// "direction" => $thrixty_options["direction"],
							// "cycle_duration" => $thrixty_options["cycle_duration"],
							// "sensitivity_x" => $thrixty_options["sensitivity_x"],
							// "autoload" => $thrixty_options["autoload"],
							// "autoplay" => $thrixty_options["autoplay"],
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
		if( !$atts["object_name"] ){
			// abort - this shortcode is not interpretable
			// error = 1
			return "";
		} else {
			// fill the mandatory fields
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
			$basepath = trailingslashit($basepath);
			$div_attrs["basepath"] = $basepath;

			/* filelist_path_small */
			if( !!$atts["filelist_path_small"] ){
				$div_attrs["filelist_path_small"] = trailingslashit($atts["object_name"]).$atts["filelist_path_small"];
			} else if( !!$thrixty_options["filelist_path_small"] ){
				$div_attrs["filelist_path_small"] = trailingslashit($atts["object_name"]).$thrixty_options["filelist_path_small"];
			} else {
				$div_attrs["filelist_path_small"] = "";
			}
			/* filelist_path_large */
			if( !!$atts["filelist_path_large"] ){
				$div_attrs["filelist_path_large"] = trailingslashit($atts["object_name"]).$atts["filelist_path_large"];
			} else if( !!$thrixty_options["filelist_path_large"] ){
				$div_attrs["filelist_path_large"] = trailingslashit($atts["object_name"]).$thrixty_options["filelist_path_large"];
			} else {
				$div_attrs["filelist_path_large"] = "";
			}

			// check all mandatories being filled
			if( !$div_attrs["basepath"] && !$div_attrs["filelist_path_small"] && !$div_attrs["filelist_path_large"] ){
				// abort - the paths are not correct
				// error = 2
				return "";
			} else {
				/* append optionals where needed */
				/* zoom_mode */
				if( !!$atts["zoom_mode"] ){
					$div_attrs["zoom_mode"] = $atts["zoom_mode"];
				} else if( !!$thrixty_options["zoom_mode"] ){
					$div_attrs["zoom_mode"] = $thrixty_options["zoom_mode"];
				}
				/* outbox_position */
				if( !!$atts["outbox_position"] ){
					$div_attrs["outbox_position"] = $atts["outbox_position"];
				} else if( !!$thrixty_options["outbox_position"] ){
					$div_attrs["outbox_position"] = $thrixty_options["outbox_position"];
				}
				/* position_indicator */
				if( !!$atts["position_indicator"] ){
					$div_attrs["position_indicator"] = $atts["position_indicator"];
				} else if( !!$thrixty_options["position_indicator"] ){
					$div_attrs["position_indicator"] = $thrixty_options["position_indicator"];
				}
				/* zoom_control */
				if( !!$atts["zoom_control"] ){
					$div_attrs["zoom_control"] = $atts["zoom_control"];
				} else if( !!$thrixty_options["zoom_control"] ){
					$div_attrs["zoom_control"] = $thrixty_options["zoom_control"];
				}
				/* direction */
				if( !!$atts["direction"] ){
					$div_attrs["direction"] = $atts["direction"];
				} else if( !!$thrixty_options["direction"] ){
					$div_attrs["direction"] = $thrixty_options["direction"];
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
				/* autoload */
				if( !!$atts["autoload"] ){
					$div_attrs["autoload"] = $atts["autoload"];
				} else if( !!$thrixty_options["autoload"] ){
					$div_attrs["autoload"] = $thrixty_options["autoload"];
				}
				/* autoplay */
				if( !!$atts["autoplay"] ){
					$div_attrs["autoplay"] = $atts["autoplay"];
				} else if( !!$thrixty_options["autoplay"] ){
					$div_attrs["autoplay"] = $thrixty_options["autoplay"];
				}
			}
		}

		/* Build the Thrixty Div. */
		$returning = "<div ";
			$returning .= "id=\"thrixty_box_$player_counter\" "; /* this is, what the global counter is for */
			$returning .= "class=\"thrixty-player\" ";
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
				<p>Hier k&ouml;nnen Sie allgemeine Standardwerte festlegen.</p>
				<form action="options.php" method="post">
					<?php settings_fields('thrixty_options'); ?>
					<!--TODO: tooltip oder so-->
					<b>Bitte lesen Sie die <a href="#pageinfo">Seiteninformationen</a>, bevor Sie hier Einstellungen vornehmen!</b>
					<p>
						Nicht gef&uuml;llte Felder greifen auf die Standardwerte des Thrixtyplayers selbst zurück.
					</p>
					<style>
						#thrixty_settings_table td{
							vertical-align: top;
							border: 1px solid lightgray;
							border-collapse: collapse;
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
							<td rowspan="3">
								Dies ist der Grundpfad, von dem aus nach den beiden Filelists gesucht wird.<br>
								Beispiel: <b>http://example.com/360_pictures/</b>[filelist-paths]<br>
								F&uuml;r diese Option gibt es Shortcuts, die einen Teil des Pfades herleiten:<br>
								<b>"__SITE__"</b>: Verweist auf die URL der Startseite.<br>
								<b>"__PLUGIN__"</b>: Verweist auf Hauptordner dieses Plugins, wie er auch f&uuml;r die Ressourcen benutzt wird.<br>
								<b>"__UPLOAD__"</b>: Verweist auf den Uploadordner, der in Wordpress verwendet wird.<br>
								<?php if( isset($box3d_options["path"]) ){ ?>
									Der alte Wert aus Box3D: <b><?php echo $box3d_options["path"]; ?></b><br>
								<? } ?>
							</td>
						</tr>
						<tr>
							<td>Filelist Path Small</td>
							<td>
								<input id='plugin_filelist_path_small' name='thrixty_options[filelist_path_small]' size='40' type='text' placeholder='[!MANDATORY!]' value='<?php echo $thrixty_options['filelist_path_small']; ?>' />
							</td>
							<!--<td>filelist_path_small</td>-->
						</tr>
						<tr>
							<td>Filelist Path Large</td>
							<td>
								<input id='plugin_filelist_path_large' name='thrixty_options[filelist_path_large]' size='40' type='text' placeholder='[!MANDATORY!]' value='<?php echo $thrixty_options['filelist_path_large']; ?>' />
							</td>
							<!--<td>filelist_path_large</td>-->
						</tr>
						<tr>
							<td>Zoom Mode</td>
							<td>
								<input id='plugin_zoom_mode' name='thrixty_options[zoom_mode]' size='40' type='text' placeholder='[Thrixty Standard]' value='<?php echo $thrixty_options['zoom_mode']; ?>' />
							</td>
							<td>
								Hier kann die Zoom Art gew&auml;hlt werden.<br>
								Der Inbox-Zoom zoomt das Bild direkt gr&ouml;&szlig;er.<br>
								Der Outbox-Zoom erzeugt dagegen ein extra Fenster.<br>
								(Im Fullscreen wird aus dem Outbox vorr&uuml;bergehend in den normalen Inbox-Zoom gewechselt!)<br>
								M&ouml;gliche Werte:<br>
								<b>inbox</b>, outbox<br>
							</td>
						</tr>
						<tr>
							<td>Outbox Position</td>
							<td>
								<input id='plugin_outbox_position' name='thrixty_options[outbox_position]' size='40' type='text' placeholder='[Thrixty Standard]' value='<?php echo $thrixty_options['outbox_position']; ?>' />
							</td>
							<td>
								Wenn der Outbox Zoom verwendet wird, kann hier gew&auml;hlt werden, wo das Fenster auftauchen soll.<br>
								M&ouml;gliche Werte:<br>
								<b>right</b>, bottom, left, top<br>
							</td>
						</tr>
						<tr>
							<td>Position Indicator</td>
							<td>
								<input id='plugin_position_indicator' name='thrixty_options[position_indicator]' size='40' type='text' placeholder='[Thrixty Standard]' value='<?php echo $thrixty_options['position_indicator']; ?>' />
							</td>
							<td>
								Damit man sich in dem vergr&ouml;&szlig;erten Bild zurechtfindet, kann man dazu einen Markierer anzeigen.<br>
								Die Minimap ist ein stark verkleinertes Bild und beschreibt daran den momentanen Ausschnitt.<br>
								Der Marker ist ein Rechteck innerhalb des Bildes, das den momentanen Ausschnitt markiert.<br>
								M&ouml;gliche Werte:<br>
								<b>minimap</b>, marker, none<br>
							</td>
						</tr>
						<tr>
							<td>Zoom Control</td>
							<td>
								<input id='plugin_zoom_control' name='thrixty_options[zoom_control]' size='40' type='text' placeholder='[Thrixty Standard]' value='<?php echo $thrixty_options['zoom_control']; ?>' />
							</td>
							<td>
								Hier wird eingestellt, wie der Kunde sich in dem vergr&ouml;&szlig;erten Bild bewegen kann.<br>
								Im Progressiven Modus wird die Mausposition genutzt, um den Bildausschnitt laufend zu verschieben.<br>
								Im Klassischen Modus wird der Positions Anzeiger benutzt, um den Bildausschnitt zu verschieben.<br>
								M&ouml;gliche Werte:<br>
								<b>progressive</b>, classic<br>
							</td>
						</tr>
						<tr>
							<td>Direction</td>
							<td>
								<input id='plugin_direction' name='thrixty_options[direction]' size='40' type='text' placeholder='[Thrixty Standard]' value='<?php echo $thrixty_options['direction']; ?>' />
							</td>
							<td>
								Dies ist die Richtung, in die sich die Objekte drehen sollen.<br>
								Objekte, die sich im Uhrzeigersinn drehen, werden <b>"forward"</b> drehend genannt.<br>
								Objekte, die sich gegen den Uhrzeigersinn drehen, werden "backward" drehend genannt.<br>
								<b>Sie sollten Ihre Aufnahmen immer in diesselbe Richtung drehend aufnehmen!</b><br>
								Falls Sie Ihre Aufnahmen bereits gegen den Uhrzeigersinn aufgenommen haben, stellen Sie diese Option um auf "backward".<br>
								<?php if( isset($box3d_options["direction"]) ){ ?>
									Der alte Wert aus Box3D: <b><?php echo $box3d_options["direction"]; ?></b><br>
								<? } ?>
							</td>
						</tr>
						<tr>
							<td>Cycle Duration</td>
							<td>
								<input id='plugin_cycle_duration' name='thrixty_options[cycle_duration]' size='40' type='text' placeholder='[Thrixty Standard]' value='<?php echo $thrixty_options['cycle_duration']; ?>' />
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
								<input id='plugin_sensitivity_x' name='thrixty_options[sensitivity_x]' size='40' type='text' placeholder='[Thrixty Standard]' value='<?php echo $thrixty_options['sensitivity_x']; ?>' />
							</td>
							<td>
								Dies ist die Anzahl an Pixeln, ab welcher Distanz ein angefangener Klick als Geste z&auml;hlt.<br>
								Dies ist wichtig, um das "Wurstfinger-Problem" zu umgehen.<br>
								Die (empfohlene) Standardeinstellung: <b>20</b> Pixel<br>
							</td>
						</tr>
						<tr>
							<td>Autoload</td>
							<td>
								<input id='plugin_autoload' name='thrixty_options[autoload]' size='40' type='text' placeholder='[Thrixty Standard]' value='<?php echo $thrixty_options['autoload']; ?>' />
							</td>
							<td>
								Diese Option gibt an, ob die Player automatisch ihre Bilder laden sollen.<br>
								Auf Mobil-geräten wird diese Option vom Thrixty ignoriert und niemals Bilder automatisch geladen.</b>
								M&ouml;gliche Werte:<br>
								<b>on</b>, off<br>
							</td>
						</tr>
						<tr>
							<td>Autoplay</td>
							<td>
								<input id='plugin_autoplay' name='thrixty_options[autoplay]' size='40' type='text' placeholder='[Thrixty Standard]' value='<?php echo $thrixty_options['autoplay']; ?>' />
							</td>
							<td>
								Dies gibt an, ob Player Instanzen ihre Animation automatisch abspielen sollen.<br>
								M&ouml;gliche Werte:<br>
								<b>first</b>, all_on, all_off<br>
							</td>
						</tr>
					</table>
					<br>
					<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
				</form>
				<br>
				<hr>
				<h3 id="pageinfo">Seiteninformationen</h3>
				<p>
					&Uuml;ber den "3D" Button im Artikel Editor (visuell) k&ouml;nnen Sie einen Shortcode generieren.<br>
					Alle im <b><i>Shortcode</i></b> angegebenen Optionen <b><i>&uuml;berschreiben</i></b> die allgemeinen Einstellungen, die sie hier t&auml;tigen.<br>
					Die allgemeinen Einstellungen sind daf&uuml;r gedacht, <b><i>alle Objekte auf Ihrer Seite gleich darzustellen.</i></b><br>
				</p>
				<p>
					Wenn es f&uuml;r eine Option keinen allgemeing&uuml;ltigen Wert gibt (Feld leer lassen), dann wird die Option bei der Shortcodegenerierug abgefragt.<br>
					Dies wird mit den beiden Optionen <b>thrixtyplayer-filelist-path-small</b> und <b>thrixtyplayer-filelist-path-large</b> immer passieren!<br>
				</p>
				<p>
					Die allgemeinen Einstellungen sind vor allem dann hilfreich, wenn alle Ihre f&uuml;r den Player ben&ouml;tigten Listen im selben Grundordner liegen (empfohlen).<br>
				</p>
				<br>
				<p>
					Ein typischer Thrixty Shortcode:<br>
					<b>[thrixty object_name="example" ]</b><br>
					Die Player-Generierung fällt bei nicht angebenen Werten auf die Einstellungen dieser Seite zurück.<br>
					Sollten diese nicht gesetzt worden sein, kann es passieren, dass der Player die entsprechenden Bilddateien nicht findet.<br>
				</p>
				<hr>
				<h3>Error Sektion</h3>
				<p>
					&gt;&gt;&gt; Beschriebungen hier &lt;&lt;&lt;
				</p>
				<h3 id="paraminfo">Erkl&auml;rung der Parameter und ihrer Werte</h3>
				[Hier waren die Beschreibungen]
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
			// this array will be filled with posts to be updated by this function
			$post_arr = array();

			// build argument array for WP_Query
			//  - always look for posts with a box3d shortcode
			$wp_query_args = array("s" => "[box3d");
			//  - when given specific numbers instead of the keyword "all", filter them
			if( $post_ids_string != "all" ){
				$wp_query_args["post__in"] = array();
				$tmp_arr = explode(",", $post_ids_string);

				foreach( $tmp_arr as $id ){
					// remove everything, that is not a digit
					$id = preg_replace( "/[^0-9]/", "", $id );
					// add to query, if there is a number left
					if( $id != "" ){
						$wp_query_args["post__in"] []= $id;
					}
				} unset($tmp_arr, $id);
			}

			// The typical Shortcode RegEx which gets returned by "get_shortcode_regex()".
			//   The function delivers it with all registered Shortcodes though,
			//   so this is aiming for box3d shortcodes only.
			$shortcode_regex = '/\[(\[?)(box3d)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s';


			// execute the WP_Query and give back all found posts
			$the_query = new WP_Query( $wp_query_args );
			$posts = $the_query->posts;

			// loop through all those found posts
			foreach( $posts as $post ){

				// get single post text
				$post_content = $post->post_content;

				// use the regex to search for Shortcodes
				$hits = array();
				preg_match_all($shortcode_regex, $post_content, $hits, PREG_OFFSET_CAPTURE); // found things will get stored in $hits

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
					$new_shortcode = "[thrixty ";
					/// basepath
						// check for old shortcode attribute path
						if( isset($old_sc_atts["path"]) && $old_sc_atts["path"] != "" ){
							// if it was set, set basepath to that old value
							$new_shortcode .= "basepath='".$old_sc_atts["path"]."' ";
						// if not, check for different old and new basepath options
						} else if( isset($box3d_options["path"]) && isset($thrixty_options["basepath"]) && $box3d_options["path"] != $thrixty_options["basepath"] ){
							// if they are indeed different, the new shortcode with the old path and object can only work with a basepath attribute
							$new_shortcode .= "basepath='".$box3d_options["path"]."' ";
						}
					/// filelist small and large paths
						if( isset($old_sc_atts["object"]) && $old_sc_atts["object"] != "" ){
							$new_shortcode .= "object_name='".$old_sc_atts["object"]."' ";
							// TODO: if these paths are the same as in settings, do not append to the shortcode
							$new_shortcode .= "filelist_path_small='small/Filelist.txt' ";
							$new_shortcode .= "filelist_path_large='large/Filelist.txt' ";
						}
					/// direction
						if( isset($old_sc_atts["direction"]) && $old_sc_atts["direction"] != "" ){
							$new_shortcode .= "direction='".$old_sc_atts["direction"]."' ";
						}
					$new_shortcode .= "]";

					// write new shortcode in place of the old
					$post_content = substr_replace($post_content, $new_shortcode, $position, strlen($old_shortcode)-1 );
				}

				// assign new content to post
				// echo "<pre>";
				// 	var_dump($post_content);
				// echo "</pre>";
				$post->post_content = $post_content;
				wp_update_post($post);

			}
		}

	// /





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