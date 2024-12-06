<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmSeasonCache implements CacheData
{
	private $seasons = [];

	/**
	 * {@inheritdoc}
	 */
	public function synchronize()
	{
		$this->seasons = [];

		$result = PersistenceContext::get_querier()->select('SELECT *
			FROM ' . ScmSetup::$scm_season_table . '
			ORDER BY season_name DESC'
		);

		while ($row = $result->fetch())
		{
			$this->seasons[$row['id_season']] = $row;
		}
		$result->dispose();
	}

	public function get_seasons()
	{
		return $this->seasons;
	}

	public function season_exists($id)
	{
		return array_key_exists($id, $this->seasons);
	}

	public function get_season($id)
	{
		if ($this->season_exists($id))
		{
			return $this->seasons[$id];
		}
		return null;
	}

	public function get_seasons_number()
	{
		return count($this->seasons);
	}

	/**
	 * Loads and returns the scm cached data.
	 * @return ScmCache The cached data
	 */
	public static function load()
	{
		return CacheManager::load(__CLASS__, 'scm', 'seasons');
	}

	/**
	 * Invalidates the current scm cached data.
	 */
	public static function invalidate()
	{
		CacheManager::invalidate('scm', 'seasons');
	}
}
?>
