<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballGroupService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/** number to letter */
	public static function ntl(int $number) : string
    {
        return chr(64 + $number);
    }

    /** Add all matches of all days of the $compet_id competition when it's a hat ranking competition into database */
    public static function set_hat_days_matches(int $compet_id, int $days, int $teams_number)
    {
        $now = new Date();
        for ($day = 1; $day <= $days; $day++)
        {
            for ($match = 1; $match <= ($teams_number / 2); $match++)
            {
                self::$db_querier->insert(FootballSetup::$football_match_table, array(
                    'match_compet_id' => $compet_id,
                    'match_type' => 'G',
                    'match_group' => $day,
                    'match_order' => $match,
                    'match_home_id' => 0,
                    'match_away_id' => 0,
                    'match_date' => $now->get_timestamp()
                ));
            }
        }
    }

    /** Add all matches of all groups of the $compet_id competition to database */
    public static function set_groups_matches(int $compet_id) : void
    {
        // Debug::stop(FootballCompetService::get_compet($compet_id)->get_creation_date()->get_timestamp());
        $c_return_matches = FootballCompetService::get_compet_match_type($compet_id) == FootballDivision::RETURN_MATCHES;
        // build groups from compet teams list
        $groups = self::get_group_teams_list($compet_id);
        // Build schedule
        $full_schedule = [];
        $now = new Date();
        foreach ($groups as $index => $teams) {
            $schedule = self::get_group_matches($teams);
            $full_schedule[$index] = $schedule;
        }
        // Build match list
        foreach ($full_schedule as $group => $schedule) {
            $match_order = 1;
            foreach ($schedule as $round => $matches) {
                foreach ($matches as $i => $match) {
                    self::$db_querier->insert(FootballSetup::$football_match_table, array(
                        'match_compet_id' => $compet_id,
                        'match_type' => 'G',
                        'match_group' => $group,
                        'match_order' => $match_order,
                        'match_home_id' => FootballParamsService::get_params($compet_id)->get_fill_matches() ? $match[0]['id_team'] : 0,
                        'match_away_id' => FootballParamsService::get_params($compet_id)->get_fill_matches() ? $match[1]['id_team'] : 0,
                        'match_date' => FootballParamsService::get_params($compet_id)->get_fill_matches() ? FootballCompetService::get_compet($compet_id)->get_creation_date()->get_timestamp() : $now->get_timestamp()
                    ));
                    $match_order++;
                }
            }
            if ($c_return_matches)
            {
                $match_order_r = $match_order;
                foreach ($schedule as $round => $matches) {
                    foreach ($matches as $i => $match) {
                        self::$db_querier->insert(FootballSetup::$football_match_table, array(
                            'match_compet_id' => $compet_id,
                            'match_type' => 'G',
                            'match_group' => $group,
                            'match_order' => $match_order_r,
                            'match_home_id' => FootballParamsService::get_params($compet_id)->get_fill_matches() ? $match[1]['id_team'] : 0,
                            'match_away_id' => FootballParamsService::get_params($compet_id)->get_fill_matches() ? $match[0]['id_team'] : 0,
                            'match_date' => $now->get_timestamp()
                        ));
                        $match_order_r++;
                    }
                }
            }
        }
    }

    /** get all matches in a group */
    private static function get_group_matches(array $group_teams) : array
    {
        $teams_number = count($group_teams);
        $schedule = [];
        for ($round = 0; $round < $teams_number - 1; $round++) {
            for ($match = 0; $match < $teams_number / 2; $match++) {
                $home = ($round + $match) % ($teams_number - 1);
                $away = ($teams_number - 1 - $match + $round) % ($teams_number - 1);

                if ($match == 0) {
                    $away = $teams_number - 1;
                }
                $schedule[$round][] = [$group_teams[$home], $group_teams[$away]];
            }
        }
        return $schedule;
    }

    /** Get list of teams in a group */
    public static function get_group_teams_list(int $compet_id) : array
    {
        $compet_teams = FootballTeamService::get_teams($compet_id);
        $groups = [];
        foreach($compet_teams as $team)
        {
            $group_nb = $team['team_group'];
            if(!isset($groups[$group_nb]))
                $groups[$group_nb] = [];
            $groups[$group_nb][] = $team;
        }
        return $groups;
    }

    /** Get list of matches in a day or group */
    public static function matches_list_from_group(int $compet_id, string $stage = '', string $group = '') : array
    {
        $compet_matches = FootballMatchService::get_matches($compet_id);
        $matches = [];
        if ($group)
            foreach($compet_matches as $match)
            {
                if($match['match_type'] == $stage && $match['match_group'] == $group)
                {
                    $matches[] = $match;
                }
            }
        else
        foreach($compet_matches as $match)
        {
            if($match['match_type'] == $stage)
            {
                $group_nb = $match['match_group'];
                if(!isset($matches[$group_nb]))
                    $matches[$group_nb] = [];
                $matches[$group_nb][] = $match;
            }
        }
        return $matches;
    }
}