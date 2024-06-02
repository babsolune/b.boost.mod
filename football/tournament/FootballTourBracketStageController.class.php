<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballTourBracketStageController extends DefaultModuleController
{
    private $compet;
    private $params;
    private $looser_bracket;
    private $rounds_number;
    private $teams_number;
    private $teams_per_group;
	protected function get_template_to_use()
	{
		return new FileTemplate('football/FootballTournamentBracketController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
        if ($this->looser_bracket)
            $this->build_looser_view();
		$this->build_winner_view();
		$this->check_authorizations();

        $this->view->put_all(array(
            'MENU' => FootballMenuService::build_compet_menu($this->compet_id()),
            'C_ONE_DAY' => FootballMatchService::one_day_compet($this->compet_id()),
            'C_LOOSER_BRACKET' => $this->get_params()->get_looser_bracket(),
            'JS_DOC' => FootballTournamentService::build_bracket_js_matches($this->compet_id(), $this->teams_number, $this->teams_per_group),
            'C_HAS_MATCHES' => FootballMatchService::has_matches($this->compet_id())
        ));

		return $this->generate_response();
	}

    private function init()
    {
        $this->looser_bracket = $this->get_params()->get_rounds_number();
        $this->rounds_number = $this->get_params()->get_rounds_number();
        $this->teams_number = FootballTeamService::get_teams_number($this->compet_id());
        $this->teams_per_group = $this->get_params()->get_teams_per_group();
    }

	private function build_winner_view()
	{
        $matches = FootballTournamentService::matches_list_from_group($this->compet_id(), 'W');
        $matches = call_user_func_array('array_merge', $matches);

        $this->view->put_all(array(
            'ROUNDS_NUMBER' => $this->rounds_number,
        ));

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
        $matches = FootballTournamentService::matches_list_from_group($this->compet_id(), 'L');
        $matches = call_user_func_array('array_merge', $matches);

        $this->view->put_all(array(
            'ROUNDS_NUMBER' => $this->rounds_number,
        ));

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
		$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::calendar($compet->get_id_compet()));

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
		$breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::calendar($compet->get_id_compet()));
        $breadcrumb->add($this->lang['football.matches.bracket.stage'], FootballUrlBuilder::display_bracket_stage($compet->get_id_compet()));

		return $response;
	}
}
?>
