<?php
/**
 * Adds Custom Post types Shows .
 */
add_action('init', 'soul_create_shows');
function soul_create_shows() {

    $labels = array(
        'name' => _x('SHOWS', 'post type general name'),
        'singular_name' => _x('show', 'post type singular name'),
        'add_new' => _x('Add New', 'show'),
        'add_new_item' => __('Add New show'),
        'edit_item' => __('Edit show'),
        'new_item' => __('New show'),
        'view_item' => __('View show'),
        'search_items' => __('Search shows'),
        'not_found' =>  __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'menu_icon' => 'dashicons-editor-video',
        'supports' => array('title','editor','excerpt','thumbnail')
      );

    register_post_type( 'shows' , $args );
}

// Add show type taxonomy for sticky option

add_action( 'init', 'soul_register_taxonomy_shows' );

function soul_register_taxonomy_shows() {

    $labels = array( 
        'name' => _x( 'show_types', 'show_types' ),
        'singular_name' => _x( 'show_type', 'show_types' ),
        'search_items' => _x( 'Search show_types', 'show_types' ),
        'popular_items' => _x( 'Popular show_types', 'show_types' ),
        'all_items' => _x( 'All show_types', 'show_types' ),
        'parent_item' => _x( 'Parent show_type', 'show_types' ),
        'parent_item_colon' => _x( 'Parent show_type:', 'show_types' ),
        'edit_item' => _x( 'Edit show_type', 'show_types' ),
        'update_item' => _x( 'Update show_type', 'show_types' ),
        'add_new_item' => _x( 'Add New show_type', 'show_types' ),
        'new_item_name' => _x( 'New show_type', 'show_types' ),
        'separate_items_with_commas' => _x( 'Separate show_types with commas', 'show_types' ),
        'add_or_remove_items' => _x( 'Add or remove show_types', 'show_types' ),
        'choose_from_most_used' => _x( 'Choose from the most used show_types', 'show_types' ),
        'menu_name' => _x( 'show_types', 'show_types' ),
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => false,
        'show_ui' => true,
        'show_tagcloud' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'show_types', array('shows'), $args );
}

/* Display list shows */
add_action("manage_posts_custom_column",  "soul_shows_custom_columns");
add_filter("manage_shows_posts_columns", "soul_shows_edit_columns");

function soul_shows_edit_columns($columns){
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => "Show",
        "show_author" => "Author",
        "show_date" => "Date",
        "show_venue" => "Venue"
  );
  return $columns;
}

function soul_shows_custom_columns($column){
    global $post;
    $custom = get_post_custom();

    switch ($column) {
    case "show_date":
            echo soul_format_date($custom["show_date"][0]);
            break;

    //grc 03/2015
    case "show_start_date":
            echo $custom["show_start_date"][0];
            break;

    case "show_time":
            echo $custom["show_time"][0];
            break;

    case "show_venue":
            echo $custom["show_venue"][0];
            break;

    case "show_url":
            echo $custom["show_url"][0];
            break;

    case "show_tel":
            echo $custom["show_tel"][0];
            break;

    case "show_price":
            echo $custom["show_price"][0];
            break;

    case "show_featuring":
            echo $custom["show_featuring"][0];
            break;

    case "show_author":
            echo $custom["show_author"][0];
            break;

    default:
       echo "error";
    }
}

/* Add sortable shows column */
add_filter("manage_edit-shows_sortable_columns", "soul_show_date_column_register_sortable");
add_filter("request", "soul_show_date_column_orderby" );

function soul_show_date_column_register_sortable( $columns ) {
        $columns['show_date'] = 'show_date';
        return $columns;
}

function soul_show_date_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'show_date' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'show_date',
            'orderby' => 'meta_value_num'
        ) );
    }
    return $vars;
}

/* add taxonomy column */
// add_filter( 'manage_taxonomies_for_shows_columns', 'show_types_columns' );
// function show_types_columns( $taxonomies ) {
//     $taxonomies[] = 'show-types';
//     return $taxonomies;
// }

/* Add shows details metabox */
add_action("admin_init", "soul_shows_admin_init");

function soul_shows_admin_init(){
  add_meta_box("show_meta", "show Details", "soul_show_details_meta", "shows", "normal", "default");
}

