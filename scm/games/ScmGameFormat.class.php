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
    public static function format_categories($foreach)
    {
        $view = new FileTemplate('scm/format/ScmFormatCategory.tpl');
        $lang = LangLoader::get_all_langs('scm');
        $view->add_lang($lang);

        $categories = [];
        foreach($foreach as $game)
        {
            $category = ScmEventService::get_event($game['game_event_id'])->get_category();
            $categories[$category->get_id()][] = $game;
        }
        ksort($categories);

        foreach ($categories as $cat => $games)
        {
            $category = CategoriesService::get_categories_manager()->get_categories_cache()->get_category($cat);
            $view->assign_block_vars('categories', [
                'CATEGORY_NAME' => $category->get_name(),
                'U_CATEGORY'    => ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel()
            ]);

            foreach($games as $game)
            {
                $item = new ScmGame();
                $item->set_properties($game);
                $view->assign_block_vars('categories.items', $item->get_template_vars());
            }
        }
        return $view;
    }

    public static function format_cluster($foreach, $round = false)
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

        foreach ($blocks as $block => $sub_blocks)
        {
            $view->assign_block_vars('blocks', [
                'C_ROUND' => $round,
                'TITLE' => $block
            ]);

            foreach ($sub_blocks as $sub_block => $games)
            {
                $view->assign_block_vars('blocks.sub_blocks', [
                    'C_SEVERAL_DATES' => !ScmGameService::one_day_event($event_id),
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

    public static function format_event($event_id, $foreach, $round = false, $bracket = true)
    {
        $view = new FileTemplate('scm/format/ScmFormatEvent.tpl');
        $lang = LangLoader::get_all_langs('scm');
        $view->add_lang($lang);

        foreach ($foreach as $block => $games)
        {
            $view->assign_block_vars('blocks', [
                'C_SEVERAL_DATES' => !ScmGameService::one_day_event($event_id),
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