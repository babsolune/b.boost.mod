<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmDaysRankingController extends DefaultModuleController
{
    private $event;
	protected function get_template_to_use()
	{
		return new FileTemplate('scm/ScmDaysRankingController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_view();
		$this->build_days_view();
		$this->check_authorizations();

        $this->view->put('C_ONE_DAY', ScmGameService::one_day_event($this->event_id()));

		return $this->generate_response();
	}

	private function build_view()
	{
        $section = AppContext::get_request()->get_getstring('section', '');
        $day = AppContext::get_request()->get_getint('day', 0);
        switch($section) {
            case ('') :
                $final_ranks = ScmRankingService::general_ranking($this->event_id());
                break;
            case ('home') :
                $final_ranks = ScmRankingService::home_ranking($this->event_id());
                break;
            case ('away') :
                $final_ranks = ScmRankingService::away_ranking($this->event_id());
                break;
            case ('attack') :
                $final_ranks = ScmRankingService::attack_ranking($this->event_id());
                break;
            case ('defense') :
                $final_ranks = ScmRankingService::defense_ranking($this->event_id());
                break;
            case ('day') :
                $final_ranks = ScmRankingService::general_days_ranking($this->event_id(), $day);
                break;
            default :
                $final_ranks = ScmRankingService::general_ranking($this->event_id());
                break;
        }

        // Display ranks to view
        $prom = ScmParamsService::get_params($this->event_id())->get_promotion();
        $playoff_prom = ScmParamsService::get_params($this->event_id())->get_playoff_prom();
        $playoff_releg = ScmParamsService::get_params($this->event_id())->get_playoff_releg();
        $releg = ScmParamsService::get_params($this->event_id())->get_relegation();
        $prom_color = ScmConfig::load()->get_promotion_color();
        $playoff_prom_color = ScmConfig::load()->get_playoff_prom_color();
        $playoff_releg_color = ScmConfig::load()->get_playoff_releg_color();
        $releg_color = ScmConfig::load()->get_relegation_color();
        $color_count = count($final_ranks);

        foreach ($final_ranks as $i => $team_rank)
        {
            if ($prom && $i < $prom) {
                $rank_color = $prom_color;
            } elseif ($playoff_prom && $i >= $prom && $i < ($prom + $playoff_prom)) {
                $rank_color = $playoff_prom_color;
            } elseif ($playoff_releg && $i >= ($color_count - $releg - $playoff_releg) && $i < ($color_count - $releg)) {
                $rank_color = $playoff_releg_color;
            } elseif ($releg && $i >= ($color_count - $releg)) {
                $rank_color = $releg_color;
            } else {
                $rank_color = 'rgba(0,0,0,0)';
            }
            $event_slug = ScmEventService::get_event_slug($this->event_id());
            $this->view->assign_block_vars('ranks', [
                'C_FAV'           => ScmParamsService::check_fav($this->event_id(), $team_rank['team_id']),
                'C_FORFEIT'       => $team_rank['status'] == 'forfeit',
                'RANK'            => $i + 1,
                'RANK_COLOR'      => $rank_color,
                'U_TEAM_CALENDAR' => !empty($team_rank['team_id']) ? ScmUrlBuilder::display_team_calendar($this->event_id(), $event_slug, $team_rank['team_id'])->rel() : '#',
                'TEAM_NAME'       => !empty($team_rank['team_id']) ? ScmTeamService::get_team_name($team_rank['team_id']) : '',
                'TEAM_LOGO'       => !empty($team_rank['team_id']) ? ScmTeamService::get_team_logo($team_rank['team_id']) : '',
                'POINTS'          => $team_rank['points'],
                'PLAYED'          => $team_rank['played'],
                'WIN'             => $team_rank['win'],
                'DRAW'            => $team_rank['draw'],
                'LOSS'            => $team_rank['loss'],
                'GOALS_FOR'       => $team_rank['goals_for'],
                'GOALS_AGAINST'   => $team_rank['goals_against'],
                'GOAL_AVERAGE'    => $team_rank['goal_average'],
                'OFF_BONUS'       => $team_rank['off_bonus'],
                'DEF_BONUS'       => $team_rank['def_bonus'],
            ]);
        }

        $slug = ScmEventService::get_event_slug($this->event_id());
        foreach (ScmDayService::get_days($this->event_id()) as $day)
        {
            $this->view->assign_block_vars('days', [
                'C_DAY_PLAYED' => $day['day_played'] || $day['day_round'] == 1,
                'DAY'   => $day['day_round'],
                'U_DAY' => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, 'day', $day['day_round'])->rel(),
            ]);
        }
        $params = ScmParamsService::get_params($this->event_id())->get_bonus();
        $this->view->put_all([
            'MENU'           => ScmMenuService::build_event_menu($this->event_id()),
            'C_HAS_GAMES'    => ScmGameService::has_games($this->event_id()),
            'C_BONUS_SINGLE' => $params == ScmParams::BONUS_SINGLE,
            'C_BONUS_DOUBLE' => $params == ScmParams::BONUS_DOUBLE,
            'C_GENERAL_DAYS' => $section == 'day',
            'U_GENERAL'      => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, '')->rel(),
            'U_GENERAL_DAYS' => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, 'day', ScmDayService::get_last_day($this->event_id()))->rel(),
            'U_HOME'         => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, 'home')->rel(),
            'U_AWAY'         => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, 'away')->rel(),
            'U_ATTACK'       => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, 'attack')->rel(),
            'U_DEFENSE'      => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, 'defense')->rel(),
        ]);
	}

    private function build_days_view()
    {
        $day = AppContext::get_request()->get_getint('day', 0);
        $prev_day = ScmDayService::get_last_day($this->event_id());
        $prev_dates = [];
        foreach(ScmGameService::get_games_in_cluster($this->event_id(), $prev_day) as $game)
        {
            $prev_dates[] = Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT);
        }

        foreach (array_unique($prev_dates) as $date)
        {
            $this->view->assign_block_vars('prev_dates', [
                'DATE' => $date
            ]);
            foreach(ScmGameService::get_games_in_cluster($this->event_id(), $prev_day) as $game)
            {
                $item = new ScmGame();
                $item->set_properties($game);
                if ($date == Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT))
                    $this->view->assign_block_vars('prev_dates.prev_days', $item->get_template_vars());
            }
        }

        $next_day = ScmDayService::get_next_day($this->event_id());
        $next_dates = [];
        foreach(ScmGameService::get_games_in_cluster($this->event_id(), $next_day) as $game)
        {
            $next_dates[] = Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT);
        }

        foreach (array_unique($next_dates) as $date)
        {
            $this->view->assign_block_vars('next_dates', [
                'DATE' => $date
            ]);
            foreach(ScmGameService::get_games_in_cluster($this->event_id(), $next_day) as $game)
            {
                $item = new ScmGame();
                $item->set_properties($game);
                if ($date == Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT))
                    $this->view->assign_block_vars('next_dates.next_days', $item->get_template_vars());
            }
        }

        $this->view->put_all([
            'C_EVENT_ENDING' => empty($day) && ScmDayService::get_last_day($this->event_id()) == count(ScmDayService::get_days($this->event_id())) || $day == count(ScmDayService::get_days($this->event_id())),
            'LAST_DAY' => $prev_day,
            'NEXT_DAY' => $next_day,
        ]);
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
		$not_authorized = !ScmAuthorizationsService::check_authorizations($event->get_id_category())->moderation() && !ScmAuthorizationsService::check_authorizations($event->get_id_category())->write();

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
		$breadcrumb->add($this->lang['scm.games.groups.stage'], ScmUrlBuilder::display_groups_rounds($event->get_id(), $event->get_event_slug()));

		return $response;
	}
}
?>
