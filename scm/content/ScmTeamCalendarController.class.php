<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmTeamCalendarController extends DefaultModuleController
{
    private $event;
    private $team_id;
    private $team_name;
    private $params;

	protected function get_template_to_use()
	{
		return new FileTemplate('scm/content/ScmTeamCalendarController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
        $this->init($request);
		$this->build_view();
        if ($this->event->get_event_type() == ScmEvent::CHAMPIONSHIP || ($this->event->get_event_type() == ScmEvent::TOURNAMENT && $this->params->get_hat_ranking()))
        {
            $this->build_donut();
            $this->build_ranking();
        }
		$this->check_authorizations();

        $this->view->put('C_ONE_DAY', $this->get_event()->get_oneday());

		return $this->generate_response();
	}

    private function init(HTTPRequestCustom $request)
    {
        $this->team_id = $request->get_getint('team_id', 0);
        $team_club_id = ScmTeamService::get_team($this->team_id)->get_team_club_id();
        $this->team_name = ScmClubCache::load()->get_club_full_name($team_club_id);
        $this->params = ScmParamsService::get_params($this->event_id());
    }

	private function build_view()
	{
        $games = ScmGameService::get_team_games($this->event_id(), $this->team_id);
        usort($games, function ($a, $b) {
            return $a['game_date'] - $b['game_date'];
        });

        foreach ($games as $game)
        {
            $item = new ScmGame();
            $item->set_properties($game);

            $score_status = $item->get_game_home_pen() ? (int)$item->get_game_home_pen() - (int)$item->get_game_away_pen() : (int)$item->get_game_home_score() - (int)$item->get_game_away_score();
            $team_status = '';
            if ($score_status > 0 && $this->team_id == $item->get_game_home_id())
                $team_status = "success";
            elseif ($score_status < 0 && $this->team_id == $item->get_game_home_id())
                $team_status = "error";
            elseif ($score_status > 0 && $this->team_id == $item->get_game_away_id())
                $team_status = "error";
            elseif ($score_status < 0 && $this->team_id == $item->get_game_away_id())
                $team_status = "success";
            elseif ($item->get_game_home_score() != '' && (int)$item->get_game_away_score() != '' && $score_status === 0)
                $team_status = "moderator";

            $this->view->assign_block_vars('games', array_merge($item->get_template_vars(),[
                'C_IS_HOME_TEAM' => $this->team_id == $item->get_game_home_id(),
                'C_IS_AWAY_TEAM' => $this->team_id == $item->get_game_away_id(),
                'TEAM_STATUS' => $team_status,
                'DAY' => $item->get_game_cluster(),
                'ROUND' => $item->get_game_type() == 'G' ? (ScmParamsService::get_params($item->get_game_event_id())->get_hat_ranking() ? $item->get_game_cluster() : $item->get_game_round()) : 'B' . $item->get_game_cluster(),
            ]));
            $item->get_details_template($this->view, 'games');
        }

        $this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),
            'C_CHARTS' => $this->event->get_event_type() == ScmEvent::CHAMPIONSHIP || ($this->event->get_event_type() == ScmEvent::TOURNAMENT && $this->params->get_hat_ranking()),
            'C_HAS_GAMES' => ScmGameService::has_games($this->event_id()),
            'C_IS_DAY' => $this->event->get_event_type() == ScmEvent::CHAMPIONSHIP,
            'C_GENERAL_FORFEIT' => ScmTeamService::get_team($this->team_id)->get_team_status() == ScmParams::FORFEIT,
            'TEAM_NAME' => $this->team_name
        ]);
	}

    public function build_donut()
    {
        $games = ScmGameService::get_team_games($this->event_id(), $this->team_id);
        $win = $draw = $loss = 0;
        foreach ($games as $game)
        {
            $item = new ScmGame();
            $item->set_properties($game);

            $score_status = (int)$item->get_game_home_score() - (int)$item->get_game_away_score();

            if ($score_status > 0 && $this->team_id == $item->get_game_home_id())
                $win += 1;
            elseif ($score_status < 0 && $this->team_id == $item->get_game_home_id())
                $loss += 1;
            elseif ($score_status > 0 && $this->team_id == $item->get_game_away_id())
                $loss += 1;
            elseif ($score_status < 0 && $this->team_id == $item->get_game_away_id())
                $win += 1;
            elseif ($item->get_game_home_score() != '' && (int)$item->get_game_away_score() != '' && $score_status === 0)
                $draw += 1;
        }

        $this->view->assign_block_vars('charts', array_merge($item->get_template_vars(),[
            'WIN'  => $win,
            'DRAW' => $draw,
            'LOSS' => $loss,
        ]));
    }

    private function build_ranking()
    {
        // x coord
        $teams_number = ScmTeamService::get_teams_number($this->event_id());
        // y coord
        $c_return_games = $this->get_event()->get_event_game_type() == ScmEvent::RETURN_GAMES;
        $params = ScmParamsService::get_params($this->event_id());
        $c_hat_ranking = $params->get_hat_ranking();
        $days_number = $c_hat_ranking ? $params->get_hat_days() : ($c_return_games ? ($teams_number - 1) * 2 : $teams_number - 1);

        $days = $ranks = [];
        $rankings = ScmRankingContentService::get_ranking_content($this->event_id());
        ksort($rankings);

        foreach ($rankings as $cluster => $teams)
        {
            $days[$cluster] = $cluster;
            foreach ($teams as $team)
            {
                if ($this->team_id == $team['team_id'])
                    $ranks[$cluster] = $team['rank'];
            }
        }

        $this->view->put('TEAMS_NUMBER', $teams_number);

        for ($i = 1; $i <= $days_number; $i++)
        {
            $this->view->assign_block_vars('ranks', [
                'DAY' => $i,
                'C_HAS_RANK' => isset($days[$i]),
                'RANK' => isset($days[$i]) ? $ranks[$i] : ''
            ]);
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
		$graphical_environment->set_page_title($this->team_name, $event->get_event_name() . ($category->get_id() != Category::ROOT_CATEGORY ? ' - ' . $category->get_name() : '') . ' - ' . $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
		$graphical_environment->get_seo_meta_data()->set_description(StringVars::replace_vars($this->lang['scm.seo.description.event.team'], ['team' => $this->team_name, 'event' => $event->get_event_name()]));
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::display_team_calendar($event->get_id(), $event->get_event_slug(), $this->team_id));

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
        if ($event->get_is_sub())
            $breadcrumb->add(ScmEventService::get_master_name($event->get_id()), ScmEventService::get_master_url($event->get_id()));
		$breadcrumb->add($event->get_is_sub() ? ScmDivisionService::get_division($event->get_division_id())->get_division_name() : $event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));
		$breadcrumb->add($this->team_name, ScmUrlBuilder::display_team_calendar($event->get_id(), $event->get_event_slug(), $this->team_id));

		return $response;
	}
}
?>
