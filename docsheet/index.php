<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/init.php';

$url_controller_mappers = array(
	// Configuration
	new UrlControllerMapper('AdminDocsheetConfigController', '`^/admin(?:/config)?/?$`'),

	//Categories
	new UrlControllerMapper('DefaultCategoriesManagementController', '`^/categories/?$`'),
	new UrlControllerMapper('DocsheetCategoriesFormController', '`^/categories/add/?([0-9]+)?/?$`', array('id_parent')),
	new UrlControllerMapper('DocsheetCategoriesFormController', '`^/categories/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('DocsheetDeleteCategoryController', '`^/categories/([0-9]+)/delete/?$`', array('id')),

	// Items Management
	new UrlControllerMapper('DocsheetItemsManagerController', '`^/manage/?$`'),
	new UrlControllerMapper('DocsheetItemFormController', '`^/add/?([0-9]+)?/?$`', array('id_category')),
	new UrlControllerMapper('DocsheetItemFormController', '`^/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('DocsheetItemHistoryController', '`^/([0-9]+)/history/?$`', array('id')),
	new UrlControllerMapper('DocsheetItemArchiveController', '`^/([0-9]+)/archive/([0-9]+)/?$`', array('id', 'content_id')),
	new UrlControllerMapper('DocsheetRestoreContentController', '`^/([0-9]+)/restore/([0-9]+)/?$`', array('id', 'content_id')),
	new UrlControllerMapper('DocsheetDeleteItemController', '`^/([0-9]+)/delete/([0-9]+)/?$`', array('id', 'content_id')),
	new UrlControllerMapper('DocsheetItemController', '`^/([0-9]+)-([a-z0-9-_]+)/([0-9]+)-([a-z0-9-_]+)?/?$`', array('id_category', 'rewrited_name_category', 'id', 'rewrited_name')),
	new UrlControllerMapper('DocsheetReorderItemsController', '`^/reorder/?([0-9]+)?-?([a-z0-9-_]+)?/?$`', array('id_category', 'rewrited_name')),
	new UrlControllerMapper('DocsheetTrackItemController', '`^/([0-9]+)/track/?$`', array('id')),
	new UrlControllerMapper('DocsheetUntrackItemController', '`^/([0-9]+)/untrack/?$`', array('id')),

	// Keywords
	new UrlControllerMapper('DocsheetTagController', '`^/tag/([a-z0-9-_]+)?/?([a-z_]+)?/?([a-z]+)?/?([0-9]+)?/?$`', array('tag', 'page')),

	new UrlControllerMapper('DocsheetPendingItemsController', '`^/pending(?:/([a-z_]+))?/?([a-z]+)?/?([0-9]+)?/?$`', array('page')),
	new UrlControllerMapper('DocsheetMemberItemsController', '`^/member/([0-9]+)?/?([0-9]+)?/?$`', array('user_id', 'page')),
	new UrlControllerMapper('DocsheetTrackedItemsController', '`^/tracked/([0-9]+)?/?([0-9]+)?/?$`', array('user_id', 'page')),

	new UrlControllerMapper('DocsheetExplorerController', '`^/explorer/?$`'),
	new UrlControllerMapper('DocsheetCategoryController', '`^(?:/([0-9]+)-([a-z0-9-_]+))?/?([a-z_]+)?/?([a-z]+)?/?([0-9]+)?/?([0-9]+)?/?$`', array('id_category', 'rewrited_name', 'page', 'subcategories_page')),
);
DispatchManager::dispatch($url_controller_mappers);
?>
