<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmRankingCache
{
    public static function set_cache_file_ranking(int $event_id, $cluster)
    {
        $content = self::set_cache_content($event_id, $cluster);
        file_put_contents(PATH_TO_ROOT . '/scm/ranking/cache/' . self::cache_file($event_id) . '.json', $content);
    }

    public static function get_cache_file_ranking($event_id)
    {
        $filepath = '/scm/ranking/cache/' . self::cache_file($event_id) . '.json';
        if(file_exists(PATH_TO_ROOT . $filepath))
        {
            if (json_last_error() === JSON_ERROR_NONE) {
                $content = file_get_contents(PATH_TO_ROOT . $filepath);
                return json_decode($content, true);
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    public static function set_cache_cluster_ranking(int $event_id, $cluster)
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

    public static function set_cache_content($event_id, $cluster)
    {
        $content = self::get_cache_file_ranking($event_id);
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
                $content[$i] = self::set_cache_cluster_ranking($event_id, $i);
            }
        }
        else
            $content[$cluster] = self::set_cache_cluster_ranking($event_id, $cluster);
        return json_encode($content);
    }

    public static function cache_file($event_id)
    {
        $event = ScmEventService::get_event($event_id);
        $pool = $event->get_pool() ? '-' . $event->get_pool() : '';
        $category = $event->get_category();
        $division = self::division_name($event_id);
        $c_is_sub = $event->get_is_sub();
        $division_master = $c_is_sub ? self::division_name($event->get_master_id()) : '';
        $season = self::season_name($event_id);
        return Url::encode_rewrite($category->get_name() . '-' . $season . ($c_is_sub ? '-' . $division_master : '') . '-' . $division . $pool);
    }

    public static function cache_file_link($event_id)
    {
        $filename = self::cache_file($event_id);
        $file = new File(TPL_PATH_TO_ROOT . '/scm/ranking/cache/' . $filename . '.json');
        return $file->get_path();
    }

    public static function division_name($event_id)
    {
        $event = ScmEventService::get_event($event_id);
        return ScmDivisionService::get_division($event->get_division_id())->get_division_name();
    }

    public static function season_name($event_id)
    {
        $event = ScmEventService::get_event($event_id);
        return ScmSeasonService::get_season($event->get_season_id())->get_season_name();
    }

    public static function is_championship($event_id)
    {
        $event = ScmEventService::get_event($event_id);
        return $event->get_event_type() == ScmEvent::CHAMPIONSHIP;
    }

    public static function is_tournament($event_id)
    {
        $event = ScmEventService::get_event($event_id);
        return $event->get_event_type() == ScmEvent::TOURNAMENT;
    }
}
?>
