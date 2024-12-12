<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/init.php';
$config = ScmConfig::load();

// Disable/enable left and right columns.
$columns_disabled = ThemesManager::get_theme(AppContext::get_current_user()->get_theme())->get_columns_disabled();
if ($config->is_left_column_disabled())
    $columns_disabled->set_disable_left_columns(true);
if ($config->is_right_column_disabled())
    $columns_disabled->set_disable_right_columns(true);

$url_controller_mappers = [
	// Configuration
	new UrlControllerMapper('AdminScmConfigController', '`^/admin(?:/config)?/?$`'),

	//Categories
	new UrlControllerMapper('DefaultCategoriesManagementController', '`^/categories/?$`'),
	new UrlControllerMapper('ScmCategoriesFormController', '`^/categories/add/?([0-9]+)?/?$`', ['id_parent']),
	new UrlControllerMapper('ScmCategoriesFormController', '`^/categories/([0-9]+)/edit/?$`', ['id']),
	new UrlControllerMapper('DefaultDeleteCategoryController', '`^/categories/([0-9]+)/delete/?$`', ['id']),

	// Event Management
	new UrlControllerMapper('ScmEventsManagerController', '`^/manage/?$`'),
	new UrlControllerMapper('ScmEventFormController', '`^/add/?([0-9]+)?/?$`', ['id']),
	new UrlControllerMapper('ScmEventFormController', '`^/([0-9]+)-([a-z0-9-_]+)/edit/?$`', ['id']),
	new UrlControllerMapper('ScmEventDeleteController', '`^/([0-9]+)/delete/?$`', ['id']),

	// Event
	new UrlControllerMapper('ScmEventHomeController', '`^/?([0-9]+)-([a-z0-9-_]+)?/informations/?$`', ['event_id', 'event_slug']),
	new UrlControllerMapper('ScmParamsFormController', '`^/?([0-9]+)-([a-z0-9-_]+)?/params/?$`', ['event_id', 'event_slug']),
	new UrlControllerMapper('ScmTeamsFormController', '`^/?([0-9]+)-([a-z0-9-_]+)?/teams/?$`', ['event_id', 'event_slug']),

    // Content display
	new UrlControllerMapper('ScmTeamCalendarController', '`^/?([0-9]+)-([a-z0-9-_]+)?/calendar/team/?([0-9]+)?/?$`', ['event_id', 'event_slug', 'team_id']),
	new UrlControllerMapper('ScmDaysCalendarController', '`^/?([0-9]+)-([a-z0-9-_]+)?/calendar/matchday/?([0-9]+)?/?$`', ['event_id', 'event_slug', 'cluster']),
	new UrlControllerMapper('ScmDaysCalendarFullController', '`^/?([0-9]+)-([a-z0-9-_]+)?/calendar/full/?([0-9]+)?/?$`', ['event_id', 'event_slug']),
	new UrlControllerMapper('ScmDaysRankingController', '`^/?([0-9]+)-([a-z0-9-_]+)?/ranking/?([a-z]+)?/?([0-9]+)?/?$`', ['event_id', 'event_slug', 'section', 'day']),
	new UrlControllerMapper('ScmDaysCheckerController', '`^/?([0-9]+)-([a-z0-9-_]+)?/calendar/checker/?$`', ['event_id', 'event_slug']),
	new UrlControllerMapper('ScmDaysDelayedController', '`^/?([0-9]+)-([a-z0-9-_]+)?/calendar/delayed/?$`', ['event_id', 'event_slug']),
	new UrlControllerMapper('ScmGroupController', '`^/?([0-9]+)-([a-z0-9-_]+)?/group/?([0-9]+)?/?([a-z]+)?/?$`', ['event_id', 'event_slug', 'cluster', 'type']),
	new UrlControllerMapper('ScmBracketController', '`^/?([0-9]+)-([a-z0-9-_]+)?/bracket/?$`', ['event_id', 'event_slug']),
    // Content edit
    new UrlControllerMapper('ScmDaysFormController', '`^/?([0-9]+)-([a-z0-9-_]+)?/builder/matchdays/?$`', ['event_id', 'event_slug']),
	new UrlControllerMapper('ScmDayGamesFormController', '`^/?([0-9]+)-([a-z0-9-_]+)?/edit/matchdays/?([0-9]+)?/?$`', ['event_id', 'event_slug', 'cluster']),
    new UrlControllerMapper('ScmGroupsFormController', '`^/?([0-9]+)-([a-z0-9-_]+)?/builder/groups/?$`', ['event_id', 'event_slug']),
	new UrlControllerMapper('ScmGroupGamesFormController', '`^/?([0-9]+)-([a-z0-9-_]+)?/edit/groups/?([0-9]+)?/?$`', ['event_id', 'event_slug', 'cluster']),
	new UrlControllerMapper('ScmBracketsFormController', '`^/?([0-9]+)-([a-z0-9-_]+)?/builder/brackets/?$`', ['event_id', 'event_slug']),
	new UrlControllerMapper('ScmBracketGamesFormController', '`^/?([0-9]+)-([a-z0-9-_]+)?/edit/brackets/?([0-9]+)?/?$`', ['event_id', 'event_slug', 'cluster']),
	new UrlControllerMapper('ScmDetailsGameFormController', '`^/?([0-9]+)-([a-z0-9-_]+)?/details/?([A-Z]+)?/?([0-9]+)?/?([0-9]+)?/?([0-9]+)?/?$`', ['event_id', 'event_slug', 'type', 'cluster', 'round', 'order']),

	// Clubs
	new UrlControllerMapper('ScmClubsManagerController', '`^/clubs/manage/?$`'),
	new UrlControllerMapper('ScmClubFormController', '`^/club/add/?$`'),
	new UrlControllerMapper('ScmClubFormController', '`^/club/([0-9]+)-([a-z0-9-_]+)/edit/?$`', ['club_id', 'club_slug']),
	new UrlControllerMapper('ScmClubDeleteController', '`^/club/([0-9]+)/delete/?$`', ['club_id']),
	new UrlControllerMapper('ScmClubsController', '`^/clubs/list/?$`'),
	new UrlControllerMapper('ScmClubController', '`^/club/([0-9]+)-([a-z0-9-_]+)/?$`', ['club_id', 'club_slug']),
	new UrlControllerMapper('ScmVisitClubController', '`^/club/visit/([0-9]+)/?$`', ['club_id']),

	// Divisions
	new UrlControllerMapper('ScmDivisionsManagerController', '`^/division/manage/?$`'),
	new UrlControllerMapper('ScmDivisionFormController', '`^/division/add/?$`'),
	new UrlControllerMapper('ScmDivisionFormController', '`^/division/([0-9]+)/edit/?$`', ['id']),
	new UrlControllerMapper('ScmDivisionDeleteController', '`^/division/([0-9]+)/delete/?$`', ['id']),

	// Seasons
	new UrlControllerMapper('ScmSeasonsManagerController', '`^/season/manage/?$`'),
	new UrlControllerMapper('ScmSeasonFormController', '`^/season/add/?$`'),
	new UrlControllerMapper('ScmSeasonFormController', '`^/season/([0-9]+)/edit/?$`', ['id']),
	new UrlControllerMapper('ScmSeasonDeleteController', '`^/season/([0-9]+)/delete/?$`', ['id']),

	new UrlControllerMapper('ScmHomeController', $config->get_homepage() == ScmConfig::EVENT_LIST ? '`^/?$`' : '`^/event_list/?$`'),
	new UrlControllerMapper('ScmExplorerController', $config->get_homepage() == ScmConfig::EXPLORER ? '`^/?$`' : '`^/explorer/?$`'),
    new UrlControllerMapper('ScmCategoryController', '`^(?:/([0-9]+)-([a-z0-9-_]+))?/?([a-z_]+)?/?([a-z]+)?/?([0-9]+)?/?([0-9]+)?/?$`', ['id_category', 'rewrited_name', 'field', 'sort', 'page', 'subcategories_page']),
];
DispatchManager::dispatch($url_controller_mappers);
?>
