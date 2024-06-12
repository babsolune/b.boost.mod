<?php
/**
 * @return Url
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballUrlBuilder
{
	private static $dispatcher = '/football';

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

################################ Competitions
	public static function manage()
	{
		return DispatchManager::get_url(self::$dispatcher, '/manage/');
	}

	public static function add($id_category = null)
	{
		$id_category = !empty($id_category) ? $id_category . '/' : '';
		return DispatchManager::get_url(self::$dispatcher, '/add/' . $id_category);
	}

	public static function edit($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $id . '/edit/');
	}

	public static function delete($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $id . '/delete/?' . 'token=' . AppContext::get_session()->get_token());
	}

################################ Clubs
	public static function manage_clubs()
	{
		return DispatchManager::get_url(self::$dispatcher, '/club/manage/');
	}

	public static function add_club()
	{
		return DispatchManager::get_url(self::$dispatcher, '/club/add/');
	}

	public static function edit_club($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/club/' . $id . '/edit/');
	}

    public static function delete_club($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/club/' . $id . '/delete/?' . 'token=' . AppContext::get_session()->get_token());
	}

    public static function display_club($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/club/' . $id);
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
    public static function compet_home($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/informations/' . $compet_id);
	}

    // Groups
	public static function display_groups_rounds($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/groups/rounds/' . $compet_id);
	}

    // Brackets
	public static function display_brackets_rounds($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/brackets/rounds/' . $compet_id);
	}

    // Days ranking
	public static function display_days_ranking($compet_id, $section = '', $day = '')
	{
        $section = !empty($section) ? '/' . $section : '';
        $day = !empty($day) ? '/' . $day : '';
		return DispatchManager::get_url(self::$dispatcher, '/days/ranking/' . $compet_id . $section . $day);
	}

    // Days calendar
	public static function display_days_calendar($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/days/calendar/' . $compet_id);
	}

    // Team calendar
	public static function display_team_calendar($compet_id, $team_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/team/calendar/' . $compet_id . '/' . $team_id);
	}

################################ Setup
    // Teams
	public static function edit_teams($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/teams/' . $compet_id);
	}

    // Params
	public static function edit_params($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/params/' . $compet_id);
	}

    // Days
    // Edit days and build days matches list
	public static function edit_days($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/days/edit/' . $compet_id);
	}
    // Edit days matches
	public static function edit_days_matches($compet_id, $round = 1)
	{
		return DispatchManager::get_url(self::$dispatcher, '/days/matches/edit/' . $compet_id . '/round/' . $round);
	}

    // Groups 
    // Edit groups and build group matches list
	public static function edit_groups($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/groups/edit/' . $compet_id);
	}

    // Edit groups matches
	public static function edit_groups_matches($compet_id, $round = 1)
	{
		return DispatchManager::get_url(self::$dispatcher, '/groups/matches/edit/' . $compet_id . '/round/' . $round);
	}

    // Bracket
    // Edit bracket and build bracket matches list
	public static function edit_brackets($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/brackets/edit/' . $compet_id);
	}
    // Edit bracket matches
	public static function edit_brackets_matches($compet_id, $round = 1)
	{
		return DispatchManager::get_url(self::$dispatcher, '/brackets/matches/edit/' . $compet_id . '/round/' . $round);
	}

################################ Main Controllers
	public static function dead_link($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/dead_link/' . $id);
	}

	public static function home()
	{
		return DispatchManager::get_url(self::$dispatcher, '/');
	}
}
?>
