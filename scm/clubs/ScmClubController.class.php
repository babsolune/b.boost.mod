<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmClubController extends DefaultModuleController
{
    private $club;

	protected function get_template_to_use()
	{
		return new FileTemplate('scm/ScmClubController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_view();
		$this->build_colors_view();
		$this->build_event_view();
		$this->check_authorizations();

		return $this->generate_response();
	}

	private function build_view()
	{
		$club = $this->get_club();

		$this->view->put_all($club->get_template_vars());
	}

	private function build_colors_view()
	{
		$colors = $this->get_club()->get_club_colors();
		$colors_number = count($colors);
        $this->view->put('C_COLORS', $colors_number > 0);

		$i = 1;
		foreach ($colors as $name => $color)
		{
			$this->view->assign_block_vars('colors', [
				'C_SEPARATOR' => $i < $colors_number,
				'NAME' => $name,
				'COLOR' => $color,
			]);
			$i++;
		}
	}

	private function build_event_view()
	{
		$club = $this->get_club();
        $event_cache = ScmEventCache::load();
        $teams = ScmTeamCache::load()->get_teams();

        foreach ($teams as $team)
        {
            if ($team['team_club_id'] == $club->get_id_club())
            {
                if($event_cache->get_event($team['team_event_id']))
                {
                    $event = new ScmEvent();
                    $event->set_properties($event_cache->get_event($team['team_event_id']));
                    $this->view->assign_block_vars('events', $event->get_template_vars());
                }
            }
        }
		$this->view->put_all($club->get_template_vars());
	}

	private function get_club()
	{
		if ($this->club === null)
		{
			$id = AppContext::get_request()->get_getint('club_id', 0);
			if (!empty($id))
			{
				try {
					$this->club = ScmClubService::get_club($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->club = new ScmClub();
		}
		return $this->club;
	}

	private function check_authorizations()
	{
		if (!ScmAuthorizationsService::check_authorizations()->read())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }
	}

	private function generate_response()
	{
		$club = $this->get_club();
        $cache = ScmClubCache::load();
        $response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($cache->get_club_full_name($club->get_id_club()), $this->lang['scm.module.title']);

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());
		$breadcrumb->add($this->lang['scm.clubs'], ScmUrlBuilder::display_clubs());
		$breadcrumb->add($cache->get_club_full_name($club->get_id_club()), ScmUrlBuilder::display_club($club->get_id_club(), $club->get_club_slug()));

		return $response;
	}
}
?>
