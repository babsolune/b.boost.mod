<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 10 11
 * @since       PHPBoost 6.0 - 2022 11 18
 */

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/init.php';

$config = WikiConfig::load();

$url_controller_mappers = array(
	// Configuration
	new UrlControllerMapper('AdminWikiConfigController', '`^/admin(?:/config)?/?$`'),

	//Categories
	new UrlControllerMapper('DefaultCategoriesManagementController', '`^/categories/?$`'),
	new UrlControllerMapper('WikiCategoriesFormController', '`^/categories/add/?([0-9]+)?/?$`', array('id_parent')),
	new UrlControllerMapper('WikiCategoriesFormController', '`^/categories/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('WikiDeleteCategoryController', '`^/categories/([0-9]+)/delete/?$`', array('id')),

	// Items Management
	new UrlControllerMapper('WikiItemsManagerController', '`^/manage/?$`'),
	new UrlControllerMapper('WikiItemFormController', '`^/add/?([0-9]+)?/?$`', array('id_category')),
	new UrlControllerMapper('WikiItemFormController', '`^/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('WikiItemHistoryController', '`^/([0-9]+)/history/?$`', array('id')),
	new UrlControllerMapper('WikiItemArchiveController', '`^/([0-9]+)/archive/([0-9]+)/?$`', array('id', 'content_id')),
	new UrlControllerMapper('WikiRestoreContentController', '`^/([0-9]+)/restore/([0-9]+)/?$`', array('id', 'content_id')),
	new UrlControllerMapper('WikiDeleteItemController', '`^/([0-9]+)/delete/([0-9]+)/?$`', array('id', 'content_id')),
	new UrlControllerMapper('WikiItemController', '`^/([0-9]+)-([a-z0-9-_]+)/([0-9]+)-([a-z0-9-_]+)?/?$`', array('id_category', 'rewrited_name_category', 'id', 'rewrited_name')),
	new UrlControllerMapper('WikiReorderItemsController', '`^/reorder/?([0-9]+)?-?([a-z0-9-_]+)?/?$`', array('id_category', 'rewrited_name')),
	new UrlControllerMapper('WikiTrackItemController', '`^/([0-9]+)/track/?$`', array('id')),
	new UrlControllerMapper('WikiUntrackItemController', '`^/([0-9]+)/untrack/?$`', array('id')),

	// Keywords
	new UrlControllerMapper('WikiTagController', '`^/tag/([a-z0-9-_]+)?/?([a-z_]+)?/?([a-z]+)?/?([0-9]+)?/?$`', array('tag', 'page')),

	new UrlControllerMapper('WikiPendingItemsController', '`^/pending(?:/([a-z_]+))?/?([a-z]+)?/?([0-9]+)?/?$`', array('page')),
	new UrlControllerMapper('WikiMemberItemsController', '`^/member/([0-9]+)?/?([0-9]+)?/?$`', array('user_id', 'page')),
	new UrlControllerMapper('WikiTrackedItemsController', '`^/tracked/([0-9]+)?/?([0-9]+)?/?$`', array('user_id', 'page')),

	new UrlControllerMapper('WikiExplorerController', $config->get_homepage() == WikiConfig::EXPLORER ? '`^/?$`' : '`^/explorer/?$`'),
	new UrlControllerMapper('WikiIndexController', $config->get_homepage() == WikiConfig::INDEX ? '`^/?$`' : '`^/index/?$`'),
	new UrlControllerMapper('WikiCategoryController', '`^(?:/([0-9]+)-([a-z0-9-_]+))?/?([a-z_]+)?/?([a-z]+)?/?([0-9]+)?/?([0-9]+)?/?$`', array('id_category', 'rewrited_name', 'page', 'subcategories_page')),
);
DispatchManager::dispatch($url_controller_mappers);
?>
