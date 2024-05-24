<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballExtensionPointProvider extends ItemsModuleExtensionPointProvider
{
	public function home_page()
	{
		return new DefaultHomePageDisplay($this->get_id(), FootballHomeController::get_view($this->get_id()));
	}

	public function css_files()
	{
		$module_css_files = new ModuleCssFiles();
        $module_css_files->adding_running_module_displayed_file('football.css');
        $module_css_files->adding_running_module_displayed_file('bracket.css');
		return $module_css_files;
	}
}
?>
