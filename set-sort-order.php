<?php
/**
 * Plugin Name: Set Sort Order
 * Plugin URI: https://vegasgeek.com
 * Description: Customize the sort order for your taxonomies and post types.
 * Author: VegasGeek
 * Version: 1.0
 * Author URI: https://vegasgeek.com
 * Text Domain: setsortorder
 *
 * @package SetSortOrder
 */

require_once( 'options-page.php' );

function sso_sort_the_archives( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {
		if ( is_tax() ) {
			$tax_slug     = null;
			$current_term = get_queried_object();
			if ( isset( $current_term->taxonomy ) ) {
				$tax_slug = $current_term->taxonomy;
			} else {
				return;
			}

			if ( 'Default' === get_option( 'sso_taxonomy_' . $tax_slug . '_sort_by' ) ) {
				return;
			}

			$query->set( 'orderby', get_option( 'sso_taxonomy_' . $tax_slug . '_sort_by' ) );

			if ( 'Default' !== get_option( 'sso_taxonomy_' . $tax_slug . '_sort_dir' ) ) {
				$query->set( 'order', get_option( 'sso_taxonomy_' . $tax_slug . '_sort_dir' ) );
			}
		}
	}

}
add_filter( 'pre_get_posts', 'sso_sort_the_archives' );
