<?php
	/**
	 * Plugin Name: Thrixty Player
	 * Plugin URI:
	 * Description: Wordpress Plugin, that is building a Player for 360° photography.
	 *   It uses Shortcodes to generate HTML-Code, ready to be used as the Players base.
	 *   The versionnumber of this plugin reflects the version of the used ThrixtyPlayer.
	 * Author: F.Heitmann @ Fuchs EDV
	 * Author URI:
	 * Version: 1.3dev
	 *
	 * @package Wordpress
	 * @subpackage Thrixty Player
	 * @since 4.1.0
	 * @version 1.3dev
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
					"basepath" => "__PLUGIN__/objects/",
					"zoom_mode" => "inbox",
					"zoom_control" => "progressive",
					"position_indicator" => "minimap",
					"outbox_position" => "right",
					"seconds_per_turn" => "5",
					"sensitivity_x" => "20",
					"direction" => "forward",
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

		add_settings_section('thrixty_settings_section', 'Thrixty Player Einstellungen', 'thrixty_options_section', 'thrixty_options_page');
			add_settings_field('plugin_basepath', 'Basepath', 'thrixty_options_basepath', 'thrixty_options_page', 'thrixty_settings_section');
			add_settings_field('plugin_zoom_mode', 'Zoom Mode', 'thrixty_options_zoom_mode', 'thrixty_options_page', 'thrixty_settings_section');
			add_settings_field('plugin_zoom_control', 'Zoom Control', 'thrixty_options_zoom_control', 'thrixty_options_page', 'thrixty_settings_section');
			add_settings_field('plugin_position_indicator', 'Position Indicator', 'thrixty_options_position_indicator', 'thrixty_options_page', 'thrixty_settings_section');
			add_settings_field('plugin_outbox_position', 'Outbox Position', 'thrixty_options_outbox_position', 'thrixty_options_page', 'thrixty_settings_section');
			add_settings_field('plugin_seconds_per_turn', 'Seconds per Turn', 'thrixty_options_seconds_per_turn', 'thrixty_options_page', 'thrixty_settings_section');
			add_settings_field('plugin_sensitivity_x', 'Sensitivity X', 'thrixty_options_sensitivity_x', 'thrixty_options_page', 'thrixty_settings_section');
			add_settings_field('plugin_direction', 'Direction', 'thrixty_options_direction', 'thrixty_options_page', 'thrixty_settings_section');
	}
	// These functions belong to the admin init...
		function thrixty_options_section(){
		?>
			<b>Bitte lesen Sie die <a href="#pageinfo">Seiteninformationen</a> und die <a href="#paraminfo">Parametererkl&auml;rungen</a>, bevor Sie hier Einstellungen vornehmen!</b>
			<p>
				Nicht gef&uuml;llte Felder greifen auf die Standardwerte des Thrixtyplayers selbst zurück.
			</p>
		<?php
		}
		function thrixty_options_basepath() {
			$options = get_option('thrixty_options');
			echo "<input id='plugin_basepath' name='thrixty_options[basepath]' size='40' type='text' value='{$options['basepath']}' />";
		}
		function thrixty_options_seconds_per_turn() {
			$options = get_option('thrixty_options');
			echo "<input id='plugin_seconds_per_turn' name='thrixty_options[seconds_per_turn]' size='40' type='text' value='{$options['seconds_per_turn']}' />";
		}
		function thrixty_options_direction() {
			$options = get_option('thrixty_options');
			echo "<input id='plugin_direction' name='thrixty_options[direction]' size='40' type='text' value='{$options['direction']}' />";
		}
		function thrixty_options_sensitivity_x() {
			$options = get_option('thrixty_options');
			echo "<input id='plugin_sensitivity_x' name='thrixty_options[sensitivity_x]' size='40' type='text' value='{$options['sensitivity_x']}' />";
		}
		function thrixty_options_zoom_mode() {
			$options = get_option('thrixty_options');
			echo "<input id='plugin_zoom_mode' name='thrixty_options[zoom_mode]' size='40' type='text' value='{$options['zoom_mode']}' />";
		}
		function thrixty_options_zoom_control() {
			$options = get_option('thrixty_options');
			echo "<input id='plugin_zoom_control' name='thrixty_options[zoom_control]' size='40' type='text' value='{$options['zoom_control']}' />";
		}
		function thrixty_options_position_indicator() {
			$options = get_option('thrixty_options');
			echo "<input id='plugin_position_indicator' name='thrixty_options[position_indicator]' size='40' type='text' value='{$options['position_indicator']}' />";
		}
		function thrixty_options_outbox_position() {
			$options = get_option('thrixty_options');
			echo "<input id='plugin_outbox_position' name='thrixty_options[outbox_position]' size='40' type='text' value='{$options['outbox_position']}' />";
		}
	// /

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
		// JS
		wp_enqueue_script('thrixty_init', plugins_url("thrixty_base/scripts/thrixty_initialize.js", __FILE__));
		// CSS
		wp_enqueue_style('thrixty_css', plugins_url("thrixty_base/style/thrixty_styles.css", __FILE__));
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

						$to_sc_gen = array(
							"basepath" => $thrixty_options["basepath"],
							"filelist_path_small" => "",
							"filelist_path_large" => "",
							"zoom_mode" => $thrixty_options["zoom_mode"],
							"zoom_control" => $thrixty_options["zoom_control"],
							"position_indicator" => $thrixty_options["position_indicator"],
							"outbox_position" => $thrixty_options["outbox_position"],
							"seconds_per_turn" => $thrixty_options["seconds_per_turn"],
							"sensitivity_x" => $thrixty_options["sensitivity_x"],
							"direction" => $thrixty_options["direction"],
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
		global $player_counter;
		// This global is counting the number of initialized Players.

		// attributes, that will be given to the generated div.
		//   and their values, if not overridden.
		$div_attributes = array(
			"id" => "thrixty_box_$player_counter", // this is, what the global counter is for
			"class" => "thrixty-player",
			"tabindex" => $player_counter, // this is, what the global counter is for
			"filelist_path_small" => "",
			"filelist_path_large" => "",
		);


		// Integrate options into the div_attributes array.
		$thrixty_options = get_option("thrixty_options");
		$thrixty_options_whitelist = array("basepath", "direction", "outbox_position", "position_indicator", "seconds_per_turn", "sensitivity_x", "zoom_control", "zoom_mode");
		foreach( $thrixty_options_whitelist as $key ){
			if( isset($thrixty_options[$key]) && $thrixty_options[$key] != "" ){
				$div_attributes[$key] = $thrixty_options[$key];
			}
		}


		// Integrate shortcode attributes into the combined div_attributes/options array.
		$shortcode_attributes = $atts;
		$shortcode_attributes_whitelist = array("basepath", "direction", "filelist_path_small", "filelist_path_large", "outbox_position", "position_indicator", "seconds_per_turn", "sensitivity_x", "zoom_control", "zoom_mode");
		foreach( $shortcode_attributes_whitelist as $key ){
			if( isset($shortcode_attributes[$key]) && $shortcode_attributes[$key] != "" ){
				$div_attributes[$key] = $shortcode_attributes[$key];
			}
		}


		// Build the div
		$returning = "<div ";
		// convert $div_attributes to actual html attributes
		foreach( $div_attributes as $key => $value ){
			switch( $key ){
				case "basepath":
					$value = str_replace("__PLUGIN__", plugins_url("", __FILE__), $value);
					$upload_dir = wp_upload_dir();
					$value = str_replace("__UPLOAD__", $upload_dir["baseurl"], $value);
					$value = str_replace("__SITE__", get_site_url(), $value);
					$value = trailingslashit($value);
				// intended Fallthrough
				case "zoom_mode":
				case "zoom_control":
				case "position_indicator":
				case "outbox_position":
				case "seconds_per_turn":
				case "sensitivity_x":
				case "direction":
				case "filelist_path_small":
				case "filelist_path_large":
					// always prepend
					$returning .= "thrixty-";

				// intended Fallthrough
				default:
					// the shortcode cant stand hyphens...
					// so translate the underscores back to hypens
					$returning .= str_replace("_", "-", $key)."=\"$value\" ";
					break;
			}
		}
		$returning .= "></div>";

		// Increase the counter for initialized players.
		$player_counter += 1;

		return $returning;
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
		add_options_page('Thrixty Player', 'Thrixty Player', 'manage_options', 'thrixty_options_page', 'thrixty_options_page_recieve');
	}
	// These functions belong to the thrixty settings page.
		function thrixty_options_page_recieve(){
			// if this POST var was set, the button for box3d to thrixty conversion was pressed.
			if( isset($_POST['post_ids']) ){
				thrixty_convert_box3d_shortcodes($_POST['post_ids']);
			}
			// now build the HTML
			thrixty_options_page_html();
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
							$new_shortcode .= "filelist_path_small='".$old_sc_atts["object"]."/small/Filelist.txt' ";
							$new_shortcode .= "filelist_path_large='".$old_sc_atts["object"]."/large/Filelist.txt' ";
						}
					/// direction
						if( isset($old_sc_atts["direction"]) && $old_sc_atts["direction"] != "" ){
							$new_shortcode .= "direction='".$old_sc_atts["direction"]."' ";
						}
					/// zoom_position => outbox_position
						if( isset($old_sc_atts["zoom_position"]) && $old_sc_atts["zoom_position"] != "" ){
							$new_shortcode .= "outbox_position='".$old_sc_atts["zoom_position"]."' ";
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
					<?php do_settings_sections('thrixty_options_page'); ?>
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
					Wenn <b>keine</b> Einstellungen angegeben wurden, sieht ein typischer Shortcode so aus:<br>
					<b>[thrixty basepath="http://example.com/360_pictures/" thrixtyplayer-filelist-path-small="test_small.txt" thrixtyplayer-filelist-path-large="test_large.txt" thrixtyplayer-seconds-per-turn="5" thrixtyplayer-direction="forward" thrixtyplayer-sensitivity-x="20" thrixtyplayer-zoom-mode="inbox_minimap" ]</b><br>
					<br>
					Wenn <b>alle</b> Einstellungen angegeben wurden, sieht ein typischer Shortcode so aus:<br>
					<b>[thrixty thrixtyplayer-filelist-path-small="test_small.txt" thrixtyplayer-filelist-path-large="test_large.txt" ]</b><br>
				</p>
				<br>
				<hr>
				<h3 id="paraminfo">Erkl&auml;rung der Parameter und ihrer Werte</h3>
				<p>
					<b>Basepath:</b><br>
					Dies ist der Grundpfad, von dem aus nach den beiden Filelists gesucht wird.<br>
					Beispiel: <b>http://example.com/360_pictures/</b>[filelist-paths]<br>
					F&uuml;r diese Option gibt es Shortcuts, die einen Teil des Pfades herleiten:<br>
					<b>"__SITE__"</b>: Verweist auf die URL der Startseite.<br>
					<b>"__PLUGIN__"</b>: Verweist auf Hauptordner dieses Plugins, wie er auch f&uuml;r die Ressourcen benutzt wird.<br>
					<b>"__UPLOAD__"</b>: Verweist auf den Uploadordner, der in Wordpress verwendet wird.<br>
					<?php if( isset($box3d_options["path"]) ){ ?>
						Der alte Wert aus Box3D: <b><?php echo $box3d_options["path"]; ?></b><br>
					<? } ?>
				</p>
				<p>
					<b>Zoom Mode</b><br>
					Hier kann die Zoom Art gew&auml;hlt werden.<br>
					Der Inbox-Zoom zoomt das Bild direkt gr&ouml;&szlig;er.<br>
					Der Outbox-Zoom erzeugt dagegen ein extra Fenster.<br>
					(Im Fullscreen wird aus dem Outbox vorr&uuml;bergehend in den normalen Inbox-Zoom gewechselt!)<br>
					M&ouml;gliche Werte:<br>
					<b>inbox</b>, outbox<br>
				</p>
				<p>
					<b>Outbox Position</b><br>
					Wenn der Outbox Zoom verwendet wird, kann hier gew&auml;hlt werden, wo das Fenster auftauchen soll.<br>
					M&ouml;gliche Werte:<br>
					<b>right</b>, bottom, left, top<br>
				</p>
				<p>
					<b>Positions Anzeiger</b><br>
					Damit man sich in dem vergr&ouml;&szlig;erten Bild zurechtfindet, kann man dazu einen Markierer anzeigen.<br>
					Die Minimap ist ein stark verkleinertes Bild und beschreibt daran den momentanen Ausschnitt.<br>
					Der Marker ist ein Rechteck innerhalb des Bildes, das den momentanen Ausschnitt markiert.<br>
					M&ouml;gliche Werte:<br>
					<b>minimap</b>, marker, none<br>
				</p>
				<p>
					<b>Zoom Steuerung</b><br>
					Hier wird eingestellt, wie der Kunde sich in dem vergr&ouml;&szlig;erten Bild bewegen kann.<br>
					Im Progressiven Modus wird die Mausposition genutzt, um den Bildausschnitt laufend zu verschieben.<br>
					Im Klassischen Modus wird der Positions Anzeiger benutzt, um den Bildausschnitt zu verschieben.<br>
					M&ouml;gliche Werte:<br>
					<b>progressive</b>, classic<br>
				</p>




				<p>
					<b>Seconds per Turn</b><br>
					Dies ist die Zeit, die eine ganze Umdrehung dauern soll. Dies sollte f&uuml;r alle Objekte gleich sein, um Gleichm&auml;&szlig;igkeit &uuml;ber die ganze Seite zu gew&auml;hrleisten.<br>
					Die (empfohlene) Standardeinstellung: <b>5</b> Sekunden f&uuml;r eine komplette Drehung<br>
					<?php if( isset($box3d_options["framerate"]) ){ ?>
						<td><?php echo (72 / $box3d_options["framerate"]); ?></td>
					<? } ?>
				</p>
				<p>
					<b>Sensitivity X</b><br>
					Dies ist die Anzahl an Pixeln, ab welcher Distanz ein angefangener Klick als Geste z&auml;hlt.<br>
					Dies ist wichtig, um das "Wurstfinger-Problem" zu umgehen.<br>
					Die (empfohlene) Standardeinstellung: <b>20</b> Pixel<br>
				</p>
				<p>
					<b>Direction</b><br>
					Dies ist die Richtung, in die sich die Objekte drehen sollen.<br>
					Objekte, die sich im Uhrzeigersinn drehen, werden <b>"forward"</b> drehend genannt.<br>
					Objekte, die sich gegen den Uhrzeigersinn drehen, werden "backward" drehend genannt.<br>
					<b>Sie sollten Ihre Aufnahmen immer in diesselbe Richtung drehend aufnehmen!</b><br>
					Falls Sie Ihre Aufnahmen bereits gegen den Uhrzeigersinn aufgenommen haben, stellen Sie diese Option um auf "backward".<br>
					<?php if( isset($box3d_options["direction"]) ){ ?>
						Der alte Wert aus Box3D: <b><?php echo $box3d_options["direction"]; ?></b><br>
					<? } ?>
				</p>
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
	// /





// FOLGENDEN CODE NACH ENTWICKLUNG ENTFERNEN oder auskommentieren.
// add_action( 'wp_print_scripts', 'disableAutoSave' );
// function disableAutoSave(){
// 	wp_deregister_script('autosave');
// }


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

// 	$args = array(
// 		's' => '['.$string,
// 	);

// 	$the_query = new WP_Query( $args );

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

// 	wp_reset_postdata();
// 	return ob_get_clean();
// }



?>