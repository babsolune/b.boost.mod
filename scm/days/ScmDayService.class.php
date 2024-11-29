<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmDayService
{
	private static $db_querier;
	protected static $module_id = 'scm';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/** Create a new entry in the database day table */
	public static function add_day(ScmDay $day)
	{
		$result = self::$db_querier->insert(ScmSetup::$scm_day_table, $day->get_properties());

		return $result->get_last_inserted_id();
	}

	/** Update a day entry */
	public static function update_day(ScmDay $day, int $id)
	{
		self::$db_querier->update(ScmSetup::$scm_day_table, $day->get_properties(), 'WHERE id_day = :id', ['id' => $id]);
	}

	/** Delete all day entries of a event */
	public static function delete_days(int $event_id)
	{
        $days = self::get_days($event_id);
        foreach ($days as $day)
        {
            self::$db_querier->delete(ScmSetup::$scm_day_table, 'WHERE day_event_id = :event_id', ['event_id' => $event_id]);
        }
    }

	public static function get_day(int $event_id, int $day_round)
	{
        $event_days = [];
        foreach (self::get_days($event_id) as $day)
        {
            $event_days[] = $day['day_round'];
        }

        if (in_array($day_round, $event_days))
        {
            $row = self::$db_querier->select_single_row_query('SELECT days.*
                FROM ' . ScmSetup::$scm_day_table . ' days
                WHERE days.day_event_id = :event_id
                AND days.day_round = :day_round', [
                    'event_id' => $event_id,
                    'day_round' => $day_round,
                ]
            );
            $day = new ScmDay();
            $day->set_properties($row);
            return $day;
        }
        else 
            return null;
	}

    /** Get all days from a event */
	public static function get_days(int $event_id)
	{
		$results = self::$db_querier->select('SELECT *
            FROM ' . ScmSetup::$scm_day_table . '
            WHERE day_event_id = :event_id
            ORDER BY id_day', [
                'event_id' => $event_id
            ]
        );

        $days = [];
        while($row = $results->fetch())
        {
            $days[] = $row;
        }
        return $days;
	}

    public static function set_days_games(int $event_id)
    {
        $now = new Date();
        $c_return_games = ScmEventService::get_event_game_type($event_id) == ScmDivision::RETURN_GAMES;
        $teams_number = ScmTeamService::get_teams_number($event_id);

        $games_number = $c_return_games ? ($teams_number - 1) * 2 : ($teams_number - 1);

        for ($i = $games_number; $i >= 1; $i--)
        {
            for ($j = 1; $j <= $teams_number / 2; $j++)
            {
                self::$db_querier->insert(ScmSetup::$scm_game_table, [
                    'game_event_id' => $event_id,
                    'game_type' => 'D',
                    'game_group' => $i,
                    'game_round' => 0,
                    'game_order' => $j,
                    'game_home_id' => 0,
                    'game_away_id' => 0,
                    'game_date' => $now->get_timestamp()
                ]);
            }
        }
    }

    public static function day_has_scores(array $games):bool
    {
        return (bool)array_filter($games, function($score) {
            if ($score == '0') $score = 1;
            return !empty($score);
        });
    }

    public static function update_day_played(int $event_id, int $day_round, int $check)
    {
		self::$db_querier->update(ScmSetup::$scm_day_table, ['day_played' => $check], 'WHERE day_event_id = :event_id AND day_round = :day_round', ['event_id' => $event_id, 'day_round' => $day_round]);
    }

    public static function get_last_day(int $event_id)
    {
        $days = self::get_days($event_id);
        $ids = [];
        foreach($days as $day)
        {
            if($day['day_played'])
                $ids[] = $day['day_round'];
        }

        return !empty(end($ids)) ? end($ids) : 0;
    }

    public static function get_next_day(int $event_id)
    {
        $days = self::get_days($event_id);
        $ids = [];
        foreach($days as $day)
        {
            if(!$day['day_played'])
                $ids[] = $day['day_round'];
        }
        return $ids ? $ids[0] : 0;
    }
}