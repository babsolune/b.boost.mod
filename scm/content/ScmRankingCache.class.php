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
    public static function set_ranking_cache(int $event_id, $cluster)
    {
        $content = self::set_content($event_id, $cluster);
        file_put_contents(PATH_TO_ROOT . '/scm/cache/' . self::cache_file($event_id) . '.json', $content);
    }

    public static function get_ranking($event_id)
    {
        $content = file_get_contents(PATH_TO_ROOT . '/scm/cache/' . self::cache_file($event_id) . '.json');
        return json_decode($content, true);
    }

    public static function set_content(int $event_id, $cluster)
    {
        $rankings = [];
        foreach (ScmRankingService::general_days_ranking($event_id, $cluster) as $id => $options)
        {
            $rankings[] = ['rank' => $id + 1] + $options;
        }
        $cache_file = '/scm/cache/' . self::cache_file($event_id) . '.json';
        if (file_exists(PATH_TO_ROOT . $cache_file))
        {
            $json_file = file_get_contents(PATH_TO_ROOT . $cache_file);
            $content = json_decode($json_file, true);
        }
        else
            $content = [];

            if(isset($content[$cluster]))
                $content[$cluster] = $rankings;
            else
                $content[$cluster] = $rankings;
        return json_encode($content);
    }

    public static function cache_file($event_id)
    {
        $event = ScmEventService::get_event($event_id);
        $category = $event->get_category();
        $division = self::division_name($event_id);
        $c_is_sub = $event->get_is_sub();
        $division_master = $c_is_sub ? self::division_name($event->get_master_id()) : '';
        $season = self::season_name($event_id);
        return $category->get_name() . '-' . $season . ($c_is_sub ? '-' . $division_master : '') . '-' . $division;
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
}
?>
