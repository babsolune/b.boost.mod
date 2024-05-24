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

    // Categories
	public static function display_category($id, $rewrited_name, $page = 1, $subcategories_page = 1)
	{
		$config = FootballConfig::load();
		$category = $id > 0 ? $id . '-' . $rewrited_name . '/' : '';
		$page = $page !== 1 || $subcategories_page !== 1 ? $page . '/' : '';
		$subcategories_page = $subcategories_page !== 1 ? $subcategories_page . '/' : '';
		return DispatchManager::get_url(self::$dispatcher, '/' . $category . $page . $subcategories_page);
	}

    // Competitions
	public static function manage()
	{
		return DispatchManager::get_url(self::$dispatcher, '/manage/');
	}

	public static function display_pending()
	{
		return DispatchManager::get_url(self::$dispatcher, '/pending/');
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

	public static function display($id_category, $category_rewrited_name, $id, $compet_slug)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $id_category . '-' . $category_rewrited_name . '/' . $id . '-' . $compet_slug . '/');
	}

    // Clubs
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

    // Seasons
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

    // Divisions
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

    // Params
	public static function params($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/params/' . $compet_id);
	}

    // Teams
	public static function teams($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/teams/' . $compet_id);
	}

    // Groups
	public static function groups($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/groups/' . $compet_id);
	}

    // Groups
	public static function matches($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/matches/' . $compet_id);
	}

    // Groups
	public static function results($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/results/' . $compet_id);
	}

    // Display groups stage
	public static function groups_stage($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/groups_stage/' . $compet_id);
	}

    // Display finals stage
	public static function finals_stage($compet_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/finals_stage/' . $compet_id);
	}

    // Controllers
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
