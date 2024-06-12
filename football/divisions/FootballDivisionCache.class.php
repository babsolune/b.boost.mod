<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballDivisionCache implements CacheData
{
	private $divisions = array();

	/**
	 * {@inheritdoc}
	 */
	public function synchronize()
	{
		$this->divisions = array();

		$result = PersistenceContext::get_querier()->select('SELECT division.*
			FROM ' . FootballSetup::$football_division_table . ' division
			ORDER BY division.id_division DESC'
		);

		while ($row = $result->fetch())
		{
			$this->divisions[$row['id_division']] = $row;
		}
		$result->dispose();
	}

	public function get_divisions() : array
	{
		return $this->divisions;
	}

	public function division_exists(int $id) : bool
	{
		return array_key_exists($id, $this->divisions);
	}

	public function get_division(int $id)
	{
		if ($this->division_exists($id))
		{
			return $this->divisions[$id];
		}
		return null;
	}

	public function get_divisions_number() : int
	{
		return count($this->divisions);
	}

	/**
	 * Loads and returns the football cached data.
	 * @return FootballCache The cached data
	 */
	public static function load()
	{
		return CacheManager::load(__CLASS__, 'football', 'divisions');
	}

	/**
	 * Invalidates the current football cached data.
	 */
	public static function invalidate()
	{
		CacheManager::invalidate('football', 'divisions');
	}
}
?>
