<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballParamsService
{
	private static $db_querier;
	protected static $module_id = 'football';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/**
	 * @desc Create a params for a new entry in the database table.
	 */
	public static function add_params(int $compet_id)
	{
		$result = self::$db_querier->insert(FootballSetup::$football_params_table, array('params_compet_id' => $compet_id));
		return $result->get_last_inserted_id();
	}

    public static function delete_params(int $id)
	{
		self::$db_querier->delete(FootballSetup::$football_params_table, 'WHERE params_compet_id = :id', array('id' => $id));
    }

	/**
	 * @desc Update params of an entry.
	 * @param FootballParams $params : FootballParams to update
	 */
	public static function update_params(FootballParams $params)
	{
		self::$db_querier->update(FootballSetup::$football_params_table, $params->get_properties(), 'WHERE id_params = :id', array('id' => $params->get_id_params()));
	}

	/**
	 * @desc Return the compet with all its properties from its id.
	 * @param int $compet_id Item identifier
	 */
	public static function get_params(int $compet_id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT params.*, compet.*
		FROM ' . FootballSetup::$football_params_table . ' params
		LEFT JOIN ' . FootballSetup::$football_compet_table . ' compet ON compet.id_compet = params.params_compet_id
		WHERE params.params_compet_id = :id', array(
			'id' => $compet_id
		));

		$params = new FootballParams();
		$params->set_properties($row);
		return $params;
	}

	/**
	 * Check if team id is favorite team id
	 * @param int $compet_id Item identifier
	 */
	public static function check_fav(int $compet_id, int $team_id)
	{
		return $team_id == self::get_params($compet_id)->get_favorite_team_id();
	}
}
?>
