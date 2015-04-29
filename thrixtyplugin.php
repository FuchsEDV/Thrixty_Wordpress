<?php
	/**
	 * @package Thrixty Player
	 * @version 1.0
	 */

	/*
		Plugin Name: Thrixty Player
		Plugin URI: keine
		Description: thrixty player plugin
		Author: Ich
		Version: 1.0
		Author URI: Ich
	*/









	/* PLUGIN INSTALL HOOKS */
	register_activation_hook(__FILE__, "thrixty_activation");
	function thrixty_activation(){
		$option_name = "thrixty_options";
		$base_settings = array(
			"basepath" => "__PLUGIN__/objects/",
			"zoom_mode" => "inbox_minimap",
			"seconds_per_turn" => "5",
			"sensitivity_x" => "20",
			"direction" => "forward",
		);
		if( !get_option($option_name) ){
			add_option($option_name, $base_settings);
		}
	}
	// register_deactivation_hook(__FILE__, "thrixty_deactivation");
	function thrixty_deactivation(){
		// do nothing
	}
	register_uninstall_hook(__FILE__, "thrixty_uninstall");
	function thrixty_uninstall(){
		$option_name = "thrixty_options";
		delete_option($option_name);
	}











	/* PLUGIN ADMIN PAGE */
	add_action('admin_menu', 'thrixty_player_settings_site');
	function thrixty_player_settings_site() {
		add_options_page('Thrixty Player', 'Thrixty Player', 'manage_options', 'thrixty_options_page', 'thrixty_options_page_html');
	}

	function thrixty_options_page_html() {
		$thrixty_options = get_option('thrixty_options');
		$box3d_options = get_option("box3d_options");
		// echo "<pre>";
		// var_dump($thrixty_options);
		// var_dump($box3d_options);
		// echo "</pre>";
		if( isset($_POST['site_ids']) ){
			global $wpdb;

			$post_ids_string = $_POST['site_ids'];
			$post_arr = [];


			$wp_query_args = array("s" => "[box3d");
			if( $post_ids_string != "all" ){
				$post_ids_arr = array();
				$tmp_arr = explode(",", $post_ids_string);
				foreach( $tmp_arr as $id ){
					// $id = trim($id);
					$id = preg_replace( "/[^0-9]/", "", $id );
					if( "" != $id ){
						$post_ids_arr []= $id;
					}
				} unset($tmp_arr, $id);

				$wp_query_args["post__in"] = $post_ids_arr;
			}



			// this is the shortcode, that would be returned by "get_shortcode_regex()"; but this conversion is supposed to only trigger on box3d shortcodes!
			$shortcode_regex = '/\[(\[?)(box3d)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s';


			// get all posts with this shortcode attribute
			$the_query = new WP_Query( $wp_query_args );
			$posts = $the_query->posts;

			// each found post will get replaces
			foreach( $posts as $post ){
				// echo "<pre>";
				// var_dump( $post->post_content );
				// echo "</pre>";
				// get inside text
				$post_content = $post->post_content;

				// get position and length of first find (TODO: erweitere zu schleife)
				$hits = array();
				preg_match_all($shortcode_regex, $post_content, $hits, PREG_OFFSET_CAPTURE); // found things will get stored in $hits
				$hits = array_reverse($hits[0]);
				foreach( $hits as $hit ){
					// shortcode_parse_atts forgets the space before the closing bracket...
					$old_shortcode = substr_replace( $hit[0], " ", -1, 0 );
					$position = $hit[1];

					// parse to array
					$old_sc_atts = shortcode_parse_atts($old_shortcode);

					// calculate thrixty shortcode
					$new_shortcode = "[thrixty ";
						// insert stuff here





						// basepath guessing
						// check for old shortcode attribute path
						if( isset($old_sc_atts["path"]) && $old_sc_atts["path"] != "" ){
							// if it was set, set basepath to that old value
							$new_shortcode .= "basepath='".$old_sc_atts["path"]."' ";
						// if not, check for different old and new basepath options
						} else if( isset($box3d_options["path"]) && isset($thrixty_options["basepath"]) && $box3d_options["path"] != $thrixty_options["basepath"] ){
							// if they are indeed different, the new shortcode with the old path and object can only work with a basepath attribute
							$new_shortcode .= "basepath='".$box3d_options["path"]."' ";
						}




						if( isset($old_sc_atts["object"]) && $old_sc_atts["object"] != "" ){
							$new_shortcode .= "filelist_path_small='".$old_sc_atts["object"]."/small/Filelist.txt' ";
							$new_shortcode .= "filelist_path_large='".$old_sc_atts["object"]."/large/Filelist.txt' ";
						}
						if( isset($old_sc_atts["direction"]) && $old_sc_atts["direction"] != "" ){
							$new_shortcode .= "direction='".$old_sc_atts["direction"]."' ";
						}


					$new_shortcode .= "]";

					// write new shortcode at the place of the old
					$post_content = substr_replace($post_content, $new_shortcode, $position, strlen($old_shortcode)-1 );

				}

				// assign new content to post
				echo "<pre>";
					var_dump($post_content);
				echo "</pre>";
				$post->post_content = $post_content;
				wp_update_post($post);

			}
		}
		?>
		<div class="wrap">
			<h2>Thrixty Player - Allgemeine Einstellungen</h2>
			<p>Hier können Sie allgemeine Standardwerte festlegen.</p>
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
				Über den "3D" Button im Artikel Editor (visuell) können Sie einen Shortcode generieren.<br>
				Alle im <b><i>Shortcode</i></b> angegebenen Optionen <b><i>überschreiben</i></b> die allgemeinen Einstellungen, die sie hier tätigen.<br>
				Die allgemeinen Einstellungen sind dafür gedacht, <b><i>alle Objekte auf Ihrer Seite gleich darzustellen.</i></b><br>
			</p>
			<p>
				Wenn es für eine Option keinen allgemeingültigen Wert gibt (Feld leer lassen), dann wird die Option bei der Shortcodegenerierug abgefragt.<br>
				Dies wird mit den beiden Optionen <b>thrixtyplayer-filelist-path-small</b> und <b>thrixtyplayer-filelist-path-large</b> immer passieren!<br>
			</p>
			<p>
				Die allgemeinen Einstellungen sind vor allem dann hilfreich, wenn alle Ihre für den Player benötigten Listen im selben Grundordner liegen (empfohlen).<br>
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
			<h3 id="paraminfo">Erklärung der Parameter und ihrer Werte</h3>
			<p>
				<b>Basepath:</b><br>
				Dies ist der Grundpfad, von dem aus nach den beiden Filelists gesucht wird.<br>
				Beispiel: <b>http://example.com/360_pictures/</b>[filelist-paths]<br>
				Für diese Option gibt es Shortcuts, die einen Teil des Pfades herleiten:<br>
				<b>"__SITE__"</b>: Verweist auf die URL der Startseite.<br>
				<b>"__PLUGIN__"</b>: Verweist auf Hauptordner dieses Plugins, wie er auch für die Ressourcen benutzt wird.<br>
				<b>"__UPLOAD__"</b>: Verweist auf den Uploadordner, der in Wordpress verwendet wird.<br>
				<?php if( isset($box3d_options["path"]) ){ ?>
					Der alte Wert aus Box3D: <b><?php echo $box3d_options["path"]; ?></b><br>
				<? } ?>
			</p>
			<p>
				<b>Zoom Mode</b><br>
				Hier kann die Zoom Strategie gewählt werden.<br>
				Der Inbox-Zoom zoomt das Bild direkt größer.<br>
				Der Outbox-Zoom erzeugt dagegen ein Extra Fenster an der angegebenen Position.<br>
				(Im Fullscreen wird aus dem Outbox vorrübergehend in den normalen Inbox-Zoom gewechselt!)<br>
				Mögliche Werte:<br>
				<b>inbox</b>, inbox_minimap, outbox_[top, right, bottom, left]<br>
			</p>
			<p>
				<b>Seconds per Turn</b><br>
				Dies ist die Zeit, die eine ganze Umdrehung dauern soll. Dies sollte für alle Objekte gleich sein, um Gleichmäßigkeit über die ganze Seite zu gewährleisten.<br>
				Die (empfohlene) Standardeinstellung: <b>5</b> Sekunden für eine komplette Drehung<br>
				<?php if( isset($box3d_options["framerate"]) ){ ?>
					<td><?php echo (72 / $box3d_options["framerate"]); ?></td>
				<? } ?>
			</p>
			<p>
				<b>Sensitivity X</b><br>
				Dies ist die Anzahl an Pixeln, ab welcher Distanz ein angefangener Klick als Geste zählt.<br>
				Dies ist wichtig, um das "Wurstfinger-Problem" zu umgehen.<br>
				Die (empfohlene) Standardeinstellung: <b>20</b> Pixel<br>
			</p>
			<p>
				<b>Direction</b><br>
				Dies ist die Richtung, in die sich die Objekte drehen sollen.<br>
				Objekte, die sich im Uhrzeigersinn drehen, werden <b>"forward"</b> drehend genannt.<br>
				Objekte, die sich gegen den Uhrzeigersinn drehen, werden "<b>backward</b>" drehend genannt.<br>
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
					Fuer den Fall, dass Sie vorher Box3D verwendet haben, können Sie hier automatisch Box3D Shortcodes in Thrixty Shortcodes übersetzen lassen.<br>
					Bitte bedenken Sie dabei, dass es einige Funktionen nicht mehr gibt oder auch neue dazu gekommen sind.<br>
					Die Filelists selber werden NICHT kontrolliert!<br>
					<br>
					<b>Bitte setzen Sie ihre Einstellungen</b>, bevor Sie diese Funktion nutzen!<br>
					Insbesondere <b>Basepath</b> sollte gesetzt sein!<br>
					<br>
					Geben Sie entweder "all" an, um alle Posts und Pages nach dem alten Shortcode durchsuchen zu lassen, oder eine komma-getrennte Liste mit den entsprechenden IDs.<br>
					<input name="site_ids" type="text" placeholder="Post IDs or 'all'" />
					<input name="Submit" type="submit" value="Start Box3D to Thrixty Conversion" /><br>
					<i><b>ACHTUNG! Dies kann nur per Hand rückgängig gemacht werden!</b></i><br>
				</p>
			</form>
		</div><?php
	}





	/* PLUGIN OPTIONS */
	add_action('admin_init', 'thrixty_admin_init');
	function thrixty_admin_init(){
		register_setting( 'thrixty_options', 'thrixty_options');

		add_settings_section('thrixty_settings_section', 'Thrixty Player Einstellungen', 'thrixty_options_section', 'thrixty_options_page');
		add_settings_field('plugin_basepath', 'Basepath', 'thrixty_options_basepath', 'thrixty_options_page', 'thrixty_settings_section');
		add_settings_field('plugin_zoom_mode', 'Zoom Mode', 'thrixty_options_zoom_mode', 'thrixty_options_page', 'thrixty_settings_section');
		add_settings_field('plugin_seconds_per_turn', 'Seconds per Turn', 'thrixty_options_seconds_per_turn', 'thrixty_options_page', 'thrixty_settings_section');
		add_settings_field('plugin_sensitivity_x', 'Sensitivity X', 'thrixty_options_sensitivity_x', 'thrixty_options_page', 'thrixty_settings_section');
		add_settings_field('plugin_direction', 'Direction', 'thrixty_options_direction', 'thrixty_options_page', 'thrixty_settings_section');
	}
	function thrixty_options_section(){
		?><b>Bitte lesen Sie die <a href="#pageinfo">Seiteninformationen</a> und die <a href="#paraminfo">Parametererklärungen</a>, bevor Sie hier Einstellungen vornehmen!</b><?php
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





	/* PLUGIN USAGE */
	// Ressources
	add_action("init", "register_ressources");
	function register_ressources(){
		// Diese global steht für die Anzahl der Player auf der momentan gerenderten Seite.
		global $player_counter;
		$player_counter = 0;
		// JS
		wp_enqueue_script('thrixty_init', plugins_url("thrixty_base/scripts/thrixty_initialize.js", __FILE__));
		// CSS
		wp_enqueue_style('thrixty_css', plugins_url("thrixty_base/style/thrixty_styles.css", __FILE__));
	}

	// Shortcode Handler
	add_shortcode("thrixty", "thrixty_player_shortcode_handler");
	function thrixty_player_shortcode_handler($atts){
		global $player_counter; // Anzahl player.

		$settings = array(
			"id" => "thrixty_box_".$player_counter, // sollte noch bei mehreren hochzählen
			"class" => "thrixty-player",
			"tabindex" => $player_counter, // sollte noch bei mehreren hochzählen

			"basepath" => "__PLUGIN__/objects",
			"zoom_mode" => "inbox",
			"seconds_per_turn" => "5",
			"sensitivity_x" => "20",
			"direction" => "forward",

			"filelist_path_small" => "",
			"filelist_path_large" => "",
		);



		// alle settings, die von der option überschrieben werden, überschreiben:

		$thrixty_options = get_option("thrixty_options");
		$thrixty_options_whitelist = array("basepath", "zoom_mode", "seconds_per_turn", "sensitivity_x", "direction");

		foreach( $thrixty_options_whitelist as $key ){
			if( isset($thrixty_options[$key]) ){
				$settings[$key] = $thrixty_options[$key];
			}
		}


		// alle settings, die von dem shortcode überschrieben werden, überschreiben:

		$shortcode_options = $atts;
		$shortcode_options_whitelist = array("basepath", "zoom_mode", "seconds_per_turn", "sensitivity_x", "direction", "filelist_path_small", "filelist_path_large");


		foreach( $shortcode_options_whitelist as $key ){
			if( isset($shortcode_options[$key]) ){
				$settings[$key] = $shortcode_options[$key];
			}
		}

		// Build Players HTML
		$returning = "<div ";
		foreach( $settings as $key => $value ){
			switch( $key ){
				case "basepath":
					$value = str_replace("__PLUGIN__", plugins_url("", __FILE__), $value);
					$upload_dir = wp_upload_dir();
					$value = str_replace("__UPLOAD__", $upload_dir["baseurl"], $value);
					$value = str_replace("__SITE__", get_site_url(), $value);
					$value = trailingslashit($value);
				//// INTENDED FALLTHROUGH!!!
				case "zoom_mode":
				case "seconds_per_turn":
				case "sensitivity_x":
				case "direction":
				case "filelist_path_small":
				case "filelist_path_large":
					// stuff
					$returning .= "thrixty-";
				//// INTENDED FALLTHROUGH!!!
				default:
					$returning .= str_replace("_", "-", $key)."=\"".$value."\" ";
					break;
			}
		}
		$returning .= "></div>";


		// Erhöhe die Anzahl der gerenderten Player um eins.
		$player_counter += 1;

		return $returning;
	}





	// add Shortcode Generator Button to TinyMCE Editor
	add_action('init', 'add_thrixty_gen_button');
	function add_thrixty_gen_button() {
		global $pagenow;
		if( 'post.php' == $pagenow || 'post-new.php' == $pagenow ){
			if( current_user_can('edit_posts') || current_user_can('edit_pages') ){
				if( get_user_option('rich_editing') == "true" ) {
					// Add button functionality in form of a script file.
					add_filter('mce_external_plugins', 'integrate_thrixty_mce_plugin');
					function integrate_thrixty_mce_plugin($plugin_array) {
						$plugin_array['thrixty'] = plugins_url('tinymce_plugin/thrixty_shortcode_generator.js' , __FILE__ );
						return $plugin_array;
					}
					// Add the button itself
					add_filter('mce_buttons', 'show_shortcode_generator_button');
					function show_shortcode_generator_button($buttons) {
						array_push($buttons, "|", "thrixty");
						return $buttons;
					}
					// The generator needs some information
					add_action('wp_print_scripts', 'give_thrixty_options_to_js');
					function give_thrixty_options_to_js(){
						$thrixty_options = get_option("thrixty_options");

						$to_sc_gen = array(
							"basepath" => $thrixty_options["basepath"],
							"filelist_path_small" => "",
							"filelist_path_large" => "",
							"zoom_mode" => $thrixty_options["zoom_mode"],
							"seconds_per_turn" => $thrixty_options["seconds_per_turn"],
							"sensitivity_x" => $thrixty_options["sensitivity_x"],
							"direction" => $thrixty_options["direction"],
						);

						?><script>
							var thrixty_sc_gen_var = '<?php echo json_encode($to_sc_gen); ?>';
							thrixty_sc_gen_var = JSON.parse(thrixty_sc_gen_var);
						</script><?php
					}
				}
			}
		}
	}

















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
