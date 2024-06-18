<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballClubController extends DefaultModuleController
{
    private $club;

	protected function get_template_to_use()
	{
		return new FileTemplate('football/FootballClubController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_view();
		$this->build_compet_view();
		$this->check_authorizations();

		return $this->generate_response();
	}

	private function build_view()
	{
		$club = $this->get_club();

		$this->view->put_all($club->get_template_vars());
	}

	private function build_compet_view()
	{
		$club = $this->get_club();
        $compet_cache = FootballCompetCache::load();
        $teams = FootballTeamCache::load()->get_teams();
        $compet_list = [];
        foreach ($teams as $team)
        {
            if ($team['team_club_id'] == $club->get_id_club())
            {
                $compet = new FootballCompet();
                $compet->set_properties($compet_cache->get_item($team['team_compet_id']));
                $this->view->assign_block_vars('compets', $compet->get_template_vars());
            }
        }
		$this->view->put_all($club->get_template_vars());
	}

	private function get_club()
	{
		if ($this->club === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->club = FootballClubService::get_club($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->club = new FootballClub();
		}
		return $this->club;
	}

	private function check_authorizations()
	{
		if (!FootballAuthorizationsService::check_authorizations()->read())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }
	}

	private function generate_response()
	{
		$club = $this->get_club();
        $cache = FootballClubCache::load();
        $response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($cache->get_club_full_name($club->get_id_club()), $this->lang['football.module.title']);

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());
		$breadcrumb->add($this->lang['football.clubs'], FootballUrlBuilder::display_clubs());
		$breadcrumb->add($cache->get_club_full_name($club->get_id_club()), FootballUrlBuilder::display_club($club->get_id_club()));

		return $response;
	}
}
?>
