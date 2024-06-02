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
$config = FootballConfig::load();

// Disable/enable left and right columns.
$columns_disabled = ThemesManager::get_theme(AppContext::get_current_user()->get_theme())->get_columns_disabled();
if ($config->is_left_column_disabled())
    $columns_disabled->set_disable_left_columns(true);
if ($config->is_right_column_disabled())
    $columns_disabled->set_disable_right_columns(true);

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
	new UrlControllerMapper('FootballCompetFormController', '`^/add/?([0-9]+)?/?$`', array('id')),
	new UrlControllerMapper('FootballCompetFormController', '`^/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('FootballDeleteCompetController', '`^/([0-9]+)/delete/?$`', array('id')),

	// Item
	new UrlControllerMapper('FootballCalendarController', '`^/calendar/?([0-9]+)?/?$`', array('id')),
	new UrlControllerMapper('FootballParamsFormController', '`^/params/?([0-9]+)?/?$`', array('id')),
	new UrlControllerMapper('FootballTeamsFormController', '`^/teams/?([0-9]+)?/?$`', array('id')),
    // Tournament display
	new UrlControllerMapper('FootballTourGroupsStageController', '`^/groups/stage/?([0-9]+)?/?$`', array('id')),
	new UrlControllerMapper('FootballTourBracketStageController', '`^/bracket/stage/?([0-9]+)?/?$`', array('id')),
    // Tournament edit
    new UrlControllerMapper('FootballTourGroupsFormController', '`^/groups/edit/?([0-9]+)?/?$`', array('id')),
	new UrlControllerMapper('FootballTourGroupsMatchesFormController', '`^/groups/matches/edit/?([0-9]+)?/?$`', array('id')),
	new UrlControllerMapper('FootballTourBracketFormController', '`^/bracket/edit/?([0-9]+)?/?$`', array('id')),

	// Clubs
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

	// todo
	new UrlControllerMapper('FootballTagController', '`^/tag/([a-z0-9-_]+)?/?([a-z_]+)?/?([a-z]+)?/?([0-9]+)?/?$`', array('tag', 'field', 'sort', 'page')),
	new UrlControllerMapper('FootballPendingItemsController', '`^/pending(?:/([a-z_]+))?/?([a-z]+)?/?([0-9]+)?/?$`', array('field', 'sort', 'page')),
	new UrlControllerMapper('FootballMemberItemsController', '`^/member/([0-9]+)?/?([0-9]+)?/?$`', array('user_id', 'page')),

    new UrlControllerMapper('FootballHomeController', '`^/?$`'),
    new UrlControllerMapper('FootballCategoryController', '`^(?:/([0-9]+)-([a-z0-9-_]+))?/?([a-z_]+)?/?([a-z]+)?/?([0-9]+)?/?([0-9]+)?/?$`', array('id_category', 'rewrited_name', 'field', 'sort', 'page', 'subcategories_page')),
);
DispatchManager::dispatch($url_controller_mappers);
?>
