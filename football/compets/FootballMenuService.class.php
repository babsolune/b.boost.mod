<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballMenuService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	public static function build_compet_menu($compet_id, $round = 1)
	{
        $lang = LangLoader::get_all_langs('football');
        $compet = FootballCompetService::get_compet($compet_id);
        $category = CategoriesService::get_categories_manager()->get_categories_cache()->get_category($compet->get_id_category());
        $division = FootballDivisionCache::load()->get_division($compet->get_compet_division_id());
        $season = FootballSeasonCache::load()->get_season($compet->get_compet_season_id());

        $view = new FileTemplate('football/FootballMenuController.tpl');
        $view->add_lang(LangLoader::get_all_langs('football'));

        $c_has_teams   = count(FootballTeamService::get_teams($compet_id)) > 0  && count(FootballTeamService::get_teams($compet_id)) % 2 == 0;
        $c_has_days    = FootballParamsService::get_params($compet_id)->get_victory_points();
        $c_has_rounds  = FootballParamsService::get_params($compet_id)->get_rounds_number();
        $c_has_groups  = FootballParamsService::get_params($compet_id)->get_teams_per_group();
        $c_has_matches = FootballMatchService::has_matches($compet_id);
        $bracket_round = FootballParamsService::get_params($compet_id)->get_hat_ranking() ? FootballParamsService::get_params($compet_id)->get_rounds_number() + 1 : FootballParamsService::get_params($compet_id)->get_rounds_number();

        $view->put_all(array(
            'C_CONTROLS'     => FootballAuthorizationsService::check_authorizations($category->get_id())->manage_compets(),
            'C_CHAMPIONSHIP' => $division['division_compet_type'] == FootballDivision::CHAMPIONSHIP,
            'C_CUP'          => $division['division_compet_type'] == FootballDivision::CUP,
            'C_TOURNAMENT'   => $division['division_compet_type'] == FootballDivision::TOURNAMENT,
            'C_HAS_TEAMS'    => $c_has_teams,
            'C_HAS_DAYS'     => $c_has_days,
            'C_HAS_ROUNDS'   => $c_has_rounds,
            'C_HAS_GROUPS'   => $c_has_groups,
            'C_HAS_MATCHES'  => $c_has_matches,
            'C_ONE_DAY'      => FootballMatchService::one_day_compet($compet_id),

            'HEADER_CATEGORY' => $category->get_name(),
            'HEADER_TYPE'     => FootballDivisionService::get_compet_type_lang($compet->get_compet_division_id()),
            'HEADER_DIVISION' => $division['division_name'],
            'HEADER_SEASON'   => $season['season_name'],

            'U_HOME'                 => FootballUrlBuilder::compet_home($compet->get_id_compet())->rel(),

            'U_ROUND_GROUPS'         => FootballUrlBuilder::display_groups_rounds($compet_id, $round)->rel(),
            'U_ROUND_BRACKETS'       => FootballUrlBuilder::display_brackets_rounds($compet_id)->rel(),
            'U_DAYS_CALENDAR'        => FootballUrlBuilder::display_days_calendar($compet_id, FootballDayService::get_last_day($compet_id))->rel(),
            'U_DAYS_RANKING'         => FootballUrlBuilder::display_days_ranking($compet_id)->rel(),

            'U_EDIT_TEAMS'           => FootballUrlBuilder::edit_teams($compet_id)->rel(),
            'U_EDIT_PARAMS'          => FootballUrlBuilder::edit_params($compet_id)->rel(),
            'U_EDIT_DAYS'            => FootballUrlBuilder::edit_days($compet_id)->rel(),
            'U_EDIT_DAYS_MATCHES'    => FootballUrlBuilder::edit_days_matches($compet_id, FootballDayService::get_last_day($compet_id))->rel(),
            'U_EDIT_GROUPS'          => FootballUrlBuilder::edit_groups($compet_id)->rel(),
            'U_EDIT_GROUPS_MATCHES'  => FootballUrlBuilder::edit_groups_matches($compet_id, 1)->rel(),
            'U_EDIT_BRACKET'         => FootballUrlBuilder::edit_brackets($compet_id)->rel(),
            'U_EDIT_BRACKET_MATCHES' => FootballUrlBuilder::edit_brackets_matches($compet_id, $bracket_round)->rel(),            'HEADER_CATEGORY' => $category->get_name(),
            'HEADER_TYPE'            => FootballDivisionService::get_compet_type_lang($compet->get_compet_division_id()),
            'HEADER_DIVISION'        => $division['division_name'],
            'HEADER_SEASON'          => $season['season_name'],
        ));

        if ($c_has_matches)
        {
            $current_url      = $_SERVER['REQUEST_URI'];
            $c_edit_days      = self::compare_url($current_url) == self::compare_url(FootballUrlBuilder::edit_days_matches($compet_id, $round)->rel());
            $c_display_days   = self::compare_url($current_url) == self::compare_url(FootballUrlBuilder::display_days_calendar($compet_id, $round)->rel());
            $c_edit_groups    = self::compare_url($current_url) == self::compare_url(FootballUrlBuilder::edit_groups_matches($compet_id, $round)->rel());
            $c_display_groups = self::compare_url($current_url) == self::compare_url(FootballUrlBuilder::display_groups_rounds($compet_id, $round)->rel());
            $c_edit_brackets  = self::compare_url($current_url) == self::compare_url(FootballUrlBuilder::edit_brackets_matches($compet_id, $round)->rel());

            $view->put_all(array(
                'C_EDIT_DAYS_MATCHES'     => $c_edit_days,
                'C_DAYS_MATCHES'          => $c_display_days,
                'C_EDIT_GROUPS_MATCHES'   => $c_edit_groups,
                'C_GROUPS_MATCHES'        => $c_display_groups,
                'C_EDIT_BRACKETS_MATCHES' => $c_edit_brackets,
            ));

            $groups = [];
            foreach (FootballMatchService::get_matches($compet_id) as $match)
            {
                $groups[] = $match['match_type'] . '|' . $match['match_group'];
            }
            $array_groups = array_unique($groups);
            usort($array_groups, function($a, $b) {
                $aParts = explode('|', $a);
                $bParts = explode('|', $b);
                // Sort 'G' first, then 'L', then 'W'
                if ($aParts[0] != $bParts[0]) {
                    if ($aParts[0] == 'D') return -1;
                    if ($bParts[0] == 'D') return 1;
                    if ($aParts[0] == 'G') return -1;
                    if ($bParts[0] == 'G') return 1;
                    if ($aParts[0] == 'L') return -1;
                    if ($bParts[0] == 'L') return 1;
                }

                // Sort 'L' and 'W' descending, 'G' ascending || 'D' ascending
                if ($aParts[0] == 'G' || $aParts[0] == 'D') {
                    if ($aParts[1] == $bParts[1]) return 0;
                    return ($aParts[1] < $bParts[1]) ? -1 : 1;
                } else {
                    if ($aParts[1] == $bParts[1]) return 0;
                    return ($aParts[1] > $bParts[1]) ? -1 : 1;
                }
            });

            $c_hat_ranking = FootballParamsService::get_params($compet_id)->get_hat_ranking();
            $rounds_number = FootballParamsService::get_params($compet_id)->get_rounds_number();

            foreach ($array_groups as $group)
            {
                $group_details = explode('|', $group);
                $c_days = $c_groups = $c_brackets = false;
                switch ($group_details[0]) {
                    case 'D':
                        $c_days = true;
                        $type = $lang['football.day'];
                        break;
                    case 'G':
                        $c_groups = true;
                        $type = $c_hat_ranking ? $lang['football.day'] : $lang['football.group'];
                        break;
                    case 'W':
                        $c_brackets = true;
                        $type = $lang['football.round'];
                        break;
                    default : 
                        $type = $lang['football.group'];
                        break;
                }

                if ($c_edit_days && $c_days)
                    $view->assign_block_vars('days', array(
                        'L_TYPE' => $type,
                        'NUMBER' => $group_details[1],
                        'U_DAY'  => FootballUrlBuilder::edit_days_matches($compet_id, $group_details[1])->rel()
                    ));
                elseif ($c_display_days && $c_days)
                    $view->assign_block_vars('days', array(
                        'L_TYPE' => $type,
                        'NUMBER' => $group_details[1],
                        'U_DAY'  => FootballUrlBuilder::display_days_calendar($compet_id, $group_details[1])->rel()
                    ));
                elseif ($c_edit_groups && $c_groups)
                    $view->assign_block_vars('groups', array(
                        'L_TYPE'  => $type,
                        'NUMBER'  => $c_hat_ranking ? $group_details[1] : FootballGroupService::ntl($group_details[1]),
                        'U_GROUP' => FootballUrlBuilder::edit_groups_matches($compet_id, $group_details[1])->rel()
                    ));
                elseif ($c_display_groups && $c_groups)
                    $view->assign_block_vars('groups', array(
                        'L_TYPE'  => $type,
                        'NUMBER'  => $c_hat_ranking ? $group_details[1] : FootballGroupService::ntl($group_details[1]),
                        'U_GROUP' => FootballUrlBuilder::display_groups_rounds($compet_id, $group_details[1])->rel()
                    ));
                elseif ($c_edit_brackets && $c_brackets)
                    $view->assign_block_vars('bracket', array(
                        'BRACKET_ROUND' => $c_hat_ranking && ($group_details[1] == $rounds_number + 1) ? $lang['football.round.playoff'] : $lang['football.round.' . $group_details[1] . ''],
                        'U_BRACKET'     => FootballUrlBuilder::edit_brackets_matches($compet_id, $group_details[1])->rel()
                    ));
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
