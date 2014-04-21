<?php

class GH_Members_Showoff_Shortcode {

	/**
	 * Plugin instance.
	 *
	 * @var GH_Profile_Widget_Plugin
	 */
	private $plugin;

	public function __construct( GH_Members_Showoff_Plugin $plugin ) {
		$this->plugin = $plugin;

		add_shortcode( 'gh_members_showoff', array( $this, 'render' ) );
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array  $atts
	 * @param string $content
	 */
	public function render( $atts ) {
		$defaults = array(
			'org'            => '',
			'limit'          => 0,
			'order_username' => '',
			'org_info'       => true,
		);

		$args = shortcode_atts( $defaults, $atts );
		if ( empty( $args['org'] ) ) {
			return '';
		}

		$cache_key = md5( maybe_serialize( $args ) ) . '_members_shortcode';

		return $this->_get_view( $cache_key, $args );
	}

	/**
	 * Get view to render.
	 *
	 * @param  string $cache_key
	 * @param  array  $args
	 * @return string
	 */
	private function _get_view( $cache_key, array $args ) {
		$plugin  = $this->plugin;
		$members = $plugin->cache->get( $cache_key, $args );

		ob_start();
		require apply_filters( 'gh_members_showoff_shortcode_view_path', $plugin->views_path . 'members-listing.php' );

		return ob_get_clean();
	}
}
