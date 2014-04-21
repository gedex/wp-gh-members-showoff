<?php

class GH_Members_Showoff_Plugin {

	/**
	 * Runs the plugin. Basically doing initialization
	 * and register callbacks to hooks.
	 *
	 * @param string               $path
	 * @param WP_GitHub_API_Plugin $api
	 */
	public function run( $path, $api ) {
		// Basic plugin information.
		$this->name    = 'gh_members_showoff'; // This maybe used to prefix options, slug of menu or page, and filters/actions.
		$this->version = '0.1.0';

		// Path.
		$this->plugin_path   = trailingslashit( plugin_dir_path( $path ) );
		$this->plugin_url    = trailingslashit( plugin_dir_url( $path ) );
		$this->includes_path = $this->plugin_path . trailingslashit( 'includes' );
		$this->views_path    = $this->plugin_path . trailingslashit( 'views' );
		$this->css_path      = $this->plugin_url  . trailingslashit( 'assets/css' );

		// Cache wrapper.
		require_once $this->includes_path . 'cache.php';
		$this->cache = new GH_Members_Showoff_Cache( $this );

		// GitHub API plugin that this plugins depends on.
		$this->api = $api;

		// Shortcode.
		require_once $this->includes_path . 'shortcode.php';
		$this->shortcode = new GH_Members_Showoff_Shortcode( $this );

		// Widget registration. Enqueue styles when widget is active.
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_action( 'init',         array( $this, 'enqueue_style' ) );

		// Register hook for 'gh_members_showoff' if implemented by
		// theme or plugin.
		add_action( 'gh_members_showoff', array( $this, 'render' ) );
	}

	/**
	 * Register the widget.
	 *
	 * @action widgets_init
	 */
	public function register_widget() {
		require_once $this->includes_path . 'widget.php';
		register_widget( 'GH_Members_Showoff_Widget' );
	}

	/**
	 * Enqueue style for the widget only if widget is active.
	 *
	 * @action init
	 */
	public function enqueue_style() {
		if ( is_active_widget( false, false, 'gh_members_showoff_widget' ) && ! is_admin() ) {
			wp_enqueue_style( 'gh-octicons' );
			wp_enqueue_style( 'gh-members-showoff-widget', $this->css_path . 'widget.css', false, $this->version, 'all' );
		}
		wp_enqueue_style( 'gh-members-showoff-shortcode', $this->css_path . 'shortcode.css', false, $this->version, 'all' );
	}

	/**
	 * Allows you put `do_action( 'gh_members_showoff' )` anywhere.
	 *
	 * @param array $args
	 *
	 * @action gh_members_showoff
	 */
	public function render( $args = array() ) {
		$defaults = array(
			'org'            => '',
			'limit'          => 0,
			'order_username' => '',
			'org_info'       => true,
		);
		$args = wp_parse_args( $args, $defaults );
		if ( empty( $args['org'] ) ) {
			return;
		}

		$cache_key = md5( maybe_serialize( $args ) ) . '_members_listing';
		$members   = $this->cache->get( $cache_key, $args );

		ob_start();
		require apply_filters( 'gh_members_showoff_view_path', $this->views_path . 'members-listing.php' );

		echo ob_get_clean();
	}
}
