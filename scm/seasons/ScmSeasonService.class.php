<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmSeasonService
{
	private static $db_querier;
	protected static $module_id = 'scm';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/**
	 * @desc Count items number.
	 * @param string $condition (optional) : Restriction to apply to the list of items
	 */
	public static function count_seasons($condition = '', $params = [])
	{
		return self::$db_querier->count(ScmSetup::$scm_season_table, $condition, $params);
	}

	/**
	 * @desc Create a new entry in the database season table.
	 * @param ScmSeason $season : new ScmSeason
	 */
	public static function add_season(ScmSeason $season)
	{
		$result = self::$db_querier->insert(ScmSetup::$scm_season_table, $season->get_properties());

		return $result->get_last_inserted_id();
	}

	/**
	 * @desc Update a season entry.
	 * @param ScmSeason $season : ScmSeason to update
	 */
	public static function update_season(ScmSeason $season)
	{
		self::$db_querier->update(ScmSetup::$scm_season_table, $season->get_properties(), 'WHERE id_season = :id', ['id' => $season->get_id_season()]);
	}

	/**
	 * @desc Delete a season entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param ScmSeason $params : Params of the condition
	 */
	public static function delete_season(int $id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
		self::$db_querier->delete(ScmSetup::$scm_season_table, 'WHERE id_season = :id', ['id' => $id]);

		// self::$db_querier->delete(DB_TABLE_EVENTS, 'WHERE module=:module AND id_in_module=:id', ['module' => 'scm', 'id_season' => $id]);
	}

	/**
	 * @desc Return the item with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_season(int $id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT season.*
		FROM ' . ScmSetup::$scm_season_table . ' season
		WHERE season.id_season = :id', ['id' => $id]);

		$season = new ScmSeason();
		$season->set_properties($row);
		return $season;
	}

    public static function check_season(int $season_id)
    {
        $now = new Date();
        $season_name = ScmSeasonService::get_season($season_id)->get_season_name();
        $season_parts = explode('-', $season_name);

        return in_array($now->get_year(), $season_parts);
    }
}
?>
