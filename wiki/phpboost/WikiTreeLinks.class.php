<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 10 11
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class WikiTreeLinks extends DefaultTreeLinks
{
	protected function get_module_additional_actions_tree_links(&$tree)
	{
		$module_id = 'wiki';
		$current_user = AppContext::get_current_user()->get_id();
        $config = WikiConfig::load();

		$tree->add_link(new ModuleLink(LangLoader::get_message('wiki.my.items', 'common', $module_id), WikiUrlBuilder::display_member_items($current_user), WikiAuthorizationsService::check_authorizations()->write() || WikiAuthorizationsService::check_authorizations()->contribution() || WikiAuthorizationsService::check_authorizations()->moderation()));
		$tree->add_link(new ModuleLink(LangLoader::get_message('wiki.my.tracked', 'common', $module_id), WikiUrlBuilder::tracked_member_items($current_user), WikiAuthorizationsService::check_authorizations()->write() || WikiAuthorizationsService::check_authorizations()->contribution() || WikiAuthorizationsService::check_authorizations()->moderation()));

        $tree->add_link(new ModuleLink(LangLoader::get_message('wiki.overview', 'common', $module_id), $config->get_homepage() !== WikiConfig::OVERVIEW && WikiUrlBuilder::overview()));
		$tree->add_link(new ModuleLink(LangLoader::get_message('wiki.explorer', 'common', $module_id), $config->get_homepage() !== WikiConfig::EXPLORER && WikiUrlBuilder::explorer()));
	}
}
?>
