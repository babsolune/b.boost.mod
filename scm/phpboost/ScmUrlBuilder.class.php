<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmUrlBuilder
{
	private static $dispatcher = '/scm';

	public static function configuration()
	{
		return DispatchManager::get_url(self::$dispatcher, '/admin/config');
	}

################################ Categories
	public static function display_category($id, $rewrited_name, $page = 1, $subcategories_page = 1)
	{
		$category = $id > 0 ? $id . '-' . $rewrited_name . '/' : '';
		$page = $page !== 1 || $subcategories_page !== 1 ? $page . '/' : '';
		$subcategories_page = $subcategories_page !== 1 ? $subcategories_page . '/' : '';
		return DispatchManager::get_url(self::$dispatcher, '/' . $category . $page . $subcategories_page);
	}

################################ Events
	public static function manage()
	{
		return DispatchManager::get_url(self::$dispatcher, '/manage/');
	}

	public static function add($id_category = null)
	{
		$id_category = !empty($id_category) ? $id_category . '/' : '';
		return DispatchManager::get_url(self::$dispatcher, '/add/' . $id_category);
	}

	public static function edit($event_id, $event_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/edit/');
	}

	public static function delete($event_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '/delete/?' . 'token=' . AppContext::get_session()->get_token());
	}

################################ Clubs
	public static function manage_clubs()
	{
		return DispatchManager::get_url(self::$dispatcher, '/clubs/manage/');
	}

	public static function add_club()
	{
		return DispatchManager::get_url(self::$dispatcher, '/club/add/');
	}

	public static function edit_club(int $club_id, string $club_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/club/' . $club_id . '-' . $club_slug . '/edit/');
	}

    public static function delete_club(int $club_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/club/' . $club_id . '/delete/?' . 'token=' . AppContext::get_session()->get_token());
	}

    public static function display_clubs()
	{
		return DispatchManager::get_url(self::$dispatcher, '/clubs/list/');
	}

    public static function display_club(int $club_id, string $club_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/club/' . $club_id . '-' . $club_slug);
	}

	public static function visit_club($club_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/club/visit/' . $club_id);
	}

################################ Seasons
	public static function manage_seasons()
	{
		return DispatchManager::get_url(self::$dispatcher, '/season/manage/');
	}

	public static function add_season()
	{
		return DispatchManager::get_url(self::$dispatcher, '/season/add/');
	}

	public static function edit_season($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/season/' . $id . '/edit/');
	}

	public static function delete_season($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/season/' . $id . '/delete/?' . 'token=' . AppContext::get_session()->get_token());
	}

################################ Divisions
	public static function manage_divisions()
	{
		return DispatchManager::get_url(self::$dispatcher, '/division/manage/');
	}

	public static function add_division()
	{
		return DispatchManager::get_url(self::$dispatcher, '/division/add/');
	}

	public static function edit_division($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/division/' . $id . '/edit/');
	}

	public static function delete_division($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/division/' . $id . '/delete/?' . 'token=' . AppContext::get_session()->get_token());
	}

################################ Display
    public static function event_home($event_id, $event_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/informations/');
	}

    // Groups
	public static function display_groups_rounds($event_id, $event_slug, $group = 1)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/group/' . $group);
	}

    // Brackets
	public static function display_brackets_rounds($event_id, $event_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/bracket/');
	}

    // Days ranking
	public static function display_days_ranking($event_id, $event_slug, $section = '', $cluster = '')
	{
        $section = !empty($section) ? '/' . $section : '';
        $day = !empty($day) ? '/' . $day : '';
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/ranking/' . $section . $cluster);
	}

    // Days calendar
	public static function display_days_calendar($event_id, $event_slug, $cluster = '')
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/calendar/matchday/' . $cluster);
	}

    // Days calendar
	public static function display_days_calendar_full($event_id, $event_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/calendar/full/');
	}

    // Days calendar
	public static function days_checker($event_id, $event_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/calendar/checker/');
	}

    // Days calendar
	public static function days_delayed($event_id, $event_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/calendar/delayed/');
	}

    // Team calendar
	public static function display_team_calendar($event_id, $event_slug, $team_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/calendar/team/' . $team_id);
	}

################################ Setup
    // Teams
	public static function edit_teams($event_id, $event_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/teams/');
	}

    // Params
	public static function edit_params($event_id, $event_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/params/');
	}

    // Days
    // Edit days and build days games list
	public static function edit_days($event_id, $event_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/builder/matchdays/');
	}
    // Edit days games
	public static function edit_days_games($event_id, $event_slug, $cluster = 1)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/edit/matchdays/' . $cluster);
	}

    // Groups
    // Edit groups and build group games list
	public static function edit_groups($event_id, $event_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/builder/groups/');
	}

    // Edit groups games
	public static function edit_groups_games($event_id, $event_slug, $cluster = 1)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/edit/groups/' . $cluster);
	}

    // Bracket
    // Edit bracket and build bracket games list
	public static function edit_brackets($event_id, $event_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/builder/brackets/');
	}
    // Edit bracket games
	public static function edit_brackets_games($event_id, $event_slug, $cluster = 1)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/edit/brackets/' . $cluster);
	}

    // Edit details games
	public static function edit_details_game($event_id, $event_slug, $type, $cluster, $round, $order)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $event_id . '-' . $event_slug . '/details/' . $type . '/' . $cluster . '/' . $round . '/' . $order);
	}

################################ Main Controllers
	public static function dead_link($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/dead_link/' . $id);
	}

	public static function display_event_list()
	{
		return DispatchManager::get_url(self::$dispatcher, '/event_list/');
	}

	public static function display_explorer()
	{
		return DispatchManager::get_url(self::$dispatcher, '/explorer/');
	}

	public static function home()
	{
		return DispatchManager::get_url(self::$dispatcher, '/');
	}
}
?>
