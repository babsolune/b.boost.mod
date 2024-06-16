<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballMatchService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/** Create a new entry in the database match table */
	public static function add_match(FootballMatch $match)
	{
		$result = self::$db_querier->insert(FootballSetup::$football_match_table, $match->get_properties());

		return $result->get_last_inserted_id();
	}

	/** Update a match entry */
	public static function update_match(FootballMatch $match, int $id)
	{
		self::$db_querier->update(FootballSetup::$football_match_table, $match->get_properties(), 'WHERE id_match = :id', array('id' => $id));
	}

	/** Update a match date */
	public static function update_match_date(int $compet_id, int $match_group, int $day_date)
	{
		self::$db_querier->update(FootballSetup::$football_match_table, array('match_date' => $day_date), 'WHERE match_compet_id = :compet_id AND match_group = :match_group', array('compet_id' => $compet_id, 'match_group' => $match_group));
	}

	/** Delete all match entries of a competition */
	public static function delete_matches(int $compet_id)
	{
        $matches = self::get_matches($compet_id);
        foreach ($matches as $match)
        {
            self::$db_querier->delete(FootballSetup::$football_match_table, 'WHERE match_compet_id = :compet_id', array('compet_id' => $compet_id));
        }
    }

    /** Get all matches from a competition */
	public static function get_matches(int $compet_id) : array
	{
		$results = self::$db_querier->select('SELECT matches.*
            FROM ' . FootballSetup::$football_match_table . ' matches
            WHERE matches.match_compet_id = :compet_id
            ORDER BY matches.match_group ASC, matches.match_order ASC', array(
                'compet_id' => $compet_id
            )
        );

        $matches = [];
        while($row = $results->fetch())
        {
            $matches[] = $row;
        }
        return $matches;
	}

    /** Get all matches from a day in competition */
	public static function get_matches_in_day(int $compet_id, int $day_round) : array
	{
		$results = self::$db_querier->select('SELECT matches.*
            FROM ' . FootballSetup::$football_match_table . ' matches
            WHERE matches.match_compet_id = :compet_id
            ORDER BY matches.match_group ASC, matches.match_order ASC', array(
                'compet_id' => $compet_id
            )
        );

        $matches = [];
        while($row = $results->fetch())
        {
            if ($row['match_group'] == $day_round)
            $matches[] = $row;
        }
        return $matches;
	}

    /** Get all matches from a competition */
	public static function get_team_matches(int $compet_id, int $team_id) : array
	{
		$results = self::$db_querier->select('SELECT matches.*, compet.*
            FROM ' . FootballSetup::$football_match_table . ' matches
            LEFT JOIN ' . FootballSetup::$football_compet_table . ' compet ON compet.id_compet = matches.match_compet_id
            WHERE matches.match_compet_id = :compet_id
            AND (matches.match_home_id = :team_id OR matches.match_away_id = :team_id)
            ORDER BY matches.match_group ASC, matches.match_order ASC', array(
                'compet_id' => $compet_id,
                'team_id' => $team_id
            )
        );

        $matches = [];
        while($row = $results->fetch())
        {
            $matches[] = $row;
        }
        return $matches;
	}

    /** check if a competition has declared matches */
    public static function has_matches(int $compet_id) : bool
    {
        return count(self::get_matches($compet_id)) > 0;
    }

    /** 
     * get a match based on match group ids
     * @param int $compet_id the id of its competition
     * @param string $type its type of group 'G' = group or 'D' = day | 'W' or 'L' = bracket
     * @param int $group its group number
     * @param int $order its order number
    */
	public static function get_match(int $compet_id, string $type, int $group, int $order)
	{
        $compet_matches = [];
        foreach (self::get_matches($compet_id) as $match)
        {
            $compet_matches[] = $match['match_type'].$match['match_group'].$match['match_order'];
        }

        if (in_array($type.$group.$order, $compet_matches))
        {
            $row = self::$db_querier->select_single_row_query('SELECT matches.*
                FROM ' . FootballSetup::$football_match_table . ' matches
                WHERE matches.match_compet_id = :compet_id
                AND matches.match_type = :type
                AND matches.match_group = :group
                AND matches.match_order = :order', array(
                    'compet_id' => $compet_id,
                    'type' => $type,
                    'group' => $group,
                    'order' => $order,
                )
            );
            $match = new FootballMatch();
            $match->set_properties($row);
            return $match;
        }
        else 
            return null;
	}

    /** Check if all matches date are on the same day */
    public static function one_day_compet(int $compet_id) : bool
    {
        $matches = self::get_matches($compet_id);

        if(count($matches) > 0)
        {
            $first_day = date('j', $matches[0]['match_date']);
            $first_month = date('n', $matches[0]['match_date']);
            $first_year = date('Y', $matches[0]['match_date']);

            $same_day = true;
            for ($i = 1; $i < count($matches); $i++) {
                $day = date('j', $matches[$i]['match_date']);
                $month = date('n', $matches[$i]['match_date']);
                $year = date('Y', $matches[$i]['match_date']);

                if ($day != $first_day || $month != $first_month || $year != $first_year) {
                    $same_day = false;
                    break;
                }
            }
            return $same_day;
        }
        else
            return false;
    }

    // Check if match is live
    public static function is_live($compet_id, $match_id)
	{
        $now = new Date();
        $match = FootballMatchCache::load()->get_match($match_id);
        $match_duration = FootballParamsService::get_params($compet_id)->get_match_duration();
        $overtime_duration = FootballParamsService::get_params($compet_id)->get_overtime_duration();
        $full_duration = $match['match_type'] == 'G' || $match['match_type'] == 'D' ? $match_duration : $match_duration + $overtime_duration;

        $is_live = false;
        if ($now->get_timestamp() > $match['match_date'])
        {
            if ($now->get_timestamp() > ($match['match_date'] + ($full_duration * 60))) {
                $is_live = false;
            } else {
                $is_live = true;
            }
        }
		return $is_live;
	}
}
?>
