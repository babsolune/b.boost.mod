<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/init.php';

$url_controller_mappers = array(
	// Configuration
	new UrlControllerMapper('AdminClubsConfigController', '`^/admin(?:/config)?/?$`'),

	// Categories
	new UrlControllerMapper('DefaultCategoriesManagementController', '`^/categories/?$`'),
	new UrlControllerMapper('DefaultCategoriesFormController', '`^/categories/add/?([0-9]+)?/?$`', array('id_parent')),
	new UrlControllerMapper('DefaultCategoriesFormController', '`^/categories/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('DefaultDeleteCategoryController', '`^/categories/([0-9]+)/delete/?$`', array('id')),

	// Item
	new UrlControllerMapper('ClubsItemsManagerController', '`^/manage/?$`'),
	new UrlControllerMapper('ClubsItemFormController', '`^/add/?([0-9]+)?/?$`', array('id_category')),
	new UrlControllerMapper('ClubsItemFormController', '`^/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('ClubsDeleteItemController', '`^/([0-9]+)/delete/?$`', array('id')),
	new UrlControllerMapper('ClubsItemController', '`^/([0-9]+)-([a-z0-9-_]+)/([0-9]+)-([a-z0-9-_]+)?/?$`', array('id_category', 'rewrited_name_category', 'id', 'rewrited_name')),
	new UrlControllerMapper('ClubsVisitItemController', '`^/visit/([0-9]+)/?$`', array('id')),
	new UrlControllerMapper('ClubsDeadLinkController', '`^/dead_link/([0-9]+)/?$`', array('id')),

	// Contributions
	new UrlControllerMapper('ClubsPendingItemsController', '`^/pending(?:/([a-z]+))?/?([a-z]+)?/?([0-9]+)?/?$`', array('field', 'sort', 'page')),
	new UrlControllerMapper('ClubsMemberItemsController', '`^/member/([0-9]+)?/?([0-9]+)?/?$`', array('user_id', 'page')),

	// Homepage
	new UrlControllerMapper('ClubsCategoryController', '`^(?:/([0-9]+)-([a-z0-9-_]+))?/?([a-z]+)?/?([a-z]+)?/?([0-9]+)?/?([0-9]+)?/?$`', array('id_category', 'rewrited_name', 'field', 'sort', 'page', 'subcategories_page'))
);
DispatchManager::dispatch($url_controller_mappers);
?>
