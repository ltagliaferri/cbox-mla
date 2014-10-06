<?php
/**
 * This file contains customizations specific to the MLA
 * implementation of CBOX. 
 */ 

// Include files instead of cluttering up this file. 
require_once 'engine/includes/advanced-search.php'; 
require_once 'engine/includes/allowed-tags.php'; 
require_once 'engine/includes/avatars.php';
require_once 'engine/includes/custom.php';
require_once 'engine/includes/custom-filters.php';

/**
 * Set this to true to put Infinity into developer mode. Developer mode will refresh the dynamic.css on every page load.
 */
define( 'INFINITY_DEV_MODE', true );

/* This script contains Buddypress customizations for MLA group types. */ 

/* MLA edits to BP literals. 
/* These don't seem to have any effect, since slugs appear to be handled by WP pages, 
 * but I'll keep these in just in case they affect something else elsewhere.
 */ 
define( 'BP_FRIENDS_SLUG', 'contacts' );
define( 'BP_BLOGS_SLUG', 'sites' );

// Change "en_US" to your locale
define( 'BPLANG', 'en_US' );
if ( file_exists( WP_LANG_DIR . '/buddypress-' . BPLANG . '.mo' ) ) {
	load_textdomain( 'buddypress', WP_LANG_DIR . '/buddypress-' . BPLANG . '.mo' );
}

/* This function filters out membership activities from the group activity stream, 
 * so that "so-and-so joined the group X" doesn't clutter the activity stream. 
 */ 

/* this is a jQuery hack to check the checkbox on 
 * Create a Group → 4. Forum → Group Forum → 
 * “Yes. I want this Group to have a forum” 
 * by default. 
 */ 
function mla_check_create_forum_for_new_group() {
	if ( wp_script_is( 'jquery','done' ) ) { ?>
		<script type="text/javascript">
		jq('#bbp-create-group-forum').prop('checked', true);
		</script>
  <?php }
}
add_action( 'wp_footer', 'mla_check_create_forum_for_new_group' );

/* disable visual editor entirely, for everyone */ 
/* add_filter('user_can_richedit' , '__return_false', 50); */ 

/* 
 * Remove redundant email status button in group headings; 
 * this is handled by the group tab "Email Options" 
 */
remove_action( 'bp_group_header_meta', 'ass_group_subscribe_button' );

/* 
 * Remove forum subscribe link. Users are already subscribed to the forums 
 * when they subscribe to the group. Having more fine-grained control over 
 * subscriptions is unnecessary and confusing.
 */ 
function mla_remove_forum_subscribe_link( $link ){ 
	return ''; //making this empty so that it will get rid of the forum subscribe link
} 
add_filter( 'bbp_get_forum_subscribe_link', 'mla_remove_forum_subscribe_link' );

/* 
 * Remove subscription link from groups directory. 
 * Because we're about to rewrite it!
 * Get ready for the magic.  
 */ 
remove_action( 'bp_directory_groups_actions', 'ass_group_subscribe_button' );

function mla_ass_group_subscribe_button() {
	global $bp, $groups_template;

	if ( ! empty( $groups_template ) ) {
		$group =& $groups_template->group;
	}
	else {
		$group = groups_get_current_group();
	}

	if ( ! is_user_logged_in() || ! empty($group->is_banned) || ! $group->is_member)
		return;

	// if we're looking at someone elses list of groups hide the subscription
	if (bp_displayed_user_id() && (bp_loggedin_user_id() != bp_displayed_user_id()))
		return;

	$group_status = ass_get_group_subscription_status( bp_loggedin_user_id(), $group->id );

	if ($group_status == 'no')
		$group_status = NULL;

	$status_desc = __( 'Your email status is ', 'bp-ass' );
	$link_text = __( 'change', 'bp-ass' );
	$gemail_icon_class = ' gemail_icon';
	$sep = '';

	if ( ! $group_status ) {
		//$status_desc = '';
		$link_text = __( 'Get email updates', 'bp-ass' );
		$sep = '';
	}

	$status = ass_subscribe_translate( $group_status );

	$notifications_url = home_url().'/groups/'.groups_get_slug( $group->id ).'/notifications/'; 
	?>

	<div class="group-subscription-div">
		<a class="group-subscription-options-link" id="gsublink-<?php echo esc_attr( $group->id ); ?>" href="<?php echo esc_html( $notifications_url ); ?>" title="<?php _e( 'Change your email subscription options for this group', 'bp-ass' );?>"><span class="group-subscription-status<?php echo esc_attr( $gemail_icon_class ); ?>" id="gsubstat-<?php echo esc_attr( $group->id ); ?>"><?php echo $status; ?></span> <?php echo $sep; ?></a>
	</div>

	<?php
}

