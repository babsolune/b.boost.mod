<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/init.php';

$url_controller_mappers = array(
	// Configuration
	new UrlControllerMapper('AdminFootballConfigController', '`^/admin(?:/config)?/?$`'),

	//Categories
	new UrlControllerMapper('DefaultCategoriesManagementController', '`^/categories/?$`'),
	new UrlControllerMapper('FootballCategoriesFormController', '`^/categories/add/?([0-9]+)?/?$`', array('id_parent')),
	new UrlControllerMapper('FootballCategoriesFormController', '`^/categories/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('DefaultDeleteCategoryController', '`^/categories/([0-9]+)/delete/?$`', array('id')),

	// Items Management
	new UrlControllerMapper('FootballCompetsManagerController', '`^/manage/?$`'),
	new UrlControllerMapper('FootballCompetFormController', '`^/add/?([0-9]+)?/?$`', array('id_category')),
	new UrlControllerMapper('FootballCompetFormController', '`^/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('FootballDeleteCompetController', '`^/([0-9]+)/delete/?$`', array('id')),
	new UrlControllerMapper('FootballCompetController', '`^/([0-9]+)-([a-z0-9-_]+)/([0-9]+)-([a-z0-9-_]+)?/?$`', array('id_category', 'rewrited_name_category', 'id', 'rewrited_name')),

	// Params
	new UrlControllerMapper('FootballGroupsFormController', '`^/groups/?([0-9]+)?/?$`', array('id')),
	new UrlControllerMapper('FootballMatchesFormController', '`^/matches/?([0-9]+)?/?$`', array('id')),
	new UrlControllerMapper('FootballResultsFormController', '`^/results/?([0-9]+)?/?$`', array('id')),
	new UrlControllerMapper('FootballParamsFormController', '`^/params/?([0-9]+)?/?$`', array('id')),
	new UrlControllerMapper('FootballTeamsFormController', '`^/teams/?([0-9]+)?/?$`', array('id')),
	new UrlControllerMapper('FootballStageGroupsController', '`^/groups_stage/?([0-9]+)?/?$`', array('id')),
	new UrlControllerMapper('FootballStageFinalsController', '`^/finals_stage/?([0-9]+)?/?$`', array('id')),

	// Clubs
	new UrlControllerMapper('FootballClubAjaxCountryController', '`^/club/ajax_club/?$`'),
	new UrlControllerMapper('FootballClubsManagerController', '`^/club/manage/?$`'),
	new UrlControllerMapper('FootballClubFormController', '`^/club/add/?$`'),
	new UrlControllerMapper('FootballClubFormController', '`^/club/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('FootballDeleteClubController', '`^/club/([0-9]+)/delete/?$`', array('id')),
	new UrlControllerMapper('FootballClubController', '`^/club/([0-9]+)/?$`', array('id')),

	// Divisions
	new UrlControllerMapper('FootballDivisionsManagerController', '`^/division/manage/?$`'),
	new UrlControllerMapper('FootballDivisionFormController', '`^/division/add/?$`'),
	new UrlControllerMapper('FootballDivisionFormController', '`^/division/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('FootballDeleteDivisionController', '`^/division/([0-9]+)/delete/?$`', array('id')),

	// Seasons
	new UrlControllerMapper('FootballSeasonsManagerController', '`^/season/manage/?$`'),
	new UrlControllerMapper('FootballSeasonFormController', '`^/season/add/?$`'),
	new UrlControllerMapper('FootballSeasonFormController', '`^/season/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('FootballDeleteSeasonController', '`^/season/([0-9]+)/delete/?$`', array('id')),

	// Keywords
	new UrlControllerMapper('FootballTagController', '`^/tag/([a-z0-9-_]+)?/?([a-z_]+)?/?([a-z]+)?/?([0-9]+)?/?$`', array('tag', 'field', 'sort', 'page')),
	new UrlControllerMapper('FootballPendingItemsController', '`^/pending(?:/([a-z_]+))?/?([a-z]+)?/?([0-9]+)?/?$`', array('field', 'sort', 'page')),
	new UrlControllerMapper('FootballMemberItemsController', '`^/member/([0-9]+)?/?([0-9]+)?/?$`', array('user_id', 'page')),

    new UrlControllerMapper('FootballHomeController', '`^/?$`'),
    new UrlControllerMapper('FootballCategoryController', '`^(?:/([0-9]+)-([a-z0-9-_]+))?/?([a-z_]+)?/?([a-z]+)?/?([0-9]+)?/?([0-9]+)?/?$`', array('id_category', 'rewrited_name', 'field', 'sort', 'page', 'subcategories_page')),
);
DispatchManager::dispatch($url_controller_mappers);
?>
