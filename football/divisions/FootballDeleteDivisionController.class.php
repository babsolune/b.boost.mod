<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

// namespace PHPBoost\Football\Controllers\Divisions;

class FootballDeleteDivisionController extends DefaultModuleController
{
	public function execute(HTTPRequestCustom $request)
	{
		AppContext::get_session()->csrf_get_protect();

		$division = $this->get_division($request);

		if (!$division->is_authorized_to_manage() || AppContext::get_current_user()->is_readonly())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}

		FootballDivisionService::delete_division($division->get_id_division());

		if (!FootballAuthorizationsService::check_authorizations()->manage_divisions())
			ContributionService::generate_cache();

		FootballCompetService::clear_cache();
		HooksService::execute_hook_action('delete', self::$module_id, $division->get_properties());

		AppContext::get_response()->redirect(($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), FootballUrlBuilder::manage_divisions()->rel()) ? $request->get_url_referrer() : FootballUrlBuilder::manage_divisions()), StringVars::replace_vars($this->lang['football.division.message.success.delete'], array('title' => $division->get_division_name())));
	}

	private function get_division(HTTPRequestCustom $request)
	{
		$id = $request->get_getint('id', 0);
		if (!empty($id))
		{
			try {
				return FootballDivisionService::get_division($id);
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}
	}
}
?>
