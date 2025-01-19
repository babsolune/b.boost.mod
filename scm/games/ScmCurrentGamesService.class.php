<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmCurrentGamesService
{
    public static function display_current_games()
    {
        $view = new FileTemplate('scm/ScmCurrentGamesController.tpl');
        $lang = LangLoader::get_all_langs('scm');
        $view->add_lang($lang);

        $view->put_all([
            'C_CURRENT_GAMES' => count(ScmGameService::get_current_games()) > 0,
            'C_BEFORE_GAMES' => count(ScmGameService::get_before_current_games()) > 0,
            'GAMES_LIST'        => ScmGameFormat::format_categories(ScmGameService::get_current_games(), true),
            'BEFORE_GAMES_LIST' => ScmGameFormat::format_categories(ScmGameService::get_before_current_games(), true),
        ]);
        return $view;
    }
}

?>