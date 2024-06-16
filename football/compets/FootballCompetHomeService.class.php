<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballCompetHomeService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

    public static function build_rounds_calendar(int $compet_id)
    {
        $view = new FileTemplate('football/FootballRoundsHomeController.tpl');
        $view->add_lang(LangLoader::get_all_langs('football'));

        // Display group team list
        $groups = FootballGroupService::get_group_teams_list($compet_id);
        ksort($groups);

        $view->put('GROUPS_NUMBER', count($groups));

        foreach ($groups as $k => $group)
        {
            $view->assign_block_vars('team_groups', array(
                'GROUP' => FootballGroupService::ntl($k)
            ));
            foreach ($group as $team)
            {
                // debug::dump($team);
                $view->assign_block_vars('team_groups.teams', array(
                    'TEAM_NAME' => $team['club_name'],
                    'TEAM_LOGO' => $team['club_logo'],
                ));
            }
        }

        // Display matches of the day
        $results = self::$db_querier->select('SELECT games.*
            FROM ' . FootballSetup::$football_match_table . ' games
            WHERE games.match_compet_id = :id
            ORDER BY games.match_date ASC, games.match_order ASC', array(
                'id' => $compet_id
            )
        );

        $now = new Date();
        $c_one_day = FootballMatchService::one_day_compet($compet_id);
        $view->put_all(array(
            'C_HAT_RANKING'   => FootballParamsService::get_params($compet_id)->get_hat_ranking(),
            'C_PLAYGROUNDS'   => FootballParamsService::get_params($compet_id)->get_display_playgrounds(),
            'C_HAS_MATCHES'   => FootballMatchService::has_matches($compet_id),
            'C_ONE_DAY'       => $c_one_day,
            'TEAMS_NUMBER'    => FootballTeamService::get_teams_number($compet_id),
            'TEAMS_PER_GROUP' => FootballParamsService::get_params($compet_id)->get_teams_per_group(),
            'TODAY'           => Date::to_format($now->get_timestamp(), Date::FORMAT_DAY_MONTH_YEAR_TEXT),
        ));

        if($c_one_day)
        {
            while($row = $results->fetch())
            {
                $match = new FootballMatch();
                $match->set_properties($row);

                $items = $match->get_match_type() == 'G' ? 'groups' : 'brackets';

                // Debug::dump(FootballMatchService::is_live($compet_id, $match->get_id_match()));
                $view->assign_block_vars($items, array_merge($match->get_array_tpl_vars(), array(
                    'GROUP_NAME' => FootballGroupService::ntl($match->get_match_group()),
                    'DAY_NAME' => $match->get_match_group(),
                    'U_GROUP' => FootballUrlBuilder::display_groups_rounds($compet_id, $match->get_match_group())->rel()
                )));
            }
        }
        else 
        {
            $matchdays = [];
            foreach($results as $item)
            {
                $date = Date::to_format($item['match_date'], Date::FORMAT_DAY_MONTH_YEAR);
                if (!array_key_exists($date, $matchdays))
                $matchdays[$date][] = $item;
            }

            foreach ($matchdays as $date => $matches)
            {
                $date_elements = explode("/", $date);
                $date_elements = array_reverse($date_elements);
                $reversed_date = implode("/", $date_elements);
                $view->assign_block_vars('matchdays', array(
                    'DATE' => Date::to_format(strtotime($reversed_date), Date::FORMAT_DAY_MONTH_YEAR_TEXT),
                ));
                foreach($results as $row)
                {
                    $match = new FootballMatch();
                    $match->set_properties($row);

                    $items = $match->get_match_type() == 'G' ? 'groups' : 'brackets';
                    if($date == Date::to_format($row['match_date'], Date::FORMAT_DAY_MONTH_YEAR))
                    {
                        $view->assign_block_vars('matchdays.' . $items, array_merge($match->get_array_tpl_vars(), array(
                            'GROUP_NAME' => FootballGroupService::ntl($match->get_match_group()),
                            'DAY_NAME' => $match->get_match_group(),
                            'U_GROUP' => FootballUrlBuilder::display_groups_rounds($compet_id, $match->get_match_group())->rel()
                        )));
                    }
                }
            }
        }
        return $view;
    }
}
?>
