<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballDeleteSeasonController extends DefaultModuleController
{
	public function execute(HTTPRequestCustom $request)
	{
		AppContext::get_session()->csrf_get_protect();

		$season = $this->get_season($request);

		if (!$season->is_authorized_to_manage() || AppContext::get_current_user()->is_readonly())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}

		FootballSeasonService::delete_season($season->get_id_season());

		FootballCompetService::clear_cache();
		HooksService::execute_hook_action('delete', self::$module_id, $season->get_properties());

		AppContext::get_response()->redirect(($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), FootballUrlBuilder::manage_seasons()->rel()) ? $request->get_url_referrer() : FootballUrlBuilder::manage_seasons()), StringVars::replace_vars($this->lang['football.season.message.success.delete'], array('title' => $season->get_season_name())));
	}

	private function get_season(HTTPRequestCustom $request)
	{
		$id = $request->get_getint('id', 0);
		if (!empty($id))
		{
			try {
				return FootballSeasonService::get_season($id);
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}
	}
}
?>
