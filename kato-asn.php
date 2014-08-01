<?php
/**
 * Plugin Name: Kato ASN
 * Plugin URI: http://asn.kato.me
 * Description: Special functions for Kato ASN
 * Version: 0.0.1
 * Author: Leo J.
 * Author URI: http://lji.me
 */
function remove_notes_meta() {
	remove_meta_box( 'tagsdiv-courses', 'notes', 'side' );
	remove_meta_box( 'subjectsdiv', 'notes', 'side' );
	remove_meta_box( 'tagsdiv-courses', 'revision-guide', 'side' );
	remove_meta_box( 'subjectsdiv', 'revision-guide', 'side' );
}

add_action( 'admin_menu' , 'remove_notes_meta' );



add_filter('post_type_link', 'tdd_permalinks', 100, 2);
 
function tdd_permalinks($permalink, $post){
 
$no_data = '0.0';
 
$post_id = $post->ID;
 
// if($post->post_type !== 'notes' || empty($permalink) || in_array($post->post_status, array('draft', 'pending', 'auto-draft'))) {return $permalink;}
 
$var1 = get_post_meta($post_id, 'syllabus_number', true);
 
// $var1 = sanitize_title($var1);

$var1 = strtolower($var1);
 
if(!$var1) { $var1 = $no_data; }
 
$permalink = str_replace('%syllabus_number%', $var1, $permalink);

return $permalink;

}


add_action('init', 'tdd_add_rewrite_rules');
 
function tdd_add_rewrite_rules()
 
{
 
// Register custom rewrite rules

add_rewrite_tag('%syllabus_number%', '([^/]+)', 'syllabus_number=');

}

add_filter( 'postmeta_form_limit' , 'customfield_limit_increase' );
function customfield_limit_increase( $limit ) {
	$limit = 100;
	return $limit;
}

function kato_asn_notes_subject_before_content($atts){
	if(is_single() && get_post_type() == "notes"){
		$custom_content = "<div class='subject-details'><table><tr><td class='subject-label'>Course</td><td colspan='2'>";
		$courses = get_the_terms(get_the_ID(), 'courses');
		$courses = array_slice($courses, 0, 1);
		$custom_content .= $courses[0]->name;
		$custom_content .= "</td></tr><tr><td class='subject-label'>Examination Authority</td><td colspan='2'>";
		$subjects = get_the_terms(get_the_ID(), 'subjects');
		$subjects = array_slice($subjects, 0, 1);
		$t_id = $subjects[0]->term_id;
		$term_meta = get_option( "taxonomy_" . $t_id );
		$custom_content .= $term_meta['authority'];
		$custom_content .= "</td></tr><tr><td class='subject-label'>Subject</td><td colspan='2'>";
		$custom_content .= $subjects[0]->name;
		$custom_content .= "</td></tr><tr><td class='subject-label'>Syllabus</td><td clas'syllabus_number'>";
		$custom_content .= get_field('syllabus_number') . "</td><td>" . get_field('syllabus_statement');
		$custom_content .= "</td></tr></table></div>";
		print $custom_content;
		}
}
add_shortcode( 'kato_asn_notes_subject_table', 'kato_asn_notes_subject_before_content' );

add_filter( 'pre_get_posts', 'kato_asn_get_posts' );

function kato_asn_get_posts( $query ) {

	if ( is_home() && $query->is_main_query() )
		$query->set( 'post_type', array( 'post', 'notes' ) );

	return $query;
}

add_action('wp_head','wpse86994_remove_action',1); // prioroty of 1, but can be anything higher (lower number) then the priority of the action

function wpse86994_remove_action() {
  remove_action('generate_credits','generate_add_footer_info');
}


add_action('generate_credits', 'kato_asn_generate_info');

function kato_asn_generate_info(){
	?> <span class="copyright"><?php _e('Copyright','generate');?> &copy; <?php echo date('Y'); ?></span> &#9679; <span class="kato-footer">Accelerated Study Notes is a product of <a href="http://kato.education/">Kato Education</a>, a <a href="http://kato.me/">Kato Media</a> institution. </span> <?
}

