<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class DocumentationTreeLinks extends DefaultTreeLinks
{
	protected function get_module_additional_actions_tree_links(&$tree)
	{
		$module_id = 'documentation';
		$current_user = AppContext::get_current_user()->get_id();

		$tree->add_link(new ModuleLink(LangLoader::get_message('documentation.my.items', 'common', $module_id), DocumentationUrlBuilder::display_member_items($current_user), DocumentationAuthorizationsService::check_authorizations()->write() || DocumentationAuthorizationsService::check_authorizations()->contribution() || DocumentationAuthorizationsService::check_authorizations()->moderation()));
	}
}
?>
