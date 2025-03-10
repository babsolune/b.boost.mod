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
	public static function update_game_date(int $event_id, int $game_cluster, int $day_date)
	{
		self::$db_querier->update(ScmSetup::$scm_game_table, ['game_date' => $day_date], 'WHERE game_event_id = :event_id AND game_cluster = :game_cluster', ['event_id' => $event_id, 'game_cluster' => $game_cluster]);
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

    /** Get all games from an event */
	public static function get_games(int $event_id) : array
	{
		$results = self::$db_querier->select('SELECT *
            FROM ' . ScmSetup::$scm_game_table . '
            WHERE game_event_id = :event_id
            ORDER BY game_date, game_cluster ASC, game_order ASC', [
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
	public static function get_games_in_cluster(int $event_id, int $cluster) : array
	{
		$results = self::$db_querier->select('SELECT *
            FROM ' . ScmSetup::$scm_game_table . '
            WHERE game_event_id = :event_id
            AND (game_type = :group OR game_type = :day)
            ORDER BY game_date, game_cluster ASC, game_order ASC', [
                'event_id' => $event_id,
                'group' => 'G',
                'day' => 'D'
            ]
        );

        $games = [];
        while($row = $results->fetch())
        {
            if ($row['game_cluster'] == $cluster)
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
            ORDER BY games.game_cluster ASC, games.game_order ASC', [
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

    /** Get all games from a event */
	public static function get_last_team_games(int $event_id, int $team_id, int $day, int $limit) : array
	{
		$results = self::$db_querier->select('SELECT games.*, event.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            LEFT JOIN ' . ScmSetup::$scm_event_table . ' event ON event.id = games.game_event_id
            WHERE games.game_event_id = :event_id
            AND games.game_cluster <= :day
            AND (games.game_home_id = :team_id OR games.game_away_id = :team_id)
            ORDER BY games.game_cluster DESC
            LIMIT :limit', [
                'event_id' => $event_id,
                'team_id' => $team_id,
                'day' => $day,
                'limit' => $limit,
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
     * get a game based on game cluster ids
     * @param int $event_id the id of its event
     * @param string $type 'G' = cluster or 'D' = day | 'B' = bracket | 'P' = practice
     * @param int $cluster its cluster number
     * @param int $round its round number
     * @param int $order its order number
    */
	public static function get_game(int $event_id, string $type, int $cluster, int $round, int $order)
	{
        $event_games = [];
        foreach (self::get_games($event_id) as $game)
        {
            $event_games[] = $game['game_type'].$game['game_cluster'].$game['game_round'].$game['game_order'];
        }
        if (in_array($type.$cluster.$round.$order, $event_games))
        {
            $row = self::$db_querier->select_single_row_query('SELECT *
                FROM ' . ScmSetup::$scm_game_table . '
                WHERE game_event_id = :event_id
                AND game_type = :type
                AND game_cluster = :cluster
                AND game_round = :round
                AND game_order = :order', [
                    'event_id' => $event_id,
                    'type'     => $type,
                    'cluster'  => $cluster,
                    'round'    => $round,
                    'order'    => $order,
                ]
            );
            $game = new ScmGame();
            $game->set_properties($row);
            return $game;
        }
        else 
            return null;
	}

    // Check if game is live
    public static function is_live(int $event_id, int $game_id) : bool
	{
        $now = new Date();
        $game = ScmGameCache::load()->get_game($game_id);
        $params = ScmParamsService::get_params($event_id);
        $game_duration = $params->get_game_duration();
        $overtime_duration = $params->get_has_overtime() ? $params->get_overtime_duration() : 0;
        $full_duration = $game['game_type'] == 'B' ? $game_duration + $overtime_duration : $game_duration;

        if ($now->get_timestamp() > $game['game_date'] && $now->get_timestamp() < ($game['game_date'] + ($full_duration * 60)))
            return true;
		return false;
	}

    // Check current games
    public static function get_current_games():array
	{
        $running_events = ScmEventService::get_running_events_id();
        $events_id = $running_events ? implode(', ', $running_events) : 0;
        $now = new Date();
        $games = self::$db_querier->select('SELECT games.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            WHERE games.game_event_id IN (' . $events_id . ')
            ORDER BY games.game_date'
        );
        $current_games = [];
        foreach ($games as $game)
        {
            $today = Date::to_format($now->get_timestamp(), Date::FORMAT_DAY_MONTH_YEAR);
            $game_date = Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR);
            if ($today == $game_date)
                $current_games[] = $game;
        }
        return $current_games;
	}

    // Check current games
    public static function get_week_games():array
	{
        $running_events = ScmEventService::get_running_events_id();
        $events_id = $running_events ? implode(', ', $running_events) : 0;
        $now = new Date();
        // week
        $today = new DateTime();
        $start = clone $today;
        $start->modify('monday this week');
        $start->setTime(00, 00, 00);
        $end = clone $today;
        $end->modify('sunday this week');
        $end->setTime(23, 59, 59);
        $start_day = $start->getTimestamp();
        $end_day = $end->getTimestamp();
        // /week
        $games = self::$db_querier->select('SELECT games.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            WHERE games.game_event_id IN (' . $events_id . ')
            ORDER BY games.game_date'
        );
        $current_games = [];
        foreach ($games as $game)
        {
            if ($start_day <= $game['game_date'] && $game['game_date'] <= $end_day)
                $current_games[] = $game;
        }
        return $current_games;
	}

    // Check current games
    public static function get_before_current_games():array
	{
        $running_events = ScmEventService::get_running_events_id();
        $events_id = $running_events ? implode(', ', $running_events) : 0;
        $now = new Date();
        $yesterday = $now->get_timestamp() - 86400;
        $games = self::$db_querier->select('SELECT games.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            WHERE games.game_event_id IN (' . $events_id . ')
            ORDER BY games.game_date', [
                'now' => $now->get_timestamp()
            ]
        );
        $current_games = [];
        foreach ($games as $game)
        {
            $day = Date::to_format($yesterday, Date::FORMAT_DAY_MONTH_YEAR);
            $game_date = Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR);
            if ($day == $game_date)
                $current_games[] = $game;
        }
        return $current_games;
	}

    public static function has_details($event_id, $type, $cluster, $round, $order)
    {
        $game = self::get_game($event_id, $type, $cluster, $round, $order);
        $has_details = false;
        if(
            $game->get_game_home_pen() || $game->get_game_away_pen()
            || $game->get_game_home_goals() || $game->get_game_away_goals()
            || $game->get_game_home_yellow() || $game->get_game_away_yellow()
            || $game->get_game_home_red() || $game->get_game_away_red()
            || $game->get_game_video()->rel()
        )
            $has_details = true;

        return $has_details;
    }
}
?>
