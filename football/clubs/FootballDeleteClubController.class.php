<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballDeleteClubController extends DefaultModuleController
{
	public function execute(HTTPRequestCustom $request)
	{
		AppContext::get_session()->csrf_get_protect();

		$club = $this->get_club($request);

		if (!$club->is_authorized_to_manage() || AppContext::get_current_user()->is_readonly())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}

		FootballClubService::delete_club($club->get_id_club());

		if (!FootballAuthorizationsService::check_authorizations()->manage_clubs())
			ContributionService::generate_cache();

		FootballCompetService::clear_cache();
		HooksService::execute_hook_action('delete', self::$module_id, $club->get_properties());

		AppContext::get_response()->redirect(
            ($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), FootballUrlBuilder::manage_clubs()->rel()) ? $request->get_url_referrer() : FootballUrlBuilder::manage_clubs()),
            StringVars::replace_vars($this->lang['football.club.message.success.delete'], array('name' => $club->get_club_name()))
        );
	}

	private function get_club(HTTPRequestCustom $request)
	{
		$id = $request->get_getint('id', 0);
		if (!empty($id))
		{
			try {
				return FootballClubService::get_club($id);
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}
	}
}
?>
