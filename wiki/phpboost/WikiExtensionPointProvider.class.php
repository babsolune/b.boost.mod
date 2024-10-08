<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 10 11
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class WikiExtensionPointProvider extends ItemsModuleExtensionPointProvider
{
	public function home_page()
	{
        $config = WikiConfig::load();
        if ($config->get_homepage() == WikiConfig::EXPLORER)
            return new DefaultHomePageDisplay($this->get_id(), WikiExplorerController::get_view($this->get_id()));
        elseif ($config->get_homepage() == WikiConfig::OVERVIEW)
            return new DefaultHomePageDisplay($this->get_id(), WikiIndexController::get_view($this->get_id()));
        else
            return new DefaultHomePageDisplay($this->get_id(), WikiCategoryController::get_view($this->get_id()));
	}

	public function user()
	{
		return new WikiUserExtensionPoint();
	}
}
?>
