<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ClubsService
{
	private static $db_querier;

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	 /**
	 * @desc Count items number.
	 * @param string $condition (optional) : Restriction to apply to the list of items
	 */
	public static function is_gmap_enabled()
	{
		return ModulesManager::is_module_installed('GoogleMaps') && ModulesManager::is_module_activated('GoogleMaps') && !empty(GoogleMapsConfig::load()->get_api_key()) && !empty(GoogleMapsConfig::load()->get_default_marker_latitude()) && !empty(GoogleMapsConfig::load()->get_default_marker_longitude());
	}

	 /**
	 * @desc Count items number.
	 * @param string $condition (optional) : Restriction to apply to the list of items
	 */
	public static function count($condition = '', $parameters = array())
	{
		return self::$db_querier->count(ClubsSetup::$clubs_table, $condition, $parameters);
	}

	 /**
	 * @desc Create a new entry in the database table.
	 * @param string[] $item : new ClubsItem
	 */
	public static function add(ClubsItem $item)
	{
		$result = self::$db_querier->insert(ClubsSetup::$clubs_table, $item->get_properties());

		return $result->get_last_inserted_id();
	}

	 /**
	 * @desc Update an entry.
	 * @param string[] $item : Club to update
	 */
	public static function update(ClubsItem $item)
	{
		self::$db_querier->update(ClubsSetup::$clubs_table, $item->get_properties(), 'WHERE id=:id', array('id' => $item->get_id()));
	}

	 /**
	 * @desc Update the number of views of a link.
	 * @param string[] $item : ClubsItem to update
	 */
	public static function update_views_number(ClubsItem $item)
	{
		self::$db_querier->update(ClubsSetup::$clubs_table, array('views_number' => $item->get_views_number()), 'WHERE id=:id', array('id' => $item->get_id()));
	}

	 /**
	 * @desc Delete an entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param string[] $parameters : Parameters of the condition
	 */
	public static function delete(int $id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
			self::$db_querier->delete(ClubsSetup::$web_table, 'WHERE id=:id', array('id' => $id));

			self::$db_querier->delete(DB_TABLE_EVENTS, 'WHERE module=:module AND id_in_module=:id', array('module' => 'clubs', 'id' => $id));
	}

	 /**
	 * @desc Return the properties of an item.
	 * @param string $condition : Restriction to apply to the list
	 * @param string[] $parameters : Parameters of the condition
	 */
	public static function get_item($condition, array $parameters)
	{
		$row = self::$db_querier->select_single_row_query('SELECT clubs.*, member.*
		FROM ' . ClubsSetup::$clubs_table . ' clubs
		LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = clubs.author_user_id
		' . $condition, $parameters);

		$item = new ClubsItem();
		$item->set_properties($row);
		return $item;
	}

	public static function clear_cache()
	{
		Feed::clear_cache('clubs');
		// ClubsCache::invalidate();
		CategoriesService::get_categories_manager()->regenerate_cache();
	}
}
?>
