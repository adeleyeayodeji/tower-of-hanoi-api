<?php

/**
 * File Description:
 * API class
 *
 * @link    https://adeleyeayodeji.com/
 * @since   1.0.0
 *
 * @author  Adeleye Ayodeji (https://adeleyeayodeji.com)
 * @package Tower_Of_Hanoi_Api
 */

namespace Tower_Of_Hanoi_Api\App\Api;

use Tower_Of_Hanoi_Api\Base;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class Api extends Base
{
	/**
	 * Initialize the API.
	 *
	 * @since 1.0.0
	 */
	public static function init()
	{
		// Register routes when the REST API is initialized
		add_action('rest_api_init', array(__CLASS__, 'register_routes'));
	}

	/**
	 * Register routes.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register_routes()
	{
		// Register routes for the game state
		register_rest_route('toh/v1', '/state', array(
			'methods' => 'GET',
			'callback' => array(__CLASS__, 'get_state'),
			'permission_callback' => array(__CLASS__, 'check_permission')
		));

		// Register routes for moving a disk
		register_rest_route('toh/v1', '/move/(?P<from>\d+)/(?P<to>\d+)', array(
			'methods' => 'POST',
			'callback' => array(__CLASS__, 'move_disk'),
			'args' => [
				'from' => [
					'validate_callback' => array(__CLASS__, 'validate_peg'),
					'required' => true
				],
				'to' => [
					'validate_callback' => array(__CLASS__, 'validate_peg'),
					'required' => true
				]
			],
			'permission_callback' => array(__CLASS__, 'check_permission')
		));

		// Register routes for resetting the game
		register_rest_route('toh/v1', '/reset', array(
			'methods' => 'POST',
			'callback' => array(__CLASS__, 'reset_game'),
			'permission_callback' => array(__CLASS__, 'check_permission')
		));
	}

	/**
	 * Check if the user has permission to access the API
	 * @return bool
	 */
	static function check_permission(): bool
	{
		return true;
	}

	/**
	 * Get the current state of the game.
	 *
	 * @return WP_REST_Response
	 * @since 1.0.0
	 */
	static function get_state(): WP_REST_Response
	{
		$state = self::get_game_state();
		return new WP_REST_Response($state);
	}

	/**
	 * Retrieve game state from transient or initialize if not set.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	static function get_game_state(): array
	{
		$state = get_transient(
			transient: 'toh_game_state'
		);
		if (!$state) {
			$state = self::initialize_game();
			//set the transient to the game state
			set_transient(
				transient: 'toh_game_state',
				value: $state,
				expiration: HOUR_IN_SECONDS
			);
		}
		return $state;
	}

	/**
	 * Initialize the game state.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	static function initialize_game(): array
	{
		return [
			'pegs' => [
				[7, 6, 5, 4, 3, 2, 1], // Peg 1 with all 7 disks
				[], // Peg 2 empty
				[]  // Peg 3 empty
			],
			'completed' => false,
			'moves' => 0 // Track number of moves
		];
	}

	/**
	 * Move a disk between pegs and track the number of moves
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response|WP_Error
	 */
	static function move_disk(WP_REST_Request $request): WP_REST_Response|WP_Error
	{
		try {
			$from = (int) $request->get_param('from');
			$to = (int) $request->get_param('to');

			$state = self::get_game_state();

			if ($state['completed']) {
				return new WP_Error('game_over', 'The game is already finished.', array('status' => 400));
			}

			if (empty($state['pegs'][$from])) {
				return new WP_Error('invalid_move', 'Invalid move. No disks on the selected peg.', array('status' => 400));
			}

			// Validate move: No larger disk on smaller disk
			if (!empty($state['pegs'][$to]) && end($state['pegs'][$from]) > end($state['pegs'][$to])) {
				return new WP_Error('invalid_move', 'Invalid move. Cannot place a larger disk on a smaller one.', array('status' => 400));
			}

			// Perform the disk move
			$disk = array_pop($state['pegs'][$from]);
			$state['pegs'][$to][] = $disk;

			// Increment the move counter
			$state['moves']++;

			// Check if the game is completed (all disks on peg 3)
			if (count($state['pegs'][2]) === 7) {
				$state['completed'] = true;
			}

			// Save the new state
			set_transient(
				transient: 'toh_game_state',
				value: $state,
				expiration: HOUR_IN_SECONDS
			);

			return new WP_REST_Response($state);
		} catch (\Exception $e) {
			return new WP_Error('move_disk_error', $e->getMessage(), array('status' => 500));
		}
	}

	/**
	 * Validate that 'from' and 'to' parameters are within 0-2 (valid pegs)
	 * @param $param
	 * @param $request
	 * @param $key
	 * @return bool
	 */
	static function validate_peg($param, $request, $key): bool
	{
		//sanitize the input to an integer
		$param = absint(
			maybeint: $param
		);
		return in_array($param, [0, 1, 2], true);
	}

	/**
	 * Reset the game state to start over
	 * @return WP_REST_Response
	 */
	static function reset_game(): WP_REST_Response
	{
		$state = self::initialize_game();
		//set the transient to the game state
		set_transient(
			transient: 'toh_game_state',
			value: $state,
			expiration: HOUR_IN_SECONDS
		);
		return new WP_REST_Response($state);
	}
}
