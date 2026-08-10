<?php
/**
 * Inbox Admin Class.
 *
 * @author  MetricThemes
 * @package inbox
 * @since   
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Inbox_Admin' ) ) :

/**
 * Inbox_Admin Class.
 */
class Inbox_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );
		add_action( 'load-themes.php', array( $this, 'admin_notice' ) );
	}

	/**
	 * Add admin menu.
	 */
	public function admin_menu() {
		$theme = wp_get_theme( get_template() );

		$page = add_theme_page( esc_html__( 'About', 'inbox' ) . ' ' . $theme->display( 'Name' ), esc_html__( 'About', 'inbox' ) . ' ' . $theme->display( 'Name' ), 'activate_plugins', 'inbox-welcome', array( $this, 'welcome_screen' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function enqueue_styles() {
		$inbox_theme = wp_get_theme();
		$inbox_version = $inbox_theme->get( 'Version' );

		wp_enqueue_style( 'inbox-welcome', get_template_directory_uri() . '/inc/admin/css/admin-welcome.css', array(), $inbox_version );
	}

	/**
	 * Add admin notice.
	 */
	public function admin_notice() {
		global $inbox_version, $pagenow;

		wp_enqueue_style( 'inbox-message', get_template_directory_uri() . '/inc/admin/css/admin-welcome.css', array(), $inbox_version );

		// Let's bail on theme activation.
		if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
			update_option( 'Inbox_Admin_notice_welcome', 1 );

		// No option? Let run the notice wizard again..
		} elseif( ! get_option( 'Inbox_Admin_notice_welcome' ) ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
		}
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public static function hide_notices() {
		if ( isset( $_GET['inbox-hide-notice'] ) && isset( $_GET['inbox_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['inbox_notice_nonce'], 'inbox_hide_notices_nonce' ) ) {
				wp_die( esc_html( 'Action failed. Please refresh the page and retry.', 'inbox' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html( 'Cheatin&#8217; huh?', 'inbox' ) );
			}

			$hide_notice = sanitize_text_field( $_GET['inbox-hide-notice'] );
			update_option( 'Inbox_Admin_notice_welcome', 1 );
		}
	}

	/**
	 * Show welcome notice.
	 */
	public function welcome_notice() {
		?>
		<div class="notice notice-success">
			<a style="top:70px;" class="inbox-message-close is-dismissible notice-dismiss" href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'activated' ), add_query_arg( 'inbox-hide-notice', 'welcome' ) ), 'inbox_hide_notices_nonce', 'inbox_notice_nonce' ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss', 'inbox' ); ?></span></a>
            <?php /* translators: %1$1: welcome page url %2$2: welcome page text */ ?>
			<p><?php printf( esc_html__( 'Thank you for choosing Inbox! To fully take advantage of everything that our theme offers please make sure you visit our %1$1swelcome page%2$2s. We have just released a premium version of Inbox Theme with more customization options. ', 'inbox' ), '<a href="' . esc_url( admin_url( 'themes.php?page=inbox-welcome' ) ) . '">', '</a>' ); ?></p>
            <p class="submit">
				<a class="button-secondary" href="https://metricthemes.com/theme/inbox-pro/" target="_blank"><?php esc_html_e( 'Upgrade to Inbox Pro', 'inbox' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Intro text/links shown to all about pages.
	 *
	 * @access private
	 */
	private function intro() {
		global $inbox_version;
		$theme = wp_get_theme( get_template() );

		// Drop minor version if 0
		$major_version = substr( $inbox_version, 0, 3 );
		?>
		<div class="inbox-theme-info">
				<h1>
					<?php esc_html_e('Getting Started With', 'inbox'); ?>
					<?php echo esc_html($theme->display( 'Name' )); ?>
					<?php printf( '%s', $major_version); ?>
				</h1>

			<div class="welcome-description-wrap">
				<div class="about-text"><?php echo esc_html($theme->display( 'Description' )); ?></div>

				<div class="inbox-screenshot">
					<img src="<?php echo esc_url( get_template_directory_uri() ) . '/screenshot.png'; ?>" />
				</div>
			</div>
		</div>

		<p class="inbox-actions">
			<!-- Theme Demo -->
			<a href="<?php echo esc_url( 'https://preview.metricthemes.com/inbox/' ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Theme Demo', 'inbox' ); ?></a>

			<!-- Theme Details -->
			<a href="<?php echo esc_url('https://metricthemes.com/theme/inbox/'); ?>" class="button button-primary docs" target="_blank"><?php esc_html_e( 'Theme Details', 'inbox' ); ?></a>

			<!-- Theme Documentaion  -->
			<a href="<?php echo esc_url( 'https://metricthemes.com/documentation/inbox-pro-documentation/' ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'Documentation', 'inbox' ); ?></a>

			<!-- Go To Pro -->
			<a href="<?php echo esc_url( 'https://metricthemes.com/theme/inbox-pro/' ); ?>" class="button button-primary docs" target="_blank"><?php esc_html_e( 'Inbox Pro', 'inbox' ); ?></a>
		</p>
		<div class="tab-only-wrapper">
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( empty( $_GET['tab'] ) && $_GET['page'] == 'inbox-welcome' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'inbox-welcome' ), 'themes.php' ) ) ); ?>">
				<?php echo esc_html($theme->display( 'Name' )); ?>
			</a>
			
			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'free_vs_pro' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'inbox-welcome', 'tab' => 'free_vs_pro' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Free Vs Pro', 'inbox' ); ?>
			</a>

			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'more_themes' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'inbox-welcome', 'tab' => 'more_themes' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Get More Themes', 'inbox' ); ?>
			</a>

			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'changelog' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'inbox-welcome', 'tab' => 'changelog' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Changelog', 'inbox' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Welcome screen page.
	 */
	public function welcome_screen() {
		$current_tab = empty( $_GET['tab'] ) ? 'about' : wp_unslash(sanitize_title( $_GET['tab'] ));

		// Look for a {$current_tab}_screen method.
		if ( is_callable( array( $this, $current_tab . '_screen' ) ) ) {
			return $this->{ $current_tab . '_screen' }();
		}

		// Fallback to about screen.
		return $this->about_screen();
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		$theme = wp_get_theme( get_template() );
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<div class="changelog point-releases contentsection-wrap">
				<div class="under-the-hood two-col">
               
					<div class="col">
						<h3><?php esc_html_e( 'Theme Customizer', 'inbox' ); ?></h3>
						<p><?php esc_html_e( 'All our theme options are available via Customize screen.', 'inbox' ) ?></p>
						<p><a href="<?php echo esc_url(admin_url( 'customize.php' )); ?>" class="button button-secondary"><?php esc_html_e( 'Customize', 'inbox' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php esc_html_e( 'Documentation', 'inbox' ); ?></h3>
						<p><?php esc_html_e( 'Please view our documentation page to setup the theme.', 'inbox' ) ?></p>
						<p><a href="<?php echo esc_url( 'https://metricthemes.com/documentation/inbox-documentation/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Documentation', 'inbox' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php esc_html_e( 'Got theme support question?', 'inbox' ); ?></h3>
						<p><?php esc_html_e( 'Please ask it through our support ticket system', 'inbox' ) ?></p>
						<p><a href="<?php echo esc_url( 'https://metricthemes.com/contact-us/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Support', 'inbox' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php esc_html_e( 'Need more features?', 'inbox' ); ?></h3>
						<p><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'inbox' ) ?></p>
						<p><a href="<?php echo esc_url( 'https://metricthemes.com/theme/inbox-pro/' ); ?>" class="button button-secondary"><?php esc_html_e( 'View PRO version', 'inbox' ); ?></a></p>
					</div>

					<div class="col">
						<h3>
							<?php
							esc_html_e( 'Translate', 'inbox' );
							echo ' ' . esc_html($theme->display( 'Name' ));							
							?>
						</h3>
						<p><?php esc_html_e( 'Click below to translate this theme into your own language.', 'inbox' ) ?></p>
						<p>
							<a href="<?php echo esc_url( 'https://translate.wordpress.org/projects/wp-themes/inbox' ); ?>" class="button button-secondary">
								<?php
								esc_html_e( 'Translate', 'inbox' );
								echo ' ' . esc_html($theme->display( 'Name' ));
								?>
							</a>
						</p>
					</div>

				</div>
			</div>

			<div class="return-to-dashboard">
				<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
					<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
						<?php is_multisite() ? esc_html_e( 'Return to Updates', 'inbox' ) : esc_html_e( 'Return to Dashboard &rarr; Updates', 'inbox' ); ?>
					</a> |
				<?php endif; ?>
				<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? esc_html_e( 'Go to Dashboard &rarr; Home', 'inbox' ) : esc_html_e( 'Go to Dashboard', 'inbox' ); ?></a>
			</div>

		</div>
		<?php
	}

		/**
	 * Output the changelog screen.
	 */
	public function changelog_screen() {
		global $wp_filesystem;

		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>
			<div class="contentsection-wrap">
			<p class="about-description"><?php esc_html_e( 'View changelog below:', 'inbox' ); ?></p>

			<?php
				$changelog_file = apply_filters( 'inbox_changelog_file', get_template_directory() . '/readme.txt' );

				// Check if the changelog file exists and is readable.
				if ( $changelog_file && is_readable( $changelog_file ) ) {
					WP_Filesystem();
					$changelog = $wp_filesystem->get_contents( $changelog_file );
					$changelog_list = $this->parse_changelog( $changelog );

					echo wp_kses_post( $changelog_list );
				}
			?>
		</div>            
		</div>
		<?php
	}

	/**
	 * Parse changelog from readme file.
	 * @param  string $content
	 * @return string
	 */
	private function parse_changelog( $content ) {
		$matches   = null;
		$regexp    = '~==\s*Changelog\s*==(.*)($)~Uis';
		$changelog = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$changes = explode( '\r\n', trim( $matches[1] ) );

			$changelog .= '<pre class="changelog">';

			foreach ( $changes as $index => $line ) {
				$changelog .= wp_kses_post( preg_replace( '~(=\s*Version\s*(\d+(?:\.\d+)+)\s*=|$)~Uis', '<span class="title">${1}</span>', $line ) );
			}

			$changelog .= '</pre>';
		}

		return wp_kses_post( $changelog );
	}


	/**
	 * Output the free vs pro screen.
	 */
	public function free_vs_pro_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>
			<div class="contentsection-wrap">
			<p class="about-description"><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'inbox' ); ?></p>

			<table>
				<thead>
					<tr>
						<th class="table-feature-title"><h3><?php esc_html_e('Features', 'inbox'); ?></h3></th>
						<th><h3><?php esc_html_e('Inbox', 'inbox'); ?></h3></th>
						<th><h3 class="inbox-pro-header"><a href="<?php echo esc_url('https://metricthemes.com/themes/inbox-pro/'); ?>"><?php esc_html_e('Inbox Pro', 'inbox'); ?></a></h3></th>
					</tr>
					
					<!-- Header Section -->
					<tr>
						<td><h3><?php esc_html_e('Custom Logo', 'inbox'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Responsive Design', 'inbox'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Translation Ready', 'inbox'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Optimized For Easy Readability', 'inbox'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Cross Browser Compatibility', 'inbox'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					
					<tr>
						<td><h3><?php esc_html_e('Support', 'inbox'); ?></h3></td>
						<td><?php esc_html_e('Limited', 'inbox'); ?></td>
						<td><?php esc_html_e('Priority', 'inbox'); ?></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Social Media Icons', 'inbox'); ?></h3></td>
						<td><?php esc_html_e('100+', 'inbox'); ?></td>
						<td><?php esc_html_e('100+', 'inbox'); ?></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Customizer Options', 'inbox'); ?></h3></td>
						<td><?php esc_html_e('Minimal', 'inbox'); ?></td>
						<td><?php esc_html_e('40+ Options', 'inbox'); ?></td>
					</tr>
					
					<tr>
						<td><h3><?php esc_html_e('Custom Google Fonts', 'inbox'); ?></h3></td>
						<td><?php esc_html_e('1', 'inbox'); ?></td>
						<td><?php esc_html_e('600+', 'inbox'); ?></td>
					</tr>										

					<tr>
						<td><h3><?php esc_html_e('Option to Remove Read More Button', 'inbox'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
	
    				<tr>
						<td><h3><?php esc_html_e('Unlimited Color Option', 'inbox'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					
					<tr>
						<td><h3><?php esc_html_e('Footer Editor', 'inbox'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
                    
					<tr>
						<td><h3><?php esc_html_e('Typography Options', 'inbox'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>

					<tr>
						<td><h3><?php esc_html_e('Breadcrumbs', 'inbox'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
                    

					<tr>
						<td></td>
						<td></td>
						<td class="btn-wrapper">
							<a href="<?php echo esc_url( 'https://metricthemes.com/theme/inbox-pro/' ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e('View Pro','inbox'); ?></a>
						</td>
					</tr>
		
				</tbody>
			</table>
		</div>
		</div>
		<?php
	}

	/**
	 * Output the more themes screen
	 */
	public function more_themes_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>
			<div class="contentsection-wrap">            
			<div class="theme-browser rendered">
				<div class="themes wp-clearfix">
					<?php
						// Set the argument array with author name.
						$args = array(
							'author' => 'metricthemes',
						);
						// Set the $request array.
						$request = array(
							'body' => array(
								'action'  => 'query_themes',
								'request' => serialize( (object)$args )
							)
						);
						$themes = $this->metricthemes_get_themes( $request );
						$active_theme = wp_get_theme()->get( 'Name' );
						$counter = 1;

						// For currently active theme.
						foreach ( $themes->themes as $theme ) {
							if( $active_theme == $theme->name ) { ?>

								<div id="<?php echo esc_attr($theme->slug); ?>" class="theme active">
									<div class="theme-screenshot">
										<img src="<?php echo esc_url($theme->screenshot_url); ?>" />
									</div>
									<h3 class="theme-name" ><strong><?php esc_html_e( 'Active', 'inbox' ); ?></strong>: <?php echo esc_html($theme->name); ?></h3>
									<div class="theme-actions">
										<a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo esc_url(get_site_url()). '/wp-admin/customize.php' ?>"><?php esc_html_e( 'Customize', 'inbox' ); ?></a>
									</div>
								</div><!-- .theme active -->
							<?php
							$counter++;
							break;
							}
						}

						// For all other themes.
						foreach ( $themes->themes as $theme ) {
							if( $active_theme != $theme->name ) {
								// Set the argument array with author name.
								$args = array(
									'slug' => $theme->slug,
								);
								// Set the $request array.
								$request = array(
									'body' => array(
										'action'  => 'theme_information',
										'request' => serialize( (object)$args )
									)
								);
								$theme_details = $this->metricthemes_get_themes( $request );
							?>
								<div id="<?php echo esc_attr($theme->slug); ?>" class="theme">
									<div class="theme-screenshot">
										<img src="<?php echo esc_url($theme->screenshot_url); ?>"/>
									</div>

									<h3 class="theme-name"><?php echo esc_html($theme->name); ?></h3>

									<div class="theme-actions">
										<?php if( wp_get_theme( $theme->slug )->exists() ) { ?>											
											<!-- Activate Button -->
											<a  class="button button-secondary activate"
												href="<?php echo wp_nonce_url( admin_url( 'themes.php?action=activate&amp;stylesheet=' . urlencode( $theme->slug ) ), 'switch-theme_' . $theme->slug );?>" ><?php esc_html_e( 'Activate', 'inbox' ) ?></a>
										<?php } else {
											// Set the install url for the theme.
											$install_url = add_query_arg( array(
													'action' => 'install-theme',
													'theme'  => $theme->slug,
												), self_admin_url( 'update.php' ) );
										?>
											<!-- Install Button -->
											<a data-toggle="tooltip" data-placement="bottom" title="<?php echo 'Downloaded ' . number_format( $theme_details->downloaded ) . ' times'; ?>" class="button button-secondary activate" href="<?php echo esc_url( wp_nonce_url( $install_url, 'install-theme_' . $theme->slug ) ); ?>" ><?php esc_html_e( 'Install Now', 'inbox' ); ?></a>
										<?php } ?>

										<a class="button button-primary load-customize hide-if-no-customize" target="_blank" href="<?php echo esc_url($theme->preview_url); ?>"><?php esc_html_e( 'Live Preview', 'inbox' ); ?></a>
									</div>
								</div><!-- .theme -->
								<?php
							}
						}


					?>
				</div>
			</div></div><!-- .end div -->
		</div><!-- .ena wrapper -->
        </div>
		<?php
	}

	/** 
	 * Get all our themes by using API.
	 */
	private function metricthemes_get_themes( $request ) {

		// Generate a cache key that would hold the response for this request:
		$key = 'inbox_' . md5( serialize( $request ) );

		// Check transient. If it's there - use that, if not re fetch the theme
		if ( false === ( $themes = get_transient( $key ) ) ) {

			// Transient expired/does not exist. Send request to the API.
			$response = wp_remote_post( 'http://api.wordpress.org/themes/info/1.0/', $request );

			// Check for the error.
			if ( !is_wp_error( $response ) ) {

				$themes = unserialize( wp_remote_retrieve_body( $response ) );

				if ( !is_object( $themes ) && !is_array( $themes ) ) {

					// Response body does not contain an object/array
					return new WP_Error( 'theme_api_error', 'An unexpected error has occurred' );
				}

				// Set transient for next time... keep it for 24 hours should be good
				set_transient( $key, $themes, 60 * 60 * 24 );
			}
			else {
				// Error object returned
				return $response;
			}
		}
		return $themes;
	}


}

endif;

return new Inbox_Admin();
