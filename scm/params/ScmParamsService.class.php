<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmParamsService
{
	private static $db_querier;
	protected static $module_id = 'scm';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/**
	 * @desc Create a params for a new entry in the database table.
	 */
	public static function add_params(int $event_id)
	{
		$result = self::$db_querier->insert(ScmSetup::$scm_params_table, ['params_event_id' => $event_id]);
		return $result->get_last_inserted_id();
	}

    public static function delete_params(int $id)
	{
		self::$db_querier->delete(ScmSetup::$scm_params_table, 'WHERE params_event_id = :id', ['id' => $id]);
    }

	/**
	 * @desc Update params of an entry.
	 * @param ScmParams $params : ScmParams to update
	 */
	public static function update_params(ScmParams $params)
	{
		self::$db_querier->update(ScmSetup::$scm_params_table, $params->get_properties(), 'WHERE id_params = :id', ['id' => $params->get_id_params()]);
	}

	/**
	 * @desc Return the event with all its properties from its id.
	 * @param int $event_id Item identifier
	 */
	public static function get_params(int $event_id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT params.*, event.*
            FROM ' . ScmSetup::$scm_params_table . ' params
            LEFT JOIN ' . ScmSetup::$scm_event_table . ' event ON event.id = params.params_event_id
            WHERE params.params_event_id = :id', [
                'id' => $event_id
            ]
        );

		$params = new ScmParams();
		$params->set_properties($row);
		return $params;
	}

	/**
	 * Check if team id is favorite team id
	 * @param int $event_id Item identifier
	 */
	public static function check_fav(int $event_id, int $team_id)
	{
		return $team_id == self::get_params($event_id)->get_favorite_team_id();
	}
}
?>
