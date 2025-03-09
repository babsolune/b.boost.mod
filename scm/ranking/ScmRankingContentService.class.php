<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmRankingContentService
{
	private static $db_querier;

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

    /** Return the ranking of the event from table with all its properties */
	public static function get_ranking(int $event_id):ScmRanking
	{
		$row = self::$db_querier->select_single_row_query('SELECT ranking.*
            FROM ' . ScmSetup::$scm_ranking_table . ' ranking
            WHERE ranking.ranking_event_id = :event_id', [
                'event_id' => $event_id
            ]
        );
		$ranking = new ScmRanking();
		$ranking->set_properties($row);
		return $ranking;
	}

    /** Get only content data from table */
    public static function get_ranking_content(int $event_id):array
    {
        if(self::get_ranking($event_id)->get_content())
        {
            if (json_last_error() === JSON_ERROR_NONE) {
                $content = self::get_ranking($event_id)->get_content();
                return json_decode($content, true);
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    /** Calculate ranking form 1 cluster */
    public static function set_cluster_ranking_content(int $event_id, int $cluster):array
    {
        $params = ScmParamsService::get_params($event_id);
        $rankings = [];
        if (self::is_championship($event_id))
        {
            foreach (ScmRankingService::general_days_ranking($event_id, $cluster) as $id => $options)
            {
                $team_name = ScmTeamService::get_team_name($options['team_id']);
                $rankings[] = ['rank' => $id + 1] + ['team_name' => $team_name] + $options;
            }
        }
        elseif (self::is_tournament($event_id) && $params->get_hat_ranking())
        {
            foreach (ScmRankingService::general_days_ranking($event_id, $cluster) as $id => $options)
            {
                $team_name = ScmTeamService::get_team_name($options['team_id']);
                $rankings[] = ['rank' => $id + 1] + ['team_name' => $team_name] + $options;
            }
        }
        elseif (self::is_tournament($event_id))
        {
            foreach (ScmRankingService::general_groups_ranking($event_id, $cluster) as $id => $options)
            {
                $team_name = ScmTeamService::get_team_name($options['team_id']);
                $rankings[] = ['rank' => $id + 1] + ['team_name' => $team_name] + $options;
            }
        }
        return $rankings;
    }

    /** Define content to set in table and insert in table */
    public static function set_ranking_content(int $event_id, int $cluster):void
    {
        $content = self::get_ranking_content($event_id);
        $params = ScmParamsService::get_params($event_id);

        if (self::is_championship($event_id))
            $cluster_number = ScmDayService::get_last_day($event_id);
        elseif (self::is_tournament($event_id) && $params->get_hat_ranking())
            $cluster_number = ScmGroupService::get_last_matchday_hat($event_id);
        elseif (self::is_tournament($event_id))
            $cluster_number = $params->get_groups_number();

        if(isset($content[$cluster]))
        {
            for ($i = $cluster; $i <= $cluster_number; $i++)
            {
                $content[$i] = self::set_cluster_ranking_content($event_id, $i);
            }
        }
        else
            $content[$cluster] = self::set_cluster_ranking_content($event_id, $cluster);

        $content = json_encode($content);
        self::$db_querier->update(ScmSetup::$scm_ranking_table, ['content' => $content], 'WHERE ranking_event_id = :event_id', ['event_id' => $event_id]);
	}

    /** Check if event is championshio */
    public static function is_championship(int $event_id):bool
    {
        $event = ScmEventService::get_event($event_id);
        return $event->get_event_type() == ScmEvent::CHAMPIONSHIP;
    }

    /** Check if event is tournament */
    public static function is_tournament(int $event_id):bool
    {
        $event = ScmEventService::get_event($event_id);
        return $event->get_event_type() == ScmEvent::TOURNAMENT;
    }
}