<?php
/*##################################################
 *                           index.php
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

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/init.php';

$url_controller_mappers = array(
	//Config
	new UrlControllerMapper('AdminModcatConfigController', '`^/admin(?:/config)?/?$`'),

	//Manage categories
	new UrlControllerMapper('ModcatCategoriesManagerController', '`^/categories/?$`'),
	new UrlControllerMapper('ModcatCategoriesFormController', '`^/categories/add/?([0-9]+)?/?$`', array('id_parent')),
	new UrlControllerMapper('ModcatCategoriesFormController', '`^/categories/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('ModcatDeleteCategoryController', '`^/categories/([0-9]+)/delete/?$`', array('id')),

	//Manage items
	new UrlControllerMapper('ModcatItemsManagerController', '`^/manage/?$`'),
	new UrlControllerMapper('ModcatItemFormController', '`^/add/?([0-9]+)?/?$`', array('category_id')),
	new UrlControllerMapper('ModcatItemFormController', '`^(?:/([0-9]+))/edit/?([0-9]+)?/?$`', array('id', 'page')),
	new UrlControllerMapper('ModcatDeleteItemController', '`^/([0-9]+)/delete/?$`', array('id')),

	//Display items
	new UrlControllerMapper('ModcatDisplayTagController', '`^/tag(?:/([a-z0-9-_]+))?/?([a-z]+)?/?([a-z]+)?/?([0-9]+)?/?$`', array('tag', 'field', 'sort', 'page')),
	new UrlControllerMapper('ModcatDisplayPendingItemsController', '`^/pending(?:/([a-z]+))?/?([a-z]+)?/?([0-9]+)?/?$`', array('field', 'sort', 'page')),
	new UrlControllerMapper('ModcatDisplayItemController', '`^(?:/([0-9]+)-([a-z0-9-_]+)/([0-9]+)-([a-z0-9-_]+))/?([0-9]+)?/?$`', array('category_id', 'rewrited_name_category', 'id', 'rewrited_title', 'page')),

	//Utilities
	new UrlControllerMapper('ModcatPrintItemController', '`^/print/([0-9]+)-([a-z0-9-_]+)/?$`', array('id', 'rewrited_title')),

	//Display home and categories
	new UrlControllerMapper('ModcatDisplayCategoryController', '`^(?:/([0-9]+)-([a-z0-9-_]+))?/?([a-z]+)?/?([a-z]+)?/?([0-9]+)?/?([0-9]+)?/?$`', array('category_id', 'rewrited_name', 'field', 'sort', 'page', 'subcategories_page'))
);

DispatchManager::dispatch($url_controller_mappers);

?>
