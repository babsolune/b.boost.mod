<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballClubDeadLinkController extends AbstractController
{
	private $club;

	public function execute(HTTPRequestCustom $request)
	{
		$id = $request->get_getint('id', 0);

		if (!empty($id) && AppContext::get_current_user()->check_level(User::MEMBER_LEVEL))
		{
			try {
				$this->club = FootballClubService::get_club($id);
			} catch (RowNotFoundException $e) {
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}

		if ($this->club !== null && (!FootballAuthorizationsService::check_authorizations($this->club->get_id_category())->read() || !FootballAuthorizationsService::check_authorizations()->display_download_link()))
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
		else if ($this->club !== null && $this->club->is_published())
		{
			if (!PersistenceContext::get_querier()->row_exists(PREFIX . 'events', 'WHERE id_in_module=:id_in_module AND module=\'football\' AND current_status = 0', array('id_in_module' => $this->club->get_id_club())))
			{
				$contribution = new Contribution();
				$contribution->set_id_in_module($this->club->get_id_club());
				$contribution->set_entitled(StringVars::replace_vars(LangLoader::get_message('contribution.dead.link.name', 'contribution-lang'), array('link_name' => $this->club->get_club_name())));
				$contribution->set_fixing_url(FootballUrlBuilder::edit_club($this->club->get_id())->relative());
				$contribution->set_description(LangLoader::get_message('contribution.dead.link.clue', 'contribution-lang'));
				$contribution->set_poster_id(AppContext::get_current_user()->get_id());
				$contribution->set_module('football');
				$contribution->set_type('alert');
				$contribution->set_auth(
					Authorizations::capture_and_shift_bit_auth(
						CategoriesService::get_categories_manager()->get_heritated_authorizations($this->club->get_id_category(), Category::MODERATION_AUTHORIZATIONS, Authorizations::AUTH_CHILD_PRIORITY),
						Category::MODERATION_AUTHORIZATIONS, Contribution::CONTRIBUTION_AUTH_BIT
					)
				);

				ContributionService::save_contribution($contribution);
			}

			DispatchManager::redirect(new UserContributionSuccessController());
		}
		else
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}
	}
}
?>
