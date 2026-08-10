<?php
/**
 * @copyright   &copy; 2005-2026 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2024 06 12
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

    public static function get_district_data($file)
    {
        $json_file = str_replace('../', '', $file);
        $full_path = PATH_TO_ROOT . '/' . $json_file;

        if (!file_exists($full_path) || !is_file($full_path)) {
            return null;
        }

        $json_content = file_get_contents($full_path);

        return json_decode($json_content, true);
    }

    public static function get_country_url($data) {
        if (!is_array($data)) {
            return null;
        }

        foreach ($data as $href) {
            if (is_array($href) && !empty($href['href'])) {
                return $href['href'];
            }
        }

        return null;
    }

    public static function get_league($data, $lref) {
        if (!is_array($data) || !isset($data['leagues']) || !is_array($data['leagues'])) {
            return null;
        }

        foreach ($data['leagues'] as $league) {
            if (is_array($league) && isset($league['lref']) && $league['lref'] === $lref) {
                return $league['league'] ?? null;
            }
        }

        return null;
    }

    public static function get_league_url($data, $lref) {
        if (!is_array($data) || !isset($data['leagues']) || !is_array($data['leagues'])) {
            return null;
        }
        foreach ($data['leagues'] as $league) {
            if (is_array($league) && isset($league['lref']) && $league['lref'] === $lref) {
                return $league['lref'];
            }
        }
        return null;
    }

    public static function get_district($data, $dref) {
        if (!is_array($data) || !isset($data['leagues']) || !is_array($data['leagues'])) {
            return null;
        }
        foreach ($data['leagues'] as $league) {
            foreach ($league['districts'] as $district) {
                if (is_array($league) && isset($district['dref']) && $district['dref'] === $dref) {
                    return $district['district'] ?? null;
                }
            }
        }
        return null;
    }

    public static function get_district_url($data, $dref) {
        if (!is_array($data) || !isset($data['leagues']) || !is_array($data['leagues'])) {
            return null;
        }
        foreach ($data['leagues'] as $league) {
            foreach ($league['districts'] as $district) {
                if (is_array($league) && isset($district['dref']) && $district['dref'] === $dref) {
                    return $district['dref'];
                }
            }
        }
        return null;
    }

    public static function sort_club_list(array $clubs): array
    {
        $classified_clubs = [
            'all' => [],
        ];
        foreach ($clubs as $club)
        {
            $district = isset($club['club_district']) ? TextHelper::deserialize($club['club_district']) : [];

            // if club_district is empty, add club to "all" category
            if (empty($district)) {
                $classified_clubs['all'][] = $club;
                continue;
            }

            $code = $district[0]['code'] ?? null;
            $lref = $district[0]['lref'] ?? null;
            $dref = $district[0]['dref'] ?? null;
            $file = $district[0]['file'] ?? null;

            if ($code !== null) {
                if (!isset($classified_clubs[$code])) {
                    $classified_clubs[$code] = [];
                }
                $classified_clubs[$code]['file'] = $file;

                if ($lref !== null) {
                    if (!isset($classified_clubs[$code][$lref])) {
                        $classified_clubs[$code][$lref] = [];
                    }

                    if ($dref !== null) {
                        if (!isset($classified_clubs[$code][$lref][$dref])) {
                            $classified_clubs[$code][$lref][$dref] = [];
                        }
                        $classified_clubs[$code][$lref][$dref][] = $club;
                    } else {
                        $classified_clubs[$code][$lref][] = $club;
                    }
                } else {
                    $classified_clubs[$code][] = $club;
                    $classified_clubs[$code]['file'][] = $file;
                }
            }
        }
        return $classified_clubs;
    }
}
?>