add_action( 'bp_directory_groups_actions', 'mla_ass_group_subscribe_button' );

/* Remove forum title, since in our use cases forum titles have the same names as
 * their parent groups, and users see a redundant title on group forums pages. 
 */
function mla_remove_forum_title( $title ) { 
} 
add_filter( 'bbp_get_forum_title', 'mla_remove_forum_title' ); 

/* 
 * Remove profile group tab from edit profile page when there's only one profile
 * group. I just says "Profile" and is kind of confusing. 
 */ 
function mla_remove_profile_group_tab( $tabs, $groups ) { 
	if ( count( $groups ) > 1 ) { 
		return $tabs; 
	} else { 
		return; 
	} 
} 
add_filter( 'xprofile_filter_profile_group_tabs', 'mla_remove_profile_group_tab' ); 

/** 
 * Remove "What's new in ___, Jonathan?" 
 * Taken from BP-Group-Announcements. 
 */ 
// Disable the activity update form on the group home page. Props r-a-y
add_action( 'bp_before_group_activity_post_form', create_function( '', 'ob_start();' ), 9999 );
add_action( 'bp_after_group_activity_post_form', create_function( '', 'ob_end_clean();' ), 0 );

/* 
 * Hide settings page (we don't want users changing their 
 * e-mail or password).
 */
function change_settings_subnav() {

	$args = array(
		'parent_slug' => 'settings',
		'screen_function' => 'bp_core_screen_notification_settings',
		'subnav_slug' => 'notifications', 
	);

	bp_core_new_nav_default( $args );

}
add_action( 'bp_setup_nav', 'change_settings_subnav', 5 );

function remove_general_subnav() {
	global $bp;
	bp_core_remove_subnav_item( $bp->settings->slug, 'general' );
}

add_action( 'wp', 'remove_general_subnav', 2 );



/*
 * Remove misbehaving forums tab on profile pages.
 */

function remove_forums_nav() {
	bp_core_remove_nav_item( 'forums' );
}
add_action( 'wp', 'remove_forums_nav', 3 );

/**
 * Removes Forums from Howdy dropdown
 */
function mlac_remove_forums_from_adminbar( $wp_admin_bar ) {
	$wp_admin_bar->remove_menu( 'my-account-forums' );
}
add_action( 'admin_bar_menu', 'mlac_remove_forums_from_adminbar', 9999 );

// force reload css on new versions
function my_wp_default_styles( $styles )
{
	//use epoch time for version
	$styles->default_version = '2.0.2';
}
add_action( 'wp_default_styles', 'my_wp_default_styles' );

function mla_remove_name_from_edit_profile($cols) { 
	// Assuming "1" is going to be "name." 
	// We have to rebuild the array, too. 
	$cols['left'] = array_values( array_diff( $cols['left'], array( 1 ) ) ); 
	return $cols; 
} 
add_filter('cacap_header_edit_columns', 'mla_remove_name_from_edit_profile'); 

function mla_is_group_committee() { 
	// if mla_oid starts with "M," it's a committee
	return ('M' == substr( groups_get_groupmeta( bp_get_current_group_id(), 'mla_oid' ), 0, 1 ) ) ? true : false; 
} 

function mla_remove_membership_request_from_committees() {
	if ( mla_is_group_committee() ) { 
		bp_core_remove_subnav_item( bp_get_current_group_slug(), 'request-membership' ); 
	} 
}
add_filter( 'bp_setup_nav', 'mla_remove_membership_request_from_committees' ); 


