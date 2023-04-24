<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class DocsheetTreeLinks extends DefaultTreeLinks
{
	protected function get_module_additional_actions_tree_links(&$tree)
	{
		$module_id = 'docsheet';
		$current_user = AppContext::get_current_user()->get_id();

		$tree->add_link(new ModuleLink(LangLoader::get_message('docsheet.my.items', 'common', $module_id), DocsheetUrlBuilder::display_member_items($current_user), DocsheetAuthorizationsService::check_authorizations()->write() || DocsheetAuthorizationsService::check_authorizations()->contribution() || DocsheetAuthorizationsService::check_authorizations()->moderation()));
		$tree->add_link(new ModuleLink(LangLoader::get_message('docsheet.my.tracked', 'common', $module_id), DocsheetUrlBuilder::tracked_member_items($current_user), DocsheetAuthorizationsService::check_authorizations()->write() || DocsheetAuthorizationsService::check_authorizations()->contribution() || DocsheetAuthorizationsService::check_authorizations()->moderation()));
		$tree->add_link(new ModuleLink(LangLoader::get_message('docsheet.explorer', 'common', $module_id), DocsheetUrlBuilder::explorer()));
	}
}
?>
