<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmGroupController extends DefaultModuleController
{
    private $event;
	protected function get_template_to_use()
	{
		return new FileTemplate('scm/ScmGroupController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_view();
		$this->check_authorizations();


		return $this->generate_response();
	}

	private function build_view()
	{
        $this->view->put_all([
            'C_ONE_DAY' => ScmGameService::one_day_event($this->event_id()),
            'C_DISPLAY_PLAYGROUNDS' => $this->get_params()->get_display_playgrounds()
        ]);
        $group = AppContext::get_request()->get_getint('round', 0);
        if($this->get_params()->get_hat_days())
        {
            $this->view->put_all([
                'C_HAT_DAYS' => true,
                'DAY' => $group
            ]);
            $days = ScmGroupService::games_list_from_group($this->event_id(), 'G');

            $days_games = ScmGroupService::games_list_from_group($this->event_id(), 'G', $group);
            $dates = [];
            foreach($days_games as $game)
            {
                $dates[] = Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT);
            }

            foreach (array_unique($dates) as $date)
            {
                $this->view->assign_block_vars('dates', [
                    'DATE' => $date
                ]);
                foreach($days_games as $game)
                {
                    $item = new ScmGame();
                    $item->set_properties($game);
                    if ($date == Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT))
                        $this->view->assign_block_vars('dates.games', $item->get_template_vars());
                }
            }

            $ranks = [];
            foreach ($days as $day => $games)
            {
                foreach ($games as $game)
                {
                    $ranks[] = [
                        'team_id' => $game['game_home_id'],
                        'goals_for' => $game['game_home_score'],
                        'goals_against' => $game['game_away_score'],
                    ];
                    $ranks[] = [
                        'team_id' => $game['game_away_id'],
                        'goals_for' => $game['game_away_score'],
                        'goals_against' => $game['game_home_score'],
                    ];
                }
            }

            $teams = [];
            for($i = 0; $i < count($ranks); $i++)
            {
                $points = $played = $win = $draw = $loss = 0;
                if ($ranks[$i]['goals_for'] > $ranks[$i]['goals_against'])
                {
                    $points = $this->get_params()->get_victory_points();
                    $win = $played = 1;
                }
                elseif ($ranks[$i]['goals_for'] != '' && ($ranks[$i]['goals_for'] === $ranks[$i]['goals_against']))
                {
                    $points = $this->get_params()->get_draw_points();
                    $draw = $played = 1;
                }
                elseif (($ranks[$i]['goals_for'] < $ranks[$i]['goals_against']))
                {
                    $points = $this->get_params()->get_loss_points();
                    $loss = $played = 1;
                }
                if ($ranks[$i]['team_id'])
                $teams[] = [
                    'team_id' => $ranks[$i]['team_id'],
                    'points' => $points,
                    'played' => $played,
                    'win' => $win,
                    'draw' => $draw,
                    'loss' => $loss,
                    'goals_for' => (int)$ranks[$i]['goals_for'],
                    'goals_against' => (int)$ranks[$i]['goals_against'],
                    'goal_average' => (int)$ranks[$i]['goals_for'] - (int)$ranks[$i]['goals_against'],
                ];
            }

            $ranks = [];
            foreach ($teams as $team) {
                $team_id = $team['team_id'];

                if (!isset($ranks[$team_id])) {
                    $ranks[$team_id] = $team;
                } else {
                    $ranks[$team_id]['points'] += $team['points'];
                    $ranks[$team_id]['played'] += $team['played'];
                    $ranks[$team_id]['win'] += $team['win'];
                    $ranks[$team_id]['draw'] += $team['draw'];
                    $ranks[$team_id]['loss'] += $team['loss'];
                    $ranks[$team_id]['goals_for'] += $team['goals_for'];
                    $ranks[$team_id]['goals_against'] += $team['goals_against'];
                    $ranks[$team_id]['goal_average'] += $team['goals_for'] - $team['goals_against'];
                }
            }
            $ranks = array_values($ranks);
            usort($ranks, function($a, $b)
            {
                if ($a['points'] == $b['points']) {
                    if ($a['win'] == $b['win']) {
                        if ($a['goal_average'] == $b['goal_average']) {
                            if ($a['goals_for'] == $b['goals_for']) {
                                if ($a['goals_for'] == $b['goals_for']) {
                                    return 0;
                                }
                            }
                            return $b['goals_for'] - $a['goals_for'];
                        }
                        return $b['goal_average'] - $a['goal_average'];
                    }
                    return $b['win'] - $a['win'];
                }
                return $b['points'] - $a['points'];
            });

            $prom = $this->get_params()->get_promotion();
            $playoff = $this->get_params()->get_playoff();
            $releg = $this->get_params()->get_relegation();
            $prom_color = ScmConfig::load()->get_promotion_color();
            $playoff_color = ScmConfig::load()->get_playoff_color();
            $releg_color = ScmConfig::load()->get_relegation_color();
            $color_count = count($ranks);

            foreach ($ranks as $i => $team_rank)
            {
                if ($prom && $i < $prom) {
                    $rank_color = $prom_color;
                } elseif ($playoff && $i >= $prom && $i < ($prom + $playoff)) {
                    $rank_color = $playoff_color;
                } else if ($releg && $i >= $color_count - $releg) {
                    $rank_color = $releg_color;
                } else {
                    $rank_color = 'rgba(0,0,0,0)';
                }
                $this->view->assign_block_vars('ranks', array_merge(
                    Date::get_array_tpl_vars(new Date($game['game_date'], Timezone::SERVER_TIMEZONE), 'game_date'),
                    [
                        'C_FAV' => ScmParamsService::check_fav($this->event_id(), $team_rank['team_id']),
                        'RANK' => $i + 1,
                        'RANK_COLOR' => $rank_color,
                        'TEAM_NAME' => !empty($team_rank['team_id']) ? ScmTeamService::get_team_name($team_rank['team_id']) : '',
                        'TEAM_LOGO' => !empty($team_rank['team_id']) ? ScmTeamService::get_team_logo($team_rank['team_id']) : '',
                        'POINTS' => $team_rank['points'],
                        'PLAYED' => $team_rank['played'],
                        'WIN' => $team_rank['win'],
                        'DRAW' => $team_rank['draw'],
                        'LOSS' => $team_rank['loss'],
                        'GOALS_FOR' => $team_rank['goals_for'],
                        'GOALS_AGAINST' => $team_rank['goals_against'],
                        'GOAL_AVERAGE' => $team_rank['goal_average'],
                    ]
                ));
            }
        }
        else
        {
            $group_games = ScmGroupService::games_list_from_group($this->event_id(), 'G', $group);
            $this->view->put('GROUP', ScmGroupService::ntl($group));

            $matchdays = [];
            foreach($group_games as $game)
            {
                $matchdays[$game['game_round']][Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][] = $game;
            }

            foreach ($matchdays as $matchday => $dates)
            {
                $this->view->assign_block_vars('matchdays', [
                    'MATCHDAY' => $matchday
                ]);
                foreach ($dates as $date => $games)
                {
                    $this->view->assign_block_vars('matchdays.dates', [
                        'DATE' => $date
                    ]);
                    foreach($games as $game)
                    {
                        $item = new ScmGame();
                        $item->set_properties($game);
                        $this->view->assign_block_vars('matchdays.dates.games', $item->get_template_vars());
                    }
                }
            }

            $ranks = [];
            foreach ($group_games as $game)
            {
                $item = new ScmGame();
                $item->set_properties($game);

                $this->view->assign_block_vars('games', $item->get_template_vars());

                $ranks[] = [
                    'team_id' => $game['game_home_id'],
                    'goals_for' => $game['game_home_score'],
                    'goals_against' => $game['game_away_score'],
                ];
                $ranks[] = [
                    'team_id' => $game['game_away_id'],
                    'goals_for' => $game['game_away_score'],
                    'goals_against' => $game['game_home_score'],
                ];
            }

            $teams = [];
            for($i = 0; $i < count($ranks); $i++)
            {
                $points = $played = $win = $draw = $loss = 0;
                if ($ranks[$i]['goals_for'] > $ranks[$i]['goals_against'])
                {
                    $points = $this->get_params()->get_victory_points();
                    $win = $played = 1;
                }
                elseif ($ranks[$i]['goals_for'] != '' && ($ranks[$i]['goals_for'] === $ranks[$i]['goals_against']))
                {
                    $points = $this->get_params()->get_draw_points();
                    $draw = $played = 1;
                }
                elseif (($ranks[$i]['goals_for'] < $ranks[$i]['goals_against']))
                {
                    $points = $this->get_params()->get_loss_points();
                    $loss = $played = 1;
                }
                if ($ranks[$i]['team_id'])
                $teams[] = [
                    'team_id' => $ranks[$i]['team_id'],
                    'points' => $points,
                    'played' => $played,
                    'win' => $win,
                    'draw' => $draw,
                    'loss' => $loss,
                    'goals_for' => (int)$ranks[$i]['goals_for'],
                    'goals_against' => (int)$ranks[$i]['goals_against'],
                    'goal_average' => (int)$ranks[$i]['goals_for'] - (int)$ranks[$i]['goals_against'],
                ];
            }

            $ranks = [];
            foreach ($teams as $team) {
                $team_id = $team['team_id'];

                if (!isset($ranks[$team_id])) {
                    $ranks[$team_id] = $team;
                } else {
                    $ranks[$team_id]['points'] += $team['points'];
                    $ranks[$team_id]['played'] += $team['played'];
                    $ranks[$team_id]['win'] += $team['win'];
                    $ranks[$team_id]['draw'] += $team['draw'];
                    $ranks[$team_id]['loss'] += $team['loss'];
                    $ranks[$team_id]['goals_for'] += $team['goals_for'];
                    $ranks[$team_id]['goals_against'] += $team['goals_against'];
                    $ranks[$team_id]['goal_average'] += $team['goals_for'] - $team['goals_against'];
                }
            }
            $ranks = array_values($ranks);

            usort($ranks, function($a, $b)
            {
                if ($a['points'] == $b['points']) {
                    if ($a['win'] == $b['win']) {
                        if ($a['goal_average'] == $b['goal_average']) {
                            if ($a['goals_for'] == $b['goals_for']) {
                                if ($a['goals_for'] == $b['goals_for']) {
                                    return 0;
                                }
                            }
                            return $b['goals_for'] - $a['goals_for'];
                        }
                        return $b['goal_average'] - $a['goal_average'];
                    }
                    return $b['win'] - $a['win'];
                }
                return $b['points'] - $a['points'];
            });

            $prom = $this->get_params()->get_promotion();
            $playoff = $this->get_params()->get_playoff();
            $releg = $this->get_params()->get_relegation();
            $prom_color = ScmConfig::load()->get_promotion_color();
            $playoff_color = ScmConfig::load()->get_playoff_color();
            $releg_color = ScmConfig::load()->get_relegation_color();
            $color_count = count($ranks);

            foreach ($ranks as $i => $team_rank)
            {
                if ($prom && $i < $prom) {
                    $rank_color = $prom_color;
                } elseif ($playoff && $i >= $prom && $i < ($prom + $playoff)) {
                    $rank_color = $playoff_color;
                } else if ($releg && $i >= $color_count - $releg) {
                    $rank_color = $releg_color;
                } else {
                    $rank_color = 'rgba(0,0,0,0)';
                }
                $this->view->assign_block_vars('ranks', array_merge(
                    Date::get_array_tpl_vars(new Date($game['game_date'], Timezone::SERVER_TIMEZONE), 'game_date'),
                    [
                        'C_FAV' => ScmParamsService::check_fav($this->event_id(), $team_rank['team_id']),
                        'RANK' => $i + 1,
                        'RANK_COLOR' => $rank_color,
                        'TEAM_NAME' => !empty($team_rank['team_id']) ? ScmTeamService::get_team_name($team_rank['team_id']) : '',
                        'TEAM_LOGO' => !empty($team_rank['team_id']) ? ScmTeamService::get_team_logo($team_rank['team_id']) : '',
                        'POINTS' => $team_rank['points'],
                        'PLAYED' => $team_rank['played'],
                        'WIN' => $team_rank['win'],
                        'DRAW' => $team_rank['draw'],
                        'LOSS' => $team_rank['loss'],
                        'GOALS_FOR' => $team_rank['goals_for'],
                        'GOALS_AGAINST' => $team_rank['goals_against'],
                        'GOAL_AVERAGE' => $team_rank['goal_average'],
                    ]
                ));
            }

        }

        $this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),
            'C_HAS_GAMES' => ScmGameService::has_games($this->event_id())
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
		$breadcrumb->add($this->lang['scm.games.groups.stage'], ScmUrlBuilder::display_groups_rounds($event->get_id(), $event->get_event_slug()));

		return $response;
	}
}
?>
