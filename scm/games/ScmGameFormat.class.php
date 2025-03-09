<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmGameFormat
{
    public static function format_categories($foreach, $c_class = false)
    {
        $view = new FileTemplate('scm/format/ScmFormatCategory.tpl');
        $lang = LangLoader::get_all_langs('scm');
        $view->add_lang($lang);

        $view->put('C_CLASS', $c_class);

        $categories = [];
        foreach($foreach as $game)
        {
            $category = ScmEventService::get_event($game['game_event_id'])->get_category();
            $categories[$category->get_id()][$game['game_event_id']][] = $game;
        }
        ksort($categories);

        foreach ($categories as $cat => $event)
        {
            $category = CategoriesService::get_categories_manager()->get_categories_cache()->get_category($cat);
            $view->assign_block_vars('categories', [
                'CATEGORY_NAME' => $category->get_name(),
                'U_CATEGORY'    => ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel()
            ]);

            foreach ($event as $event_id => $games)
            {
                $division_id = ScmEventService::get_event($event_id)->get_division_id();
                $view->assign_block_vars('categories.events', [
                    'C_IS_SUB'       => ScmEventService::is_sub_event($event_id),
                    'MASTER_EVENT'   => ScmEventService::get_master_division($event_id),
                    'U_MASTER_EVENT' => ScmEventService::get_master_url($event_id),
                    'EVENT'          => ScmDivisionService::get_division($division_id)->get_division_name(),
                    'U_EVENT'        => ScmUrlBuilder::event_home($event_id, ScmEventService::get_event_slug($event_id))->rel(),
                ]);
                foreach($games as $game)
                {
                    $item = new ScmGame();
                    $item->set_properties($game);
                    $view->assign_block_vars('categories.events.items', array_merge($item->get_template_vars(), [
                        'C_LATE'         => $item->get_game_cluster() < ScmDayService::get_last_day($item->get_game_event_id()),
                        'C_HAT_RANKING' => ScmParamsService::get_params($item->get_game_event_id())->get_hat_ranking()
                    ]));
                }
            }
        }
        return $view;
    }

    public static function format_cluster($foreach, $c_class = false, $round = false)
    {
        $view = new FileTemplate('scm/format/ScmFormatCluster.tpl');
        $lang = LangLoader::get_all_langs('scm');
        $view->add_lang($lang);
        usort($foreach, function($a, $b) {
            return strcmp($a['game_date'], $b['game_date']);
        });


        $blocks = $event_id = [];
        foreach($foreach as $game)
        {
            $event_id[] = $game['game_event_id'];
            if ($round)
                $blocks[$game['game_round']][Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][] = $game;
            else
                $blocks[Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][$game['game_round']][] = $game;
        }
        $event_id = implode('', array_unique($event_id));

        $view->put_all([
            'C_CLASS' => $c_class,
            'C_ONE_DAY' => ScmEventService::get_event($event_id)->get_oneday()
        ]);

        foreach ($blocks as $block => $sub_blocks)
        {
            $view->assign_block_vars('blocks', [
                'C_ROUND' => $round,
                'TITLE' => $block
            ]);

            foreach ($sub_blocks as $sub_block => $games)
            {
                $view->assign_block_vars('blocks.sub_blocks', [
                    'C_SEVERAL_DATES' => !ScmEventService::get_event($event_id)->get_oneday(),
                    'C_SUB_ROUND' => $round,
                    'SUB_TITLE' => $sub_block
                ]);
                foreach($games as $game)
                {
                    $item = new ScmGame();
                    $item->set_properties($game);
                    if (!$round && $block == Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT))
                        $view->assign_block_vars('blocks.sub_blocks.items', $item->get_template_vars());
                    else
                        $view->assign_block_vars('blocks.sub_blocks.items', $item->get_template_vars());
                }
            }
        }
        return $view;
    }

    public static function format_event($event_id, $foreach, $c_class = false, $round = false, $bracket = true)
    {
        $view = new FileTemplate('scm/format/ScmFormatEvent.tpl');
        $lang = LangLoader::get_all_langs('scm');
        $view->add_lang($lang);

        $view->put('C_CLASS', $c_class);

        foreach ($foreach as $block => $games)
        {
            $view->assign_block_vars('blocks', [
                'C_SEVERAL_DATES' => !ScmEventService::get_event($event_id)->get_oneday(),
                'C_ROUND' => $round,
                'TITLE' => $block
            ]);
            foreach($games as $game)
            {
                $item = new ScmGame();
                $item->set_properties($game);

                if (!$round && !$bracket) {
                    $c_link = false;
                    $link_name = '';
                } elseif($round && !$bracket) {
                    $c_link = true;
                    $link_name = $lang['scm.group'] . ' ' . ScmGroupService::ntl($item->get_game_cluster());
                } elseif(!$round && $bracket) {
                    $c_link = false;
                    $link_name = '';
                } else {
                    $c_link = false;
                    $link_name = '';
                }
                $view->assign_block_vars('blocks.items', $item->get_template_vars(), [
                    'C_LINK' => $c_link,
                    'CLUSTER_NAME' => $link_name,
                    'U_GROUP' => ScmUrlBuilder::display_groups_rounds($event_id, ScmEventService::get_event_slug($event_id), $item->get_game_cluster())->rel()
                ]);
            }
        }
        return $view;
    }
}

?>