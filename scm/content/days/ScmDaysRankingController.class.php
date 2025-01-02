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
		$this->build_view($request);
		$this->build_days_view($request);
		$this->check_authorizations();

        $this->view->put('C_ONE_DAY', ScmGameService::one_day_event($this->event_id()));

		return $this->generate_response();
	}

	private function build_view(HTTPRequestCustom $request)
	{
        $section = $request->get_getstring('section', '');
        $day = $request->get_getint('day', 0) != 0 ? $request->get_getint('day', 0) : ScmDayService::get_last_day($this->event_id());
        $event_slug = ScmEventService::get_event_slug($this->event_id());
        $params = ScmParamsService::get_params($this->event_id());
        $config = ScmConfig::load();

        switch($section) {
            case ('') :
                $c_chart = true;
                $final_ranks = ScmRankingService::general_ranking($this->event_id());
                break;
            case ('home') :
                $c_chart = false;
                $final_ranks = ScmRankingService::home_ranking($this->event_id());
                break;
            case ('away') :
                $c_chart = false;
                $final_ranks = ScmRankingService::away_ranking($this->event_id());
                break;
            case ('attack') :
                $c_chart = false;
                $final_ranks = ScmRankingService::attack_ranking($this->event_id());
                break;
            case ('defense') :
                $c_chart = false;
                $final_ranks = ScmRankingService::defense_ranking($this->event_id());
                break;
            case ('day') :
                $c_chart = true;
                $final_ranks = ScmRankingService::general_days_ranking($this->event_id(), $day);
                break;
            default :
                $c_chart = true;
                $final_ranks = ScmRankingService::general_ranking($this->event_id());
                break;
        }

        // Charts params
        // x coord
        $teams_number = ScmTeamService::get_teams_number($this->event_id());
        // y coord
        $c_return_games = ScmEventService::get_event_game_type($this->event_id()) == ScmDivision::RETURN_GAMES;
        $c_hat_ranking = $params->get_hat_ranking();
        $days_number = $c_hat_ranking ? $params->get_hat_days() : ($c_return_games ? ($teams_number - 1) * 2 : $teams_number - 1);
        $full_rankings = ScmRankingContentService::get_ranking_content($this->event_id());

        // Params to display background colors
        $prom = $params->get_promotion();
        $playoff_prom = $params->get_playoff_prom();
        $playoff_releg = $params->get_playoff_releg();
        $releg = $params->get_relegation();
        $prom_color = $config->get_promotion_color();
        $playoff_prom_color = $config->get_playoff_prom_color();
        $playoff_releg_color = $config->get_playoff_releg_color();
        $releg_color = $config->get_relegation_color();
        $color_count = count($final_ranks);

        // Display ranks to view
        foreach ($final_ranks as $i => $team_rank)
        {
            // Display background colors
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

            // Display table rank
            $this->view->assign_block_vars('ranks', [
                'C_FAV'           => ScmParamsService::check_fav($this->event_id(), $team_rank['team_id']),
                'C_FORFEIT'       => $team_rank['status'] == 'forfeit',
                'C_HAS_TEAM_LOGO' => ScmTeamService::get_team_logo($team_rank['team_id']),
                'RANK'            => $i + 1,
                'RANK_COLOR'      => $rank_color,
                'TEAM_ID'         => !empty($team_rank['team_id']) ? $team_rank['team_id'] : 0,
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

            // Display 5 last days results of a team
            foreach (ScmRankingService::get_health_shape($this->event_id(), $team_rank['team_id'], $day, 5) as $results)
            {
                $this->view->assign_block_vars('ranks.form', [
                    'C_PLAYED' => $results['result'] != 'delayed',
                    'L_PLAYED' => $this->lang['scm.rank.health.' . $results['result'] . ''],
                    'CLASS' => $results['class'],
                    'SCORE' => $results['score']
                ]);
            }

            // display team charts
            $days = $team_array = [];
            $full_rankings = ScmRankingContentService::get_ranking_content($this->event_id());
            ksort($full_rankings);
            for ($i = 1; $i <= $days_number; $i++)
            {
                $this->view->assign_block_vars('ranks.matchdays', [
                    'MATCHDAY' => $i
                ]);
            }

            foreach ($full_rankings as $cluster => $teams)
            {
                foreach ($teams as $team)
                {
                    if (!empty($team_rank['team_id']) && $team['team_id'] == $team_rank['team_id'])
                        $team_array[$team['team_name']][$cluster][] = $team['rank'];
                }
            }
            foreach ($team_array as $team => $days)
            {
                foreach ($days as $matchday => $rank)
                {
                    if ($matchday <= $day)
                        $this->view->assign_block_vars('ranks.days', [
                            'DAY' => $matchday,
                            'RANK' => $rank[0],
                        ]);
                }
            }
        }

        // Display all day played buttons in 'day' page
        $slug = ScmEventService::get_event_slug($this->event_id());
        foreach (ScmDayService::get_days($this->event_id()) as $day)
        {
            $this->view->assign_block_vars('days', [
                'C_DAY_PLAYED' => $day['day_played'] || $day['day_round'] == 1,
                'DAY'   => $day['day_round'],
                'U_DAY' => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, 'day', $day['day_round'])->rel(),
            ]);
        }

        $prom_line_color = $this->hex_to_rgb($prom_color);
        $po_prom_line_color = $this->hex_to_rgb($playoff_prom_color);
        $po_releg_line_color = $this->hex_to_rgb($playoff_releg_color);
        $releg_line_color = $this->hex_to_rgb($releg_color);

        // Main templates vars
        $this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),

            'C_CHARTS' => $c_chart,
            'TEAMS_NUMBER' => $teams_number,
            'DAYS_NUMBER' => $days_number,
            'PROM_LINE' => $prom,
            'PROM_LINE_COLOR' => 'rgb(' . implode(', ', $prom_line_color) . ')',
            'PO_PROM_LINE' => $prom + $playoff_prom,
            'PO_PROM_LINE_COLOR' => 'rgb(' . implode(', ', $po_prom_line_color) . ')',
            'PO_RELEG_LINE' => $teams_number - ($releg + $playoff_releg),
            'PO_RELEG_LINE_COLOR' => 'rgb(' . implode(', ', $po_releg_line_color) . ')',
            'RELEG_LINE' => $teams_number - $releg,
            'RELEG_LINE_COLOR' => 'rgb(' . implode(', ', $releg_line_color) . ')',

            'C_HAS_GAMES'    => ScmGameService::has_games($this->event_id()),
            'C_BONUS_SINGLE' => $params->get_bonus() == ScmParams::BONUS_SINGLE,
            'C_BONUS_DOUBLE' => $params->get_bonus() == ScmParams::BONUS_DOUBLE,
            'C_GENERAL_DAYS' => $section == 'day',
            'U_GENERAL'      => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, '')->rel(),
            'U_GENERAL_DAYS' => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, 'day', ScmDayService::get_last_day($this->event_id()))->rel(),
            'U_HOME'         => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, 'home')->rel(),
            'U_AWAY'         => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, 'away')->rel(),
            'U_ATTACK'       => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, 'attack')->rel(),
            'U_DEFENSE'      => ScmUrlBuilder::display_days_ranking($this->event_id(), $slug, 'defense')->rel(),
        ]);
	}

    private function hex_to_rgb($color)
    {
        $hex = ltrim($color, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return [$r, $g, $b];
    }

    private function build_days_view(HTTPRequestCustom $request)
    {
        $day = $request->get_getint('day', 0);
        $prev_day = $day ? $day : ScmDayService::get_last_day($this->event_id());

        $next_day = $day ? $day + 1 : ScmDayService::get_next_day($this->event_id());

        $this->view->put_all([
            'C_EVENT_STARTING' => ScmDayService::get_next_day($this->event_id()) == 1,
            'C_EVENT_ENDING' => ($day ? $day : ScmDayService::get_last_day($this->event_id())) == count(ScmDayService::get_days($this->event_id())),
            'PREV_DAY' => $prev_day,
            'NEXT_DAY' => $next_day,
            'PREV_GAMES' => ScmGameFormat::format_cluster(ScmGameService::get_games_in_cluster($this->event_id(), $prev_day), false),
            'NEXT_GAMES' => ScmGameFormat::format_cluster(ScmGameService::get_games_in_cluster($this->event_id(), $next_day), false),
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
		$graphical_environment->set_page_title($this->lang['scm.days.ranking'], $event->get_event_name() . ($category->get_id() != Category::ROOT_CATEGORY ? ' - ' . $category->get_name() : '') . ' - ' . $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
		$graphical_environment->get_seo_meta_data()->set_description(StringVars::replace_vars($this->lang['scm.seo.description.event.ranking'], ['event' => $event->get_event_name()]));
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));

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
		$breadcrumb->add($this->lang['scm.days.ranking'], ScmUrlBuilder::display_days_ranking($event->get_id(), $event->get_event_slug()));

		return $response;
	}
}
?>
