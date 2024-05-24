<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballTeamService
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
	public static function count_teams($condition = '', $params = array())
	{
		return self::$db_querier->count(FootballSetup::$football_team_table, $condition, $params);
	}

	/**
	 * @desc Create a new entry in the database team table.
	 * @param FootballTeam $team : new FootballTeam
	 */
	public static function add_team(FootballTeam $team)
	{
		$result = self::$db_querier->insert(FootballSetup::$football_team_table, $team->get_properties());

		return $result->get_last_inserted_id();
	}

	/**
	 * @desc Update a team entry.
	 * @param FootballTeam $team : FootballTeam to update
	 */
	public static function update_team(FootballTeam $team)
	{
		self::$db_querier->update(FootballSetup::$football_team_table, $team->get_properties(), 'WHERE id_team=:id', array('id' => $team->get_id_team()));
	}

	/**
	 * @desc Update a team entry.
	 * @param FootballTeam $team : FootballTeam to update
	 */
	public static function update_team_group($id, $group)
	{
		self::$db_querier->update(FootballSetup::$football_team_table, array('team_group' => $group), 'WHERE id_team = :id', array('id' => $id));
	}

	/**
	 * @desc Delete a team entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param string[] $params : Params of the condition
	 */
	public static function delete_team(int $id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
		self::$db_querier->delete(FootballSetup::$football_team_table, 'WHERE team_club_id = :id', array('id' => $id));

		// self::$db_querier->delete(DB_TABLE_EVENTS, 'WHERE module=:module AND id_in_module=:id', array('module' => 'football', 'id' => $id));
	}

	/**
	 * @desc Return the item with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_team(int $id)
	{
        if($id !== 0)
        {
            $row = self::$db_querier->select_single_row_query('SELECT teams.*
            FROM ' . FootballSetup::$football_team_table . ' teams
            WHERE teams.id_team = :id', array(
                'id' => $id
            ));

            $team = new FootballTeam();
            $team->set_properties($row);
            return $team;
        }
	}

	/**
	 * @desc Return the item with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_teams($compet_id)
	{
		$results = self::$db_querier->select('SELECT teams.*, compet.*
            FROM ' . FootballSetup::$football_team_table . ' teams
            LEFT JOIN ' . FootballSetup::$football_compet_table . ' compet ON compet.id_compet = teams.team_compet_id
            WHERE teams.team_compet_id = :id
            ORDER BY teams.id_team', array(
                'id' => $compet_id
            )
        );

        $teams = [];
        while($row = $results->fetch())
        {
            $teams[] = $row;
        }
        return $teams;
	}

	public static function get_team_in_group($compet_id, $group)
	{
        $groups = [];
        foreach (FootballTeamService::get_teams($compet_id) as $team_group)
        {
            $groups[] = $team_group['team_group'];
        }

        if (in_array($group, $groups))
        {
            $row = self::$db_querier->select_single_row_query('SELECT teams.*
                FROM ' . FootballSetup::$football_team_table . ' teams
                WHERE teams.team_compet_id = :id
                AND teams.team_group = :group', array(
                    'id' => $compet_id,
                    'group' => $group
                )
            );
            $team = new FootballTeam();
            $team->set_properties($row);

            return $team;
        }
	}

	public static function get_compet_teams_number($compet_id)
	{
		$teams_list = array();

		$result = PersistenceContext::get_querier()->select('SELECT team.*, compet.*
			FROM ' . FootballSetup::$football_team_table . ' team
			LEFT JOIN ' . FootballSetup::$football_compet_table . ' compet ON compet.id_compet = team.team_compet_id
			WHERE team.team_compet_id = :id', array(
				'id' => $compet_id
			)
		);

		while ($row = $result->fetch())
		{
			$teams_list[] = $row['id_team'];
		}

		return count($teams_list);
	}
}
?>
