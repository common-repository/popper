<?php
/**
 * This file handles the Display Rule conditions for Popper blocks.
 *
 * @package Popper
 */

namespace Popper;

/**
 * The conditions class.
 */
class Conditions {
	/**
	 * Instance.
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Output our available location conditions.
	 *
	 * @since 1.7
	 *
	 * @return array
	 */
	public static function get_conditions() {
		$types = array(
			'general' => array(
				'label' => esc_attr__( 'General', 'popper' ),
				'locations' => array(
					'general:site'       => esc_attr__( 'Entire Site', 'popper' ),
					'general:front_page' => esc_attr__( 'Front Page', 'popper' ),
					'general:blog'       => esc_attr__( 'Blog', 'popper' ),
					'general:singular'   => esc_attr__( 'All Singular', 'popper' ),
					'general:archive'    => esc_attr__( 'All Archives', 'popper' ),
					'general:author'     => esc_attr__( 'Author Archives', 'popper' ),
					'general:date'       => esc_attr__( 'Date Archives', 'popper' ),
					'general:search'     => esc_attr__( 'Search Results', 'popper' ),
					'general:no_results' => esc_attr__( 'No Search Results', 'popper' ),
					'general:404'        => esc_attr__( '404 Template', 'popper' ),
				),
			),
		);

		// Add the post types.
		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		foreach ( $post_types as $post_type_slug => $post_type ) {

			if ( in_array( $post_type_slug, array( 'fl-theme-layout' ) ) ) {
				continue;
			}

			$post_type_object = get_post_type_object( $post_type_slug );
			$counts = wp_count_posts( $post_type_slug );
			$count = $counts->publish + $counts->future + $counts->draft + $counts->pending + $counts->private;

			// Add the post type.
			$types[ $post_type_slug ] = array(
				'label'   => esc_html( $post_type->labels->name ),
				'locations' => array(
					'post:' . $post_type_slug => esc_html( $post_type->labels->singular_name ),
				),
			);

			// Add the post type archive.
			if ( 'post' === $post_type_slug || ! empty( $post_type_object->has_archive ) ) {
				$types[ $post_type_slug . '_archive' ] = array(
					/* translators: post type name */
					'label' => sprintf( esc_html_x( '%s Archives', '%s is a singular post type name', 'popper' ), $post_type->labels->singular_name ),
					'locations' => array(
						/* translators: post type name */
						'archive:' . $post_type_slug => sprintf( esc_html_x( '%s Archive', '%s is a singular post type name', 'popper' ), $post_type->labels->singular_name ),
					),
				);
			}

			// Add the taxonomies for the post type.
			$taxonomies = get_object_taxonomies( $post_type_slug, 'objects' );

			foreach ( $taxonomies as $taxonomy_slug => $taxonomy ) {

				$public = $taxonomy->public && $taxonomy->show_ui;

				if ( 'post_format' === $taxonomy_slug ) {
					continue;
				}

				$label = str_replace(
					array(
						$post_type->labels->name,
						$post_type->labels->singular_name,
					),
					'',
					$taxonomy->labels->singular_name
				);

				if ( isset( $types[ $post_type_slug . '_archive' ]['locations'] ) ) {
					/* translators: '%1$s is post type label. %2$s is taxonomy label. */
					$types[ $post_type_slug . '_archive' ]['locations'][ 'taxonomy:' . $taxonomy_slug ] = sprintf( esc_html_x( '%1$s %2$s Archive', '%1$s is post type label. %2$s is taxonomy label.', 'popper' ), $post_type->labels->singular_name, $label );
				}

				if ( isset( $types[ $post_type_slug ]['locations'] ) ) {
					$types[ $post_type_slug ]['locations'][ $post_type_slug . ':taxonomy:' . $taxonomy_slug ] = esc_html( $post_type->labels->singular_name . ' ' . $label );
				}
			}
		}

		return $types;
	}

	/**
	 * Output our available user conditions.
	 *
	 * @since 1.7
	 *
	 * @return array
	 */
	public static function get_user_conditions() {
		$rules = array(
			'general' => array(
				'label' => esc_attr__( 'General', 'popper' ),
				'rules' => array(
					'general:all'        => esc_attr__( 'All Users', 'popper' ),
					'general:logged_in'  => esc_attr__( 'Logged In', 'popper' ),
					'general:logged_out' => esc_attr__( 'Logged Out', 'popper' ),
				),
			),
			'role' => array(
				'label' => esc_attr__( 'Roles', 'popper' ),
				'rules' => array(),
			),
		);

		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . '/wp-admin/includes/user.php';
		}

		$roles = \get_editable_roles();

		foreach ( $roles as $slug => $data ) {
			$rules['role']['rules'][ $slug ] = $data['name'];
		}

