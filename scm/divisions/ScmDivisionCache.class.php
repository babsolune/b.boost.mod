<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmDivisionCache implements CacheData
{
	private $divisions = [];

	/**
	 * {@inheritdoc}
	 */
	public function synchronize()
	{
		$this->divisions = [];

		$result = PersistenceContext::get_querier()->select('SELECT division.*
			FROM ' . ScmSetup::$scm_division_table . ' division
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
	 * Loads and returns the scm cached data.
	 * @return ScmCache The cached data
	 */
	public static function load()
	{
		return CacheManager::load(__CLASS__, 'scm', 'divisions');
	}

	/**
	 * Invalidates the current scm cached data.
	 */
	public static function invalidate()
	{
		CacheManager::invalidate('scm', 'divisions');
	}
}
?>
