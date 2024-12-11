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

    /** Display colors of the club */
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

    /** Display the event list of the club */
	private function build_event_view()
	{
		$club = $this->get_club();
        $now = new Date();
        $event_cache = ScmEventCache::load();
        $teams_list = ScmTeamCache::load()->get_teams();
        $clubs = ScmClubCache::load()->get_clubs();

        $clubs_family = [];
        foreach ($clubs as $club_event)
        {
            if($club_event['id_club'] == $club->get_id_club() || ($club_event['club_affiliate'] && $club_event['club_affiliation'] == $club->get_id_club()))
            {
                $clubs_family[] = $club_event['id_club'];
            }
        }

        $categories = [];
        foreach ($teams_list as $team)
        {
            if (in_array($team['team_club_id'], $clubs_family))
                $categories[$team['id_category']][] = $team;
        }
        ksort($categories);

        foreach ($categories as $category => $teams)
        {
            $category_details = CategoriesService::get_categories_manager()->get_categories_cache()->get_category($category);

            $this->view->assign_block_vars('categories', [
                'CATEGORY_NAME' => $category_details->get_name(),
				'U_CATEGORY' => ScmUrlBuilder::display_category($category_details->get_id(), $category_details->get_rewrited_name())->rel(),
            ]);

            foreach ($teams as $team)
            {
                if (in_array($team['team_club_id'], $clubs_family))
                {
                    $event = $event_cache->get_event($team['team_event_id']);
                    $real_event_id = $event['is_sub'] ? $event['master_id'] : $event['id'];
                    $real_event = $event_cache->get_event($real_event_id);
                    if($real_event['start_date'] < $now->get_timestamp() && $now->get_timestamp() < $real_event['end_date'])
                    {
                        $event = new ScmEvent();
                        $event->set_properties($event_cache->get_event($team['team_event_id']));
                        $this->view->assign_block_vars('categories.events', array_merge($event->get_template_vars(),
                            [
                                'CLUB_NAME' => ScmClubCache::load()->get_club_name($team['team_club_id'])
                            ]
                        ));
                    }
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
        $description = StringVars::replace_vars($this->lang['scm.seo.description.club'], ['club' => $cache->get_club_full_name($club->get_id_club())]);
        $graphical_environment->get_seo_meta_data()->set_description($description);

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());
		$breadcrumb->add($this->lang['scm.clubs'], ScmUrlBuilder::display_clubs());
		$breadcrumb->add($cache->get_club_full_name($club->get_id_club()), ScmUrlBuilder::display_club($club->get_id_club(), $club->get_club_slug()));

		return $response;
	}
}
?>
