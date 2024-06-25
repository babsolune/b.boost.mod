<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmTeamCache implements CacheData
{
	private $teams = array();

	/**
	 * {@inheritdoc}
	 */
	public function synchronize()
	{
		$this->teams = array();

		$result = PersistenceContext::get_querier()->select('SELECT team.*, event.*
			FROM ' . ScmSetup::$scm_team_table . ' team
			LEFT JOIN ' . ScmSetup::$scm_event_table . ' event ON event.id = team.team_event_id
			WHERE team.team_event_id = event.id
			ORDER BY team.id_team DESC'
		);

		while ($row = $result->fetch())
		{
			$this->teams[$row['id_team']] = $row;
		}
		$result->dispose();
	}

	public function get_teams()
	{
		return $this->teams;
	}

	public function team_exists($id)
	{
		return array_key_exists($id, $this->teams);
	}

	public function get_team($id)
	{
		if ($this->team_exists($id))
		{
			return $this->teams[$id];
		}
		return null;
	}

	public function get_teams_number()
	{
		return count($this->teams);
	}

	/**
	 * Loads and returns the scm cached data.
	 * @return ScmCache The cached data
	 */
	public static function load()
	{
		return CacheManager::load(__CLASS__, 'scm', 'teams');
	}

	/**
	 * Invalidates the current scm cached data.
	 */
	public static function invalidate()
	{
		CacheManager::invalidate('scm', 'teams');
	}
}
?>
