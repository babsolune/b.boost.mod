<?php
/*##################################################
 *		    ModmixTreeLinks.class.php
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

class ModmixTreeLinks implements ModuleTreeLinksExtensionPoint
{
	public function get_actions_tree_links()
	{
		$lang = LangLoader::get('common', 'modmix');
		$tree = new ModuleTreeLinks();

		$manage_categories_link = new ModuleLink(LangLoader::get_message('categories.manage', 'categories-common'), ModmixUrlBuilder::manage_categories(), ModmixAuthorizationsService::check_authorizations()->manage_categories());
		$manage_categories_link->add_sub_link(new ModuleLink(LangLoader::get_message('categories.manage', 'categories-common'), ModmixUrlBuilder::manage_categories(), ModmixAuthorizationsService::check_authorizations()->manage_categories()));
		$manage_categories_link->add_sub_link(new ModuleLink(LangLoader::get_message('category.add', 'categories-common'), ModmixUrlBuilder::add_category(AppContext::get_request()->get_getint('category_id', Category::ROOT_CATEGORY)), ModmixAuthorizationsService::check_authorizations()->manage_categories()));
		$tree->add_link($manage_categories_link);

		$manage_modmix_link = new ModuleLink($lang['modmix.management'], ModmixUrlBuilder::manage_items(), ModmixAuthorizationsService::check_authorizations()->moderation());
		$manage_modmix_link->add_sub_link(new ModuleLink($lang['modmix.management'], ModmixUrlBuilder::manage_items(), ModmixAuthorizationsService::check_authorizations()->moderation()));
		$manage_modmix_link->add_sub_link(new ModuleLink($lang['modmix.add'], ModmixUrlBuilder::add_item(AppContext::get_request()->get_getint('category_id', Category::ROOT_CATEGORY)), ModmixAuthorizationsService::check_authorizations()->moderation()));
		$tree->add_link($manage_modmix_link);

		$tree->add_link(new AdminModuleLink(LangLoader::get_message('configuration', 'admin-common'), ModmixUrlBuilder::configuration()));

		if (!ModmixAuthorizationsService::check_authorizations()->moderation())
		{
			$tree->add_link(new ModuleLink($lang['modmix.add'], ModmixUrlBuilder::add_item(AppContext::get_request()->get_getint('category_id', Category::ROOT_CATEGORY)), ModmixAuthorizationsService::check_authorizations()->write() || ModmixAuthorizationsService::check_authorizations()->contribution()));
		}

		$tree->add_link(new ModuleLink($lang['modmix.pending.items'], ModmixUrlBuilder::display_pending_items(), ModmixAuthorizationsService::check_authorizations()->write() || ModmixAuthorizationsService::check_authorizations()->contribution() || ModmixAuthorizationsService::check_authorizations()->moderation()));

		$tree->add_link(new ModuleLink(LangLoader::get_message('module.documentation', 'admin-modules-common'), ModulesManager::get_module('modmix')->get_configuration()->get_documentation(), ModmixAuthorizationsService::check_authorizations()->write() || ModmixAuthorizationsService::check_authorizations()->contribution() || ModmixAuthorizationsService::check_authorizations()->moderation()));

		return $tree;
	}
}
?>
