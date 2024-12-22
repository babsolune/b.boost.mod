<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmSingleGamesService
{
    public static function display_games($foreach, $css_class = '')
    {
        $view = new FileTemplate('scm/ScmSingleGamesController.tpl');
        $lang = LangLoader::get_all_langs('scm');
        $view->add_lang($lang);

        $categories = [];
        foreach($foreach as $game)
        {
            $category = ScmEventService::get_event($game['game_event_id'])->get_category();
            $categories[$category->get_id()][] = $game;
        }
        ksort($categories);

        foreach ($categories as $k => $games)
        {
            $category = CategoriesService::get_categories_manager()->get_categories_cache()->get_category($k);
            $view->assign_block_vars('categories', [
                'CSS_CLASS'     => $css_class,
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
}

?>