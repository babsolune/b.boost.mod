<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmExtensionPointProvider extends ItemsModuleExtensionPointProvider
{
	public function home_page()
	{
        $config = ScmConfig::load();
        if ($config->get_homepage() == ScmConfig::EVENT_LIST)
            return new DefaultHomePageDisplay($this->get_id(), ScmCurrentEventsController::get_view($this->get_id()));
        elseif ($config->get_homepage() == ScmConfig::GAME_LIST)
            return new DefaultHomePageDisplay($this->get_id(), ScmAroundGamesController::get_view($this->get_id()));
        elseif ($config->get_homepage() == ScmConfig::CATEGORIES)
            return new DefaultHomePageDisplay($this->get_id(), ScmCategoryController::get_view($this->get_id()));
	}

    public function menus()
    {
        return new ModuleMenus([
            new ScmMiniNextGame(),
            new ScmMiniPrevGame()
        ]);
    }

    public function js_files()
    {
        $js_file = new ModuleJsFiles();
        $js_file->adding_running_module_displayed_file('chart.min.js');
        // $js_file->adding_running_module_displayed_file('loader.js');
        return $js_file;
    }

    public function user()
    {
        return false;
    }
}
?>
