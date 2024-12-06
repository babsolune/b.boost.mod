<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmGameCache implements CacheData
{
	private $games = [];

	/**
	 * {@inheritdoc}
	 */
	public function synchronize()
	{
		$this->games = [];
        $now = new Date();
		$result = PersistenceContext::get_querier()->select('SELECT game.*, event.*
			FROM ' . ScmSetup::$scm_game_table . ' game
            LEFT JOIN ' . ScmSetup::$scm_event_table . ' event ON event.id = game.game_event_id
            WHERE (event.published = 1 OR (event.published = 2 AND event.publishing_start_date < :timestamp_now AND (event.publishing_end_date > :timestamp_now OR event.publishing_end_date = 0)))
			ORDER BY game.id_game', [
                'timestamp_now' => $now->get_timestamp()
            ]
		);

		while ($row = $result->fetch())
		{
			$this->games[$row['id_game']] = $row;
		}
		$result->dispose();
	}

	public function get_games() : array
	{
		return $this->games;
	}

	public function game_exists(int $id) : bool
	{
		return array_key_exists($id, $this->games);
	}

	public function get_game(int $id)
	{
		if ($this->game_exists($id))
		{
			return $this->games[$id];
		}
		return null;
	}

	public function get_games_number() : int
	{
		return count($this->games);
	}

	/**
	 * Loads and returns the scm cached data.
	 * @return ScmCache The cached data
	 */
	public static function load()
	{
		return CacheManager::load(__CLASS__, 'scm', 'games');
	}

	/**
	 * Invalidates the current scm cached data.
	 */
	public static function invalidate()
	{
		CacheManager::invalidate('scm', 'games');
	}
}
?>