		return $rules;
	}

	/**
	 * Get our current location.
	 *
	 * @since 1.7
	 *
	 * @return array
	 */
	public static function get_current_location() {
		global $wp_query;
		global $post;

		$location       = null;
		$object         = null;
		$queried_object = get_queried_object();

		// Get the location string.
		if ( is_front_page() ) {
			$location = 'general:front_page';
		} elseif ( is_home() ) {
			$location = 'general:blog';
		} elseif ( is_author() ) {
			$location = 'general:author';
		} elseif ( is_date() ) {
			$location = 'general:date';
		} elseif ( is_search() ) {
			$location = 'general:search';

			global $wp_query;

			if ( 0 === $wp_query->found_posts ) {
				$location = 'general:no_results';
			}
		} elseif ( is_404() ) {
			$location = 'general:404';
		} elseif ( is_category() ) {

			$location = 'taxonomy:category';

			if ( is_object( $queried_object ) ) {
				$object = $queried_object->term_id;
			}
		} elseif ( is_tag() ) {

			$location = 'taxonomy:post_tag';

			if ( is_object( $queried_object ) ) {
				$object = $queried_object->term_id;
			}
		} elseif ( is_tax() ) {

			$location = 'taxonomy:' . get_query_var( 'taxonomy' );

			if ( is_object( $queried_object ) ) {
				$location = 'taxonomy:' . $queried_object->taxonomy;
				$object = $queried_object->term_id;
			}
		} elseif ( is_post_type_archive() ) {
			$location = 'archive:' . $wp_query->get( 'post_type' );
		} elseif ( is_singular() ) {

			if ( is_object( $post ) ) {
				$location = 'post:' . $post->post_type;
			}

			if ( is_object( $queried_object ) ) {
				$object = $queried_object->ID;
			}
		}

		if ( is_admin() && function_exists( 'get_current_screen' ) ) {
			$current_screen = get_current_screen();

			if ( isset( $current_screen->is_block_editor ) && $current_screen->is_block_editor ) {
				$post_id = false;

				if ( isset( $_GET['post'] ) ) { // phpcs:ignore -- Just checking if it's set.
					$post_id = absint( $_GET['post'] ); // phpcs:ignore -- No data processing going on.
				}

				if ( $post_id ) {

					// Get the location string.
					$front_page_id = get_option( 'page_on_front' );
					$blog_id       = get_option( 'page_for_posts' );

					if ( (int) $post_id === (int) $front_page_id ) {
						$location = 'general:front_page';
					} elseif ( (int) $post_id === (int) $blog_id ) {
						$location = 'general:blog';
					} else {
						if ( isset( $current_screen->post_type ) ) {
							$location = 'post:' . $current_screen->post_type;
						}

						$object = $post_id;
					}
				}
			}
		}

		return array(
			'rule' => $location,
			'object'   => $object,
		);
	}

	/**
	 * Get info on the current user.
	 *
	 * @since 1.7
	 *
	 * @return array
	 */
	public static function get_current_user() {
		$status = array();
		if ( is_user_logged_in() ) {
			$status[] = 'general:logged_in';
		} else {
			$status[] = 'general:logged_out';
		}

		$user = wp_get_current_user();

		foreach ( (array) $user->roles as $role ) {
			$status[] = $role;
		}

		return $status;
	}

	/**
	 * Figure out if we should display the element or not.
	 *
	 * @since 1.7
	 *
	 * @param array $conditionals The conditions.
	 * @param array $exclude The exclusions.
	 * @param array $roles The roles.
	 * @param array $dates The dates.
	 * @param array $running The running status.
	 * @return bool
	 */
	public static function show_data( $conditionals, $exclude, $roles, $dates, $running ) {
		$current_location = self::get_current_location();
		$show = false;

		if ( empty( $running ) ) {
			return false;
		}

		// Show depending on location conditionals.
		if ( ! $show ) {
			foreach ( (array) $conditionals as $conditional ) {
				if ( in_array( 'general:site', $conditional ) ) {
					$show = true;
				} elseif ( is_singular() && in_array( 'general:singular', $conditional ) ) {
					$show = true;
				} elseif ( is_archive() && in_array( 'general:archive', $conditional ) ) {
					$show = true;
				} elseif ( ! empty( $current_location['rule'] ) && in_array( $current_location['rule'], $conditional ) ) {
					if ( ! isset( $conditional['object'] ) || empty( $conditional['object'] ) ) {
						$show = true;
					} elseif ( in_array( $current_location['object'], $conditional['object'] ) ) {
							$show = true;
					}
				} elseif ( is_singular() && strstr( $conditional['rule'], ':taxonomy:' ) ) {
					$tax = substr( $conditional['rule'], strrpos( $conditional['rule'], ':' ) + 1 );

					if ( $tax && isset( $conditional['object'] ) && has_term( $conditional['object'], $tax ) ) {
						$show = true;
					}
				} elseif ( is_front_page() && is_home() && ( in_array( 'general:blog', $conditional ) || in_array( 'general:front_page', $conditional ) ) ) {
					// If the home page is the blog, both of general:blog and general:front_page apply.
					$show = true;
				}
			}
		}

		// Exclude based on exclusion conditionals.
		if ( $show ) {
			foreach ( (array) $exclude as $conditional ) {
				if ( is_singular() && in_array( 'general:singular', $conditional ) ) {
					$show = false;
				} elseif ( is_archive() && in_array( 'general:archive', $conditional ) ) {
					$show = false;
				} elseif ( ! empty( $current_location['rule'] ) && in_array( $current_location['rule'], $conditional ) ) {
					if ( ! isset( $conditional['object'] ) || empty( $conditional['object'] ) ) {
						$show = false;
					} elseif ( in_array( $current_location['object'], $conditional['object'] ) ) {
						$show = false;
					}
				} elseif ( is_singular() && strstr( $conditional['rule'], ':taxonomy:' ) ) {
					$tax = substr( $conditional['rule'], strrpos( $conditional['rule'], ':' ) + 1 );

					if ( $tax && isset( $conditional['object'] ) && has_term( $conditional['object'], $tax ) ) {
						$show = false;
					}
				} elseif ( is_front_page() && is_home() && ( in_array( 'general:blog', $conditional ) || in_array( 'general:front_page', $conditional ) ) ) {
					// If the home page is the blog, both of general:blog and general:front_page apply.
					$show = false;
				}
			}
		}

		// Exclude user roles.
		if ( $show && ! empty( $roles ) ) {
			$user_info = self::get_current_user();

			$check = array_intersect( $roles, $user_info );
			if ( ! count( $check ) > 0 && ! in_array( 'general:all', $roles ) ) {
				$show = false;
			}
		}

		// Exclude dates.
		if ( $show && ! empty( $dates ) ) {
			$type = $dates['type'];
			$startDate = strtotime( $dates['startDate'] );
			$endDate = strtotime( $dates['endDate'] );
			if ( $startDate > time() ) {
				$show = false;
			}
			if ( $endDate < time() ) {
				$show = false;
			}
			if ( 'evergreen' === $type ) {
				$show = true;
			}

			// Now check further for time/day.
			if ( ! empty( $dates['customDays'] ) ) {
				// reset the evergreen.
				$show = false;
				$day = gmdate( 'l' );
				$show = in_array( $day, array_keys( $dates['days'] ) );
				if ( $show ) {
					$show = self::is_between(
						$dates['days'][ $day ]['startTime'],
						$dates['days'][ $day ]['endTime']
					);
				}
			}
			if ( ! empty( $dates['customTime'] ) ) {
				$show = self::is_between( $dates['startTime'], $dates['endTime'] );
			}
		}

		return apply_filters( 'popper_show_popup', $show );
	}

	/**
	 * Returns the label for a saved location.
	 *
	 * @since 1.7
	 * @param string $saved_location The location.
	 * @return string|bool
	 */
	public static function get_saved_label( $saved_location ) {
		$locations = self::get_conditions();

		$rule = $saved_location['rule'];
		$object_id = $saved_location['object'];
		$object_type = '';
		$label = false;

		foreach ( $locations as $data ) {
			if ( isset( $data['locations'][ $rule ] ) && ! $label ) {
				$label = $data['locations'][ $rule ];

				$object_types = explode( ':', $rule );

				if ( in_array( 'taxonomy', $object_types ) && $object_id ) {
					$term = get_term( $object_id );

					if ( ! is_object( $term ) || is_wp_error( $term ) ) {
						return false;
					}

					$label .= ': ' . $term->name;
				} elseif ( ( in_array( 'post', $object_types ) || in_array( 'page', $object_types ) ) && $object_id ) {
					$posts = get_posts(
						array(
							'include' => $object_id,
							'post_type' => $object_types,
						)
					);

					if ( ! is_array( $posts ) ) {
						return false;
					}
					$label .= ': ' . implode( ', ', array_column( $posts, 'post_title' ) );
				}
			}
		}

		return empty( $label ) ? '' : $label;
	}

	/**
	 * Returns the label for a saved location.
	 *
	 * @since 1.7
	 * @param array $saved_user The users.
	 * @return string|bool
	 */
	public static function get_user_label( $saved_user ) {
		$users = self::get_user_conditions();

		$label = false;

		foreach ( $saved_user as $data ) {

			$object_types = explode( ':', $data );

			if ( in_array( 'general', $object_types ) ) {
				$label .= $users['general']['rules'][ $data ];
			} else {
				$label .= $users['role']['rules'][ $data ];
			}
			$label .= '<br />';
		}
		return $label;
	}

	/**
	 * Is between two dates
	 *
	 * @param int $from Date from.
	 * @param int $till Date till.
	 * @return boolean
	 */
	private static function is_between( $from, $till ) {
		$fromTime = strtotime( $from );
		$toTime = strtotime( $till );
		$timeZone = wp_timezone_string();
		$inputTime = strtotime( wp_date( 'H:i' ) );

		return ( $inputTime >= $fromTime && $inputTime <= $toTime );
	}
}
Conditions::get_instance();
