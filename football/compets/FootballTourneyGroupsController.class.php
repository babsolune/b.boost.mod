<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballTourneyGroupsController extends DefaultModuleController
{
    private $compet;
	protected function get_template_to_use()
	{
		return new FileTemplate('football/FootballTourneyGroupsController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_view();
		$this->check_authorizations();

        $this->view->put('C_ONE_DAY', FootballMatchService::one_day_compet($this->id_compet()));

		return $this->generate_response();
	}

	private function build_view()
	{
        $groups = FootballGroupService::match_list_from_group($this->id_compet(), 'G');

        foreach ($groups as $group => $matches)
        {
            $this->view->assign_block_vars('groups',array(
                'GROUP' => FootballGroupService::ntl($group)
            ));
            $ranks = [];
            foreach ($matches as $match)
            {
                $c_home_score = $match['match_home_score'] != '';
                $c_away_score = $match['match_away_score'] != '';

                $this->view->assign_block_vars('groups.matches', array_merge(
                    Date::get_array_tpl_vars(new Date($match['match_date'], Timezone::SERVER_TIMEZONE), 'match_date'),
                    array(
                        'C_HAS_SCORE' => $c_home_score && $c_away_score,
                        'PLAYGROUND' => $match['match_playground'],
                        'HOME_TEAM' => !empty($match['match_home_id']) ? FootballTeamService::get_team($match['match_home_id'])->get_team_club_name() : '',
                        'HOME_SCORE' => $match['match_home_score'],
                        'AWAY_SCORE' => $match['match_away_score'],
                        'AWAY_TEAM' => !empty($match['match_home_id']) ? FootballTeamService::get_team($match['match_away_id'])->get_team_club_name() : ''
                    )
                ));

                $ranks[] = [
                    'team_id' => $match['match_home_id'],
                    'goals_for' => $match['match_home_score'],
                    'goals_against' => $match['match_away_score'],
                ];
                $ranks[] = [
                    'team_id' => $match['match_away_id'],
                    'goals_for' => $match['match_away_score'],
                    'goals_against' => $match['match_home_score'],
                ];
            }

            $teams = [];
            for($i=0; $i < count($ranks); $i++)
            {
                $points = $played = $win = $draw = $loss = 0;
                if ($ranks[$i]['goals_for'] > $ranks[$i]['goals_against'])
                {
                    $points = FootballParamsService::get_params($this->id_compet())->get_victory_points();
                    $win = $played = 1;
                }
                elseif ($ranks[$i]['goals_for'] != '' && ($ranks[$i]['goals_for'] === $ranks[$i]['goals_against']))
                {
                    $points = FootballParamsService::get_params($this->id_compet())->get_draw_points();
                    $draw = $played = 1;
                }
                elseif (($ranks[$i]['goals_for'] < $ranks[$i]['goals_against']))
                {
                    $points = FootballParamsService::get_params($this->id_compet())->get_loss_points();
                    $loss = $played = 1;
                }
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
                $teamId = $team['team_id'];

                if (!isset($ranks[$teamId])) {
                    $ranks[$teamId] = $team;
                } else {
                    $ranks[$teamId]['points'] += $team['points'];
                    $ranks[$teamId]['played'] += $team['played'];
                    $ranks[$teamId]['win'] += $team['win'];
                    $ranks[$teamId]['draw'] += $team['draw'];
                    $ranks[$teamId]['loss'] += $team['loss'];
                    $ranks[$teamId]['goals_for'] += $team['goals_for'];
                    $ranks[$teamId]['goals_against'] += $team['goals_against'];
                    $ranks[$teamId]['goal_average'] += $team['goals_for'] - $team['goals_against'];
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

            $color_count = count($ranks);
            foreach ($ranks as $i => $team_rank)
            {
                $prom = FootballParamsService::get_params($this->id_compet())->get_promotion();
                $releg = FootballParamsService::get_params($this->id_compet())->get_relegation();
                $c_prom = $c_releg = false;
                if ($i < $prom)
                    $c_prom = true;
                if ($i >= $color_count - $releg)
                    $c_releg = true;
                $this->view->assign_block_vars('groups.ranks', array_merge(
                    Date::get_array_tpl_vars(new Date($match['match_date'], Timezone::SERVER_TIMEZONE), 'match_date'),
                    array(
                        'C_PROM' => $c_prom,
                        'C_RELEG' => $c_releg,
                        'PROM_COLOR' => FootballParamsService::get_params($this->id_compet())->get_promotion_color(),
                        'RELEG_COLOR' => FootballParamsService::get_params($this->id_compet())->get_relegation_color(),
                        'RANK' => $i + 1,
                        'TEAM_NAME' => !empty($team_rank['team_id']) ? FootballTeamService::get_team($team_rank['team_id'])->get_team_club_name() : '',
                        'POINTS' => $team_rank['points'],
                        'PLAYED' => $team_rank['played'],
                        'WIN' => $team_rank['win'],
                        'DRAW' => $team_rank['draw'],
                        'LOSS' => $team_rank['loss'],
                        'GOALS_FOR' => $team_rank['goals_for'],
                        'GOALS_AGAINST' => $team_rank['goals_against'],
                        'GOAL_AVERAGE' => $team_rank['goal_average'],
                    )
                ));
            }
        }

        $this->view->put_all(array(
            'MENU' => FootballCompetMenuService::build_compet_menu($this->id_compet()),
            'C_MATCHES' => count(FootballMatchService::get_matches($this->id_compet())) > 0
        ));
	}

    private function build_ranking()
    {
        
    }

	private function get_compet()
	{
		if ($this->compet === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
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

    private function id_compet()
    {
        return $this->get_compet()->get_id_compet();
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
				if ($not_authorized || ($current_user->get_id() == User::AWAYOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case FootballCompet::DEFERRED_PUBLICATION:
				if (!$compet->is_published() && ($not_authorized || ($current_user->get_id() == User::AWAYOR_LEVEL)))
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
		$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $compet->get_id_compet(), $compet->get_compet_slug()));

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
		$breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $compet->get_id_compet(), $compet->get_compet_slug()));

		return $response;
	}
}
?>
