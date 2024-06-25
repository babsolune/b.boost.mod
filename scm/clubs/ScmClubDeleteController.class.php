<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmClubDeleteController extends DefaultModuleController
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

		ScmClubService::delete_club($club->get_id_club());

		if (!ScmAuthorizationsService::check_authorizations()->manage_clubs())
			ContributionService::generate_cache();

		ScmEventService::clear_cache();
		HooksService::execute_hook_action('club_delete', 'scm', array_merge($club->get_properties(), ['title' => $club->get_club_name()]));

		AppContext::get_response()->redirect(
            ($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), ScmUrlBuilder::manage_clubs()->rel()) ? $request->get_url_referrer() : ScmUrlBuilder::manage_clubs()),
            StringVars::replace_vars($this->lang['scm.club.message.success.delete'], ['name' => $club->get_club_name()])
        );
	}

	private function get_club(HTTPRequestCustom $request)
	{
		$id = $request->get_getint('club_id', 0);
		if (!empty($id))
		{
			try {
				return ScmClubService::get_club($id);
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}
	}
}
?>
