<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmBracketController extends DefaultModuleController
{
    private $event;
    private $params;
    private $looser_bracket;
    private $teams_number;
    private $teams_per_group;

	protected function get_template_to_use()
	{
		return new FileTemplate('scm/ScmBracketController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
        if ($this->return_games()) 
        {
            $this->build_round_trip_view();
        }
        elseif ($this->get_params($this->event_id())->get_finals_type() == ScmParams::FINALS_RANKING)
        {
            $this->build_group_view();
        }
        else
        {
            $this->build_bracket_view();
        }
        $this->check_authorizations();

        $this->view->put_all([
            'MENU'             => ScmMenuService::build_event_menu($this->event_id()),
            'C_HAT_RANKING'    => ScmParamsService::get_params($this->event_id())->get_hat_ranking(),
            'C_RETURN_GAMES'   => $this->return_games(),
            'C_ONE_DAY'        => ScmGameService::one_day_event($this->event_id()),
            'C_LOOSER_BRACKET' => $this->get_params()->get_looser_bracket(),
            'C_HAS_GAMES'      => ScmGameService::has_games($this->event_id()),
            'C_FINALS_RANKING' => $this->get_params($this->event_id())->get_finals_type() == ScmParams::FINALS_RANKING,
            'C_DISPLAY_PLAYGROUNDS' => $this->get_params($this->event_id())->get_display_playgrounds()
        ]);

		return $this->generate_response();
	}

    private function init()
    {
        $this->looser_bracket = $this->get_params()->get_looser_bracket();
        $this->teams_number = ScmTeamService::get_teams_number($this->event_id());
        $this->teams_per_group = $this->get_params()->get_teams_per_group();
    }

	/** Bracket with return matches */
	private function build_round_trip_view()
	{
        $games = ScmGroupService::games_list_from_group($this->event_id(), 'B');
        $games = call_user_func_array('array_merge', $games);
        $c_hat_ranking = ScmParamsService::get_params($this->event_id())->get_hat_ranking();
        $c_draw_games = ScmParamsService::get_params($this->event_id())->get_draw_games();

        $rounds = [];
        foreach ($games as $game)
        {
            $rounds[] = $game['game_group'];
        }

        $rounds_count = array_unique($rounds);
        $key_rounds_count = array_keys($rounds_count);
        $first_key = reset($key_rounds_count);
        $last_key = end($key_rounds_count);

        foreach ($rounds_count as $key => $round)
        {
            $this->view->assign_block_vars('rounds', [
                'C_ALL_PLACES' => $key !== $first_key && $this->looser_bracket,
                'C_FINAL' => $key == $last_key,
                'C_HAT_PLAYOFF' => $c_hat_ranking && $key == $first_key,
                'C_DRAW_GAMES' => $c_draw_games,
                'L_TITLE' => $c_hat_ranking && $key == $first_key ? $this->lang['scm.round.playoff'] : $this->lang['scm.round.of.'.$this->round_title($round).'']
            ]);
            $round_games = [];
            for ($i = 0; $i < count($games); $i++)
            {
                if ($games[$i]['game_group'] == $round)
                {
                    $round_games[] = $games[$i];
                }
            }

            $c_round = $c_hat_ranking ? ($key !== $last_key && $key !== $first_key) : ($key !== $last_key);
            if ($c_round)
            {
                $chunks = array_chunk($round_games, ceil(count($round_games) / 2));
                foreach ($chunks[0] as $game_a)
                {
                    if ($game_a['game_home_id'] != 0 && $game_a['game_away_id'] != 0)
                    {
                        foreach ($chunks[1] as $game_b)
                        {
                            if(
                                $game_b['game_away_id'] != 0
                                && $game_b['game_home_id'] != 0
                                && $game_a['game_home_id'] == $game_b['game_away_id']
                                && $game_a['game_away_id'] == $game_b['game_home_id']
                            )
                            {
                                $game = new ScmGame();
                                $game->set_properties($game_a);

                                $total_home = (int)$game_a['game_home_score'] + (int)$game_b['game_away_score'];
                                $total_away = (int)$game_a['game_away_score'] + (int)$game_b['game_home_score'];

                                $this->view->assign_block_vars('rounds.games', array_merge(
                                    $game->get_template_vars(),
                                    Date::get_array_tpl_vars($game_a['game_date'], 'game_date_a'),
                                    Date::get_array_tpl_vars($game_b['game_date'], 'game_date_b'),
                                    [
                                        'C_HOME_WIN' => $total_home > $total_away || $game_b['game_away_pen'] > $game_b['game_home_pen'],
                                        'C_AWAY_WIN' => $total_away > $total_home || $game_b['game_home_pen'] > $game_b['game_away_pen'],
                                        'C_HAS_PEN' => $game_b['game_home_pen'] != '' && $game_b['game_away_pen'] != '',
                                        'GAME_DATE_A_DAY_MONTH' => Date::to_format($game_a['game_date'], Date::FORMAT_DAY_MONTH),
                                        'GAME_DATE_A_YEAR' => date('Y', $game_a['game_date']),
                                        'GAME_DATE_B_DAY_MONTH' => Date::to_format($game_b['game_date'], Date::FORMAT_DAY_MONTH),
                                        'GAME_DATE_B_YEAR' => date('Y', $game_b['game_date']),
                                        'HOME_SCORE_B' => $game_b['game_away_score'],
                                        'HOME_PEN' => $game_b['game_away_pen'],
                                        'AWAY_SCORE_B' => $game_b['game_home_score'],
                                        'AWAY_PEN' => $game_b['game_home_pen'],
                                    ]
                                ));
                            }
                        }
                    }
                    elseif ($game_a['game_home_empty'] != '' && $game_a['game_away_empty'] != '')
                    {
                        foreach ($chunks[1] as $game_b)
                        {
                            if(
                                $game_b['game_away_empty'] != ''
                                && $game_b['game_home_empty'] != ''
                                && $game_a['game_home_empty'] == $game_b['game_away_empty']
                                && $game_a['game_away_empty'] == $game_b['game_home_empty']
                            )
                            {
                                $game = new ScmGame();
                                $game->set_properties($game_a);

                                $total_home = (int)$game_a['game_home_score'] + (int)$game_b['game_away_score'];
                                $total_away = (int)$game_a['game_away_score'] + (int)$game_b['game_home_score'];

                                $this->view->assign_block_vars('rounds.games', array_merge(
                                    $game->get_template_vars(),
                                    Date::get_array_tpl_vars($game_a['game_date'], 'game_date_a'),
                                    Date::get_array_tpl_vars($game_b['game_date'], 'game_date_b'),
                                    [
                                        'C_HOME_WIN' => $total_home > $total_away || $game_b['game_away_pen'] > $game_b['game_home_pen'],
                                        'C_AWAY_WIN' => $total_away > $total_home || $game_b['game_home_pen'] > $game_b['game_away_pen'],
                                        'C_HAS_PEN' => $game_b['game_home_pen'] != '' && $game_b['game_away_pen'] != '',
                                        'GAME_DATE_A_DAY_MONTH' => Date::to_format($game_a['game_date'], Date::FORMAT_DAY_MONTH),
                                        'GAME_DATE_A_YEAR' => date('Y', $game_a['game_date']),
                                        'GAME_DATE_B_DAY_MONTH' => Date::to_format($game_b['game_date'], Date::FORMAT_DAY_MONTH),
                                        'GAME_DATE_B_YEAR' => date('Y', $game_b['game_date']),
                                        'HOME_SCORE_B' => $game_b['game_away_score'],
                                        'HOME_PEN' => $game_b['game_away_pen'],
                                        'AWAY_SCORE_B' => $game_b['game_home_score'],
                                        'AWAY_PEN' => $game_b['game_home_pen'],
                                    ]
                                ));
                            }
                        }
                    }
                    else {
                        $game = new ScmGame();
                        $game->set_properties($game_a);

                        if ($game->get_game_group() == $round)
                        $this->view->assign_block_vars('rounds.games', $game->get_template_vars());
                    }
                }
            }
            else
            {
                for ($i = 0; $i < count($games); $i++)
                {
                    $game = new ScmGame();
                    $game->set_properties($games[$i]);

                    if ($game->get_game_group() == $round)
                    $this->view->assign_block_vars('rounds.games', $game->get_template_vars());
                }
            }
        }
    }

	/** Bracket with single matches */
	private function build_bracket_view()
	{
        $games = ScmGroupService::games_list_from_group($this->event_id(), 'B');
        $games = call_user_func_array('array_merge', $games);

        usort($games, function($a, $b) {
            if ($a['game_round'] == $b['game_round']) {
                return $a['game_order'] - $b['game_order'];
            } else {
                return $b['game_round'] - $a['game_round'];
            }
        });

        $brackets = [];
        foreach($games as $game)
        {
            $brackets[$game['game_round']][$game['game_group']][] = $game;
        }

        foreach($brackets as $bracket => $rounds)
        {
            $this->view->assign_block_vars('brackets', [
                'BRACKET_NAME' => $bracket == 1 ? $this->lang['scm.winner.bracket'] : (count($brackets) == 2 ? $this->lang['scm.looser.bracket'] : $this->lang['scm.looser.bracket'] . ' ' . ScmBracketService::ntl($bracket)),
                'BRACKET_ID' => $bracket,
            ]);

            // Reverse brackets to be looser.n, looser.n-1, looser.1, winner
            $keys = $this->looser_bracket ? array_reverse(array_keys($rounds)) : array_keys($rounds);
            $values = $this->looser_bracket ? array_reverse(array_values($rounds)) : array_values($rounds);
            $r_rounds = array_combine($keys, $values);

            // Isolate first round
            $key_rounds_count = array_keys($r_rounds);
            $first_key = reset($key_rounds_count);

            foreach($r_rounds as $round => $games)
            {
                $this->view->assign_block_vars('brackets.rounds', [
                    'C_ALL_PLACES' => $round !== $first_key && $this->looser_bracket,
                    'ROUND_ID' => $round,
                    'L_TITLE' => $this->lang['scm.round.of.'.$this->round_title($round).'']
                ]);

                foreach ($games as $game)
                {
                    $item = new ScmGame();
                    $item->set_properties($game);

                    $this->view->assign_block_vars('brackets.rounds.games', $item->get_template_vars());
                }
            }
        }
	}

	/** groups if finals with group */
    private function build_group_view()
    {
        $games = ScmGroupService::games_list_from_group($this->event_id(), 'B');
        $games = call_user_func_array('array_merge', $games);
        $groups = [];
        foreach($games as $game)
        {
            $groups[$game['game_group']][$game['game_round']][] = $game;
        }
        foreach ($groups as $group => $rounds)
        {
            $this->view->assign_block_vars('groups', [
                'GROUP' => $group
            ]);
            foreach ($rounds as $round => $games)
            {
                $this->view->assign_block_vars('groups.rounds', [
                    'ROUND' => $round
                ]);
                foreach($games as $game)
                {
                    $item = new ScmGame();
                    $item->set_properties($game);
                    $this->view->assign_block_vars('groups.rounds.games', $item->get_template_vars());
                    $item->get_details_template($this->view, 'groups.rounds.games');
                }
            }
            $ranks = ScmRankingService::general_groups_finals_ranking($this->event_id(), $group);
            foreach ($ranks as $i => $team_rank)
            {
                $this->view->assign_block_vars('groups.ranks', array_merge(
                    Date::get_array_tpl_vars(new Date($game['game_date'], Timezone::SERVER_TIMEZONE), 'game_date'),
                    [
                        'C_FAV'         => ScmParamsService::check_fav($this->event_id(), $team_rank['team_id']),
                        'RANK'          => $i + 1,
                        'TEAM_NAME'     => !empty($team_rank['team_id']) ? ScmTeamService::get_team_name($team_rank['team_id']) : '',
                        'TEAM_LOGO'     => !empty($team_rank['team_id']) ? ScmTeamService::get_team_logo($team_rank['team_id']) : '',
                        'POINTS'        => $team_rank['points'],
                        'PLAYED'        => $team_rank['played'],
                        'WIN'           => $team_rank['win'],
                        'DRAW'          => $team_rank['draw'],
                        'LOSS'          => $team_rank['loss'],
                        'GOALS_FOR'     => $team_rank['goals_for'],
                        'GOALS_AGAINST' => $team_rank['goals_against'],
                        'GOAL_AVERAGE'  => $team_rank['goal_average'],
                    ]
                ));
            }
        }
    }

	/** Title of round */
	private function round_title(int $number) : string
    {
        $round_number = [1, 2, 3, 4, 5, 6, 7];
        $round_title = [1, 2, 4, 8, 16, 32, 64];
        $key = array_search($number, $round_number);
        return $round_title[$key];
    }

    private function return_games()
    {
        return ScmEventService::get_event_game_type($this->event_id()) == ScmDivision::RETURN_GAMES;
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
        if (!empty($this->event_id()))
        {
            try {
                $this->params = ScmParamsService::get_params($this->event_id());
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
		return $this->params;
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
        $breadcrumb->add($this->lang['scm.games.brackets.stage'], ScmUrlBuilder::display_brackets_rounds($event->get_id(), $event->get_event_slug()));

		return $response;
	}
}
?>
