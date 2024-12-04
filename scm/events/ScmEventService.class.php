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
	public static function count($condition = '', $params = [])
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
		self::$db_querier->update(ScmSetup::$scm_event_table, $event->get_properties(), 'WHERE id = :id', ['id' => $event->get_id()]);
	}

	/**
	 * @desc Update the number of views of a event.
	 * @param ScmEvent $event : ScmEvent to update
	 */
	public static function update_views_number(ScmEvent $event)
	{
		self::$db_querier->update(ScmSetup::$scm_event_table, ['views_number' => $event->get_views_number()], 'WHERE id = :id', ['id' => $event->get_id()]);
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
		self::$db_querier->delete(ScmSetup::$scm_event_table, 'WHERE id = :id', ['id' => $id]);

		ScmTeamService::delete_teams($id);
		ScmParamsService::delete_params($id);
		ScmDayService::delete_days($id);
		ScmGameService::delete_games($id);

		self::$db_querier->delete(DB_TABLE_EVENTS, 'WHERE module=:module AND id_in_module=:id', ['module' => 'scm', 'id' => $id]);
	}

	/**
	 * Return the event with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_event(int $event_id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT event.*
            FROM ' . ScmSetup::$scm_event_table . ' event
            WHERE event.id = :event_id', [
                'module_id' => self::$module_id,
                'event_id' => $event_id
            ]
        );

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
            WHERE params.params_event_id = :id', [
                'id' => $id
            ]
        );

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

    public static function get_event_scoring_type(int $event_id):string
    {
        return self::get_event($event_id)->get_scoring_type();
    }

    public static function is_master(int $event_id):bool
    {
        $master_list = [];
        foreach (self::get_events() as $event)
        {
            $master_list[] = $event['master_id'];
        }
        $master_list = array_unique($master_list);

        return in_array($event_id, $master_list);
    }

    public static function get_master_name(int $event_id):string
    {
        $event = self::get_event($event_id);
        $master_event_id = $event->get_master_id();
        if($master_event_id)
        {
            $master_event = self::get_event($master_event_id);
            $division = ScmDivisionService::get_division($master_event->get_division_id());
            $season = ScmSeasonService::get_season($master_event->get_season_id());
        }

        return $master_event_id ? $division->get_division_name() . ' ' . $season->get_season_name() : '';
    }

    public static function get_master_division(int $event_id):string
    {
        $event = self::get_event($event_id);
        $master_event_id = $event->get_master_id();
        if($master_event_id)
        {
            $master_event = self::get_event($master_event_id);
            $division = ScmDivisionService::get_division($master_event->get_division_id());
        }

        return $master_event_id ? $division->get_division_name() : '';
    }

    public static function get_master_season(int $event_id):string
    {
        $event = self::get_event($event_id);
        $master_event_id = $event->get_master_id();
        if($master_event_id)
        {
            $master_event = self::get_event($master_event_id);
            $season = ScmSeasonService::get_season($master_event->get_season_id());
        }

        return $master_event_id ? $season->get_season_name() : '';
    }

    public static function get_master_url(int $event_id):string
    {
        $event = self::get_event($event_id);
        $master_event_id = $event->get_master_id();
        if ($master_event_id)
        {
            $master_event = self::get_event($master_event_id);
        }

        return $master_event_id ? ScmUrlBuilder::event_home($master_event_id, $master_event->get_event_slug())->rel() : '';
    }

    public static function is_sub_event(int $event_id):bool
    {
        $event = self::get_event($event_id);

        return $event->get_is_sub();
    }

    public static function get_sub_list(int $event_id):array
    {
        $sub_list = [];
        foreach (self::get_events() as $event)
        {
            if($event['master_id'] == $event_id)
            $sub_list[] = $event;
        }
        return $sub_list;
    }

    public static function check_event_display(int $event_id):bool
    {
        $now = new Date();
        $events = [];
        foreach (self::get_events() as $event)
        {
            $item = new ScmEvent();
            $item->set_properties($event);

            $start_date = $item->get_start_date()->get_timestamp();
            $end_date = $item->get_end_date()->get_timestamp();
            if ($start_date < $now->get_timestamp() && $end_date > $now->get_timestamp() && !$item->get_is_sub())
                $events[] = $item->get_id();
        }

        return in_array($event_id, $events);
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
