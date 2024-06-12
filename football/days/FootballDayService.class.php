<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballDayService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/** Create a new entry in the database day table */
	public static function add_day(FootballDay $day)
	{
		$result = self::$db_querier->insert(FootballSetup::$football_day_table, $day->get_properties());

		return $result->get_last_inserted_id();
	}

	/** Update a day entry */
	public static function update_day(FootballDay $day, int $id)
	{
		self::$db_querier->update(FootballSetup::$football_day_table, $day->get_properties(), 'WHERE id_day = :id', array('id' => $id));
	}

	/** Delete all day entries of a competition */
	public static function delete_days(int $compet_id)
	{
        $days = self::get_days($compet_id);
        foreach ($days as $day)
        {
            self::$db_querier->delete(FootballSetup::$football_day_table, 'WHERE day_compet_id = :compet_id', array('compet_id' => $compet_id));
        }
    }

	public static function get_day(int $compet_id, int $day_round)
	{
        $compet_days = [];
        foreach (self::get_days($compet_id) as $day)
        {
            $compet_days[] = $day['day_round'];
        }

        if (in_array($day_round, $compet_days))
        {
            $row = self::$db_querier->select_single_row_query('SELECT days.*
                FROM ' . FootballSetup::$football_day_table . ' days
                WHERE days.day_compet_id = :compet_id
                AND days.day_round = :day_round', array(
                    'compet_id' => $compet_id,
                    'day_round' => $day_round,
                )
            );
            $day = new FootballDay();
            $day->set_properties($row);
            return $day;
        }
        else 
            return null;
	}

    /** Get all days from a competition */
	public static function get_days(int $compet_id) : array
	{
		$results = self::$db_querier->select('SELECT days.*
            FROM ' . FootballSetup::$football_day_table . ' days
            WHERE days.day_compet_id = :compet_id
            ORDER BY days.id_day', array(
                'compet_id' => $compet_id
            )
        );

        $days = [];
        while($row = $results->fetch())
        {
            $days[] = $row;
        }
        return $days;
	}

    // Doc of days
    public static function set_days_matches($compet_id)
    {
        $now = new Date();
        $c_return_matches = FootballCompetService::get_compet_match_type($compet_id) == FootballDivision::RETURN_MATCHES;
        $teams_number = FootballTeamService::get_teams_number($compet_id);

        $matches_number = $c_return_matches ? ($teams_number - 1) * 2 : ($teams_number - 1);

        for ($i = $matches_number; $i >= 1; $i--)
        {
            for ($j = 1; $j <= $teams_number / 2; $j++)
            {
                self::$db_querier->insert(FootballSetup::$football_match_table, array(
                    'match_compet_id' => $compet_id,
                    'match_type' => 'D',
                    'match_group' => $i,
                    'match_order' => $j,
                    'match_home_id' => 0,
                    'match_away_id' => 0,
                    'match_date' => $now->get_timestamp()
                ));
            }
        }
    }

    public static function update_day_played($compet_id, $day_round, $check)
    {
		self::$db_querier->update(FootballSetup::$football_day_table, ['day_played' => $check], 'WHERE day_compet_id = :compet_id AND day_round = :day_round', array('compet_id' => $compet_id, 'day_round' => $day_round));
    }

    public static function get_last_day($compet_id)
    {
        $days = self::get_days($compet_id);
        $ids = [];
        foreach($days as $day)
        {
            if($day['day_played'])
                $ids[] = $day['day_round'];
        }
        return end($ids);
    }

    public static function get_next_day($compet_id)
    {
        $days = self::get_days($compet_id);
        $ids = [];
        foreach($days as $day)
        {
            if(!$day['day_played'])
                $ids[] = $day['day_round'];
        }
        return $ids[0];
    }
}