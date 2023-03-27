<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class GuideCache implements CacheData
{
	private $items = array();

	/**
	 * {@inheritdoc}
	 */
	public function synchronize()
	{
		$this->items = array();

		$now = new Date();
		$config = GuideConfig::load();

		$result = PersistenceContext::get_querier()->select('
			SELECT guide.*, notes.average_notes, notes.notes_number
			FROM ' . GuideSetup::$guide_table . ' guide
			LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = guide.id AND notes.module_name = \'guide\'
			WHERE (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))' . ($config->is_limit_oldest_file_day_in_menu_enabled() ? 'AND update_date > :oldest_file_date' : '') . '
			ORDER BY i_order ASC
			LIMIT :files_number_in_menu OFFSET 0', array(
				'timestamp_now' => $now->get_timestamp()
		));

		while ($row = $result->fetch())
		{
			$this->items[$row['id']] = $row;
		}
		$result->dispose();
	}

	public function get_items()
	{
		return $this->items;
	}

	public function item_exists($id)
	{
		return array_key_exists($id, $this->items);
	}

	public function get_item($id)
	{
		if ($this->item_exists($id))
		{
			return $this->items[$id];
		}
		return null;
	}

	public function get_items_number()
	{
		return count($this->items);
	}

	/**
	 * Loads and returns the guide cached data.
	 * @return GuideCache The cached data
	 */
	public static function load()
	{
		return CacheManager::load(__CLASS__, 'guide', 'minimenu');
	}

	/**
	 * Invalidates the current guide cached data.
	 */
	public static function invalidate()
	{
		CacheManager::invalidate('guide', 'minimenu');
	}
}
?>
