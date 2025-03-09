<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 08 10
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmPracticeService
{
	private static $db_querier;
	protected static $module_id = 'scm';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

    // Add games
    public static function set_practice_games($event_id, $games_number)
    {
        $c_return_games = ScmEventService::get_event($event_id)->get_event_game_type() == ScmEvent::RETURN_GAMES;
        $games_number = $c_return_games ? $games_number * 2 : $games_number;

        for ($i = 1; $i <= $games_number; $i++)
        {
            self::$db_querier->insert(ScmSetup::$scm_game_table, [
                'game_event_id' => $event_id,
                'game_type'     => 'P',
                'game_cluster'  => 0,
                'game_round'    => 0,
                'game_order'    => $i,
                'game_home_id'  => 0,
                'game_away_id'  => 0,
                'game_date'     => ScmEventService::get_event($event_id)->get_start_date()->get_timestamp()
            ]);
        }
    }
}