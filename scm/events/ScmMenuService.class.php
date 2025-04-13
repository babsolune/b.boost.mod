<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmMenuService
{
	private static $db_querier;
	protected static $module_id = 'scm';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	public static function build_event_menu($event_id, $round = 1)
	{
        $lang     = LangLoader::get_all_langs('scm');
        $event    = ScmEventService::get_event($event_id);
        $category = CategoriesService::get_categories_manager()->get_categories_cache()->get_category($event->get_id_category());
        $division = ScmDivisionCache::load()->get_division($event->get_division_id());
        $season   = ScmSeasonCache::load()->get_season($event->get_season_id());
        $params   = ScmParamsService::get_params($event_id);

        $view = new FileTemplate('scm/menus/ScmMenuController.tpl');
        $view->add_lang(LangLoader::get_all_langs('scm'));

        $c_has_teams      = count(ScmTeamService::get_teams($event_id)) > 0;
        $c_has_days       = $params->get_victory_points();
        $c_has_rounds     = $params->get_rounds_number();
        $c_has_groups     = $params->get_teams_per_group();
        $c_has_p_games    = $params->get_games_number();
        $c_has_games      = ScmGameService::has_games($event_id);
        $c_finals_ranking = $params->get_finals_type() == ScmParams::FINALS_RANKING;
        $bracket_round = 
            $params->get_hat_ranking()
            ? $params->get_rounds_number() + 1
            : ($c_finals_ranking ? 1 : $params->get_rounds_number())
        ;

        $active_round = AppContext::get_request()->get_getint('cluster', 0);
        if ($params->get_hat_ranking() && $active_round == 0)
            $active_round = ScmGroupService::get_last_matchday_hat($event_id);
        elseif ($event->get_event_type() == ScmEvent::CHAMPIONSHIP && $active_round == 0)
            $active_round = ScmDayService::get_last_day($event_id);
        elseif ($active_round == 0)
            $active_round = $round;

        $view->put_all([
            'C_CONTROLS'     => ScmAuthorizationsService::check_authorizations($category->get_id())->manage_events(),
            'C_IS_MASTER'    => ScmEventService::is_master($event_id),
            'C_IS_SUB'       => $event->get_is_sub(),
            'C_CHAMPIONSHIP' => $event->get_event_type() == ScmEvent::CHAMPIONSHIP,
            'C_CUP'          => $event->get_event_type() == ScmEvent::CUP,
            'C_TOURNAMENT'   => $event->get_event_type() == ScmEvent::TOURNAMENT,
            'C_PRACTICE'     => $event->get_event_type() == ScmEvent::PRACTICE,
            'C_HAS_TEAMS'    => $c_has_teams,
            'C_HAS_DAYS'     => $c_has_days,
            'C_HAS_ROUNDS'   => $c_has_rounds,
            'C_HAS_GROUPS'   => $c_has_groups,
            'C_HAS_GAMES'    => $c_has_games,
            'C_HAS_P_GAMES'  => $c_has_p_games,
            'C_ONE_DAY'      => $event->get_oneday(),
            'C_SOURCES'      => $event->get_sources(),
            'C_FINALS_RANKING_TYPE' => $c_finals_ranking,
            'C_HAS_POOL'     => !empty($event->get_pool()),

            'HEADER_CATEGORY'          => $category->get_name(),
            'U_HEADER_CATEGORY'        => ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel(),
            'HEADER_MASTER_DIVISION'   => ScmEventService::get_master_division($event->get_id()),
            'HEADER_MASTER_SEASON'     => ScmEventService::get_master_season($event->get_id()),
            'HEADER_DIVISION'          => $division['division_name'],
            'HEADER_SEASON'            => $season['season_name'],
            'POOL'                     => $event->get_pool(),

            'U_HOME'         => ScmUrlBuilder::home()->rel(),
            'U_EVENT_HOME'   => ScmUrlBuilder::event_home($event_id, $event->get_event_slug())->rel(),
            'U_EVENT_MASTER' => ScmEventService::get_master_url($event->get_id()),

            'U_ROUND_GROUPS'   => ScmUrlBuilder::display_groups_rounds($event_id, $event->get_event_slug(), $active_round)->rel(),
            'U_ROUND_BRACKETS' => ScmUrlBuilder::display_brackets_rounds($event_id, $event->get_event_slug())->rel(),
            'U_PRACTICE'       => ScmUrlBuilder::display_practice($event_id, $event->get_event_slug())->rel(),
            'U_DAY_CALENDAR'   => ScmUrlBuilder::display_day_calendar($event_id, $event->get_event_slug(), $active_round)->rel(),
            'U_DAYS_CALENDAR'  => ScmUrlBuilder::display_days_calendar($event_id, $event->get_event_slug())->rel(),
            'U_DAYS_RANKING'   => ScmUrlBuilder::display_days_ranking($event_id, $event->get_event_slug())->rel(),

            'U_EVENT_EDIT'          => ScmUrlBuilder::edit($event_id, $event->get_event_slug())->rel(),
            'U_EDIT_TEAMS'          => ScmUrlBuilder::edit_teams($event_id, $event->get_event_slug())->rel(),
            'U_EDIT_PARAMS'         => ScmUrlBuilder::edit_params($event_id, $event->get_event_slug())->rel(),
            'U_EDIT_DAYS'           => ScmUrlBuilder::edit_days($event_id, $event->get_event_slug())->rel(),
            'U_EDIT_DAYS_GAMES'     => ScmUrlBuilder::edit_days_games($event_id, $event->get_event_slug(), $active_round)->rel(),
            'U_CHECK_DAYS'          => ScmUrlBuilder::days_checker($event_id, $event->get_event_slug())->rel(),
            'U_EDIT_GROUPS'         => ScmUrlBuilder::edit_groups($event_id, $event->get_event_slug())->rel(),
            'U_EDIT_GROUPS_GAMES'   => ScmUrlBuilder::edit_groups_games($event_id, $event->get_event_slug(), $active_round)->rel(),
            'U_EDIT_BRACKET'        => ScmUrlBuilder::edit_brackets($event_id, $event->get_event_slug())->rel(),
            'U_EDIT_BRACKET_GAMES'  => ScmUrlBuilder::edit_brackets_games($event_id, $event->get_event_slug(), $bracket_round)->rel(),
            'U_EDIT_PRACTICE'       => ScmUrlBuilder::edit_practice($event_id, $event->get_event_slug())->rel(),
            'U_EDIT_PRACTICE_GAMES' => ScmUrlBuilder::edit_practice_games($event_id, $event->get_event_slug())->rel(),
        ]);

        foreach (ScmEventService::get_sub_list($event_id) as $sub_event)
        {
            $item = new ScmEvent();
            $item->set_properties($sub_event);

            $view->assign_block_vars('sub_events', $item->get_template_vars());
        }

        foreach ($event->get_sources() as $name => $url)
		{
			$view->assign_block_vars('sources', $event->get_array_tpl_source_vars($name));
		}

        if ($c_has_games)
        {
            $current_url      = $_SERVER['REQUEST_URI'];
            $c_edit_days      = self::compare_url($current_url) == self::compare_url(ScmUrlBuilder::edit_days_games($event_id, $event->get_event_slug(), $round)->rel());
            $c_display_days   = self::compare_url($current_url) == self::compare_url(ScmUrlBuilder::display_day_calendar($event_id, $event->get_event_slug(), $round)->rel());
            $c_edit_groups    = self::compare_url($current_url) == self::compare_url(ScmUrlBuilder::edit_groups_games($event_id, $event->get_event_slug(), $round)->rel());
            $c_display_groups = self::compare_url($current_url) == self::compare_url(ScmUrlBuilder::display_groups_rounds($event_id, $event->get_event_slug(), $round)->rel());
            $c_edit_brackets  = self::compare_url($current_url) == self::compare_url(ScmUrlBuilder::edit_brackets_games($event_id, $event->get_event_slug(), $round)->rel());
            $c_edit_practice  = self::compare_url($current_url) == self::compare_url(ScmUrlBuilder::edit_practice_games($event_id, $event->get_event_slug())->rel());

            $view->put_all([
                'C_EDIT_DAYS_GAMES'     => $c_edit_days,
                'C_DAYS_GAMES'          => $c_display_days,
                'C_EDIT_GROUPS_GAMES'   => $c_edit_groups,
                'C_GROUPS_GAMES'        => $c_display_groups,
                'C_EDIT_BRACKETS_GAMES' => $c_edit_brackets,
                'C_EDIT_PRACTICE_GAMES' => $c_edit_practice,
            ]);

            $groups = [];
            foreach (ScmGameService::get_games($event_id) as $game)
            {
                $groups[] = $game['game_type'] . '|' . $game['game_cluster'];
            }
            $array_groups = array_unique($groups);
            usort($array_groups, function($a, $b) {
                $aParts = explode('|', $a);
                $bParts = explode('|', $b);
                // Sort 'G' first, then 'B'
                if ($aParts[0] != $bParts[0]) {
                    if ($aParts[0] == 'D') return -1;
                    if ($bParts[0] == 'D') return 1;
                    if ($aParts[0] == 'G') return -1;
                    if ($bParts[0] == 'G') return 1;
                    if ($aParts[0] == 'B') return -1;
                    if ($bParts[0] == 'B') return 1;
                }

                // Sort 'G' ascending || 'D' ascending
                if ($aParts[0] == 'G' || $aParts[0] == 'D') {
                    if ($aParts[1] == $bParts[1]) return 0;
                    return ($aParts[1] < $bParts[1]) ? -1 : 1;
                } else {
                    if ($aParts[1] == $bParts[1]) return 0;
                    return ($aParts[1] > $bParts[1]) ? -1 : 1;
                }
            });

            $c_hat_ranking = $params->get_hat_ranking();
            $rounds_number = $params->get_rounds_number();

            foreach ($array_groups as $group)
            {
                $group_details = explode('|', $group);
                $c_days = $c_groups = $c_brackets = false;
                switch ($group_details[0]) {
                    case 'D':
                        $c_days = true;
                        $type = $lang['scm.day'];
                        break;
                    case 'G':
                        $c_groups = true;
                        $type = $c_hat_ranking ? $lang['scm.day'] : $lang['scm.group'];
                        break;
                    case 'B':
                        $c_brackets = true;
                        $type = $lang['scm.round'];
                        break;
                    default : 
                        $type = $lang['scm.group'];
                        break;
                }

                if ($c_edit_days && $c_days) {
                    $view->assign_block_vars('days', [
                        'L_TYPE' => $type,
                        'NUMBER' => $group_details[1],
                        'U_DAY'  => ScmUrlBuilder::edit_days_games($event_id, $event->get_event_slug(), $group_details[1])->rel()
                    ]);
                }
                elseif ($c_display_days && $c_days) {
                    $view->assign_block_vars('days', [
                        'L_TYPE' => $type,
                        'NUMBER' => $group_details[1],
                        'U_DAY'  => ScmUrlBuilder::display_day_calendar($event_id, $event->get_event_slug(), $group_details[1])->rel()
                    ]);
                }
                elseif ($c_edit_groups && $c_groups) {
                    $view->assign_block_vars('groups', [
                        'L_TYPE'  => $type,
                        'NUMBER'  => $c_hat_ranking ? $group_details[1] : ScmGroupService::ntl($group_details[1]),
                        'U_GROUP' => ScmUrlBuilder::edit_groups_games($event_id, $event->get_event_slug(), $group_details[1])->rel()
                    ]);
                }
                elseif ($c_display_groups && $c_groups) {
                    $view->assign_block_vars('groups', [
                        'L_TYPE'  => $type,
                        'NUMBER'  => $c_hat_ranking ? $group_details[1] : ScmGroupService::ntl($group_details[1]),
                        'U_GROUP' => ScmUrlBuilder::display_groups_rounds($event_id, $event->get_event_slug(), $group_details[1])->rel()
                    ]);
                }
                elseif ($c_edit_brackets && $c_brackets) {
                    $view->assign_block_vars('bracket', [
                        'BRACKET_ROUND' => $c_hat_ranking && ($group_details[1] == $rounds_number + 1) ? $lang['scm.round.playoff'] : ($c_finals_ranking ? $lang['scm.group'] . ' ' . $group_details[1] : $lang['scm.round.' . $group_details[1] . '']),
                        'U_BRACKET'     => ScmUrlBuilder::edit_brackets_games($event_id, $event->get_event_slug(), $group_details[1])->rel()
                    ]);
                }
            }
        }
		return $view;
    }

    private static function compare_url($url)
    {
        $parsed_url = parse_url($url);
        $path_parts = explode('/', $parsed_url['path']);
        array_pop($path_parts);

        $new_path = implode('/', $path_parts);
        $new_url  = str_replace($parsed_url['path'], $new_path, $url);

        return $new_url;
    }
}
?>
