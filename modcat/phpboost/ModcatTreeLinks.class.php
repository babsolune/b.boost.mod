<?php
/*##################################################
 *		    ModcatTreeLinks.class.php
 *                            -------------------
 *   begin                : Month XX, 2017
 *   copyright            : (C) 2017 Firstname LASTNAME
 *   email                : nickname@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

/**
 * @author Firstname LASTNAME <nickname@phpboost.com>
 */

class ModcatTreeLinks implements ModuleTreeLinksExtensionPoint
{
	public function get_actions_tree_links()
	{
		$lang = LangLoader::get('common', 'modcat');
		$tree = new ModuleTreeLinks();

		$manage_categories_link = new ModuleLink(LangLoader::get_message('categories.manage', 'categories-common'), ModcatUrlBuilder::manage_categories(), ModcatAuthorizationsService::check_authorizations()->manage_categories());
		$manage_categories_link->add_sub_link(new ModuleLink(LangLoader::get_message('categories.manage', 'categories-common'), ModcatUrlBuilder::manage_categories(), ModcatAuthorizationsService::check_authorizations()->manage_categories()));
		$manage_categories_link->add_sub_link(new ModuleLink(LangLoader::get_message('category.add', 'categories-common'), ModcatUrlBuilder::add_category(AppContext::get_request()->get_getint('category_id', Category::ROOT_CATEGORY)), ModcatAuthorizationsService::check_authorizations()->manage_categories()));
		$tree->add_link($manage_categories_link);

		$manage_modcat_link = new ModuleLink($lang['modcat.management'], ModcatUrlBuilder::manage_items(), ModcatAuthorizationsService::check_authorizations()->moderation());
		$manage_modcat_link->add_sub_link(new ModuleLink($lang['modcat.management'], ModcatUrlBuilder::manage_items(), ModcatAuthorizationsService::check_authorizations()->moderation()));
		$manage_modcat_link->add_sub_link(new ModuleLink($lang['modcat.add'], ModcatUrlBuilder::add_item(AppContext::get_request()->get_getint('category_id', Category::ROOT_CATEGORY)), ModcatAuthorizationsService::check_authorizations()->moderation()));
		$tree->add_link($manage_modcat_link);

		$tree->add_link(new AdminModuleLink(LangLoader::get_message('configuration', 'admin-common'), ModcatUrlBuilder::configuration()));

		if (!ModcatAuthorizationsService::check_authorizations()->moderation())
		{
			$tree->add_link(new ModuleLink($lang['modcat.add'], ModcatUrlBuilder::add_item(AppContext::get_request()->get_getint('category_id', Category::ROOT_CATEGORY)), ModcatAuthorizationsService::check_authorizations()->write() || ModcatAuthorizationsService::check_authorizations()->contribution()));
		}

		$tree->add_link(new ModuleLink($lang['modcat.pending.items'], ModcatUrlBuilder::display_pending_items(), ModcatAuthorizationsService::check_authorizations()->write() || ModcatAuthorizationsService::check_authorizations()->contribution() || ModcatAuthorizationsService::check_authorizations()->moderation()));

		$tree->add_link(new ModuleLink(LangLoader::get_message('module.documentation', 'admin-modules-common'), ModulesManager::get_module('modcat')->get_configuration()->get_documentation(), ModcatAuthorizationsService::check_authorizations()->write() || ModcatAuthorizationsService::check_authorizations()->contribution() || ModcatAuthorizationsService::check_authorizations()->moderation()));

		return $tree;
	}
}
?>
