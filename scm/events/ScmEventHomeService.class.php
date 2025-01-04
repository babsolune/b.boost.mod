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

    public static function build_championship_home(int $event_id)
    {
        $view = new FileTemplate('scm/ScmEventChampionshipController.tpl');
        $lang = LangLoader::get_all_langs('scm');
        $view->add_lang($lang);

        // Teams list
        foreach (ScmTeamService::get_teams($event_id) as $team)
        {
            $club = ScmClubCache::load()->get_club($team['id_club']);
            $real_id = $club['club_affiliate'] ? $club['club_affiliation'] : $club['id_club'];
            $real_slug = $club['club_affiliate'] ? ScmClubService::get_club($club['club_affiliation'])->get_club_slug() : $club['club_slug'];

            $view->assign_block_vars('clubs_list', [
                'CLUB_LOGO' => Url::to_rel($team['club_logo']),
                'CLUB_SHORT_NAME' => $team['club_name'],
                'U_CLUB' => ScmUrlBuilder::display_club($real_id, $real_slug)->rel()
            ]);
        }

        $prev_day = ScmDayService::get_last_day($event_id);
        $next_day = ScmDayService::get_next_day($event_id);

        $view->put_all([
            'EVENT_ID' => $event_id,
            'C_EVENT_STARTING' => $next_day == 1,
            'L_STARTING_DATE' => StringVars::replace_vars($lang['scm.event.not.started'], ['date' => Date::to_format(ScmEventService::get_event($event_id)->get_start_date()->get_timestamp(), Date::FORMAT_DAY_MONTH_YEAR_TEXT)]),
            'C_EVENT_ENDING' => $prev_day == count(ScmDayService::get_days($event_id)),
            'PREV_GAMES' => ScmGameFormat::format_cluster(ScmGameService::get_games_in_cluster($event_id, $prev_day), false),
            'NEXT_GAMES' => ScmGameFormat::format_cluster(ScmGameService::get_games_in_cluster($event_id, $next_day), false),
            'PREV_DAY' => $prev_day,
            'NEXT_DAY' => $next_day,
        ]);

        // Ranking
        // $view->put_all([
        //     'C_CACHE_FILE' => ScmRankingCache::cache_file_link($event_id),
        //     'U_CACHE_FILE' => ScmRankingCache::cache_file_link($event_id)
        // ]);
        $final_ranks = ScmRankingService::general_ranking($event_id);

        // Display ranks to view
        $prom = ScmParamsService::get_params($event_id)->get_promotion();
        $playoff_prom = ScmParamsService::get_params($event_id)->get_playoff_prom();
        $playoff_releg = ScmParamsService::get_params($event_id)->get_playoff_releg();
        $releg = ScmParamsService::get_params($event_id)->get_relegation();
        $prom_color = ScmConfig::load()->get_promotion_color();
        $playoff_prom_color = ScmConfig::load()->get_playoff_prom_color();
        $playoff_releg_color = ScmConfig::load()->get_playoff_releg_color();
        $releg_color = ScmConfig::load()->get_relegation_color();
        $color_count = count($final_ranks);

        foreach ($final_ranks as $i => $team_rank)
        {
            if ($prom && $i < $prom) {
                $rank_color = $prom_color;
            } elseif ($playoff_prom && $i >= $prom && $i < ($prom + $playoff_prom)) {
                $rank_color = $playoff_prom_color;
            } elseif ($playoff_releg && $i >= ($color_count - $releg - $playoff_releg) && $i < ($color_count - $releg)) {
                $rank_color = $playoff_releg_color;
            } elseif ($releg && $i >= ($color_count - $releg)) {
                $rank_color = $releg_color;
            } else {
                $rank_color = 'rgba(0,0,0,0)';
            }
            $event_slug = ScmEventService::get_event_slug($event_id);
            $view->assign_block_vars('ranks', [
                'C_FAV'           => ScmParamsService::check_fav($event_id, $team_rank['team_id']),
                'C_FORFEIT'       => $team_rank['status'] == 'forfeit',
                'C_HAS_TEAM_LOGO' => ScmTeamService::get_team_logo($team_rank['team_id']),
                'RANK'            => $i + 1,
                'RANK_COLOR'      => $rank_color,
                'TEAM_ID'         => !empty($team_rank['team_id']) ? $team_rank['team_id'] : 0,
                'U_TEAM_CALENDAR' => !empty($team_rank['team_id']) ? ScmUrlBuilder::display_team_calendar($event_id, $event_slug, $team_rank['team_id'])->rel() : '#',
                'TEAM_NAME'       => !empty($team_rank['team_id']) ? ScmTeamService::get_team_name($team_rank['team_id']) : '',
                'TEAM_LOGO'       => !empty($team_rank['team_id']) ? ScmTeamService::get_team_logo($team_rank['team_id']) : '',
                'POINTS'          => $team_rank['points'],
                'PLAYED'          => $team_rank['played'],
                'WIN'             => $team_rank['win'],
                'DRAW'            => $team_rank['draw'],
                'LOSS'            => $team_rank['loss'],
                'GOALS_FOR'       => $team_rank['goals_for'],
                'GOALS_AGAINST'   => $team_rank['goals_against'],
                'GOAL_AVERAGE'    => $team_rank['goal_average'],
                'OFF_BONUS'       => $team_rank['off_bonus'],
                'DEF_BONUS'       => $team_rank['def_bonus'],
            ]);

            $params = ScmParamsService::get_params($event_id)->get_bonus();
            $view->put_all([
                'C_BONUS_SINGLE' => $params == ScmParams::BONUS_SINGLE,
                'C_BONUS_DOUBLE' => $params == ScmParams::BONUS_DOUBLE,
            ]);
        }

        return $view;
    }

    public static function build_tournament_home(int $event_id)
    {
        $view = new FileTemplate('scm/ScmEventTournamentController.tpl');
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
                $club_cache = ScmClubCache::load();
                $club = $club_cache->get_club($team['id_club']);
                $real_id = $club['club_affiliate'] ? $club['club_affiliation'] : $club['id_club'];
                $real_slug = $club['club_affiliate'] ? ScmClubService::get_club($club['club_affiliation'])->get_club_slug() : $club['club_slug'];

                $view->assign_block_vars('team_groups.teams', [
                    'TEAM_NAME' => $team['club_name'],
                    'TEAM_LOGO' => $club_cache->get_club_shield($real_id),
                    'U_CLUB' => ScmUrlBuilder::display_club($real_id, $real_slug)->rel()
                ]);
            }
        }

        // Display games of the day
        $results = self::$db_querier->select('SELECT games.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            WHERE games.game_event_id = :id
            ORDER BY games.game_date ASC, games.game_cluster ASC, games.game_order ASC', [
                'id' => $event_id
            ]
        );
        $params = ScmParamsService::get_params($event_id);
        $c_hat_ranking = ScmParamsService::get_params($event_id)->get_hat_ranking();

        $now = new Date();
        $view->put_all([
            'C_HAT_RANKING'      => $c_hat_ranking,
            'C_ROUND_RANKING'    => $params->get_finals_type() == ScmParams::FINALS_RANKING,
            'C_PLAYGROUNDS'      => $params->get_display_playgrounds(),
            'C_HAS_GAMES'        => ScmGameService::has_games($event_id),
            'C_ONE_DAY'          => ScmGameService::one_day_event($event_id),
            'ONE_DAY_DATE'       => Date::to_format(ScmEventService::get_event($event_id)->get_start_date()->get_timestamp(), Date::FORMAT_DAY_MONTH_YEAR_TEXT),
            'TEAMS_NUMBER'       => ScmTeamService::get_teams_number($event_id),
            'TEAMS_PER_GROUP'    => $params->get_teams_per_group(),
            'TODAY'              => Date::to_format($now->get_timestamp(), Date::FORMAT_DAY_MONTH_YEAR_TEXT)
        ]);

        $matchdays = [];
        foreach($results as $game)
        {
            if($game['game_type'] == 'G')
                if ($c_hat_ranking)
                    $matchdays[$game['game_cluster']][Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][] = $game;
                else
                    $matchdays[$game['game_round']][Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][] = $game;
        }

        foreach ($matchdays as $matchday => $dates)
        {
            $view->assign_block_vars('matchdays', [
                'MATCHDAY' => $matchday,
                'U_MATCHDAY' => ScmUrlBuilder::display_groups_rounds($event_id, ScmEventService::get_event_slug($event_id), $matchday)->rel(),
                'MATCHDAYS_LIST'     => ScmGameFormat::format_event($event_id, $dates, false, false),
                'ROUNDS_LIST'        => ScmGameFormat::format_event($event_id, $dates, true, false),
            ]);
        }

        $matchrounds = $matchdays = [];
        foreach($results as $game)
        {
            if($game['game_type'] != 'G')
            {
                $matchdays[] = $game['game_cluster'];
                $matchrounds[$game['game_cluster']][Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][] = $game;
            }
        }
        if (ScmParamsService::get_params($event_id)->get_finals_type() == ScmParams::FINALS_RANKING)
            ksort($matchrounds);
        else
            krsort($matchrounds);

        array_values($matchdays);
        rsort($matchdays);
        $rounds_unique = array_unique($matchdays);
        $c_first_round = reset($rounds_unique);

        foreach ($matchrounds as $matchround => $dates)
        {
            $view->assign_block_vars('matchrounds', [
                'MATCHDAYS_LIST'     => ScmGameFormat::format_event($event_id, $dates, false, true),
                'ROUNDS_LIST'        => ScmGameFormat::format_event($event_id, $dates, false, true),
                'ROUND_RANKING_LIST' => ScmGameFormat::format_event($event_id, $dates, false, false),
                'L_MATCHROUND' => ($matchround == $c_first_round && $c_hat_ranking)
                                    ? LangLoader::get_message('scm.playoff.games', 'common', 'scm')
                                    : (ScmParamsService::get_params($event_id)->get_finals_type() == ScmParams::FINALS_RANKING
                                        ? LangLoader::get_message('scm.group', 'common', 'scm') . ' ' . $matchround
                                        : LangLoader::get_message('scm.round.' . $matchround . '', 'common', 'scm'))
            ]);
        }
        return $view;
    }

    public static function build_cup_home(int $event_id)
    {
        $view = new FileTemplate('scm/ScmEventCupController.tpl');
        $view->add_lang(LangLoader::get_all_langs('scm'));

        // Display group team list
        $groups = ScmGroupService::get_group_teams_list($event_id);
        ksort($groups);
        $club = ScmClubCache::load();

        $view->put('GROUPS_NUMBER', count($groups));

        foreach ($groups as $k => $group)
        {
            $view->assign_block_vars('team_groups', []);
            foreach ($group as $team)
            {
                $club_cache = ScmClubCache::load();
                $club = $club_cache->get_club($team['id_club']);
                $real_id = $club['club_affiliate'] ? $club['club_affiliation'] : $club['id_club'];
                $real_slug = $club['club_affiliate'] ? ScmClubService::get_club($club['club_affiliation'])->get_club_slug() : $club['club_slug'];

                $view->assign_block_vars('team_groups.teams', [
                    'TEAM_NAME' => $team['club_name'],
                    'TEAM_LOGO' => $club_cache->get_club_shield($real_id),
                    'U_CLUB' => ScmUrlBuilder::display_club($real_id, $real_slug)->rel()
                ]);
            }
        }

        // Display games of the day
        $results = self::$db_querier->select('SELECT games.*
            FROM ' . ScmSetup::$scm_game_table . ' games
            WHERE games.game_event_id = :id
            ORDER BY games.game_date ASC, games.game_cluster ASC, games.game_order ASC', [
                'id' => $event_id
            ]
        );

        $now = new Date();
        $c_hat_ranking    = ScmParamsService::get_params($event_id)->get_hat_ranking();
        $view->put_all([
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
                if ($c_hat_ranking)
                    $matchdays[$game['game_cluster']][Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][] = $game;
                else
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
                        'GROUP_NAME' => ScmGroupService::ntl($item->get_game_cluster()),
                        'DAY_NAME' => $item->get_game_cluster(),
                        'U_GROUP' => ScmUrlBuilder::display_groups_rounds($event_id, ScmEventService::get_event_slug($event_id), $item->get_game_cluster())->rel()
                    ]));
                }
            }
        }

        $matchrounds = $matchdays = [];
        foreach($results as $game)
        {
            if($game['game_type'] != 'G')
            {
                $matchdays[] = $game['game_cluster'];
                $matchrounds[$game['game_cluster']][Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][] = $game;
            }
        }
        if (ScmParamsService::get_params($event_id)->get_finals_type() == ScmParams::FINALS_RANKING)
            ksort($matchrounds);
        else
            krsort($matchrounds);

        array_values($matchdays);
        rsort($matchdays);
        $rounds_unique = array_unique($matchdays);
        $c_first_round = reset($rounds_unique);

        foreach ($matchrounds as $matchround => $dates)
        {
            $view->assign_block_vars('matchrounds', [
                'ROUNDS_LIST' => ScmGameFormat::format_event($event_id, $dates, false, true),
                'L_MATCHROUND' => ($matchround == $c_first_round && $c_hat_ranking)
                                    ? LangLoader::get_message('scm.playoff.games', 'common', 'scm')
                                    : (ScmParamsService::get_params($event_id)->get_finals_type() == ScmParams::FINALS_RANKING
                                        ? LangLoader::get_message('scm.group', 'common', 'scm') . ' ' . $matchround
                                        : LangLoader::get_message('scm.round.' . $matchround . '', 'common', 'scm'))
            ]);
        }
        return $view;
    }

    public static function build_practice_home(int $event_id)
    {
        $view = new FileTemplate('scm/ScmEventPracticeController.tpl');
        $games = ScmGameService::get_games($event_id);

        $view->put_all([
            'C_HAS_GAMES' => ScmGameService::has_games($event_id),
            'GAMES_LIST' => ScmGameFormat::format_cluster($games, false),
        ]);
        return $view;
    }
}
?>
