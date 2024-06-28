<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmEventCache implements CacheData
{
	private $events = [];

	/**
	 * {@inheritdoc}
	 */
	public function synchronize()
	{
		$this->events = [];

		$now = new Date();
		$config = ScmConfig::load();

		$result = PersistenceContext::get_querier()->select('SELECT event.*
			FROM ' . ScmSetup::$scm_event_table . ' event
			WHERE (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
			ORDER BY id DESC', [
				'timestamp_now' => $now->get_timestamp()
            ]
        );

		while ($row = $result->fetch())
		{
			$this->events[$row['id']] = $row;
		}
		$result->dispose();
	}

	public function get_events()
	{
		return $this->events;
	}

	public function event_exists($id)
	{
		return array_key_exists($id, $this->events);
	}

	public function get_event($id)
	{
		if ($this->event_exists($id))
		{
			return $this->events[$id];
		}
		return null;
	}

	public function get_events_number()
	{
		return count($this->events);
	}

	/**
	 * Loads and returns the scm cached data.
	 * @return ScmCache The cached data
	 */
	public static function load()
	{
		return CacheManager::load(__CLASS__, 'scm', 'event');
	}

	/**
	 * Invalidates the current scm cached data.
	 */
	public static function invalidate()
	{
		CacheManager::invalidate('scm', 'event');
	}
}
?>
