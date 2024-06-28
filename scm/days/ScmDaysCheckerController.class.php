<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmDaysCheckerController extends DefaultModuleController
{
    private $event;
	protected function get_template_to_use()
	{
		return new FileTemplate('scm/ScmDaysCheckerController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_view();
		$this->check_authorizations();

        $this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),
        ]);

		return $this->generate_response();
	}

	private function build_view()
	{
        $teams = ScmTeamService::get_teams($this->event_id());
        $games = ScmGameService::get_games($this->event_id());
        $days = ScmDayService::get_days($this->event_id());
        $home_days_number = $away_days_number = count($days) / 2;

        $matchdays = [];
        foreach($days as $day)
        {
            $matchdays[] = $day['day_round'];
        }
        foreach ($teams as $team)
        {
            $team_item = new ScmTeam();
            $team_item->set_properties($team);
            $games_number = $games_home_number = $games_away_number = [];
            foreach ($games as $game)
            {
                if($game['game_home_id'] == $team_item->get_id_team() || $game['game_away_id'] == $team_item->get_id_team())
                    $games_number[] = $game;
                if($game['game_home_id'] == $team_item->get_id_team())
                    $games_home_number[] = $game;
                if($game['game_away_id'] == $team_item->get_id_team())
                    $games_away_number[] = $game;
            }

            $c_check_error = count($games_home_number) != $home_days_number || count($games_away_number) != $away_days_number;

            $this->view->assign_block_vars('teams', [
                'TEAM_NAME' => ScmTeamService::get_team_name($team_item->get_id_team()),
                'C_CHECK_ERROR' => $c_check_error,
                'GAMES_NUMBER' => count($games_number),
                'GAMES_HOME_NUMBER' => count($games_home_number),
                'GAMES_AWAY_NUMBER' => count($games_away_number),
            ]);

            if($c_check_error)
            {
                $game_days = $error_days = [];
                foreach ($games as $game)
                {
                    if($game['game_home_id'] == $team_item->get_id_team() || $game['game_away_id'] == $team_item->get_id_team())
                        $game_days[] = ['day' => $game['game_group']];
                }
                $ids = array_column($game_days, 'day');
                $id_counts = array_count_values($ids);

                foreach ($id_counts as $id => $count) {
                    if ($count > 1)
                        $error_days[] = $id;
                }

                foreach ($games as $game)
                {
                    if(in_array($game['game_group'],$error_days))
                    {
                        if($game['game_home_id'] == $team_item->get_id_team() || $game['game_away_id'] == $team_item->get_id_team())
                        {
                            $this->view->assign_block_vars('teams.games', [
                                'GAME_DAY' => $game['game_group'],
                                'TEAM_HOME_NAME' => ScmTeamService::get_team_name($game['game_home_id']),
                                'TEAM_AWAY_NAME' => ScmTeamService::get_team_name($game['game_away_id']),
                            ]);
                        }
                    }
                }
                $missing_days = array_diff($matchdays, array_unique($ids));
                $games_list = [];
                foreach ($games as $game)
                {
                    if (in_array($game['game_group'], $missing_days))
                    {
                        $games_list[] = $game['game_group'];
                    }
                }

                foreach ($days as $day)
                {
                    if (in_array($day['day_round'], array_unique($games_list)))
                    {
                        $this->view->assign_block_vars('teams.missing_days', [
                            'MISSING_DAY' => $day['day_round'],
                            'U_EDIT_DAYS_GAMES'    => ScmUrlBuilder::edit_days_games($team['team_event_id'], 'edit', $day['day_round'])->rel(),
                        ]);
                    }
                }
            }
        }
	}

	private function get_event()
	{
		if ($this->event === null)
		{
			$id = AppContext::get_request()->get_getint('event_id', 0);
			if (!empty($id))
			{
				try {
					$this->event = ScmEventService::get_event($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->event = new ScmEvent();
		}
		return $this->event;
	}

    private function event_id()
    {
        return $this->get_event()->get_id();
    }

    private function get_params()
    {
        return ScmParamsService::get_params($this->event_id());
    }

	private function check_authorizations()
	{
		$event = $this->get_event();

		$current_user = AppContext::get_current_user();
		$not_authorized = !ScmAuthorizationsService::check_authorizations($event->get_id_category())->moderation() && !ScmAuthorizationsService::check_authorizations($event->get_id_category())->write() && !ScmAuthorizationsService::check_authorizations($event->get_id_category())->read();

		switch ($event->get_publishing_state()) {
			case ScmEvent::PUBLISHED:
				if (!ScmAuthorizationsService::check_authorizations($event->get_id_category())->read())
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case ScmEvent::NOT_PUBLISHED:
				if ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case ScmEvent::DEFERRED_PUBLICATION:
				if (!$event->is_published() && ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL)))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			default:
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			break;
		}
	}

	private function generate_response()
	{
		$event = $this->get_event();
		$category = $event->get_category();
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($event->get_event_name(), ($category->get_id() != Category::ROOT_CATEGORY ? $category->get_name() . ' - ' : '') . $this->lang['scm.module.title']);
		$graphical_environment->get_seo_meta_data()->set_description('');
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));

		// if ($event->has_thumbnail())
		// 	$graphical_environment->get_seo_meta_data()->set_picture_url($event->get_thumbnail());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'],ScmUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($event->get_id_category(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));
		$breadcrumb->add($this->lang['scm.check.days'], ScmUrlBuilder::days_checker($event->get_id(), $event->get_event_slug()));

		return $response;
	}
}
?>
