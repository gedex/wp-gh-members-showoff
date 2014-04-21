<?php
/**
 * Cache wrapper.
 */
class GH_Members_Showoff_Cache {

	/**
	 * Plugin instance.
	 *
	 * @var GH_Profile_Widget_Plugin
	 */
	private $plugin;

	public function __construct( GH_Members_Showoff_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * @param string $id
	 * @param mixed  $value
	 */
	public function set( $id, $value ) {
		wp_cache_set( $id, $value );
	}

	/**
	 * @param string $id     Widget ID, Post ID where shortcode is being used, or timestamp
	 * @param array  $params Params to be passed to make a request
	 *
	 * @return mixed
	 */
	public function get( $id, array $params = array() ) {
		$members = wp_cache_get( $id );

		$is_valid = (
			! empty( $members['timestamp'] )
			&&
			( time() - absint( $members['timestamp'] ) < ( 60 * 60 * 24 ) ) // Makes sure data is fresh enough.
		);

		if ( ! $is_valid ) {
			try {
				$org = $params['org'];
				if ( empty( $org ) ) {
					throw new Exception( __( 'Empty GitHub org', 'github-api' ) );
				}

				$data = $this->request(
					'orgs/' . $org . '/members',
					array( 'per_page' => 100 )
				);

				$members = array(
					'timestamp' => time(),
					'data'      => $data,
					'count'     => count( $data ),
				);

				if ( ! empty( $params['order_username'] ) ) {
					$order_username = strtolower( $params['order_username'] );
					if ( ! in_array( $order_username, array( 'asc', 'desc' ) ) ) {
						$order_username = 'asc';
					}

					$sorter = function( $order_username ) {
						$multiplier = ('asc' === $order_username) ? 1 : -1;
						return function( $a, $b ) use( $multiplier ) {
							return $multiplier * strcasecmp( $a['login'], $b['login'] );
						};
					};

					usort( $members['data'], $sorter( $order_username ) );
				}

				if ( ! empty( $params['limit'] ) && $params['limit'] > 0 ) {
					$members['data'] = array_slice( $members['data'], 0, $params['limit'] );
				}

				if ( ! empty( $params['org_info'] ) ) {
					$members['org_info'] = $this->request( 'orgs/' . $org );
				}

				$this->set( $id, $members );

			} catch ( Exception $e ) {
				// @todo store and then show error message maybe?
				$this->delete( $id );
			}
		}

		if ( ! $members ) {
			return null;
		}

		return $members;
	}

	/**
	 * @param string $endpoint
	 * @param array  $params
	 *
	 * @return mixed
	 */
	public function request( $endpoint, array $params = array() ) {
		$resp     = $this->plugin->api->client->request( 'GET', $endpoint, $params );
		$status   = intval( wp_remote_retrieve_response_code( $resp ) );
		$result   = json_decode( wp_remote_retrieve_body( $resp ), true );

		while ( 200 === $status && ! empty( $resp['pagination'] ) ) {
			if ( empty( $resp['pagination']['next'] ) ) {
				break;
			}

			$params['page'] = $resp['pagination']['next'];
			$resp           = $this->plugin->api->client->request( 'GET', $endpoint, $params );
			$status         = intval( wp_remote_retrieve_response_code( $resp ) );
			$arr            = json_decode( wp_remote_retrieve_body( $resp ), true );

			if ( ! empty( $arr ) ) {
				$result = array_merge( $result, $arr );
			} else {
				break;
			}
		}

		return $result;
	}

	/**
	 * @param string $id
	 */
	public function delete( $id ) {
		wp_cache_delete( $id );
	}
}
