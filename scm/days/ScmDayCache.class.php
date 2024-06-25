<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmDayCache implements CacheData
{
	private $days = array();

	/**
	 * {@inheritdoc}
	 */
	public function synchronize()
	{
		$this->days = array();

		$result = PersistenceContext::get_querier()->select('SELECT day.*
			FROM ' . ScmSetup::$scm_day_table . ' day
			ORDER BY day.id_day'
		);

		while ($row = $result->fetch())
		{
			$this->days[$row['id_day']] = $row;
		}
		$result->dispose();
	}

	public function get_days() : array
	{
		return $this->days;
	}

	public function day_exists(int $id) : bool
	{
		return array_key_exists($id, $this->days);
	}

	public function get_day(int $id)
	{
		if ($this->day_exists($id))
		{
			return $this->days[$id];
		}
		return null;
	}

	public function get_days_number() : int
	{
		return count($this->days);
	}

	/**
	 * Loads and returns the scm cached data.
	 * @return ScmCache The cached data
	 */
	public static function load()
	{
		return CacheManager::load(__CLASS__, 'scm', 'days');
	}

	/**
	 * Invalidates the current scm cached data.
	 */
	public static function invalidate()
	{
		CacheManager::invalidate('scm', 'days');
	}
}
?>
