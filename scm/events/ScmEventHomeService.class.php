<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmEventHomeService
{
	private static $db_querier;
	protected static $module_id = 'scm';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

    public static function build_days_calendar(int $event_id)
    {
        $view = new FileTemplate('scm/ScmEventDaysController.tpl');
        $view->add_lang(LangLoader::get_all_langs('scm'));

        // Display previous and next days games
        $prev_day = ScmDayService::get_last_day($event_id);
        foreach (ScmGameService::get_games_in_day($event_id, $prev_day) as $game)
        {
            $item = new ScmGame();
            $item->set_properties($game);
            $view->assign_block_vars('prev_days', $item->get_array_tpl_vars());
        }
        $days_number = count(ScmDayService::get_days($event_id));
        $view->put('C_EVENT_ENDING', $prev_day == $days_number);
        $next_day = ScmDayService::get_next_day($event_id);
        foreach (ScmGameService::get_games_in_day($event_id, $next_day) as $game)
        {
            $item = new ScmGame();
            $item->set_properties($game);
            $view->assign_block_vars('next_days', $item->get_array_tpl_vars());
        }
        $view->put_all([
            'LAST_DAY' => $prev_day,
            'NEXT_DAY' => $next_day,
        ]);

        return $view;
    }

    public static function build_rounds_calendar(int $event_id)
    {
        $view = new FileTemplate('scm/ScmEventRoundsController.tpl');
        $view->add_lang(LangLoader::get_all_langs('scm'));

        // Display group team list
        $groups = ScmGroupService::get_group_teams_list($event_id);
        ksort($groups);
        $club = ScmClubCache::load();

        $view->put('GROUPS_NUMBER', count($groups));

        foreach ($groups as $k => $group)
        {
            $view->assign_block_vars('team_groups', [
                'GROUP' => ScmGroupService::ntl($k),
                'U_GROUP' => ScmUrlBuilder::display_groups_rounds($event_id, ScmEventService::get_event_slug($event_id), $k)->rel()
            ]);
            foreach ($group as $team)
            {
                $view->assign_block_vars('team_groups.teams', [
                    'TEAM_NAME' => $team['club_name'],
                    'TEAM_LOGO' => $club->get_club_shield($team['team_club_id']),
                ]);
            }
        }

        // Display games of the day
        $results = self::$db_querier->select('SELECT games.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            WHERE games.game_event_id = :id
            ORDER BY games.game_date ASC, games.game_order ASC', [
                'id' => $event_id
            ]
        );

        $now = new Date();
        $c_one_day = ScmGameService::one_day_event($event_id);
        $view->put_all([
            'C_HAT_RANKING'   => ScmParamsService::get_params($event_id)->get_hat_ranking(),
            'C_PLAYGROUNDS'   => ScmParamsService::get_params($event_id)->get_display_playgrounds(),
            'C_HAS_GAMES'   => ScmGameService::has_games($event_id),
            'C_ONE_DAY'       => $c_one_day,
            'ONE_DAY_DATE'    => Date::to_format(ScmEventService::get_event($event_id)->get_start_date()->get_timestamp(), Date::FORMAT_DAY_MONTH_YEAR_TEXT),
            'TEAMS_NUMBER'    => ScmTeamService::get_teams_number($event_id),
            'TEAMS_PER_GROUP' => ScmParamsService::get_params($event_id)->get_teams_per_group(),
            'TODAY'           => Date::to_format($now->get_timestamp(), Date::FORMAT_DAY_MONTH_YEAR_TEXT),
        ]);

        if($c_one_day)
        {
            while($row = $results->fetch())
            {
                $game = new ScmGame();
                $game->set_properties($row);

                $items = $game->get_game_type() == 'G' ? 'groups' : 'brackets';

                $view->assign_block_vars($items, array_merge($game->get_array_tpl_vars(), [
                    'GROUP_NAME' => ScmGroupService::ntl($game->get_game_group()),
                    'DAY_NAME' => $game->get_game_group(),
                    'U_GROUP' => ScmUrlBuilder::display_groups_rounds($event_id, ScmEventService::get_event_slug($event_id), $game->get_game_group())->rel()
                ]));
            }
        }
        else 
        {
            $matchdays = [];
            foreach($results as $item)
            {
                $date = Date::to_format($item['game_date'], Date::FORMAT_DAY_MONTH_YEAR);
                if (!array_key_exists($date, $matchdays))
                $matchdays[$date][] = $item;
            }

            foreach ($matchdays as $date => $games)
            {
                $date_elements = explode("/", $date);
                $date_elements = array_reverse($date_elements);
                $reversed_date = implode("/", $date_elements);
                $view->assign_block_vars('matchdays', [
                    'DATE' => Date::to_format(strtotime($reversed_date), Date::FORMAT_DAY_MONTH_YEAR_TEXT),
                ]);
                foreach($results as $row)
                {
                    $game = new ScmGame();
                    $game->set_properties($row);

                    $items = $game->get_game_type() == 'G' ? 'groups' : 'brackets';
                    if($date == Date::to_format($row['game_date'], Date::FORMAT_DAY_MONTH_YEAR))
                    {
                        $view->assign_block_vars('matchdays.' . $items, array_merge($game->get_array_tpl_vars(), [
                            'GROUP_NAME' => ScmGroupService::ntl($game->get_game_group()),
                            'DAY_NAME' => $game->get_game_group(),
                            'U_GROUP' => ScmUrlBuilder::display_groups_rounds($event_id, ScmEventService::get_event_slug($event_id), $game->get_game_group())->rel()
                        ]));
                    }
                }
            }
        }
        return $view;
    }
}
?>
