<?php

/**
 * @package Dice Roller
 * @version 1.1.0
 */
/*
Plugin Name: Dice Roller
Description: Lanceur de dés pour jeux de plateau et jeux de rôles. Original Version Marty Himmel.
Version:     1.1.0
Author:      Bari Jonathan
Author URI:  https://www.linkedin.com/in/jonathan-bari/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined('ABSPATH') or die('Plugin file cannot be accessed directly.');

if (!class_exists('DiceRoller')) {

	class DiceRoller {

		/**
		 * Set up the plugin.
		 */
		function __construct() {
			
			add_shortcode('dice_roller', [$this, 'shortcode']);
		}

		/**
		 * Set up the shortcode.
		 *
		 * @param	array	$atts		Attributes
		 * @param	string 	$content	Content passed in to the shortcode
		 * @return	string				Shortcode output
		 */
		function shortcode($atts, $content = null) {
			if (is_user_logged_in()) {
			$this->enqueue_js();
			$this->enqueue_css();
			ob_start();
			require_once('form/form.html');
			$html = ob_get_clean();
			return $html;
		} else {
			echo '<p class="bigText">Vous devez vous identifier pour voir le lanceur de dés</p>';
		}
		}

		/**
		 * Register scripts with WordPress.
		 */
		function enqueue_js() {
			if (!wp_script_is('dice_roller', 'enqueued')) {
				wp_register_script(
					'dice_roller',
					plugin_dir_url(__FILE__) . 'js/dice-roller.js'
				);
				wp_enqueue_script('dice_roller');
			}
		}

		function enqueue_css() {
			wp_register_style(
				'style',
				plugin_dir_url(__FILE__) . 'style/style.css'
			);
			wp_enqueue_style('style');
		}
	} // End class
} // End if(!class_exists)

function createDB(){
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS`{$wpdb->prefix}jet` (
	`id` mediumint(9) NOT NULL AUTO_INCREMENT,
	`date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`roll` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY  (id)
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );

}

function showAll() {
	global $wpdb;
	$result = $wpdb->get_results("SELECT `roll` FROM `{$wpdb->prefix}jet` ORDER BY ID DESC LIMIT 500");
	foreach ($result as $key => $value ) {
		echo "<tr><th>" . $value->roll . "</th></tr>";
	}
}

function insertDB(){
	global $wpdb;
	$wpdb->insert("{$wpdb->prefix}jet", array(
		"roll" => $_POST['rollResult'],
		"name" => wp_get_current_user()->display_name
	));
}


function onInit(){
	if(class_exists('DiceRoller')) {
		new DiceRoller();
		if (isset($_POST['rollResult'])) {
			echo "<script>document.getElementById('dice-roll').innerHTML = " . $POST['rollResult'] . "</script>";
			insertDB();
		}
	}
}


register_activation_hook( __FILE__, 'create_table' );

function create_table(){
	global $wpdb;
	if($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}jet'") != '{$wpdb->prefix}jet'){
		createDB();
	}
}
register_uninstall_hook( __FILE__, 'delete_table' );

function delete_table(){
	global $wpdb;
	$table_name = '{$wpdb->prefix}jet';
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query($sql);
	delete_option("create_table");
}

add_action('init', 'onInit');