// Add term page
function pippin_taxonomy_add_new_meta_field() {
	// this will add the custom meta field to the add new term page
	?>
	<div class="form-field">
		<label for="term_meta[code]"><?php _e( 'Subject code', 'pippin' ); ?></label>
		<input type="text" name="term_meta[code]" id="term_meta[code]" value="">
		<p class="description"><?php _e( 'Enter a value for this field','pippin' ); ?></p>
	</div>
	<div class="form-field">
		<label for="term_meta[authority]"><?php _e( 'Examination Authority', 'pippin' ); ?></label>
		<input type="text" name="term_meta[authority]" id="term_meta[authority]" value="">
		<p class="description"><?php _e( 'Enter a value for this field','pippin' ); ?></p>
	</div>
<?php
}
add_action( 'subjects_add_form_fields', 'pippin_taxonomy_add_new_meta_field', 10, 2 );

function pippin_taxonomy_edit_meta_field($term) {
 
	// put the term ID into a variable
	$t_id = $term->term_id;
 
	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" ); ?>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[code]"><?php _e( 'Subject code', 'pippin' ); ?></label></th>
		<td>
			<input type="text" name="term_meta[code]" id="term_meta[code]" value="<?php echo esc_attr( $term_meta['code'] ) ? esc_attr( $term_meta['code'] ) : ''; ?>">
			<p class="description"><?php _e( 'Enter a value for this field','pippin' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[authority]"><?php _e( 'Examination Authority', 'pippin' ); ?></label></th>
		<td>
			<input type="text" name="term_meta[authority]" id="term_meta[authority]" value="<?php echo esc_attr( $term_meta['authority'] ) ? esc_attr( $term_meta['authority'] ) : ''; ?>">
			<p class="description"><?php _e( 'Enter a value for this field','pippin' ); ?></p>
		</td>
	</tr>
<?php
}
add_action( 'subjects_edit_form_fields', 'pippin_taxonomy_edit_meta_field', 10, 2 );

// Save extra taxonomy fields callback function.
function save_taxonomy_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}  
add_action( 'edited_subjects', 'save_taxonomy_custom_meta', 10, 2 );  
add_action( 'create_subjects', 'save_taxonomy_custom_meta', 10, 2 );

// Add Custom Post Type to WP-ADMIN Right Now Widget
// Ref Link: http://wpsnipp.com/index.php/functions-php/include-custom-post-types-in-right-now-admin-dashboard-widget/
// http://wordpress.org/support/topic/dashboard-at-a-glance-custom-post-types
// http://halfelf.org/2012/my-custom-posttypes-live-in-mu/
function vm_right_now_content_table_end() {
    $args = array(
        'public' => true ,
        '_builtin' => false
    );
    $output = 'object';
    $operator = 'and';
    $post_types = get_post_types( $args , $output , $operator );
    asort($post_types);
    foreach( $post_types as $post_type ) {
        $num_posts = wp_count_posts( $post_type->name );
        $num = number_format_i18n( $num_posts->publish );
        $text = _n( $post_type->labels->singular_name, $post_type->labels->name , intval( $num_posts->publish ) );
        if ( current_user_can( 'edit_posts' ) ) {
            $cpt_name = $post_type->name;
        }
        echo '<li class="'.$cpt_name.'-count"><tr><a href="edit.php?post_type='.$cpt_name.'"><td class="first b b-' . $post_type->name . '"></td>' . $num . ' <td class="t ' . $post_type->name . '">' . $text . '</td></a></tr></li>';
    }
    $taxonomies = get_taxonomies( $args , $output , $operator );
    foreach( $taxonomies as $taxonomy ) {
        $num_terms  = wp_count_terms( $taxonomy->name );
        $num = number_format_i18n( $num_terms );
        $text = _n( $taxonomy->labels->name, $taxonomy->labels->name , intval( $num_terms ));
	if($taxonomy->name == 'post_status'){break;}
        if ( current_user_can( 'manage_categories' ) ) {
            $cpt_tax = $taxonomy->name;
        }
        echo '<li class="' . $cpt_tax . '-count"><tr><a href="edit-tags.php?taxonomy='.$cpt_tax.'"><td class="first b b-' . $taxonomy->name . '"></td>' . $num . ' <td class="t ' . $taxonomy->name . '">' . $text . '</td></a></tr></li>';
    }
}
add_action( 'dashboard_glance_items' , 'vm_right_now_content_table_end' );

add_action( 'admin_head-index.php', 'kato_asn_admin_dashboard_style' );

function kato_asn_admin_dashboard_style (){
	wp_register_style( 'kato_asn_admin_stylesheet', plugins_url( '/kato-admin.css', __FILE__ ) );
	wp_enqueue_style( 'kato_asn_admin_stylesheet' );
}