<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmGameService
{
	private static $db_querier;
	protected static $module_id = 'scm';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/** Create a new entry in the database game table */
	public static function add_game(ScmGame $game)
	{
		$result = self::$db_querier->insert(ScmSetup::$scm_game_table, $game->get_properties());

		return $result->get_last_inserted_id();
	}

	/** Update a game entry */
	public static function update_game(ScmGame $game, int $id)
	{
		self::$db_querier->update(ScmSetup::$scm_game_table, $game->get_properties(), 'WHERE id_game = :id', ['id' => $id]);
	}

	/** Update a game date */
	public static function update_game_date(int $event_id, int $game_group, int $day_date)
	{
		self::$db_querier->update(ScmSetup::$scm_game_table, ['game_date' => $day_date], 'WHERE game_event_id = :event_id AND game_group = :game_group', ['event_id' => $event_id, 'game_group' => $game_group]);
	}

	/** Delete all game entries of a event */
	public static function delete_games(int $event_id)
	{
        $games = self::get_games($event_id);
        foreach ($games as $game)
        {
            self::$db_querier->delete(ScmSetup::$scm_game_table, 'WHERE game_event_id = :event_id', ['event_id' => $event_id]);
        }
    }

    /** Get all games from a event */
	public static function get_games(int $event_id) : array
	{
		$results = self::$db_querier->select('SELECT games.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            WHERE games.game_event_id = :event_id
            ORDER BY games.game_group ASC, games.game_order ASC', [
                'event_id' => $event_id
            ]
        );

        $games = [];
        while($row = $results->fetch())
        {
            $games[] = $row;
        }
        return $games;
	}

    /** Get all games from a day in event */
	public static function get_games_in_day(int $event_id, int $day_round) : array
	{
		$results = self::$db_querier->select('SELECT games.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            WHERE games.game_event_id = :event_id
            ORDER BY games.game_group ASC, games.game_order ASC', [
                'event_id' => $event_id
            ]
        );

        $games = [];
        while($row = $results->fetch())
        {
            if ($row['game_group'] == $day_round)
            $games[] = $row;
        }
        return $games;
	}

    /** Get all games from a event */
	public static function get_team_games(int $event_id, int $team_id) : array
	{
		$results = self::$db_querier->select('SELECT games.*, event.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            LEFT JOIN ' . ScmSetup::$scm_event_table . ' event ON event.id = games.game_event_id
            WHERE games.game_event_id = :event_id
            AND (games.game_home_id = :team_id OR games.game_away_id = :team_id)
            ORDER BY games.game_group ASC, games.game_order ASC', [
                'event_id' => $event_id,
                'team_id' => $team_id
            ]
        );

        $games = [];
        while($row = $results->fetch())
        {
            $games[] = $row;
        }
        return $games;
	}

    /** check if a event has declared games */
    public static function has_games(int $event_id) : bool
    {
        return count(self::get_games($event_id)) > 0;
    }

    /** 
     * get a game based on game group ids
     * @param int $event_id the id of its event
     * @param string $type its type of group 'G' = group or 'D' = day | 'W' or 'L' = bracket
     * @param int $group its group number
     * @param int $order its order number
    */
	public static function get_game(int $event_id, string $type, int $group, int $order)
	{
        $event_games = [];
        foreach (self::get_games($event_id) as $game)
        {
            $event_games[] = $game['game_type'].$game['game_group'].$game['game_order'];
        }

        if (in_array($type.$group.$order, $event_games))
        {
            $row = self::$db_querier->select_single_row_query('SELECT games.*
                FROM ' . ScmSetup::$scm_game_table . ' games
                WHERE games.game_event_id = :event_id
                AND games.game_type = :type
                AND games.game_group = :group
                AND games.game_order = :order', [
                    'event_id' => $event_id,
                    'type' => $type,
                    'group' => $group,
                    'order' => $order,
                ]
            );
            $game = new ScmGame();
            $game->set_properties($row);
            return $game;
        }
        else 
            return null;
	}

    /** Check if all games date are on the same day */
    public static function one_day_event(int $event_id) : bool
    {
        if (self::has_games($event_id))
        {
            $cache = ScmEventCache::load()->get_event($event_id);
            $start_day = Date::to_format($cache['start_date'], Date::FORMAT_DAY_MONTH_YEAR);
            $end_day = Date::to_format($cache['end_date'], Date::FORMAT_DAY_MONTH_YEAR);

            return $start_day == $end_day;
        }
        return false;
    }

    // Check if game is live
    public static function is_live(int $event_id, int $game_id) : bool
	{
        $now = new Date();
        $game = ScmGameCache::load()->get_game($game_id);
        $game_duration = ScmParamsService::get_params($event_id)->get_game_duration();
        $overtime_duration = ScmParamsService::get_params($event_id)->get_overtime_duration();
        $full_duration = $game['game_type'] == 'G' || $game['game_type'] == 'D' ? $game_duration : $game_duration + $overtime_duration;

        if ($now->get_timestamp() > $game['game_date'] && $now->get_timestamp() < ($game['game_date'] + ($full_duration * 60)))
            return true;
		return false;
	}

    // Check current games
    public static function get_current_games():array
	{
        $now = new Date();
        $games = ScmGameCache::load()->get_games();
        usort($games, function($a, $b) {
            return strcmp($a["game_date"], $b["game_date"]);
        });
        $current_games = [];
        foreach ($games as $game)
        {
            $game_duration = ScmParamsService::get_params($game['game_event_id'])->get_game_duration();
            $overtime_duration = ScmParamsService::get_params($game['game_event_id'])->get_overtime_duration();
            $full_duration = $game['game_type'] == 'G' || $game['game_type'] == 'D' ? $game_duration : $game_duration + $overtime_duration;

            if ($game['game_date'] < $now->get_timestamp() && $now->get_timestamp() < ($game['game_date'] + ($full_duration * 60)))
                $current_games[] = $game;
        }
        return $current_games;
	}

    // Check current games
    public static function get_event_current_games(int $event_id):array
	{
        $now = new Date();
        $games = ScmGameCache::load()->get_games();
        usort($games, function($a, $b) {
            return strcmp($a["game_date"], $b["game_date"]);
        });
        $current_games = [];
        foreach ($games as $game)
        {
            $game_duration = ScmParamsService::get_params($game['game_event_id'])->get_game_duration();
            $overtime_duration = ScmParamsService::get_params($game['game_event_id'])->get_overtime_duration();
            $full_duration = $game['game_type'] == 'G' || $game['game_type'] == 'D' ? $game_duration : $game_duration + $overtime_duration;

            if (
                $game['game_event_id'] == $event_id
                && $game['game_date'] < $now->get_timestamp()
                && $now->get_timestamp() < ($game['game_date'] + ($full_duration * 60))
            )
                $current_games[] = $game;
        }
        return $current_games;
	}

    // Check current games
    public static function get_next_games()
	{
        $now = new Date();
        $full_games = ScmGameCache::load()->get_games();
        usort($full_games, function($a, $b) {
            return $a['game_date'] - $b['game_date'];
        });
        $games = [];
        foreach ($full_games as $game)
        {
            if ($now->get_timestamp() < $game['game_date'])
                $games[] = $game;
        }
        $next_games = array_slice($games, 0, ScmConfig::load()->get_next_games_number());
        return $next_games;
	}

    // Check current games
    public static function get_event_next_game():ScmGame
	{
        $now = new Date();
        $full_games = ScmGameCache::load()->get_games();
        usort($full_games, function($a, $b) {
            return $a['game_date'] - $b['game_date'];
        });
        $games = [];
        foreach ($full_games as $game)
        {
            if ($now->get_timestamp() < $game['game_date'])
                $games[] = $game;
        }
        return $games[0];
	}
}
?>
