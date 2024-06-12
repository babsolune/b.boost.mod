<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballTreeLinks implements ModuleTreeLinksExtensionPoint
{
	public function get_actions_tree_links()
	{
		$module_id = 'football';
		$current_user = AppContext::get_current_user()->get_id();

		$lang = LangLoader::get_all_langs($module_id);
		$tree = new ModuleTreeLinks();

		$categories = new ModuleLink($lang['category.categories.management'], CategoriesUrlBuilder::manage($module_id), FootballAuthorizationsService::check_authorizations()->manage_compets());
			$categories->add_sub_link(new ModuleLink($lang['category.add'], CategoriesUrlBuilder::add(AppContext::get_request()->get_getint('id_category', Category::ROOT_CATEGORY), $module_id), FootballAuthorizationsService::check_authorizations()->manage_compets()));
        $tree->add_link($categories);

		$compet = new ModuleLink($lang['football.compets.management'], FootballUrlBuilder::manage(), FootballAuthorizationsService::check_authorizations()->manage_compets());
			$compet->add_sub_link(new ModuleLink($lang['football.add.compet'], FootballUrlBuilder::add()));
		$tree->add_link($compet);

        $club = new ModuleLink($lang['football.clubs.manager'], FootballUrlBuilder::manage_clubs(), FootballAuthorizationsService::check_authorizations()->manage_clubs());
            $club->add_sub_link(new ModuleLink($lang['football.add.club'], FootballUrlBuilder::add_club(), FootballAuthorizationsService::check_authorizations()->manage_clubs()));
        $tree->add_link($club);

        $division = new ModuleLink($lang['football.divisions.manager'], FootballUrlBuilder::manage_divisions(), FootballAuthorizationsService::check_authorizations()->manage_divisions());
            $division->add_sub_link(new ModuleLink($lang['football.add.division'], FootballUrlBuilder::add_division(), FootballAuthorizationsService::check_authorizations()->manage_divisions()));
        $tree->add_link($division);

        $season = new ModuleLink($lang['football.seasons.manager'], FootballUrlBuilder::manage_seasons(), FootballAuthorizationsService::check_authorizations()->manage_seasons());
            $season->add_sub_link(new ModuleLink($lang['football.add.season'], FootballUrlBuilder::add_season(), FootballAuthorizationsService::check_authorizations()->manage_seasons()));
        $tree->add_link($season);

		$tree->add_link(new AdminModuleLink($lang['form.configuration'], FootballUrlBuilder::configuration()));

		if (ModulesManager::get_module($module_id)->get_configuration()->get_documentation())
			$tree->add_link(new ModuleLink($lang['form.documentation'], ModulesManager::get_module('football')->get_configuration()->get_documentation(), FootballAuthorizationsService::check_authorizations()->moderation()));

		return $tree;
	}
}
?>
