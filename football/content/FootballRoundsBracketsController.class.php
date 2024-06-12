<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballRoundsBracketsController extends DefaultModuleController
{
    private $compet;
    private $params;
    private $looser_bracket;
    private $teams_number;
    private $teams_per_group;
	protected function get_template_to_use()
	{
		return new FileTemplate('football/FootballRoundsBracketsController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
        if ($this->return_matches())
            $this->build_round_trip_view();
        else
        {
            if ($this->looser_bracket)
                $this->build_looser_view();
            $this->build_winner_view();
            $this->check_authorizations();
        }

        $this->view->put_all(array(
            'MENU' => FootballMenuService::build_compet_menu($this->compet_id()),
            'C_HAT_RANKING' => FootballParamsService::get_params($this->compet_id())->get_hat_ranking(),
            'C_RETURN_MATCHES' => $this->return_matches(),
            'C_ONE_DAY' => FootballMatchService::one_day_compet($this->compet_id()),
            'C_LOOSER_BRACKET' => $this->get_params()->get_looser_bracket(),
            'JS_DOC' => FootballBracketService::get_bracket_js_matches($this->compet_id(), $this->teams_number, $this->teams_per_group),
            'C_HAS_MATCHES' => FootballMatchService::has_matches($this->compet_id())
        ));

		return $this->generate_response();
	}

    private function init()
    {
        $this->looser_bracket = $this->get_params()->get_rounds_number();
        $this->teams_number = FootballTeamService::get_teams_number($this->compet_id());
        $this->teams_per_group = $this->get_params()->get_teams_per_group();
    }

	private function build_round_trip_view()
	{
        $matches = FootballGroupService::matches_list_from_group($this->compet_id(), 'W');
        $matches = call_user_func_array('array_merge', $matches);
        $c_hat_ranking = FootballParamsService::get_params($this->compet_id())->get_hat_ranking();

        $rounds = [];
        foreach ($matches as $match)
        {
            $rounds[] = $match['match_group'];
        }

        $rounds_count = array_reverse(array_unique($rounds));
        $key_rounds_count = array_keys($rounds_count);
        $first_key = reset($key_rounds_count);
        $last_key = end($key_rounds_count);

        foreach ($rounds_count as $key => $round)
        {
            $this->view->assign_block_vars('rounds', array(
                'C_ALL_PLACES' => $key !== $first_key && $this->looser_bracket,
                'C_FINAL' => $key == $last_key,
                'C_HAT_PLAYOFF' => $c_hat_ranking  && $key == $first_key,
                'L_TITLE' => $c_hat_ranking && $key == $first_key ? $this->lang['football.round.playoff'] : $this->lang['football.round.of.'.$this->round_title($round).'']
            ));
            $round_matches = [];
            for ($i = 0; $i < count($matches); $i++)
            {
                if ($matches[$i]['match_group'] == $round)
                {
                    $round_matches[] = $matches[$i];
                }
            }

            $c_round = $c_hat_ranking ? ($key !== $last_key && $key !== $first_key) : ($key !== $last_key);
            if ($c_round)
            {
                $chunks = array_chunk($round_matches, ceil(count($round_matches) / 2));
                foreach ($chunks[0] as $match_a)
                {
                    if ($match_a['match_home_id'] != 0 && $match_a['match_away_id'] != 0)
                    {
                        foreach ($chunks[1] as $match_b)
                        {
                            if(
                                $match_b['match_away_id'] != 0
                                && $match_b['match_home_id'] != 0
                                && $match_a['match_home_id'] == $match_b['match_away_id']
                                && $match_a['match_away_id'] == $match_b['match_home_id']
                            )
                            {
                                $match = new FootballMatch();
                                $match->set_properties($match_a);

                                $total_home_win = (int)$match_a['match_home_score'] + (int)$match_b['match_away_score'];
                                $total_away_win = (int)$match_a['match_away_score'] + (int)$match_b['match_home_score'];

                                $this->view->assign_block_vars('rounds.matches', array_merge(
                                    $match->get_array_tpl_vars(),
                                    Date::get_array_tpl_vars($match_a['match_date'], 'match_date_a'),
                                    Date::get_array_tpl_vars($match_b['match_date'], 'match_date_b'),
                                    array(
                                        'C_HOME_WIN' => $total_home_win > $total_away_win || $match_b['match_away_pen'] > $match_b['match_home_pen'],
                                        'C_AWAY_WIN' => $total_away_win > $total_home_win || $match_b['match_home_pen'] > $match_b['match_away_pen'],
                                        'C_HAS_PEN' => $match_b['match_home_pen'] != '' && $match_b['match_away_pen'] != '',
                                        'MATCH_DATE_A_DAY_MONTH' => Date::to_format($match_a['match_date'], Date::FORMAT_DAY_MONTH),
                                        'MATCH_DATE_A_YEAR' => date('Y', $match_a['match_date']),
                                        'MATCH_DATE_B_DAY_MONTH' => Date::to_format($match_b['match_date'], Date::FORMAT_DAY_MONTH),
                                        'MATCH_DATE_B_YEAR' => date('Y', $match_b['match_date']),
                                        'HOME_SCORE_B' => $match_b['match_away_score'],
                                        'HOME_PEN' => $match_b['match_away_pen'],
                                        'AWAY_SCORE_B' => $match_b['match_home_score'],
                                        'AWAY_PEN' => $match_b['match_home_pen'],
                                    )
                                ));
                            }
                        }
                    }
                    else {
                        $match = new FootballMatch();
                        $match->set_properties($match_a);

                        if ($match->get_match_group() == $round)
                        $this->view->assign_block_vars('rounds.matches', $match->get_array_tpl_vars());
                    }
                }
            }
            else
            {
                for ($i = 0; $i < count($matches); $i++)
                {
                    $match = new FootballMatch();
                    $match->set_properties($matches[$i]);

                    if ($match->get_match_group() == $round)
                    $this->view->assign_block_vars('rounds.matches', $match->get_array_tpl_vars());
                }
            }
        }
    }

	private function build_winner_view()
	{
        $matches = FootballGroupService::matches_list_from_group($this->compet_id(), 'W');
        $matches = call_user_func_array('array_merge', $matches);

        $rounds = [];
        foreach ($matches as $match)
        {
            $rounds[] = $match['match_group'];
        }

        $rounds_count = array_reverse(array_unique($rounds));
        $key_rounds_count = array_keys($rounds_count);
        $first_key = reset($key_rounds_count);

        foreach($rounds_count  as $key => $round)
        {
            $this->view->assign_block_vars('w_rounds', array(
                'C_ALL_PLACES' => $key !== $first_key && $this->looser_bracket,
                'ROUND_ID' => $round,
                'L_TITLE' => $this->lang['football.round.of.'.$this->round_title($round).'']
            ));

            for ($i = 0; $i < count($matches); $i++)
            {
                $match = new FootballMatch();
                $match->set_properties($matches[$i]);

                if ($match->get_match_group() == $round)
                {
                    $this->view->assign_block_vars('w_rounds.matches', $match->get_array_tpl_vars());
                }
            }
        }
	}

	private function build_looser_view()
	{
        $matches = FootballGroupService::matches_list_from_group($this->compet_id(), 'L');
        $matches = call_user_func_array('array_merge', $matches);

        $rounds = [];
        foreach ($matches as $match)
        {
            $rounds[] = $match['match_group'];
        }

        $rounds_count = array_reverse(array_unique($rounds));
        $key_rounds_count = array_keys($rounds_count);
        $first_key = reset($key_rounds_count);
        $last_key = end($key_rounds_count);
        foreach($rounds_count  as $key => $round)
        {
            $this->view->assign_block_vars('l_rounds', array(
                'C_ALL_PLACES' => $key !== $first_key && $this->looser_bracket,
                'ROUND_ID' => $round,
                'L_TITLE' => $this->lang['football.round.of.'.$this->round_title($round).'']
            ));

            for ($i = 0; $i < count($matches); $i++)
            {
                $match = new FootballMatch();
                $match->set_properties($matches[$i]);

                if ($match->get_match_group() == $round)
                {
                    $this->view->assign_block_vars('l_rounds.matches', $match->get_array_tpl_vars());
                }
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

    private function return_matches()
    {
        return FootballCompetService::get_compet_match_type($this->compet_id()) == FootballDivision::RETURN_MATCHES;
    }

	private function get_compet()
	{
		if ($this->compet === null)
		{
			$id = AppContext::get_request()->get_getint('compet_id', 0);
			if (!empty($id))
			{
				try {
					$this->compet = FootballCompetService::get_compet($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->compet = new FootballCompet();
		}
		return $this->compet;
	}

    private function compet_id()
    {
        return $this->get_compet()->get_id_compet();
    }

    private function get_params()
	{
        if (!empty($this->compet_id()))
        {
            try {
                $this->params = FootballParamsService::get_params($this->compet_id());
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
		return $this->params;
	}

	private function check_authorizations()
	{
		$compet = $this->get_compet();

		$current_user = AppContext::get_current_user();
		$not_authorized = !FootballAuthorizationsService::check_authorizations($compet->get_id_category())->moderation() && !FootballAuthorizationsService::check_authorizations($compet->get_id_category())->write() && (!FootballAuthorizationsService::check_authorizations($compet->get_id_category())->contribution() || $compet->get_author_user()->get_id() != $current_user->get_id());

		switch ($compet->get_publishing_state()) {
			case FootballCompet::PUBLISHED:
				if (!FootballAuthorizationsService::check_authorizations($compet->get_id_category())->read())
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case FootballCompet::NOT_PUBLISHED:
				if ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case FootballCompet::DEFERRED_PUBLICATION:
				if (!$compet->is_published() && ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL)))
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
		$compet = $this->get_compet();
		$category = $compet->get_category();
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($compet->get_compet_name(), ($category->get_id() != Category::ROOT_CATEGORY ? $category->get_name() . ' - ' : '') . $this->lang['football.module.title']);
		$graphical_environment->get_seo_meta_data()->set_description('');
		$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::compet_home($compet->get_id_compet()));

		// if ($compet->has_thumbnail())
		// 	$graphical_environment->get_seo_meta_data()->set_picture_url($compet->get_thumbnail());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'],FootballUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($compet->get_id_category(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), FootballUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::compet_home($compet->get_id_compet()));
        $breadcrumb->add($this->lang['football.matches.bracket.stage'], FootballUrlBuilder::display_brackets_rounds($compet->get_id_compet()));

		return $response;
	}
}
?>
