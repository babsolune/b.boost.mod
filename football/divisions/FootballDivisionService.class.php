<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballDivisionService
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
	public static function count_divisions($condition = '', $params = array())
	{
		return self::$db_querier->count(FootballSetup::$football_division_table, $condition, $params);
	}

	/**
	 * @desc Create a new entry in the database division table.
	 * @param FootballDivision $division : new FootballDivision
	 */
	public static function add_division(FootballDivision $division)
	{
		$result = self::$db_querier->insert(FootballSetup::$football_division_table, $division->get_properties());

		return $result->get_last_inserted_id();
	}

	/**
	 * @desc Update a division entry.
	 * @param FootballDivision $division : FootballDivision to update
	 */
	public static function update_division(FootballDivision $division)
	{
		self::$db_querier->update(FootballSetup::$football_division_table, $division->get_properties(), 'WHERE id_division = :id', array('id' => $division->get_id_division()));
	}

	/**
	 * @desc Delete a division entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param FootballDivision $params : Params of the condition
	 */
	public static function delete_division(int $id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
		self::$db_querier->delete(FootballSetup::$football_division_table, 'WHERE id_division = :id', array('id' => $id));

		self::$db_querier->delete(DB_TABLE_EVENTS, 'WHERE module=:module AND id_in_module=:id', array('module' => 'football', 'id_division' => $id));
	}

	/**
	 * @desc Return the item with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_division(int $id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT division.*
		FROM ' . FootballSetup::$football_division_table . ' division
		WHERE division.id_division = :id', array('id' => $id));

		$division = new FootballDivision();
		$division->set_properties($row);
		return $division;
	}

    public static function get_compet_type_lang($compet_division_id)
    {
        $lang = LangLoader::get('common', 'football');
        $division = FootballDivisionCache::load()->get_division($compet_division_id);

        $compet_type = '';
        switch($division['division_compet_type'])
        {
            case('championship') :
                $compet_type = $lang['football.championship'];
                break;
            case('cup') :
                $compet_type = $lang['football.cup'];
                break;
            case('tourney') :
                $compet_type = $lang['football.tourney'];
                break;
        };

        return $compet_type;
    }
}
?>
