<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmClubService
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
	public static function count_clubs($condition = '', $params = [])
	{
		return self::$db_querier->count(ScmSetup::$scm_club_table, $condition, $params);
	}

	/**
	 * @desc Create a new entry in the database club table.
	 * @param ScmClub string[] $club : new ScmClub
	 */
	public static function add_club(ScmClub $club)
	{
		$result = self::$db_querier->insert(ScmSetup::$scm_club_table, $club->get_properties());

		return $result->get_last_inserted_id();
	}

	/**
	 * @desc Update a club entry.
	 * @param ScmClub string[] $club : ScmClub to update
	 */
	public static function update_club(ScmClub $club)
	{
		self::$db_querier->update(ScmSetup::$scm_club_table, $club->get_properties(), 'WHERE id_club = :id_club', ['id_club' => $club->get_id_club()]);
	}

	/**
	 * @desc Delete a club entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param string[] $params : Params of the condition
	 */
	public static function delete_club(int $club_id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
		self::$db_querier->delete(ScmSetup::$scm_club_table, 'WHERE id_club = :club_id', ['club_id' => $club_id]);
    }

	/**
	 * @desc Return the item with all its properties from its id.
	 * @param int $id Item identifier
     * @return ScmClub $club
	 */
	public static function get_club(int $id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT club.*
		FROM ' . ScmSetup::$scm_club_table . ' club
		WHERE club.id_club = :id', ['id' => $id]);

		$club = new ScmClub();
		$club->set_properties($row);
		return $club;
	}

	/**
     * Clubs list
     * @return array
	 */
	public static function get_clubs()
	{
        $clubs = [];
		$results = self::$db_querier->select('SELECT club.*
            FROM ' . ScmSetup::$scm_club_table . ' club'
        );

        while ($row = $results->fetch())
        {
            $clubs[] = $row;
        }
		return $clubs;
	}
}
?>
