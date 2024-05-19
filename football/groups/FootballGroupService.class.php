<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
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

    public static function build_matches_from_groups(int $compet_id)
    {
        // build groups from compet teams list
        $groups = self::group_teams_lists($compet_id);
        // Build schedule
        $full_schedule = [];
        $now = new Date();
        foreach ($groups as $index => $teams) {
            $schedule = self::build_group_matches($teams);
            $full_schedule[$index] = $schedule;
        }
        // Build match list
        foreach ($full_schedule as $group => $schedule) {
            $match_nb = 1;
            foreach ($schedule as $round => $matches) {
                foreach ($matches as $i => $match) {
                    self::$db_querier->insert(FootballSetup::$football_match_table, array(
                        'match_compet_id' => $compet_id,
                        'match_number' => 'G'. $group . $match_nb,
                        'match_home_team_id' => $match[0]['id_team'],
                        'match_visit_team_id' => $match[1]['id_team'],
                        'match_date' => $now->get_timestamp()
                    ));
                    $match_nb++;
                }
            }
        }
    }

    private static function build_group_matches(array $group_teams)
    {
        $numTeams = count($group_teams);
        $schedule = [];
        for ($round = 0; $round < $numTeams - 1; $round++) {
            for ($match = 0; $match < $numTeams / 2; $match++) {
                $home = ($round + $match) % ($numTeams - 1);
                $away = ($numTeams - 1 - $match + $round) % ($numTeams - 1);

                if ($match == 0) {
                    $away = $numTeams - 1;
                }
                $schedule[$round][] = [$group_teams[$home], $group_teams[$away]];
            }
        }
        return $schedule;
    }

    public static function group_teams_lists($compet_id)
    {
        $compet_teams = FootballTeamService::get_teams($compet_id);
        $groups = [];
        foreach($compet_teams as $team)
        {
            $group_nb = TextHelper::substr($team['team_group'], 0, 1);
            if(!isset($groups[$group_nb]))
                $groups[$group_nb] = [];
            $groups[$group_nb][] = $team;
        }
        return $groups;
    }

    public static function group_matches_lists($compet_id)
    {
        $compet_matches = FootballMatchService::get_matches($compet_id);
        $groups = [];
        foreach($compet_matches as $match)
        {
            $group_nb = TextHelper::substr($match['match_number'], 1, TextHelper::strlen($match['match_number']) - 2);
            if(!isset($groups[$group_nb]))
                $groups[$group_nb] = [];
            $groups[$group_nb][] = $match;
        }
        return $groups;
    }
}