<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballSeasonService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/**
	 * @desc Count items number.
	 * @param string $condition (optional) : Restriction to apply to the list of items
	 */
	public static function count_seasons($condition = '', $params = array())
	{
		return self::$db_querier->count(FootballSetup::$football_season_table, $condition, $params);
	}

	/**
	 * @desc Create a new entry in the database season table.
	 * @param FootballSeason $season : new FootballSeason
	 */
	public static function add_season(FootballSeason $season)
	{
		$result = self::$db_querier->insert(FootballSetup::$football_season_table, $season->get_properties());

		return $result->get_last_inserted_id();
	}

	/**
	 * @desc Update a season entry.
	 * @param FootballSeason $season : FootballSeason to update
	 */
	public static function update_season(FootballSeason $season)
	{
		self::$db_querier->update(FootballSetup::$football_season_table, $season->get_properties(), 'WHERE id_season = :id', array('id' => $season->get_id_season()));
	}

	/**
	 * @desc Delete a season entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param FootballSeason $params : Params of the condition
	 */
	public static function delete_season(int $id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
		self::$db_querier->delete(FootballSetup::$football_season_table, 'WHERE id_season = :id', array('id' => $id));

		// self::$db_querier->delete(DB_TABLE_EVENTS, 'WHERE module=:module AND id_in_module=:id', array('module' => 'football', 'id_season' => $id));
	}

	/**
	 * @desc Return the item with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_season(int $id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT season.*
		FROM ' . FootballSetup::$football_season_table . ' season
		WHERE season.id_season = :id', array('id' => $id));

		$season = new FootballSeason();
		$season->set_properties($row);
		return $season;
	}
}
?>
