<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LATIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballMatchesFormController extends DefaultModuleController
{
    private $compet;
    private $params;
    private $match;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->redirect();
		}

		$this->view->put_all(array(
            'MENU' => FootballCompetMenuService::build_compet_menu($this->id_compet()),
            'CONTENT' => $this->form->display()
        ));

		return $this->generate_response($this->view);
	}

	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);
		$form->set_layout_title('<div class="align-center small">' . $this->lang['football.matches.management'] . '</div>');

		$groups_fieldset = new FormFieldsetHTML('groups_bracket', $this->lang['football.matches.groups.management']);
		$groups_fieldset->set_css_class('grouped-selects');
        $form->add_fieldset($groups_fieldset);

        $teams_number = FootballTeamService::get_compet_teams_number($this->id_compet());
        $teams_per_group = $this->get_params()->get_teams_per_group();
        $groups_number = (int)($teams_number / $teams_per_group);
        $groups_matches_number = $teams_per_group * ($teams_per_group - 1) / 2;

        for ($i = 1; $i <= $groups_number; $i++)
        {
            $fieldset = new FormFieldsetHTML('group_' . $i, $this->lang['football.group'] . ' ' . FootballGroupService::ntl($i));
            $fieldset->set_css_class('grouped-selects');
            $form->add_fieldset($fieldset);

            for ($j = 1; $j <= $groups_matches_number; $j++)
            {
                $match_number = FootballMatchService::get_match_in_group($this->id_compet(), 'G' . $i . $j) ? $this->get_match('G' . $i . $j)->get_match_number() : '';
                $match_date = FootballMatchService::get_match_in_group($this->id_compet(), 'G' . $i . $j) ? $this->get_match('G' . $i . $j)->get_match_date() : new Date();
                $match_playground = FootballMatchService::get_match_in_group($this->id_compet(), 'G' . $i . $j) ? $this->get_match('G' . $i . $j)->get_match_playground() : '';
                $match_home_id = FootballMatchService::get_match_in_group($this->id_compet(), 'G' . $i . $j) ? $this->get_match('G' . $i . $j)->get_match_home_id() : 0;
                $match_home_score = FootballMatchService::get_match_in_group($this->id_compet(), 'G' . $i . $j) ? $this->get_match('G' . $i . $j)->get_match_home_score() : '';
                $match_away_score = FootballMatchService::get_match_in_group($this->id_compet(), 'G' . $i . $j) ? $this->get_match('G' . $i . $j)->get_match_away_score() : '';
                $match_away_id = FootballMatchService::get_match_in_group($this->id_compet(), 'G' . $i . $j) ? $this->get_match('G' . $i . $j)->get_match_away_id() : 0;

                $fieldset->add_field(new FormFieldFree('group_match_number_' . $i . $j, '', $match_number,
                    array('class' => 'match-select free-select small text-italic')
                ));
                $fieldset->add_field(new FormFieldDateTime('group_match_date_' . $i . $j, '', $match_date,
                    array('class' => 'match-select date-select')
                ));
                if(FootballParamsService::get_params($this->id_compet())->get_display_playgrounds())
                    $fieldset->add_field(new FormFieldTextEditor('group_match_playground_' . $i . $j, '', $match_playground,
                        array('class' => 'match-select playground', 'placeholder' => $this->lang['football.field'])
                    ));
                else
                    $fieldset->add_field(new FormFieldFree('group_match_playground_' . $i . $j, '', '',
                        array('class' => 'match-select playground')
                    ));
                $fieldset->add_field(new FormFieldSimpleSelectChoice('group_home_team_' . $i . $j, '', $match_home_id,
                    $this->get_group_teams_list($i),
                    array('class' => 'home-team match-select home-select')
                ));
                $fieldset->add_field(new FormFieldTextEditor('group_home_score_' . $i . $j, '', $match_home_score,
                    array('class' => 'home-team match-select home-score', 'pattern' => '[0-9]*')
                ));
                $fieldset->add_field(new FormFieldTextEditor('group_away_score_' . $i . $j, '', $match_away_score,
                    array('class' => 'away-team match-select away-score', 'pattern' => '[0-9]*')
                ));
                $fieldset->add_field(new FormFieldSimpleSelectChoice('group_away_team_' . $i . $j, '', $match_away_id,
                    $this->get_group_teams_list($i),
                    array('class' => 'away-team match-select away-select')
                ));
            }
        }

		$finals_fieldset = new FormFieldsetHTML('looser_bracket', $this->lang['football.matches.final.management']);
		$finals_fieldset->set_css_class('grouped-selects');
        $form->add_fieldset($finals_fieldset);

        $teams_number = FootballTeamService::get_compet_teams_number($this->id_compet());
        $rounds_number = FootballParamsService::get_params($this->id_compet())->get_rounds_number() ? FootballParamsService::get_params($this->id_compet())->get_rounds_number() : (int)log($teams_number / 2, 2);

        if(FootballParamsService::get_params($this->id_compet())->get_looser_bracket())
        {
            $looser_fieldset = new FormFieldsetHTML('looser_bracket', $this->lang['football.looser.bracket']);
            $looser_fieldset->set_css_class('grouped-selects');
            $looser_fieldset->set_description('
                <div class="form-element free-select small">' . $this->lang['football.group'] . '</div>
                <div class="form-element date-select small">' . $this->lang['football.th.date'] . ' | ' . $this->lang['football.th.hourly'] . '</div>
                <div class="form-element playground small">' . $this->lang['football.th.playground'] . '</div>
                <div class="form-element home-bracket small">' . $this->lang['football.th.team'] . ' 1</div>
                <div class="form-element home-score small">' . $this->lang['football.th.score'] . ' 1</div>
                <div class="form-element home-pen small">' . $this->lang['football.th.pen'] . ' 1</div>
                <div class="form-element away-pen small">' . $this->lang['football.th.pen'] . ' 2</div>
                <div class="form-element away-score small">' . $this->lang['football.th.score'] . ' 2</div>
                <div class="form-element away-bracket small">' . $this->lang['football.th.team'] . ' 2</div>
            ');
            $form->add_fieldset($looser_fieldset);

            for ($i = $rounds_number; $i >= 1; $i--)
            {
                $looser_fieldset->add_field(new FormFieldSpacer('round_' . $i, $this->lang['football.round'] . ' ' . $this->lang['football.round.' . $i . '']));
                $matches_number = FootballParamsService::get_params($this->id_compet())->get_all_places() ? $teams_number / 4 : $this->round_matches_number($i);

                for($j = 1; $j <= $matches_number; $j++)
                {
                    $match_number = '<strong>L' . $this->lang['football.round.' . $i . ''] . $j . '</strong>';
                    $match_date = FootballMatchService::get_match_in_group($this->id_compet(), 'L' . $i . $j) ? $this->get_match('L' . $i . $j)->get_match_date() : new Date();
                    $match_playground = FootballMatchService::get_match_in_group($this->id_compet(), 'L' . $i . $j) ? $this->get_match('L' . $i . $j)->get_match_playground() : '';
                    $match_home_id = FootballMatchService::get_match_in_group($this->id_compet(), 'L' . $i . $j) ? $this->get_match('L' . $i . $j)->get_match_home_id() : 0;
                    $match_home_score = FootballMatchService::get_match_in_group($this->id_compet(), 'L' . $i . $j) ? $this->get_match('L' . $i . $j)->get_match_home_score() : '';
                    $match_home_pen = FootballMatchService::get_match_in_group($this->id_compet(), 'L' . $i . $j) ? $this->get_match('L' . $i . $j)->get_match_home_pen() : '';
                    $match_away_pen = FootballMatchService::get_match_in_group($this->id_compet(), 'L' . $i . $j) ? $this->get_match('L' . $i . $j)->get_match_away_pen() : '';
                    $match_away_score = FootballMatchService::get_match_in_group($this->id_compet(), 'L' . $i . $j) ? $this->get_match('L' . $i . $j)->get_match_away_score() : '';
                    $match_away_id = FootballMatchService::get_match_in_group($this->id_compet(), 'L' . $i . $j) ? $this->get_match('L' . $i . $j)->get_match_away_id() : 0;

                    $looser_fieldset->add_field(new FormFieldFree('l_round_match_number_' . $i . $j, '', $match_number,
                        array('class' => 'match-select free-select small text-italic L' . $this->lang['football.round.' . $i . ''] . $j)
                    ));
                    $looser_fieldset->add_field(new FormFieldDateTime('l_round_match_date_' . $i . $j, '', $match_date,
                        array('class' => 'match-select date-select')
                    ));
                    if(FootballParamsService::get_params($this->id_compet())->get_display_playgrounds())
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
                }
            }
        }

		$winner_fieldset = new FormFieldsetHTML('winner_bracket', $this->lang['football.winner.bracket']);
		$winner_fieldset->set_css_class('grouped-selects');
		$winner_fieldset->set_description('
            <div class="form-element free-select small">' . $this->lang['football.group'] . '</div>
            <div class="form-element date-select small">' . $this->lang['football.th.date'] . ' | ' . $this->lang['football.th.hourly'] . '</div>
            <div class="form-element playground small">' . $this->lang['football.th.playground'] . '</div>
            <div class="form-element home-bracket small">' . $this->lang['football.th.team'] . ' 1</div>
            <div class="form-element home-score small">' . $this->lang['football.th.score'] . ' 1</div>
            <div class="form-element home-pen small">' . $this->lang['football.th.pen'] . ' 1</div>
            <div class="form-element away-pen small">' . $this->lang['football.th.pen'] . ' 2</div>
            <div class="form-element away-score small">' . $this->lang['football.th.score'] . ' 2</div>
            <div class="form-element away-bracket small">' . $this->lang['football.th.team'] . ' 2</div>
        ');
        $form->add_fieldset($winner_fieldset);

        for ($i = $rounds_number; $i >= 1; $i--)
        {
            $winner_fieldset->add_field(new FormFieldSpacer('round_' . $i, $this->lang['football.round'] . ' ' . $this->lang['football.round.' . $i . '']));
            $matches_number = FootballParamsService::get_params($this->id_compet())->get_all_places() ? $teams_number / 4 : $this->round_matches_number($i);

            for($j = 1; $j <= $matches_number; $j++)
            {
                $match_number = '<strong>W' . $this->lang['football.round.' . $i . ''] . $j . '</strong>';
                $match_date = FootballMatchService::get_match_in_group($this->id_compet(), 'W' . $i . $j) ? $this->get_match('W' . $i . $j)->get_match_date() : new Date();
                $match_playground = FootballMatchService::get_match_in_group($this->id_compet(), 'W' . $i . $j) ? $this->get_match('W' . $i . $j)->get_match_playground() : '';
                $match_home_id = FootballMatchService::get_match_in_group($this->id_compet(), 'W' . $i . $j) ? $this->get_match('W' . $i . $j)->get_match_home_id() : 0;
                $match_home_score = FootballMatchService::get_match_in_group($this->id_compet(), 'W' . $i . $j) ? $this->get_match('W' . $i . $j)->get_match_home_score() : '';
                $match_home_pen = FootballMatchService::get_match_in_group($this->id_compet(), 'W' . $i . $j) ? $this->get_match('W' . $i . $j)->get_match_home_pen() : '';
                $match_away_pen = FootballMatchService::get_match_in_group($this->id_compet(), 'W' . $i . $j) ? $this->get_match('W' . $i . $j)->get_match_away_pen() : '';
                $match_away_score = FootballMatchService::get_match_in_group($this->id_compet(), 'W' . $i . $j) ? $this->get_match('W' . $i . $j)->get_match_away_score() : '';
                $match_away_id = FootballMatchService::get_match_in_group($this->id_compet(), 'W' . $i . $j) ? $this->get_match('W' . $i . $j)->get_match_away_id() : 0;

                $winner_fieldset->add_field(new FormFieldFree('w_round_match_number_' . $i . $j, '', $match_number,
                    array('class' => 'match-select free-select small text-italic W' . $this->lang['football.round.' . $i . ''] . $j)
                ));
                $winner_fieldset->add_field(new FormFieldDateTime('w_round_match_date_' . $i . $j, '', $match_date,
                    array('class' => 'match-select date-select')
                ));
                if(FootballParamsService::get_params($this->id_compet())->get_display_playgrounds())
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
                $winner_fieldset->add_field(new FormFieldTextEditor('w_round_home_pen_' . $i . $j, '', $match_home_pen,
                    array('class' => 'home-team match-select home-pen', 'pattern' => '[0-9]*')
                ));
                $winner_fieldset->add_field(new FormFieldTextEditor('w_round_away_pen_' . $i . $j, '', $match_away_pen,
                    array('class' => 'away-team match-select away-pen', 'pattern' => '[0-9]*')
                ));
                $winner_fieldset->add_field(new FormFieldTextEditor('w_round_away_score_' . $i . $j, '', $match_away_score,
                    array('class' => 'away-team match-select away-score', 'pattern' => '[0-9]*')
                ));
                $winner_fieldset->add_field(new FormFieldSimpleSelectChoice('w_round_away_team_' . $i . $j, '', $match_away_id,
                    $this->get_teams_list(),
                    array('class' => 'away-team match-select away-bracket')
                ));
            }
        }

        if (CSSCacheConfig::load()->is_enabled())
            $winner_fieldset->add_field(new FormFieldFree('script', '', '<script src="' . PATH_TO_ROOT . '/templates/js/finals.matches.' . FootballTeamService::get_compet_teams_number($this->id_compet()) . '.' . $teams_per_group . '.min.js"></script>'));
        else
            $winner_fieldset->add_field(new FormFieldFree('script', '', '<script src="' . PATH_TO_ROOT . '/templates/js/finals.matches.' . FootballTeamService::get_compet_teams_number($this->id_compet()) . '.' . $teams_per_group . '.js"></script>'));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function save()
	{
        $teams_number = FootballTeamService::get_compet_teams_number($this->id_compet());
        $teams_per_group = $this->get_params()->get_teams_per_group();
        $groups_number = (int)($teams_number / $teams_per_group);
        $matches_number = $teams_per_group * ($teams_per_group - 1) / 2;

        for ($i = 1; $i <= $groups_number; $i++)
        {
            for ($j = 1; $j <= $matches_number; $j++)
            {
                $match = $this->get_match('G' . $i . $j);
                $match->set_match_compet_id($this->id_compet());
                $match->set_match_number('G' . $i . $j);
                $match->set_match_date($this->form->get_value('group_match_date_' . $i . $j));
                if(FootballParamsService::get_params($this->id_compet())->get_display_playgrounds())
                    $match->set_match_playground($this->form->get_value('group_match_playground_' . $i . $j));
                $match->set_match_home_id((int)$this->form->get_value('group_home_team_' . $i . $j)->get_raw_value());
                $match->set_match_home_score($this->form->get_value('group_home_score_' . $i . $j));
                $match->set_match_away_score($this->form->get_value('group_away_score_' . $i . $j));
                $match->set_match_away_id((int)$this->form->get_value('group_away_team_' . $i . $j)->get_raw_value());

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

        $teams_number = FootballTeamService::get_compet_teams_number($this->id_compet());
        $rounds_number = FootballParamsService::get_params($this->id_compet())->get_rounds_number() ? FootballParamsService::get_params($this->id_compet())->get_rounds_number() : (int)log($teams_number / 2, 2);

        // looser bracket
        if(FootballParamsService::get_params($this->id_compet())->get_looser_bracket())
        {
            for ($i = $rounds_number; $i >= 1; $i--)
            {
                $matches_number = FootballParamsService::get_params($this->id_compet())->get_all_places() ? $teams_number / 4 : $this->round_matches_number($i);

                for($j = 1; $j <= $matches_number; $j++)
                {
                    $match = $this->get_match('L' . $i . $j);
                    $match->set_match_compet_id($this->id_compet());
                    $match->set_match_number('L' . $i . $j);
                    $match->set_match_date($this->form->get_value('l_round_match_date_' . $i . $j));
                    if(FootballParamsService::get_params($this->id_compet())->get_display_playgrounds())
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
        }

        // Winner bracket
        for ($i = $rounds_number; $i >= 1; $i--)
        {
            $matches_number = FootballParamsService::get_params($this->id_compet())->get_all_places() ? $teams_number / 4 : $this->round_matches_number($i);

            for($j = 1; $j <= $matches_number; $j++)
            {
                $match = $this->get_match('W' . $i . $j);
                $match->set_match_compet_id($this->id_compet());
                $match->set_match_number('W' . $i . $j);
                $match->set_match_date($this->form->get_value('w_round_match_date_' . $i . $j));
                if(FootballParamsService::get_params($this->id_compet())->get_display_playgrounds())
                    $match->set_match_playground($this->form->get_value('w_round_match_playground_' . $i . $j));
                $match->set_match_home_id((int)$this->form->get_value('w_round_home_team_' . $i . $j)->get_raw_value());
                $match->set_match_home_score($this->form->get_value('w_round_home_score_' . $i . $j));
                $match->set_match_home_pen($this->form->get_value('w_round_home_pen_' . $i . $j));
                $match->set_match_away_pen($this->form->get_value('w_round_away_pen_' . $i . $j));
                $match->set_match_away_score($this->form->get_value('w_round_away_score_' . $i . $j));
                $match->set_match_away_id((int)$this->form->get_value('w_round_away_team_' . $i . $j)->get_raw_value());

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

		FootballCompetService::clear_cache();
	}

    private function round_matches_number($round)
    {
        $array = [1 => 1, 2 => 2, 3 => 4, 4 => 8, 5 => 16, 6 => 32, 7 => 64];
        if (array_key_exists($round, $array)) {
            return $array[$round];
        } else {
            return null; // ou une autre valeur par défaut si la clé n'existe pas
        }
    }

	private function get_match($group)
	{
        $compet_id = $this->id_compet();
        $id = FootballMatchService::get_match_in_group($compet_id, $group) ? FootballMatchService::get_match_in_group($compet_id, $group)->get_id_match() : null;

        if($id !== null)
            try {
                $this->match = FootballMatchService::get_match($id, $group);
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
		$id = AppContext::get_request()->get_getint('id', 0);
		try {
            $this->compet = FootballCompetService::get_compet($id);
        } catch (RowNotFoundException $e) {
            $error_controller = PHPBoostErrors::unexisting_page();
            DispatchManager::redirect($error_controller);
        }
		return $this->compet;
	}

    private function id_compet()
    {
        return $this->get_compet()->get_id_compet();
    }

    private function get_group_teams_list($group)
    {
        $teams_list = [];
        foreach (FootballTeamService::get_teams($this->id_compet()) as $team)
        {
            $team_group = $team['team_group'];
            $team_group = $team_group ? TextHelper::substr($team_group, 0, 1) : '';
            if ($team_group == $group)
                $teams_list[] = $team;
        }
        $options = array();

        $options[] = new FormFieldSelectChoiceOption('', 0);
		foreach($teams_list as $team)
		{
			$options[] = new FormFieldSelectChoiceOption($team['team_club_name'], $team['id_team']);
		}

		return $options;
    }

    private function get_teams_list()
    {
        $options = [];
        $options[] = new FormFieldSelectChoiceOption('', 0);
        foreach (FootballTeamService::get_teams($this->id_compet()) as $team)
        {
			$options[] = new FormFieldSelectChoiceOption($team['team_club_name'], $team['id_team']);
        }

		return $options;
    }

    private function get_params()
	{
        $id = AppContext::get_request()->get_getint('id', 0);
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

	private function redirect()
	{
		AppContext::get_response()->redirect(FootballUrlBuilder::matches($this->id_compet()));
	}

	protected function get_template_string_content()
	{
		return '
            # INCLUDE MESSAGE_HELPE #
            # INCLUDE MENU #
            # INCLUDE CONTENT #
        ';
	}

	private function check_authorizations()
	{
		// $compet = $this->get_compet();

		// if ($compet->get_id_compet() === null)
		// {
		// 	if (!$compet->is_authorized_to_manage())
		// 	{
		// 		$error_controller = PHPBoostErrors::user_not_authorized();
		// 		DispatchManager::redirect($error_controller);
		// 	}
		// }
		// else
		// {
		// 	if (!$compet->is_authorized_to_manage())
		// 	{
		// 		$error_controller = PHPBoostErrors::user_not_authorized();
		// 		DispatchManager::redirect($error_controller);
		// 	}
		// }
		// if (AppContext::get_current_user()->is_readonly())
		// {
		// 	$controller = PHPBoostErrors::user_in_read_only();
		// 	DispatchManager::redirect($controller);
		// }
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
        $graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::matches($compet->get_id_compet()));

        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($compet->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), FootballUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        $category = $compet->get_category();
        $breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $compet->get_id_compet(), $compet->get_compet_slug()));
        $breadcrumb->add($this->lang['football.matches.management'], FootballUrlBuilder::matches($compet->get_id_compet()));

		return $response;
	}
}
?>
