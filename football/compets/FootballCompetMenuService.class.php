<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballCompetMenuService
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

        $view = new FileTemplate('football/FootballCompetMenuController.tpl');
        $view->add_lang(LangLoader::get_all_langs('football'));

        $id = AppContext::get_request()->get_getint('id', 0);
        $has_group_params = FootballParamsService::get_params($id)->get_teams_per_group() !== '0';
        $has_teams = count(FootballTeamService::get_teams($id)) > 0;
        $has_matches = count(FootballMatchService::get_matches($id)) > 0;

        $view->put_all(array(
            'C_AUTH' => CategoriesAuthorizationsService::check_authorizations()->contribution(),
            'C_CHAMPIONSHIP' => $division['division_compet_type'] == FootballDivision::CHAMPIONSHIP,
            'C_CUP' => $division['division_compet_type'] == FootballDivision::CUP,
            'C_TOURNEY' => $division['division_compet_type'] == FootballDivision::TOURNEY,
            'C_HAS_GROUP_PARAMS' => $has_group_params,
            'C_HAS_TEAMS' => $has_teams,
            'C_HAS_MATCHES' => $has_matches,

            'HEADER' => $category->get_name() . ' - ' . FootballDivisionService::get_compet_type_lang($compet->get_compet_division_id()) . ' - ' . FootballCompetService::get_compet($compet_id)->get_compet_name(),

            'U_COMPET' => FootballUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $compet->get_id_compet(), $compet->get_compet_slug())->rel(),

            'U_PARAMS' => FootballUrlBuilder::params($compet_id)->rel(),
            'U_GROUPS' => FootballUrlBuilder::groups($compet_id)->rel(),
            'U_MATCHES' => FootballUrlBuilder::matches($compet_id)->rel(),
            'U_RESULTS' => FootballUrlBuilder::results($compet_id)->rel(),
            'U_TEAMS' => FootballUrlBuilder::teams($compet_id)->rel(),
            'U_GROUPS_STAGE' => FootballUrlBuilder::groups_stage($compet_id)->rel(),
            'U_FINALS_STAGE' => FootballUrlBuilder::finals_stage($compet_id)->rel(),
        ));

		return $view;
    }
}
?>
