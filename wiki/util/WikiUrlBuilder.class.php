<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class WikiUrlBuilder
{
	private static $dispatcher = '/wiki';

	/**
	 * @return Url
	 */
	public static function configuration()
	{
		return DispatchManager::get_url(self::$dispatcher, '/admin/config');
	}

	/**
	 * @return Url
	 */
	public static function manage()
	{
		return DispatchManager::get_url(self::$dispatcher, '/manage/');
	}

	/**
	 * @return Url
	 */
	public static function display_category($id, $rewrited_name, $page = 1, $subcategories_page = 1)
	{
		$category = $id > 0 ? $id . '-' . $rewrited_name . '/' : '';
		$page = $page !== 1 || $subcategories_page !== 1 ? $page . '/' : '';
		$subcategories_page = $subcategories_page !== 1 ? $subcategories_page . '/' : '';
		return DispatchManager::get_url(self::$dispatcher, '/' . $category . $page . $subcategories_page);
	}

	/**
	 * @return Url
	 */
	public static function display_tag($rewrited_name, $page = 1)
	{
		return DispatchManager::get_url(self::$dispatcher, '/tag/' . $rewrited_name . '/' . $page);
	}

	/**
	 * @return Url
	 */
	public static function display_pending($page = 1)
	{
		return DispatchManager::get_url(self::$dispatcher, '/pending/' . $page);
	}

	/**
	 * @return Url
	 */
	public static function display_member_items($user_id, $page = 1)
	{
		$page = $page !== 1 ? $page . '/' : '';
		return DispatchManager::get_url(self::$dispatcher, '/member/' . $user_id . '/' . $page);
	}

	/**
	 * @return Url
	 */
	public static function add($id_category = null)
	{
		$id_category = !empty($id_category) ? $id_category . '/' : '';
		return DispatchManager::get_url(self::$dispatcher, '/add/' . $id_category);
	}

	/**
	 * @return Url
	 */
	public static function history($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $id . '/history/');
	}

	/**
	 * @return Url
	 */
	public static function archive($id,$content_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $id . '/archive/' . $content_id . '/');
	}

	/**
	 * @return Url
	 */
	public static function edit($id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $id . '/edit/');
	}

	/**
	 * @return Url
	 */
	public static function delete($id, $content_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $id . '/delete/' . $content_id . '/?' . 'token=' . AppContext::get_session()->get_token());
	}

	/**
	 * @return Url
	 */
	public static function delete_content($id, $content_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $id . '/delete/' . $content_id . '/?' . 'token=' . AppContext::get_session()->get_token());
	}

	/**
	 * @return Url
	 */
	public static function restore($id, $content_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $id . '/restore/' . $content_id . '/?' . 'token=' . AppContext::get_session()->get_token());
	}

	/**
	 * @return Url
	 */
	public static function display($id_category, $rewrited_name_category, $id, $rewrited_title)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $id_category . '-' . $rewrited_name_category . '/' . $id . '-' . $rewrited_title . '/');
	}

	/**
	 * @return Url
	 */
	public static function display_comments($id_category, $rewrited_name_category, $id, $rewrited_title)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $id_category . '-' . $rewrited_name_category . '/' . $id . '-' . $rewrited_title . '/#comments-list');
	}

	/**
	 * @return Url
	 */
	public static function reorder_items($id_category, $rewrited_name)
	{
		$category = $id_category > 0 ? $id_category . '-' . $rewrited_name . '/' : '';
		return DispatchManager::get_url(self::$dispatcher, '/reorder/' . $category);
	}

	/**
	 * @return Url
	 */
	public static function explorer()
	{
		return DispatchManager::get_url(self::$dispatcher, '/explorer/');
	}

	/**
	 * @return Url
	 */
	public static function index()
	{
		return DispatchManager::get_url(self::$dispatcher, '/index/');
	}

	/**
	 * @return Url
	 */
	public static function track_item($item_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $item_id . '/track/');
	}

	/**
	 * @return Url
	 */
	public static function untrack_item($item_id)
	{
		return DispatchManager::get_url(self::$dispatcher, '/' . $item_id . '/untrack/');
	}

	/**
	 * @return Url
	 */
	public static function tracked_member_items($user_id, $page = 1)
	{
		$page = $page !== 1 ? $page . '/' : '';
		return DispatchManager::get_url(self::$dispatcher, '/tracked/' . $user_id . '/' . $page);
	}

	/**
	 * @return Url
	 */
	public static function home()
	{
		return DispatchManager::get_url(self::$dispatcher, '/');
	}
}
?>
