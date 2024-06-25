<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmEventService
{
	private static $db_querier;
	protected static $module_id = 'scm';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/**
	 * Count events number.
	 * @param string $condition (optional) : Restriction to apply to the list of events
	 */
	public static function count($condition = '', $params = array())
	{
		return self::$db_querier->count(ScmSetup::$scm_event_table, $condition, $params);
	}

	/**
	 * Create a new entry in the database table.
	 * @param ScmEvent $event : new ScmEvent
	 */
	public static function add(ScmEvent $event)
	{
		$result = self::$db_querier->insert(ScmSetup::$scm_event_table, $event->get_properties());
        ScmParamsService::add_params($result->get_last_inserted_id());

		return $result->get_last_inserted_id();
	}

	/**
	 * @desc Update an entry.
	 * @param ScmEvent $event : ScmEvent to update
	 */
	public static function update(ScmEvent $event)
	{
		self::$db_querier->update(ScmSetup::$scm_event_table, $event->get_properties(), 'WHERE id = :id', array('id' => $event->get_id()));
	}

	/**
	 * @desc Update the number of views of a event.
	 * @param ScmEvent $event : ScmEvent to update
	 */
	public static function update_views_number(ScmEvent $event)
	{
		self::$db_querier->update(ScmSetup::$scm_event_table, array('views_number' => $event->get_views_number()), 'WHERE id = :id', array('id' => $event->get_id()));
	}

	/**
	 * Delete an entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param string[] $params : Params of the condition
	 */
	public static function delete(int $id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
		self::$db_querier->delete(ScmSetup::$scm_event_table, 'WHERE id = :id', array('id' => $id));

		ScmTeamService::delete_teams($id);
		ScmParamsService::delete_params($id);
		ScmDayService::delete_days($id);
		ScmGameService::delete_games($id);

		self::$db_querier->delete(DB_TABLE_EVENTS, 'WHERE module=:module AND id_in_module=:id', array('module' => 'scm', 'id' => $id));
	}

	/**
	 * Return the event with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_event(int $event_id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT event.*
		FROM ' . ScmSetup::$scm_event_table . ' event
		WHERE event.id = :event_id', array(
			'module_id' => self::$module_id,
			'event_id' => $event_id
		));

		$event = new ScmEvent();
		$event->set_properties($row);
		return $event;
	}

    public static function get_event_slug($event_id) : string 
    {
        $event = self::get_event($event_id);
        return $event->get_event_slug();
    }
    
	/**
	 * Return the event with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_events()
	{
		$results = self::$db_querier->select('SELECT event.*
		FROM ' . ScmSetup::$scm_event_table . ' event');

		return $results;
	}

	/**
	 * Return the event with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_params(int $id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT params.*, event.*
		FROM ' . ScmSetup::$scm_params_table . ' params
		LEFT JOIN ' . ScmSetup::$scm_event_table . ' event ON event.id = params.params_event_id
		WHERE params.params_event_id = :id', array(
			'id' => $id
		));

		$params = new ScmParams();
		$params->set_properties($row);
		return $params;
	}

    public static function get_event_type(int $event_id)
    {
        return ScmDivisionService::get_division(self::get_event($event_id)->get_division_id())->get_event_type();
    }

    public static function get_event_game_type(int $event_id)
    {
        return ScmDivisionService::get_division(self::get_event($event_id)->get_division_id())->get_game_type();
    }

	public static function clear_cache()
	{
		Feed::clear_cache('scm');
		ScmEventCache::invalidate();
		ScmClubCache::invalidate();
		ScmDivisionCache::invalidate();
		ScmSeasonCache::invalidate();
		ScmTeamCache::invalidate();
		ScmGameCache::invalidate();
        CategoriesService::get_categories_manager()->regenerate_cache();
	}
}
?>
