<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmDivisionService
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
	public static function count_divisions($condition = '', $params = array())
	{
		return self::$db_querier->count(ScmSetup::$scm_division_table, $condition, $params);
	}

	/**
	 * @desc Create a new entry in the database division table.
	 * @param ScmDivision $division : new ScmDivision
	 */
	public static function add_division(ScmDivision $division)
	{
		$result = self::$db_querier->insert(ScmSetup::$scm_division_table, $division->get_properties());

		return $result->get_last_inserted_id();
	}

	/**
	 * @desc Update a division entry.
	 * @param ScmDivision $division : ScmDivision to update
	 */
	public static function update_division(ScmDivision $division)
	{
		self::$db_querier->update(ScmSetup::$scm_division_table, $division->get_properties(), 'WHERE id_division = :id', array('id' => $division->get_id_division()));
	}

	/**
	 * @desc Delete a division entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param ScmDivision $params : Params of the condition
	 */
	public static function delete_division(int $id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
        foreach(ScmEventService::get_events() as $event)
        {
            if ($event['division_id'] == $id) {
                ScmEventService::delete($event['id']);
            }
        }
		self::$db_querier->delete(ScmSetup::$scm_division_table, 'WHERE id_division = :id', array('id' => $id));
	}

	/**
	 * @desc Return the item with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_division(int $id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT division.*
		FROM ' . ScmSetup::$scm_division_table . ' division
		WHERE division.id_division = :id', array('id' => $id));

		$division = new ScmDivision();
		$division->set_properties($row);
		return $division;
	}

    public static function get_event_type_lang($division_id)
    {
        $lang = LangLoader::get('common', 'scm');
        $division = ScmDivisionCache::load()->get_division($division_id);

        $event_type = '';
        switch($division['event_type'])
        {
            case('championship') :
                $event_type = $lang['scm.championship'];
                break;
            case('cup') :
                $event_type = $lang['scm.cup'];
                break;
            case('tournament') :
                $event_type = $lang['scm.tournament'];
                break;
        };

        return $event_type;
    }
}
?>
