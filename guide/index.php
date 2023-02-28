<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/init.php';

$url_controller_mappers = array(
	// Configuration
	new UrlControllerMapper('AdminGuideConfigController', '`^/admin(?:/config)?/?$`'),

	//Categories
	new UrlControllerMapper('DefaultCategoriesManagementController', '`^/categories/?$`'),
	new UrlControllerMapper('GuideCategoriesFormController', '`^/categories/add/?([0-9]+)?/?$`', array('id_parent')),
	new UrlControllerMapper('GuideCategoriesFormController', '`^/categories/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('DefaultDeleteCategoryController', '`^/categories/([0-9]+)/delete/?$`', array('id')),

	// Items Management
	new UrlControllerMapper('GuideItemsManagerController', '`^/manage/?$`'),
	new UrlControllerMapper('GuideItemFormController', '`^/add/?([0-9]+)?/?$`', array('id_category')),
	new UrlControllerMapper('GuideItemFormController', '`^/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('GuideItemHistoryController', '`^/([0-9]+)/history/?$`', array('id')),
	new UrlControllerMapper('GuideItemArchiveController', '`^/([0-9]+)/archive/([0-9]+)/?$`', array('id', 'content_id')),
	new UrlControllerMapper('GuideRestoreContentController', '`^/([0-9]+)/restore/([0-9]+)/?$`', array('id', 'content_id')),
	new UrlControllerMapper('GuideDeleteItemController', '`^/([0-9]+)/delete/([0-9]+)/?$`', array('id', 'content_id')),
	new UrlControllerMapper('GuideItemController', '`^/([0-9]+)-([a-z0-9-_]+)/([0-9]+)-([a-z0-9-_]+)?/?$`', array('id_category', 'rewrited_name_category', 'id', 'rewrited_name')),
	new UrlControllerMapper('GuideReorderItemsController', '`^/reorder/?([0-9]+)?-?([a-z0-9-_]+)?/?$`', array('id_category', 'rewrited_name')),

	// Keywords
	new UrlControllerMapper('GuideTagController', '`^/tag/([a-z0-9-_]+)?/?([a-z_]+)?/?([a-z]+)?/?([0-9]+)?/?$`', array('tag', 'page')),

	new UrlControllerMapper('GuidePendingItemsController', '`^/pending(?:/([a-z_]+))?/?([a-z]+)?/?([0-9]+)?/?$`', array('page')),
	new UrlControllerMapper('GuideMemberItemsController', '`^/member/([0-9]+)?/?([0-9]+)?/?$`', array('user_id', 'page')),

	new UrlControllerMapper('GuideExplorerController', '`^/explorer/?$`'),
	new UrlControllerMapper('GuideCategoryController', '`^(?:/([0-9]+)-([a-z0-9-_]+))?/?([a-z_]+)?/?([a-z]+)?/?([0-9]+)?/?([0-9]+)?/?$`', array('id_category', 'rewrited_name', 'page', 'subcategories_page')),
);
DispatchManager::dispatch($url_controller_mappers);
?>
