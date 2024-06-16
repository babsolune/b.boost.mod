<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
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
	public static function update_team_group($id, $group, $order)
	{
		self::$db_querier->update(FootballSetup::$football_team_table, ['team_group' => $group, 'team_order' => $order], 'WHERE id_team = :id', array('id' => $id));
	}

	/**
	 * @desc Update a team entry.
	 * @param FootballTeam $team : FootballTeam to update
	 */
	public static function update_team_penalty($id, $penalty)
	{
        if (empty($penalty)) $penalty = 0;
		self::$db_querier->update(FootballSetup::$football_team_table, ['team_penalty' => $penalty], 'WHERE id_team = :id', array('id' => $id));
	}

	/**
	 * @desc Delete a team entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param string[] $params : Params of the condition
	 */
	public static function delete_team(int $compet_id, int $club_id)
	{
        self::$db_querier->delete(FootballSetup::$football_team_table, 'WHERE team_compet_id = :compet_id AND team_club_id = :club_id', array('compet_id' => $compet_id, 'club_id' => $club_id));
	}

	/**
	 * @desc Delete a team entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param string[] $params : Params of the condition
	 */
	public static function delete_teams(int $id)
	{
        $teams = self::get_teams($id);
        foreach ($teams as $team)
        {
            self::$db_querier->delete(FootballSetup::$football_team_table, 'WHERE team_compet_id = :id', array('id' => $id));
        }
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
		$results = self::$db_querier->select('SELECT teams.*, clubs.*
            FROM ' . FootballSetup::$football_team_table . ' teams
            LEFT JOIN ' . FootballSetup::$football_club_table . ' clubs ON clubs.id_club = teams.team_club_id
            WHERE teams.team_compet_id = :id
            ORDER BY clubs.club_name', array(
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

	public static function get_team_in_group(int $compet_id, int $group, int $order)
	{
        $team_numbers = [];
        foreach (FootballTeamService::get_teams($compet_id) as $team_group)
        {
            $team_numbers[] = $team_group['team_group'].$team_group['team_order'];
        }

        if (in_array($group.$order, $team_numbers))
        {
            $row = self::$db_querier->select_single_row_query('SELECT teams.*
                FROM ' . FootballSetup::$football_team_table . ' teams
                WHERE teams.team_compet_id = :id
                AND teams.team_group = :group
                AND teams.team_order = :order', array(
                    'id' => $compet_id,
                    'group' => $group,
                    'order' => $order
                )
            );
            $team = new FootballTeam();
            $team->set_properties($row);

            return $team;
        }
	}

	public static function get_teams_number($compet_id)
	{
		return count(self::get_teams($compet_id));
	}

    public static function get_team_name($team_id)
	{
        $club = FootballClubCache::load();
        $team = self::get_team($team_id);
		return $club->get_club_name($team->get_team_club_id());
	}

    public static function get_team_logo($team_id)
	{
        $club = FootballClubCache::load();
        $team = self::get_team($team_id);
		return $club->get_club_logo($team->get_team_club_id());
	}
}
?>
