<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballBracketsMatchesFormController extends DefaultModuleController
{
    private $compet;
    private $params;
    private $hat_ranking;
    private $match;
    private $teams_number;
    private $teams_per_group;
    private $return_matches;

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
		$this->check_authorizations();

		$this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
            $this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['football.warning.matches.update'], MessageHelper::SUCCESS, 4));
		}

		$this->view->put_all(array(
            'MENU' => FootballMenuService::build_compet_menu($this->compet_id()),
            'CONTENT' => $this->form->display(),
            'JS_DOC' => $this->teams_number ? FootballBracketService::get_bracket_js_matches($this->compet_id(), $this->teams_number, $this->teams_per_group) : MessageHelper::display('', '')
        ));

		return $this->generate_response($this->view);
	}

    private function init()
    {
        $this->hat_ranking = $this->get_params()->get_hat_ranking();
        $this->teams_number = FootballTeamService::get_teams_number($this->compet_id());
        $this->teams_per_group = $this->get_params()->get_teams_per_group();
        $this->return_matches = FootballCompetService::get_compet_match_type($this->compet_id()) == FootballDivision::RETURN_MATCHES;
    }

	private function build_form()
	{
        $i = AppContext::get_request()->get_getint('round', 0);
		$form = new HTMLForm(__CLASS__);
        $form->set_css_class('floating-submit');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['football.matches.management'] . '</div>');

		$bracket_fieldset = new FormFieldsetHTML('looser_bracket', $this->lang['football.matches.brackets.stage']);
		$bracket_fieldset->set_css_class('grouped-selects');
        $bracket_fieldset->set_description('
            <div class="form-element free-select small">' . $this->lang['football.group'] . '</div>
            <div class="form-element date-select small">' . $this->lang['football.th.date'] . ' - ' . $this->lang['football.th.hourly'] . '</div>
            <div class="form-element playground small">' . $this->lang['football.th.playground'] . '</div>
            <div class="form-element home-bracket small">' . $this->lang['football.th.team'] . ' 1</div>
            <div class="form-element home-score small">' . $this->lang['football.th.score'] . ' 1</div>
            <div class="form-element home-pen small">' . $this->lang['football.th.pen'] . ' 1</div>
            <div class="form-element away-pen small">' . $this->lang['football.th.pen'] . ' 2</div>
            <div class="form-element away-score small">' . $this->lang['football.th.score'] . ' 2</div>
            <div class="form-element away-bracket small">' . $this->lang['football.th.team'] . ' 2</div>
        ');
        $form->add_fieldset($bracket_fieldset);

        $rounds_number = $this->get_params()->get_rounds_number() ? $this->get_params()->get_rounds_number() : (int)log($this->teams_number / 2, 2);

        if($this->get_params()->get_looser_bracket())
        {
            $looser_fieldset = new FormFieldsetHTML('looser_bracket', $this->lang['football.looser.bracket']);
            $looser_fieldset->set_css_class('grouped-selects');
            $form->add_fieldset($looser_fieldset);

            $looser_fieldset->add_field(new FormFieldSpacer('round_' . $i, $this->lang['football.round.' . $i . ''], array('class' => 'form-spacer-big')));
            $matches_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 4 : $this->round_matches_number($i);

            if ($this->return_matches)
                $looser_fieldset->add_field(new FormFieldSpacer('looser_first_leg_' . $i, $this->lang['football.first.leg']));
            for($j = 1; $j <= $matches_number; $j++)
            {
                $match_number = '<strong>L' . $i . $j . '</strong>';
                $match_date = $this->get_match('L', $i, $j) ? $this->get_match('L', $i, $j)->get_match_date() : new Date();
                $match_playground = $this->get_match('L', $i, $j) ? $this->get_match('L', $i, $j)->get_match_playground() : '';
                $match_home_id = $this->get_match('L', $i, $j) ? $this->get_match('L', $i, $j)->get_match_home_id() : 0;
                $match_home_score = $this->get_match('L', $i, $j) ? $this->get_match('L', $i, $j)->get_match_home_score() : '';
                $match_home_pen = $this->get_match('L', $i, $j) ? $this->get_match('L', $i, $j)->get_match_home_pen() : '';
                $match_away_pen = $this->get_match('L', $i, $j) ? $this->get_match('L', $i, $j)->get_match_away_pen() : '';
                $match_away_score = $this->get_match('L', $i, $j) ? $this->get_match('L', $i, $j)->get_match_away_score() : '';
                $match_away_id = $this->get_match('L', $i, $j) ? $this->get_match('L', $i, $j)->get_match_away_id() : 0;

                $looser_fieldset->add_field(new FormFieldFree('l_round_match_number_' . $i . $j, '', $match_number,
                    array('class' => 'match-select free-select small text-italic align-right form-L' . $i . $j)
                ));
                $looser_fieldset->add_field(new FormFieldDateTime('l_round_match_date_' . $i . $j, '', $match_date,
                    array('class' => 'match-select date-select')
                ));
                if($this->get_params()->get_display_playgrounds())
                    $looser_fieldset->add_field(new FormFieldTextEditor('l_round_match_playground_' . $i . $j, '', $match_playground,
                        array('class' => 'match-select playground', 'placeholder' => $this->lang['football.field'])
                    ));
                else
                    $looser_fieldset->add_field(new FormFieldFree('l_round_match_playground_' . $i . $j, '', '',
                        array('class' => 'match-select playground')
                    ));
                $looser_fieldset->add_field(new FormFieldSimpleSelectChoice('l_round_home_team_' . $i . $j, '', $match_home_id,
                    $this->get_teams_list(),
                    array('class' => 'home-team match-select home-bracket')
                ));
                $looser_fieldset->add_field(new FormFieldTextEditor('l_round_home_score_' . $i . $j, '', $match_home_score,
                    array('class' => 'home-team match-select home-score', 'pattern' => '[0-9]*')
                ));
                $looser_fieldset->add_field(new FormFieldTextEditor('l_round_home_pen_' . $i . $j, '', $match_home_pen,
                    array('class' => 'home-team match-select home-pen', 'pattern' => '[0-9]*')
                ));
                $looser_fieldset->add_field(new FormFieldTextEditor('l_round_away_pen_' . $i . $j, '', $match_away_pen,
                    array('class' => 'away-team match-select away-pen', 'pattern' => '[0-9]*')
                ));
                $looser_fieldset->add_field(new FormFieldTextEditor('l_round_away_score_' . $i . $j, '', $match_away_score,
                    array('class' => 'away-team match-select away-score', 'pattern' => '[0-9]*')
                ));
                $looser_fieldset->add_field(new FormFieldSimpleSelectChoice('l_round_away_team_' . $i . $j, '', $match_away_id,
                    $this->get_teams_list(),
                    array('class' => 'away-team match-select away-bracket')
                ));
                if ($this->return_matches && $j == $matches_number / 2)
                    $looser_fieldset->add_field(new FormFieldSpacer('looser_second_leg_' . $i, '<hr />' . $this->lang['football.second.leg']));
            }
        }

		$winner_fieldset = new FormFieldsetHTML('winner_bracket', $this->get_params()->get_looser_bracket() ? $this->lang['football.winner.bracket'] : '');
		$winner_fieldset->set_css_class('grouped-selects');
		$form->add_fieldset($winner_fieldset);

        $winner_fieldset->add_field(new FormFieldSpacer('round_' . $i, ($this->hat_ranking && $i == $rounds_number + 1) ? $this->lang['football.playoff.matches'] : $this->lang['football.round.' . $i . ''], array('class' => 'form-spacer-big')));
        if ($this->return_matches) {
            if ($this->hat_ranking && $i == $rounds_number + 1)
                $matches_number = $this->get_params()->get_playoff() / 2;
            elseif ($i == 1)
                $matches_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 4 : $this->round_matches_number($i);
            else
                $matches_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 2 : $this->round_matches_number($i);
        } else {
            $matches_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 4 : $this->round_matches_number($i);
            if ($i == 1 && $this->get_params()->get_third_place())
                $matches_number = 2;
        }

        if (($this->return_matches && $i != 1) && ($this->hat_ranking && $i != $rounds_number + 1))
            $winner_fieldset->add_field(new FormFieldSpacer('winner_first_leg_' . $i, $this->lang['football.first.leg']));
        for($j = 1; $j <= $matches_number; $j++)
        {
            $match_number = '<strong>W' . $i . $j . '</strong>';
            $match_date = $this->get_match('W', $i, $j) ? $this->get_match('W', $i, $j)->get_match_date() : new Date();
            $match_playground = $this->get_match('W', $i, $j) ? $this->get_match('W', $i, $j)->get_match_playground() : '';
            $match_home_id = $this->get_match('W', $i, $j) ? $this->get_match('W', $i, $j)->get_match_home_id() : 0;
            $match_home_score = $this->get_match('W', $i, $j) ? $this->get_match('W', $i, $j)->get_match_home_score() : '';
            $match_home_pen = $this->get_match('W', $i, $j) ? $this->get_match('W', $i, $j)->get_match_home_pen() : '';
            $match_away_pen = $this->get_match('W', $i, $j) ? $this->get_match('W', $i, $j)->get_match_away_pen() : '';
            $match_away_score = $this->get_match('W', $i, $j) ? $this->get_match('W', $i, $j)->get_match_away_score() : '';
            $match_away_id = $this->get_match('W', $i, $j) ? $this->get_match('W', $i, $j)->get_match_away_id() : 0;

            $winner_fieldset->add_field(new FormFieldFree('w_round_match_number_' . $i . $j, '', $match_number,
                array('class' => 'match-select free-select small text-italic align-right form-W' . $i . $j)
            ));
            $winner_fieldset->add_field(new FormFieldDateTime('w_round_match_date_' . $i . $j, '', $match_date,
                array('class' => 'match-select date-select')
            ));
            if($this->get_params()->get_display_playgrounds())
                $winner_fieldset->add_field(new FormFieldTextEditor('w_round_match_playground_' . $i . $j, '', $match_playground,
                    array('class' => 'match-select playground', 'placeholder' => $this->lang['football.field'])
                ));
            else
                $winner_fieldset->add_field(new FormFieldFree('w_round_match_playground_' . $i . $j, '', '',
                    array('class' => 'match-select playground')
                ));
            $winner_fieldset->add_field(new FormFieldSimpleSelectChoice('w_round_home_team_' . $i . $j, '', $match_home_id,
                $this->get_teams_list(),
                array('class' => 'home-team match-select home-bracket')
            ));
            $winner_fieldset->add_field(new FormFieldTextEditor('w_round_home_score_' . $i . $j, '', $match_home_score,
                array('class' => 'home-team match-select home-score', 'pattern' => '[0-9]*')
            ));
            if (($j <= $matches_number / 2 && $this->return_matches) && ($i != $rounds_number + 1 && $this->hat_ranking)) {
                $winner_fieldset->add_field(new FormFieldTextEditor('w_round_home_pen_' . $i . $j, '', '',
                    array('class' => 'home-team match-select home-pen', 'disabled' => true)
                ));
                $winner_fieldset->add_field(new FormFieldTextEditor('w_round_away_pen_' . $i . $j, '', '',
                    array('class' => 'away-team match-select away-pen', 'disabled' => true)
                ));
            } else {
                $winner_fieldset->add_field(new FormFieldTextEditor('w_round_home_pen_' . $i . $j, '', $match_home_pen,
                    array('class' => 'home-team match-select home-pen', 'pattern' => '[0-9]*')
                ));
                $winner_fieldset->add_field(new FormFieldTextEditor('w_round_away_pen_' . $i . $j, '', $match_away_pen,
                    array('class' => 'away-team match-select away-pen', 'pattern' => '[0-9]*')
                ));
            }
            $winner_fieldset->add_field(new FormFieldTextEditor('w_round_away_score_' . $i . $j, '', $match_away_score,
                array('class' => 'away-team match-select away-score', 'pattern' => '[0-9]*')
            ));
            $winner_fieldset->add_field(new FormFieldSimpleSelectChoice('w_round_away_team_' . $i . $j, '', $match_away_id,
                $this->get_teams_list(),
                array('class' => 'away-team match-select away-bracket')
            ));
            if (($this->return_matches && $j == $matches_number / 2) && ($this->hat_ranking && $i != $rounds_number + 1))
                $winner_fieldset->add_field(new FormFieldSpacer('winner_second_leg_' . $i, '<hr />' . $this->lang['football.second.leg']));
        }

        $this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$this->form = $form;
	}

	private function save()
	{
        $i = AppContext::get_request()->get_getint('round', 0);
        $rounds_number = $this->hat_ranking ? $this->get_params()->get_rounds_number() + 1 : $this->get_params()->get_rounds_number();

        // looser bracket
        if($this->get_params()->get_looser_bracket())
        {
            $matches_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 4 : $this->round_matches_number($i);

            for($j = 1; $j <= $matches_number; $j++)
            {
                $match = $this->get_match('L', $i, $j);
                $match->set_match_compet_id($this->compet_id());
                $match->set_match_type('L');
                $match->set_match_group($i);
                $match->set_match_order($j);
                $match->set_match_date($this->form->get_value('l_round_match_date_' . $i . $j));
                if($this->get_params()->get_display_playgrounds())
                    $match->set_match_playground($this->form->get_value('l_round_match_playground_' . $i . $j));
                $match->set_match_home_id((int)$this->form->get_value('l_round_home_team_' . $i . $j)->get_raw_value());
                $match->set_match_home_score($this->form->get_value('l_round_home_score_' . $i . $j));
                $match->set_match_home_pen($this->form->get_value('l_round_home_pen_' . $i . $j));
                $match->set_match_away_pen($this->form->get_value('l_round_away_pen_' . $i . $j));
                $match->set_match_away_score($this->form->get_value('l_round_away_score_' . $i . $j));
                $match->set_match_away_id((int)$this->form->get_value('l_round_away_team_' . $i . $j)->get_raw_value());

                if ($match->get_id_match() == null)
                {
                    $id = FootballMatchService::add_match($match);
                    $match->set_id_match($id);
                }
                else {
                    FootballMatchService::update_match($match, $match->get_id_match());
                }
            }
        }

        // Winner bracket
        if ($this->return_matches) {
            if ($this->hat_ranking && $i == $rounds_number) {
                // Debug::stop('a/r hat');
                $matches_number = $this->get_params()->get_playoff() / 2;
            } elseif ($i == 1) {
                // Debug::stop('a/r last');
                $matches_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 4 : $this->round_matches_number($i);
            } else {
                // Debug::stop('a/r else');
                $matches_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 2 : $this->round_matches_number($i);
            }
        } else {
            // Debug::stop('s last');
            $matches_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 4 : $this->round_matches_number($i);
            if ($i == 1 && $this->get_params()->get_third_place())
                $matches_number = 2;
        }

        for($j = 1; $j <= $matches_number; $j++)
        {
            $match = $this->get_match('W', $i, $j);
            $match->set_match_compet_id($this->compet_id());
            $match->set_match_type('W');
            $match->set_match_group($i);
            $match->set_match_order($j);
            $match->set_match_date($this->form->get_value('w_round_match_date_' . $i . $j));
            if($this->get_params()->get_display_playgrounds())
                $match->set_match_playground($this->form->get_value('w_round_match_playground_' . $i . $j));
            $match->set_match_home_id((int)$this->form->get_value('w_round_home_team_' . $i . $j)->get_raw_value());
            $match->set_match_home_score($this->form->get_value('w_round_home_score_' . $i . $j));
            $match->set_match_home_pen($this->form->get_value('w_round_home_pen_' . $i . $j));
            $match->set_match_away_pen($this->form->get_value('w_round_away_pen_' . $i . $j));
            $match->set_match_away_score($this->form->get_value('w_round_away_score_' . $i . $j));
            $match->set_match_away_id((int)$this->form->get_value('w_round_away_team_' . $i . $j)->get_raw_value());

            // Debug::dump($j);
            // Debug::dump($match->get_match_home_id());
            // Debug::dump($match->get_match_away_id());
            if ($match->get_id_match() == null)
            {
                $id = FootballMatchService::add_match($match);
                $match->set_id_match($id);
            }
            else {
                FootballMatchService::update_match($match, $match->get_id_match());
            }
        }
// Debug::stop();
		FootballCompetService::clear_cache();
	}

    private function round_matches_number($round)
    {
        return FootballBracketService::round_matches_number($round, $this->return_matches);
    }

	private function get_match($type, $group, $order)
	{
        $compet_id = $this->compet_id();
        $id = FootballMatchService::get_match($compet_id, $type, $group, $order) ? FootballMatchService::get_match($compet_id, $type, $group, $order)->get_id_match() : null;

        if($id !== null)
            try {
                $this->match = FootballMatchService::get_match($compet_id, $type, $group, $order);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        else
        {
            $this->match = new FootballMatch();
        }
		return $this->match;
	}

	private function get_compet()
	{
		$id = AppContext::get_request()->get_getint('compet_id', 0);
		try {
            $this->compet = FootballCompetService::get_compet($id);
        } catch (RowNotFoundException $e) {
            $error_controller = PHPBoostErrors::unexisting_page();
            DispatchManager::redirect($error_controller);
        }
		return $this->compet;
	}

    private function compet_id()
    {
        return $this->get_compet()->get_id_compet();
    }

    private function get_teams_list()
    {
        $options = [];
        $clubs = FootballClubCache::load();
        $options[] = new FormFieldSelectChoiceOption('', 0);
        foreach (FootballTeamService::get_teams($this->compet_id()) as $team)
        {
			$options[] = new FormFieldSelectChoiceOption($clubs->get_club_name($team['team_club_id']), $team['id_team']);
        }

		return $options;
    }

    private function get_params()
	{
        $id = AppContext::get_request()->get_getint('compet_id', 0);
        if (!empty($id))
        {
            try {
                $this->params = FootballParamsService::get_params($id);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
		return $this->params;
	}

	protected function get_template_string_content()
	{
		return '
            # INCLUDE MESSAGE_HELPER #
            # INCLUDE MENU #
            # INCLUDE CONTENT #
            # INCLUDE JS_DOC #
        ';
	}

	private function check_authorizations()
	{
		if (!$this->get_compet()->is_authorized_to_manage_compets())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }

		if (AppContext::get_current_user()->is_readonly())
		{
			$controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($controller);
		}
	}

	private function generate_response(View $view)
	{
		$compet = $this->get_compet();

		// $location_id = $compet->get_id_compet() ? 'football-edit-'. $compet->get_id_compet() : '';

		// $response = new SiteDisplayResponse($view, $location_id);
		$response = new SiteDisplayResponse($view);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());

		// if (!AppContext::get_session()->location_id_already_exists($location_id))
        //     $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['football.matches.management'], $this->lang['football.module.title']);
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['football.matches.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::edit_brackets_matches($compet->get_id_compet()));

        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($compet->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), FootballUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        $category = $compet->get_category();
        $breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::compet_home($compet->get_id_compet()));
        $breadcrumb->add($this->lang['football.matches.management'], FootballUrlBuilder::edit_brackets_matches($compet->get_id_compet()));

		return $response;
	}
}
?>
