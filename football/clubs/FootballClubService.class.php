<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballClubService
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
	public static function count_clubs($condition = '', $params = array())
	{
		return self::$db_querier->count(FootballSetup::$football_club_table, $condition, $params);
	}

	/**
	 * @desc Create a new entry in the database club table.
	 * @param FootballClub string[] $club : new FootballClub
	 */
	public static function add_club(FootballClub $club)
	{
		$result = self::$db_querier->insert(FootballSetup::$football_club_table, $club->get_properties());

		return $result->get_last_inserted_id();
	}

	/**
	 * @desc Update a club entry.
	 * @param FootballClub string[] $club : FootballClub to update
	 */
	public static function update_club(FootballClub $club)
	{
		self::$db_querier->update(FootballSetup::$football_club_table, $club->get_properties(), 'WHERE id_club = :id', array('id' => $club->get_id_club()));
	}

	/**
	 * @desc Delete a club entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param string[] $params : Params of the condition
	 */
	public static function delete_club(int $id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
		self::$db_querier->delete(FootballSetup::$football_club_table, 'WHERE id=:id', array('id' => $id));

		self::$db_querier->delete(DB_TABLE_EVENTS, 'WHERE module=:module AND id_in_module=:id', array('module' => 'football', 'id' => $id));
	}

	/**
	 * @desc Return the item with all its properties from its id.
	 * @param int $id Item identifier
     * @return FootballClub $club
	 */
	public static function get_club(int $id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT club.*
		FROM ' . FootballSetup::$football_club_table . ' club
		WHERE club.id_club = :id', array('id' => $id));

		$club = new FootballClub();
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
            FROM ' . FootballSetup::$football_club_table . ' club'
        );

        while ($row = $results->fetch())
        {
            $clubs[] = $row;
        }
		return $clubs;
	}
    
    /**
     * @param  mixed $country
     * @return array
     */
    public static function get_league_list($country)
    {
		$leagues = array();
		$leagues[] = ['' => ''];

        $league_filepath = PATH_TO_ROOT . '/football/data/' . $country . '.json';
        if (file_exists($league_filepath))
        {
            $content = file_get_contents($league_filepath);
            $data = json_decode($content, true);

            foreach($data as $values)
            {
                $leagues[] = [$values['code'] => $values['name']];
            }
        }

        return $leagues;
    }

    public static function get_league_options_list($country)
    {
		$leagues = array();
		$leagues[] = new FormFieldSelectChoiceOption('', '');

        $league_filepath = PATH_TO_ROOT . '/football/data/' . $country . '.json';
        if (file_exists($league_filepath))
        {
            $content = file_get_contents($league_filepath);
            $data = json_decode($content, true);

            foreach($data as $values)
            {
                $leagues[] = new FormFieldSelectChoiceOption($values['name'], $values['code']);
            }
        }

        return $leagues;
    }
}
?>
