<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballCompetService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/**
	 * @desc Count compets number.
	 * @param string $condition (optional) : Restriction to apply to the list of compets
	 */
	public static function count($condition = '', $params = array())
	{
		return self::$db_querier->count(FootballSetup::$football_compet_table, $condition, $params);
	}

	/**
	 * @desc Create a new entry in the database table.
	 * @param FootballCompet $compet : new FootballCompet
	 */
	public static function add(FootballCompet $compet)
	{
		$result = self::$db_querier->insert(FootballSetup::$football_compet_table, $compet->get_properties());
        FootballParamsService::add_params($result->get_last_inserted_id());

		return $result->get_last_inserted_id();
	}

	/**
	 * @desc Update an entry.
	 * @param FootballCompet $compet : FootballCompet to update
	 */
	public static function update(FootballCompet $compet)
	{
		self::$db_querier->update(FootballSetup::$football_compet_table, $compet->get_properties(), 'WHERE id_compet = :id', array('id' => $compet->get_id_compet()));
	}

	/**
	 * @desc Update the number of views of a compet.
	 * @param FootballCompet $compet : FootballCompet to update
	 */
	public static function update_views_number(FootballCompet $compet)
	{
		self::$db_querier->update(FootballSetup::$football_compet_table, array('views_number' => $compet->get_views_number()), 'WHERE id_compet = :id', array('id' => $compet->get_id_compet()));
	}

	/**
	 * @desc Delete an entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param string[] $params : Params of the condition
	 */
	public static function delete(int $id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
		self::$db_querier->delete(FootballSetup::$football_compet_table, 'WHERE id_compet = :id', array('id' => $id));

		FootballTeamService::delete_teams($id);
		FootballParamsService::delete_params($id);
		FootballDayService::delete_days($id);
		FootballMatchService::delete_matches($id);

		self::$db_querier->delete(DB_TABLE_EVENTS, 'WHERE module=:module AND id_in_module=:id', array('module' => 'football', 'id' => $id));
	}

	/**
	 * @desc Return the compet with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_compet(int $id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT compet.*, member.*
		FROM ' . FootballSetup::$football_compet_table . ' compet
		LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = compet.author_user_id
		WHERE compet.id_compet = :id', array(
			'module_id'       => self::$module_id,
			'id'              => $id,
			'current_user_id' => AppContext::get_current_user()->get_id()
		));

		$compet = new FootballCompet();
		$compet->set_properties($row);
		return $compet;
	}
    
	/**
	 * @desc Return the compet with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_compets()
	{
		$results = self::$db_querier->select('SELECT compet.*
		FROM ' . FootballSetup::$football_compet_table . ' compet');

		return $results;
	}

	/**
	 * @desc Return the compet with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_params(int $id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT params.*, compet.*
		FROM ' . FootballSetup::$football_params_table . ' params
		LEFT JOIN ' . FootballSetup::$football_compet_table . ' compet ON compet.id_compet = params.params_compet_id
		WHERE params.params_compet_id = :id', array(
			'id' => $id
		));

		$params = new FootballParams();
		$params->set_properties($row);
		return $params;
	}

    public static function get_compet_type(int $compet_id)
    {
        return FootballDivisionService::get_division(self::get_compet($compet_id)->get_compet_division_id())->get_division_compet_type();
    }

    public static function get_compet_match_type(int $compet_id)
    {
        return FootballDivisionService::get_division(self::get_compet($compet_id)->get_compet_division_id())->get_division_match_type();
    }

	public static function clear_cache()
	{
		Feed::clear_cache('football');
		FootballClubCache::invalidate();
		FootballDivisionCache::invalidate();
		FootballSeasonCache::invalidate();
		FootballTeamCache::invalidate();
		FootballMatchCache::invalidate();
        CategoriesService::get_categories_manager()->regenerate_cache();
	}
}
?>
