<?php

class GH_Members_Showoff_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			strtolower( __CLASS__ ),
			__( 'GitHub Members Show-off', 'github-api' ),
			array(
				'description' => __( 'Show off your GitHub org members.', 'github-api' ),
				'classname'   => strtolower( __CLASS__ ),
			)
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		/**
		 * @var string $name
		 * @var string $id
		 * @var string $description
		 * @var string $class
		 * @var string $before_widget
		 * @var string $before_title
		 * @var string $widget_id
		 * @var string $widget_name
		 * @var string $after_widget
		 * @var string $after_title
		 */

		if ( empty( $instance['org'] ) ) {
			return;
		}

 		echo $before_widget;
 		echo $this->_get_view( $widget_id, $instance );
 		echo $after_widget;
	}

	/**
	 * Get view to render.
	 *
	 * @param  string $widget_id Widget ID
	 * @param  array  $instance  Widget instance
	 * @return string
	 */
	private function _get_view( $widget_id, array $instance ) {
		$plugin = $GLOBALS['gh_members_showoff'];

		$cache_key = $widget_id . '_members';
		$members   = $plugin->cache->get( $cache_key, $instance );

		ob_start();
		require apply_filters( 'gh_members_showoff_widget_view_path', $plugin->views_path . 'widget.php' );

		return ob_get_clean();
	}

	public function update( $new_instance, $old_instance ) {
		$plugin = $GLOBALS['gh_members_showoff'];

		$instance = $old_instance;

		if ( ! in_array( $new_instance['order_username'], array( '', 'asc', 'desc' ) ) ) {
			$instance['order_username'] = '';
		} else {
			$instance['order_username'] = $new_instance['order_username'];
		}
		$instance['limit']     = absint( $new_instance['limit'] );
		$instance['text_link'] = strip_tags( $new_instance['text_link'] );
		$instance['org']       = preg_replace( '/[^A-Za-z0-9_\-]/', '', $new_instance['org'] );

		$cache_key = $this->id . '_members';
		$plugin->cache->delete( $cache_key );

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( $instance, array(
			'org'            => '',
			'order_username' => '',
			'limit'          => 0,
			'text_link'      => '',
		) );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('org') ); ?>"><?php _e( 'GitHub organization slug', 'github-api' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('org') ); ?>" name="<?php echo esc_attr( $this->get_field_name('org') ); ?>" value="<?php echo esc_attr( $instance['org'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('order_username') ); ?>"><?php _e( 'Order username', 'github-api' ); ?></label>
			<br>
			<select id="<?php echo esc_attr( $this->get_field_id('order_username') ); ?>" name="<?php echo esc_attr( $this->get_field_name('order_username') ); ?>" value="<?php echo esc_attr( $instance['order_username'] ); ?>">
			<?php foreach ( array( '', 'asc', 'desc' ) as $opt ) : ?>
				<option value="<?php echo esc_attr( $opt ); ?>" <?php selected( ( $opt === $instance['order_username'] ) ); ?>><?php echo esc_html( $opt ); ?></option>
			<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('limit') ); ?>"><?php _e( 'Number of members to show', 'github-api' ); ?></label>
			<input size="3" type="text" id="<?php echo esc_attr( $this->get_field_id('limit') ); ?>" name="<?php echo esc_attr( $this->get_field_name('limit') ); ?>" value="<?php echo esc_attr( $instance['limit'] ); ?>">
			<br>
			<span class="description"><?php _e( 'Set 0 to show all members', 'github-api' ); ?></span>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('text_link') ); ?>"><?php _e( 'Text for link to GitHub org members', 'github-api' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('text_link') ); ?>" name="<?php echo esc_attr( $this->get_field_name('text_link') ); ?>" value="<?php echo esc_attr( $instance['text_link'] ); ?>">
		</p>
		<?php
	}
}
