<?php
/*##################################################
 *		    ModlistTreeLinks.class.php
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

class ModlistTreeLinks implements ModuleTreeLinksExtensionPoint
{
	public function get_actions_tree_links()
	{
		$lang = LangLoader::get('common', 'modlist');
		$tree = new ModuleTreeLinks();

		$manage_categories_link = new ModuleLink(LangLoader::get_message('categories.manage', 'categories-common'), ModlistUrlBuilder::manage_categories(), ModlistAuthorizationsService::check_authorizations()->manage_categories());
		$manage_categories_link->add_sub_link(new ModuleLink(LangLoader::get_message('categories.manage', 'categories-common'), ModlistUrlBuilder::manage_categories(), ModlistAuthorizationsService::check_authorizations()->manage_categories()));
		$manage_categories_link->add_sub_link(new ModuleLink(LangLoader::get_message('category.add', 'categories-common'), ModlistUrlBuilder::add_category(AppContext::get_request()->get_getint('category_id', Category::ROOT_CATEGORY)), ModlistAuthorizationsService::check_authorizations()->manage_categories()));
		$tree->add_link($manage_categories_link);

		$manage_modlist_link = new ModuleLink($lang['modlist.management'], ModlistUrlBuilder::manage_items(), ModlistAuthorizationsService::check_authorizations()->moderation());
		$manage_modlist_link->add_sub_link(new ModuleLink($lang['modlist.management'], ModlistUrlBuilder::manage_items(), ModlistAuthorizationsService::check_authorizations()->moderation()));
		$manage_modlist_link->add_sub_link(new ModuleLink($lang['modlist.add'], ModlistUrlBuilder::add_item(AppContext::get_request()->get_getint('category_id', Category::ROOT_CATEGORY)), ModlistAuthorizationsService::check_authorizations()->moderation()));
		$tree->add_link($manage_modlist_link);

		$tree->add_link(new AdminModuleLink(LangLoader::get_message('configuration', 'admin-common'), ModlistUrlBuilder::configuration()));

		if (!ModlistAuthorizationsService::check_authorizations()->moderation())
		{
			$tree->add_link(new ModuleLink($lang['modlist.add'], ModlistUrlBuilder::add_item(AppContext::get_request()->get_getint('category_id', Category::ROOT_CATEGORY)), ModlistAuthorizationsService::check_authorizations()->write() || ModlistAuthorizationsService::check_authorizations()->contribution()));
		}

		$tree->add_link(new ModuleLink($lang['modlist.pending.items'], ModlistUrlBuilder::display_pending_items(), ModlistAuthorizationsService::check_authorizations()->write() || ModlistAuthorizationsService::check_authorizations()->contribution() || ModlistAuthorizationsService::check_authorizations()->moderation()));

		$tree->add_link(new ModuleLink(LangLoader::get_message('module.documentation', 'admin-modules-common'), ModulesManager::get_module('modlist')->get_configuration()->get_documentation(), ModlistAuthorizationsService::check_authorizations()->write() || ModlistAuthorizationsService::check_authorizations()->contribution() || ModlistAuthorizationsService::check_authorizations()->moderation()));

		return $tree;
	}
}
?>