// remove default profile link handling so we can override it below
remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data' );

// Custom xprofile interest linkifier that accepts semicolons as delimiters. 
function mla_xprofile_filter_link_profile_data( $field_value, $field_type = 'textbox' ) {

	if ( 'datebox' === $field_type ) {
		return $field_value;
	}

	if ( ! strpos( $field_value, ',' ) && !strpos( $field_value, '; ' )  && ( count( explode( ' ', $field_value ) ) > 5 ) ) { 
		return $field_value;
	}

	if ( strpos( $field_value, '; ' ) ) { 
		$list_type = 'semicolon'; 
		$values = explode( '; ', $field_value ); // semicolon-separated lists
	} else { 
		$list_type = 'comma'; 
		$values = explode( ',', $field_value ); // comma-separated lists
	}  

	if ( ! empty( $values ) ) {
		foreach ( (array) $values as $value ) {
			$value = trim( $value );

			// remove <br>s at the ends of interest lists, 
			// so that the final search term works
			$value = preg_replace( '/\<br \/\>$/', '', $value );  

			// If the value is a URL, skip it and just make it clickable.
			if ( preg_match( '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $value ) ) {
				$new_values[] = make_clickable( $value );

			// Is not clickable
			} else {

				// More than 5 spaces
				if ( count( explode( ' ', $value ) ) > 5 ) {
					$new_values[] = $value;

				// Less than 5 spaces
				} else {
					if ( preg_match( '/\.$/', $value ) ) { // if it ends in a period
						$value = preg_replace( '/\.$/', '', $value ); // remove the period at the end
						$search_url   = add_query_arg( array( 's' => urlencode( $value ) ), bp_get_members_directory_permalink() );
						$new_values[] = '<a href="' . esc_url( $search_url ) . '" rel="nofollow">' . $value . '</a>.'; // but add it back *after* the link. 
					} else if ( preg_match( '/\.\<br \/\>', $value ) ) { 
						$search_url   = add_query_arg( array( 's' => urlencode( $value ) ), bp_get_members_directory_permalink() );
						$new_values[] = '<a href="' . esc_url( $search_url ) . '" rel="nofollow">' . $value . '</a>.<br />';
					} else if ( preg_match( '/\<br \/\>', $value ) ) { 
						$search_url   = add_query_arg( array( 's' => urlencode( $value ) ), bp_get_members_directory_permalink() );
						$new_values[] = '<a href="' . esc_url( $search_url ) . '" rel="nofollow">' . $value . '</a><br />';

					} else { 
						$search_url   = add_query_arg( array( 's' => urlencode( $value ) ), bp_get_members_directory_permalink() );
						$new_values[] = '<a href="' . esc_url( $search_url ) . '" rel="nofollow">' . $value . '</a>';
					} 
				}
			}
		}

		if ( 'semicolon' == $list_type ) { 
			$values = implode( '; ', $new_values ); 
		} else { 
			$values = implode( ', ', $new_values );
		}  
	}

	return $values;
}
add_filter( 'bp_get_the_profile_field_value', 'mla_xprofile_filter_link_profile_data', 9, 2 );

function mla_custom_bp_mofile( $mofile, $domain ){
	if ( 'buddypress' == $domain ) {
		$mofile = trailingslashit( WP_LANG_DIR ) . basename( $mofile );
	}
	return $mofile;
}
add_filter( 'load_textdomain_mofile', 'mla_custom_bp_mofile', 10, 2 );

/* Load group membership data from member database on 
 * group page load. 
 */ 
function mla_update_group_membership_data() { 
	$mla_group = new MLAGroup; 
	$mla_group->sync(); 
} 
//add_action( 'bp_before_group_body', 'mla_update_group_membership_data' );

function mla_update_member_data() { 
	$mla_member = new MLAMember(); 
	if ( $mla_member->sync() ) { 
		_log( 'Success! Member data synced.' ); 
	} else { 
		_log( 'Something went wrong while trying to update member info from the member database.' ); 
	} 
} 
add_action( 'cacap_before_content', 'mla_update_member_data' );  
//add_action( 'bp_before_member_groups_content', 'mla_update_member_data' );  
