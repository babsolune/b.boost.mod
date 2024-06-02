<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballMenuService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	public static function build_compet_menu($compet_id)
	{
        $compet = FootballCompetService::get_compet($compet_id);
        $category = CategoriesService::get_categories_manager()->get_categories_cache()->get_category($compet->get_id_category());
        $division = FootballDivisionCache::load()->get_division($compet->get_compet_division_id());

        $view = new FileTemplate('football/FootballMenuController.tpl');
        $view->add_lang(LangLoader::get_all_langs('football'));

        $has_group_params = FootballParamsService::get_params($compet_id)->get_teams_per_group();
        $has_teams = count(FootballTeamService::get_teams($compet_id)) > 0  && count(FootballTeamService::get_teams($compet_id)) % 2 == 0;
        
        $view->put_all(array(
            'C_CONTROLS' => FootballAuthorizationsService::check_authorizations($category->get_id())->manage_compets(),
            'C_CHAMPIONSHIP' => $division['division_compet_type'] == FootballDivision::CHAMPIONSHIP,
            'C_CUP' => $division['division_compet_type'] == FootballDivision::CUP,
            'C_TOURNAMENT' => $division['division_compet_type'] == FootballDivision::TOURNAMENT,
            'C_HAS_GROUP_PARAMS' => $has_group_params,
            'C_HAS_TEAMS' => $has_teams,
            'C_HAS_MATCHES' => FootballMatchService::has_matches($compet_id),

            'HEADER_CATEGORY' => $category->get_name(),
            'HEADER_TYPE' => FootballDivisionService::get_compet_type_lang($compet->get_compet_division_id()),
            'HEADER_NAME' => FootballCompetService::get_compet($compet_id)->get_compet_name(),

            'U_CALENDAR' => FootballUrlBuilder::calendar($compet->get_id_compet())->rel(),

            'U_GROUPS_STAGE' => FootballUrlBuilder::display_groups_stage($compet_id)->rel(),
            'U_FINALS_STAGE' => FootballUrlBuilder::display_bracket_stage($compet_id)->rel(),

            'U_SETUP_TEAMS' => FootballUrlBuilder::teams($compet_id)->rel(),
            'U_SETUP_PARAMS' => FootballUrlBuilder::params($compet_id)->rel(),
            'U_SETUP_GROUPS' => FootballUrlBuilder::edit_groups($compet_id)->rel(),
            'U_SETUP_MATCHES' => FootballUrlBuilder::edit_groups_matches($compet_id)->rel(),
            'U_SETUP_BRACKET' => FootballUrlBuilder::edit_bracket($compet_id)->rel(),
        ));

		return $view;
    }
}
?>
