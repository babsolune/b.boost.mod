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
        $prev_dates = [];
        foreach(ScmGameService::get_games_in_day($event_id, $prev_day) as $game)
        {
            $prev_dates[] = Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT);
        }

        foreach (array_unique($prev_dates) as $date)
        {
            $view->assign_block_vars('prev_dates', [
                'DATE' => $date
            ]);
            foreach(ScmGameService::get_games_in_day($event_id, $prev_day) as $game)
            {
                $item = new ScmGame();
                $item->set_properties($game);
                if ($date == Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT))
                    $view->assign_block_vars('prev_dates.prev_days', $item->get_template_vars());
            }
        }

        $next_day = ScmDayService::get_next_day($event_id);
        $next_dates = [];
        foreach(ScmGameService::get_games_in_day($event_id, $next_day) as $game)
        {
            $next_dates[] = Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT);
        }

        foreach (array_unique($next_dates) as $date)
        {
            $view->assign_block_vars('next_dates', [
                'DATE' => $date
            ]);
            foreach(ScmGameService::get_games_in_day($event_id, $next_day) as $game)
            {
                $item = new ScmGame();
                $item->set_properties($game);
                if ($date == Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT))
                    $view->assign_block_vars('next_dates.next_days', $item->get_template_vars());
            }
        }

        $view->put_all([
            'C_EVENT_STARTING' => $prev_day == 1,
            'C_EVENT_ENDING' => $prev_day == count(ScmDayService::get_days($event_id)),
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
                'GROUP'   => $k ? ScmGroupService::ntl($k) : '',
                'U_GROUP' => ScmUrlBuilder::display_groups_rounds($event_id, ScmEventService::get_event_slug($event_id), $k)->rel(),
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
            ORDER BY games.game_date ASC, games.game_group ASC, games.game_order ASC', [
                'id' => $event_id
            ]
        );

        $now = new Date();
        $c_return_matches = ScmEventService::get_event_game_type($event_id) == ScmDivision::RETURN_GAMES;
        $c_hat_ranking    = ScmParamsService::get_params($event_id)->get_hat_ranking();
        $view->put_all([
            'C_RETURN_MATCHES' => $c_return_matches,
            'C_HAT_RANKING'    => $c_hat_ranking,
            'C_PLAYGROUNDS'    => ScmParamsService::get_params($event_id)->get_display_playgrounds(),
            'C_HAS_GAMES'      => ScmGameService::has_games($event_id),
            'C_ONE_DAY'        => ScmGameService::one_day_event($event_id),
            'ONE_DAY_DATE'     => Date::to_format(ScmEventService::get_event($event_id)->get_start_date()->get_timestamp(), Date::FORMAT_DAY_MONTH_YEAR_TEXT),
            'TEAMS_NUMBER'     => ScmTeamService::get_teams_number($event_id),
            'TEAMS_PER_GROUP'  => ScmParamsService::get_params($event_id)->get_teams_per_group(),
            'TODAY'            => Date::to_format($now->get_timestamp(), Date::FORMAT_DAY_MONTH_YEAR_TEXT),
        ]);

        $matchdays = [];
        foreach($results as $game)
        {
            if($game['game_type'] == 'G')
                $matchdays[$game['game_round']][Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][] = $game;
        }
        foreach ($matchdays as $matchday => $dates)
        {
            $view->assign_block_vars('matchdays', [
                'MATCHDAY' => $matchday
            ]);
            foreach ($dates as $date => $games)
            {
                $view->assign_block_vars('matchdays.dates', [
                    'DATE' => $date
                ]);
                foreach($games as $game)
                {
                    $item = new ScmGame();
                    $item->set_properties($game);

                    $view->assign_block_vars('matchdays.dates.groups', array_merge($item->get_template_vars(), [
                        'GROUP_NAME' => ScmGroupService::ntl($item->get_game_group()),
                        'DAY_NAME' => $item->get_game_group(),
                        'U_GROUP' => ScmUrlBuilder::display_groups_rounds($event_id, ScmEventService::get_event_slug($event_id), $item->get_game_group())->rel()
                    ]));
                }
            }
        }

        $matchrounds = $matchdays = [];
        foreach($results as $game)
        {
            if($game['game_type'] != 'G')
            {
                $matchdays[] = $game['game_group'];
                $matchrounds[$game['game_group']][Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][] = $game;
            }
        }
        krsort($matchrounds);

        array_values($matchdays);
        rsort($matchdays);
        $rounds_unique = array_unique($matchdays);
        $c_first_round = reset($rounds_unique);

        foreach ($matchrounds as $matchround => $dates)
        {
            $view->assign_block_vars('matchrounds', [
                'MATCHROUND' => $matchround == $c_first_round && $c_hat_ranking ?  LangLoader::get_message('scm.playoff.games', 'common', 'scm') : LangLoader::get_message('scm.round.'.$matchround.'', 'common', 'scm')
            ]);
            foreach ($dates as $date => $games)
            {
                $view->assign_block_vars('matchrounds.dates', [
                    'DATE' => $date
                ]);
                foreach($games as $game)
                {
                    $item = new ScmGame();
                    $item->set_properties($game);

                    $view->assign_block_vars('matchrounds.dates.brackets', array_merge($item->get_template_vars(), [
                        'GROUP_NAME' => ScmGroupService::ntl($item->get_game_group()),
                        'DAY_NAME' => $item->get_game_group(),
                        'U_GROUP' => ScmUrlBuilder::display_groups_rounds($event_id, ScmEventService::get_event_slug($event_id), $item->get_game_group())->rel()
                    ]));
                }
            }
        }
        return $view;
    }
}
?>
