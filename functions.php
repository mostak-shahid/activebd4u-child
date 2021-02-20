<?php
/**
 * Betheme Child Theme
 *
 * @package Betheme Child Theme
 * @author Muffin group
 * @link https://muffingroup.com
 */

/**
 * Child Theme constants
 * You can change below constants
 */

// white label

define('WHITE_LABEL', false);

require_once('theme-init/plugin-update-checker.php');
$themeInit = Puc_v4_Factory::buildUpdateChecker(
	'https://raw.githubusercontent.com/mostak-shahid/update/master/activebd4u-child.json',
	__FILE__,
	'activebd4u-child'
);

/**
 * Enqueue Styles
 */

function mos_enqueue_styles()
{
    wp_enqueue_script('jquery');
    wp_enqueue_style( 'fancybox', get_stylesheet_directory_uri() . '/plugins/fancybox/jquery.fancybox.min.css' );
    wp_enqueue_script('fancybox', get_stylesheet_directory_uri() . '/plugins/fancybox/jquery.fancybox.min.js', 'jquery');
    wp_enqueue_script('numscroller', get_stylesheet_directory_uri() . '/plugins/numscroller.js', 'jquery');
    
    wp_enqueue_style( 'font-awesome.min', get_stylesheet_directory_uri() . '/fonts/font-awesome-4.7.0/css/font-awesome.min.css' );
    
	// enqueue the parent stylesheet
	// however we do not need this if it is empty
	// wp_enqueue_style('parent-style', get_template_directory_uri() .'/style.css');

	// enqueue the parent RTL stylesheet

	if (is_rtl()) {
		wp_enqueue_style('mfn-rtl', get_template_directory_uri() . '/rtl.css');
	}

	// enqueue the child stylesheet

	wp_dequeue_style('style');
	wp_enqueue_style('style', get_stylesheet_directory_uri() .'/style.css');
}
add_action('wp_enqueue_scripts', 'mos_enqueue_styles', 101);
function mos_admin_styles()
{
	wp_enqueue_style('style', get_stylesheet_directory_uri() .'/css/admin-css.css');    
}
add_action('admin_enqueue_scripts', 'mos_admin_styles', 101);
/**
 * Load Textdomain
 */

function mos_textdomain()
{
	load_child_theme_textdomain('betheme', get_stylesheet_directory() . '/languages');
	load_child_theme_textdomain('mfn-opts', get_stylesheet_directory() . '/languages');
}
add_action('after_setup_theme', 'mos_textdomain');

function get_generation_ids($user_id, $generation, $limit){  
    global $wpdb; 
    $output = [];
    $gen[0] = [$user_id];
    $start_from = (intval($limit)-1) * 10;
//    var_dump($start_from);
//    die();
    for ($x = 1; $x <= $generation; $x++) {
        $gen_ind = $x - 1;
        $output[$x]['level_count'] = $wpdb->get_var("SELECT COUNT(user_id) FROM {$wpdb->prefix}usermeta WHERE meta_key='_mos_user_parent' AND `meta_value` IN (".implode(',',$gen[$gen_ind]).")");
        if ($output[$x]['level_count'] >= $start_from){
            $query = "SELECT user_id FROM {$wpdb->prefix}usermeta WHERE meta_key='_mos_user_parent' AND meta_value IN (".implode(',',$gen[$x-1]).") LIMIT {$start_from},10;";
            $level[$x] = $wpdb->get_results($query);
            if (sizeof($level[$x])){
                foreach ( $level[$x] as $value ) {
                    $output[$x]['data'][] = $value->user_id;
                }
                $gen[$x] = $output[$x]['data'];
            } else {
                $gen[$x] = [];
            }            
        }
        
    }
    return $output[$generation];
}
function remove_query_strings_split($src){
   $output = explode('?',$src);
   return $output[0];
}

require_once 'carbon-fields.php';
require_once 'shortcodes.php';
require_once 'woo.php';
require_once 'hooks.php';


