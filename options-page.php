<?php
/**
  * Register a settings page.
  * @return void
*/
function sso_register_settings_page() {
    add_options_page(
        'Set Sort Order Options',
        'Set Sort Order',
        'manage_options',
        'set-sort-order',
        'set_sort_orders_options'
    );
}
add_action('admin_menu', 'sso_register_settings_page');

function set_sort_orders_options() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'set_sort_orders_options_save')) {

            if (isset($_POST['field_list'])) {
				$field_list = explode( ',', $_POST['field_list'] );
				foreach ( $field_list as $field ) {
					if ( isset( $_POST[ $field ] ) ) {
						update_option( $field, sanitize_text_field( $_POST[ $field ] ) );
					}
				}

				add_action('admin_notices', 'sso_admin_notice__success');
            }
        }
    }

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
		<hr />
		<form action="" method="post">
		<?php
		echo wp_nonce_field( 'set_sort_orders_options_save' );

		$field_list = array();
		$post_types = sso_get_post_types();

		if ( $post_types ) : ?>

		<h2>Post Types</h2>
            <table class="form-table">
				<tr>
					<th>Post Type</th>
					<th>Sort By</th>
					<th>Sort Order</th>
				</tr>
				<?php
				foreach ( $post_types as $post_type ) :
					$display_name         = ucwords( str_replace( '_', ' ', $post_type ) );
					$sort_by_name         = 'sso_post_type_' . $post_type . '_sort_by';
					$sort_by_dir          = 'sso_post_type_' . $post_type . '_sort_dir';
					$current_sort_by_name = get_option( $sort_by_name );
					$current_sort_by_dir  = get_option( $sort_by_dir );
					$field_list[]         = $sort_by_name;
					$field_list[]         = $sort_by_dir;
				?>
                <tr>
                    <th scope="row"><?php echo $display_name; ?></th>
                    <td><select name="<?php echo $sort_by_name; ?>">
							<option value="Default" <?php selected( $current_sort_by_name, 'Default' ); ?>>Default</option>
							<option value="menu_order" <?php selected( $current_sort_by_name, 'menu_order' ); ?>>Menu Order</option>
							<option value="title" <?php selected( $current_sort_by_name, 'title' ); ?>>Title</option>
							<option value="date" <?php selected( $current_sort_by_name, 'date' ); ?>>Date</option>
						</select>
					</td>
					<td>
						<select name="<?php echo $sort_by_dir; ?>">
							<option value="Default" <?php selected( $current_sort_by_dir, 'Default' ); ?>>Default</option>
							<option value="ASC" <?php selected( $current_sort_by_dir, 'ASC' ); ?>>Ascending</option>
							<option value="DESC" <?php selected( $current_sort_by_dir, 'DESC' ); ?>>Descending</option>
						</select>
					</td>
                </tr>
				<?php endforeach; ?>
            </table>
		<?php
		endif;

		$taxonomies = sso_get_taxonomies();

		if ( $taxonomies ) : ?>

		<h2>Taxonomies</h2>
            <table class="form-table">
				<tr>
					<th>Taxonomy</th>
					<th>Sort By</th>
					<th>Sort Order</th>
				</tr>
				<?php
				foreach ( $taxonomies as $taxonomy ) :
					$display_name         = ucwords( str_replace( '-', ' ', $taxonomy ) );
					$sort_by_name         = 'sso_taxonomy_' . $taxonomy . '_sort_by';
					$sort_by_dir          = 'sso_taxonomy_' . $taxonomy . '_sort_dir';
					$current_sort_by_name = get_option( $sort_by_name );
					$current_sort_by_dir  = get_option( $sort_by_dir );
					$field_list[]         = $sort_by_name;
					$field_list[]         = $sort_by_dir;
				?>
                <tr>
                    <th scope="row"><?php echo $display_name; ?></th>
                    <td><select name="<?php echo $sort_by_name; ?>">
							<option value="Default" <?php selected( $current_sort_by_name, 'Default' ); ?>>Default</option>
							<option value="menu_order" <?php selected( $current_sort_by_name, 'menu_order' ); ?>>Menu Order</option>
							<option value="title" <?php selected( $current_sort_by_name, 'title' ); ?>>Title</option>
							<option value="date" <?php selected( $current_sort_by_name, 'date' ); ?>>Date</option>
						</select>
					</td>
					<td>
						<select name="<?php echo $sort_by_dir; ?>">
							<option value="Default" <?php selected( $current_sort_by_dir, 'Default' ); ?>>Default</option>
							<option value="ASC" <?php selected( $current_sort_by_dir, 'ASC' ); ?>>Ascending</option>
							<option value="DESC" <?php selected( $current_sort_by_dir, 'DESC' ); ?>>Descending</option>
						</select>
					</td>
                </tr>
				<?php endforeach; ?>
            </table>
		<?php endif; ?>


		<input type = "hidden" name = "field_list" value = "<?php echo implode( ',', $field_list ); ?>" />
            <?php submit_button('Save Changes'); ?>
        </form>
    </div>
    <?php
}

/**
 * Get a list of all public post types.
 *
 * @return array|bool
 */
function sso_get_post_types() {
	$skip_types = array( 'page', 'post', 'attachment' );
	$post_types = get_post_types( array( 'public' => true ) );

	$post_types = array_diff( $post_types, $skip_types );

	if ( count( $post_types ) < 1 ) {
		return false;
	}

	sort( $post_types );

	return $post_types;
}

/**
 * Get a list of all public taxonomies.
 *
 * @return array|bool
 */
function sso_get_taxonomies() {
	$skip_taxes = array( 'post_format', 'post_tag' );
	$taxonomies = get_taxonomies( array( 'public' => true ) );

	$taxonomies = array_diff( $taxonomies, $skip_taxes );

	if ( count( $taxonomies ) < 1 ) {
		return false;
	}

	sort( $taxonomies );

	return $taxonomies;
}

/**
 * Display a notice that the options have been saved.
 *
 * @return void
 */
function sso_admin_notice__success() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Options Saved', 'setsortorder' ); ?></p>
    </div>
    <?php
}
add_action( 'admin_notices', 'sso_admin_notice__success' );