function soul_format_date($unixtime) { 
    return date("F", $unixtime)." ".date("d", $unixtime).", ".date("Y", $unixtime); 
}

function soul_show_details_meta() {

    // echo '<p><strong>AFTER EDITING A FIELD, ALWAYS PASTE AGAIN THE URL STRUCTURE:</strong>
    //         <br /> &lt;a href="http://www.my-site.com/" target"_blank"&gt;My Site Title&lt;/a&gt;
    //     </p>';

    $ret = '<p><label>Author: </label><input type="text" size="70" name="show_author" value="' . soul_get_show_field("show_author") . '" /></p>';
    $ret = $ret . '<p><label>Start Date: </label><input type="text" size="30" name="show_start_date" value="' . soul_get_show_field("show_start_date") . '" /></p>';
    $ret = $ret . '<p><label>End Date: </label><input type="text" name="show_date" value="' . soul_format_date(soul_get_show_field("show_date")) . '" /><em> (mm/dd/yyyy)</em></p>';
    $ret = $ret . '<p><label>Show Time: </label><input type="text" size="30" name="show_time" value="' . soul_get_show_field("show_time") . '" /></p>';
    $ret = $ret . '<p><label>Venue: </label><input type="text" size="70" name="show_venue" value="' . soul_get_show_field("show_venue") . '" /></p>';

    echo "<br>";

// GRC test for custom field in the post
    $ret = $ret . '<p><label>Url [<em>starts with <b>http://...</b></em>]: </label><input type="url" size="70" name="show_url" value="' . soul_get_show_field("show_url") . '" /><em> http://your-site.com</em></p>';    
    $ret = $ret . '<p><label>Phone Number: </label><input type="text" size="30" name="show_tel" value="' . soul_get_show_field("show_tel") . '" /></p>';
    $ret = $ret . '<p><label>Ticket Price: </label><input type="text" size="100" name="show_price" value="' . soul_get_show_field("show_price") . '" /></p>';
    $ret = $ret . '<p><label>Featuring: </label><input type="text" size="120" name="show_featuring" value="' . soul_get_show_field("show_featuring") . '" /></p>';

    // $ret = $ret . '<p><label>Featuring: </label><input type="text" size="120" name="show_featuring" value="' . soul_get_show_field("show_featuring") . '" /></p>';
    // $ret = $ret . '<p><label>Featuring: </label><textarea rows="4" cols="50" name="show_featuring" value="' . soul_get_show_field("show_featuring") . '" ></textarea></p>';

    echo $ret;
}

function soul_get_show_field($show_field) {
    global $post;

    $custom = get_post_custom($post->ID);

    if (isset($custom[$show_field])) {
        return $custom[$show_field][0];
    }
}

/* Add saving shows details */
add_action('save_post', 'soul_save_show_details');

function soul_save_show_details(){
   global $post;

   if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
      return;

   if ( get_post_type($post) == 'show')
      return;

   if(isset($_POST["show_date"])) {
      update_post_meta($post->ID, "show_date", strtotime($_POST["show_date"]));
   }

   soul_save_show_field("show_author");
   soul_save_show_field("show_venue");
   soul_save_show_field("show_start_date"); //grc 03/2015
   soul_save_show_field("show_time");
   soul_save_show_field("show_url");
   soul_save_show_field("show_tel");
   soul_save_show_field("show_price");
   soul_save_show_field("show_featuring");
}

function soul_save_show_field($show_field) {
    global $post;

    if(isset($_POST[$show_field])) {
        update_post_meta($post->ID, $show_field, $_POST[$show_field]);
    }
}

/* function to get show details */
function soul_get_show_details($include_register_button, $include_title) {
    global $post;
    $unixtime = get_post_meta($post->ID, 'show_date', true);

    $ret = '';
    if ($include_title) {
        $ret =  $ret . '<h3><a href="' . get_permalink() . '">' . $post->post_title . '</a></h3>';
    }
    $ret = $ret . '<p><strong>Author: </strong>' . get_post_meta($post->ID, 'show_author', true) . ' - ';
    $ret = $ret . '<strong>Venue: </strong>'.get_post_meta($post->ID, 'show_venue', true) . '</p>';

    return $ret;
}