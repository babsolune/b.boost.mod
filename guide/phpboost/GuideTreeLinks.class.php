<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class GuideTreeLinks extends DefaultTreeLinks
{
	protected function get_module_additional_actions_tree_links(&$tree)
	{
		$module_id = 'guide';
		$current_user = AppContext::get_current_user()->get_id();

		$tree->add_link(new ModuleLink(LangLoader::get_message('guide.my.items', 'common', $module_id), GuideUrlBuilder::display_member_items($current_user), GuideAuthorizationsService::check_authorizations()->write() || GuideAuthorizationsService::check_authorizations()->contribution() || GuideAuthorizationsService::check_authorizations()->moderation()));
		$tree->add_link(new ModuleLink(LangLoader::get_message('guide.my.tracked', 'common', $module_id), GuideUrlBuilder::tracked_member_items($current_user), GuideAuthorizationsService::check_authorizations()->write() || GuideAuthorizationsService::check_authorizations()->contribution() || GuideAuthorizationsService::check_authorizations()->moderation()));
		$tree->add_link(new ModuleLink(LangLoader::get_message('guide.explorer', 'common', $module_id), GuideUrlBuilder::explorer()));
	}
}
?>
