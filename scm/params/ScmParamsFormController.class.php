<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 08 10
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmParamsFormController extends DefaultModuleController
{
	private $params;
	private $is_new_params;
	private $event;
	private $division;
	private $is_championship;
	private $is_cup;
	private $is_tournament;
	private $event_type;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		$this->check_authorizations();

		$this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
            if ($this->is_tournament) {
                $this->form->get_field_by_id('hat_days')->set_hidden(!$this->get_params()->get_hat_ranking());
                $this->form->get_field_by_id('brackets_number')->set_hidden(!$this->get_params()->get_looser_bracket());
                $this->form->get_field_by_id('rounds_number')->set_hidden($this->get_params()->get_finals_type() == ScmParams::FINALS_RANKING);
            }
            if (!$this->is_championship) {
                $this->form->get_field_by_id('third_place')->set_hidden($this->get_params()->get_looser_bracket());
            }
            $event_name = $this->get_event()->get_event_name();
            $this->view->put('MESSAGE_HELPER', MessageHelper::display(StringVars::replace_vars($this->lang['scm.warning.params.update'], ['event_name' => $event_name]), MessageHelper::SUCCESS, 4));
        }

		$this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),
            'CONTENT' => $this->form->display(),
            'HAS_GAMES_WARNING' => ScmGameService::has_games($this->event_id()) ? MessageHelper::display($this->lang['scm.warning.params.has.games'], MessageHelper::NOTICE) : MessageHelper::display('', '')
        ]);

		return $this->generate_response($this->view);
	}

	private function init()
	{
		$this->division = ScmDivisionCache::load()->get_division($this->get_event()->get_division_id());
		$this->is_championship = $this->division['event_type'] == ScmDivision::CHAMPIONSHIP;
		$this->is_cup = $this->division['event_type'] == ScmDivision::CUP;
		$this->is_tournament = $this->division['event_type'] == ScmDivision::TOURNAMENT;
	}

	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);
        $form->set_css_class('params-form cell-flex cell-columns-2');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['scm.params.management'] . '</div>');

		if ($this->is_tournament)
		{
            $tournament_fieldset = new FormFieldsetHTML('tournament', $this->lang['scm.params.tournament']);
            $form->add_fieldset($tournament_fieldset);

            $warning_class = ScmGameService::has_games($this->event_id()) ? 'bgc warning' : '';

            $tournament_fieldset->add_field(new FormFieldNumberEditor('groups_number', $this->lang['scm.groups.number'], $this->get_params()->get_groups_number(),
                ['class' => $warning_class, 'min' => 1, 'required' => true]
            ));
            $tournament_fieldset->add_field(new FormFieldNumberEditor('teams_per_group', $this->lang['scm.teams.per.group'], $this->get_params()->get_teams_per_group(),
                ['class' => $warning_class, 'min' => 1, 'required' => true]
            ));
            $tournament_fieldset->add_field(new FormFieldCheckbox('hat_ranking', $this->lang['scm.hat.ranking'], $this->get_params()->get_hat_ranking(),
                [
                    'class' => $warning_class,
                    'description' => $this->lang['scm.hat.ranking.clue'],
                    'events' => ['click' => '
                        if (HTMLForms.getField("hat_ranking").getValue()) {
                            HTMLForms.getField("hat_days").enable();
                            HTMLForms.getField("fill_games").disable();
                        } else {
                            HTMLForms.getField("hat_days").disable();
                            HTMLForms.getField("fill_games").enable();
                        }
                    ']
                ]
            ));
            $tournament_fieldset->add_field(new FormFieldNumberEditor('hat_days', $this->lang['scm.hat.days'], $this->get_params()->get_hat_days(),
                [
                    'description' => $this->lang['scm.hat.days.clue'],
                    'class' => $warning_class, 'min' => 1, 'required' => true,
                    'hidden' => !$this->get_params()->get_hat_ranking()
                ]
            ));
            $tournament_fieldset->add_field(new FormFieldCheckbox('fill_games', $this->lang['scm.fill.games'], $this->get_params()->get_fill_games(),
                [
                    'class' => $warning_class,
                    'description' => $this->lang['scm.fill.games.clue'],
                    'hidden' => $this->get_params()->get_hat_ranking()
                ]
            ));
            $tournament_fieldset->add_field(new FormFieldCheckbox('looser_bracket', $this->lang['scm.looser.brackets'], $this->get_params()->get_looser_bracket(),
                [
                    'class' => $warning_class,
                    'description' => $this->lang['scm.looser.brackets.clue'],
                    'events' => ['click' => '
                        if (HTMLForms.getField("looser_bracket").getValue()) {
                            HTMLForms.getField("third_place").disable();
                            HTMLForms.getField("brackets_number").enable();
                        } else {
                            HTMLForms.getField("third_place").enable();
                            HTMLForms.getField("brackets_number").disable();
                        }
                    ']
                ]
            ));
            $tournament_fieldset->add_field(new FormFieldNumberEditor('brackets_number', $this->lang['scm.brackets.number'], $this->get_params()->get_looser_bracket() ? $this->get_params()->get_brackets_number() : '',
                [
                    'class' => $warning_class,
                    'min' => 1, 'required' => true,
                    'hidden' => !$this->get_params()->get_looser_bracket()
                ]
            ));
            $tournament_fieldset->add_field(new FormFieldCheckbox('display_playgrounds', $this->lang['scm.display.playgrounds'], $this->get_params()->get_display_playgrounds()));
        }

		if ($this->is_cup || $this->is_tournament)
		{
			$bracket_fieldset = new FormFieldsetHTML('bracket', $this->lang['scm.params.bracket']);
            $form->add_fieldset($bracket_fieldset);

            if ($this->is_tournament) {
                $final_type_options = [
                    new FormFieldSelectChoiceOption($this->lang['scm.finals.round'], ScmParams::FINALS_ROUND),
                    new FormFieldSelectChoiceOption($this->lang['scm.finals.ranking'], ScmParams::FINALS_RANKING)
                ];
            }
            else
                $final_type_options = [new FormFieldSelectChoiceOption($this->lang['scm.finals.round'], ScmParams::FINALS_ROUND)];
            $bracket_fieldset->add_field(new FormFieldSimpleSelectChoice('finals_type', $this->lang['scm.finals.type'], $this->get_params()->get_finals_type(),
                $final_type_options,
                [
                    'class' => $warning_class,
                    'events' => ['click' => '
                        if (HTMLForms.getField("finals_type").getValue() == "' . ScmParams::FINALS_ROUND . '") {
                            HTMLForms.getField("rounds_number").enable();
                            HTMLForms.getField("has_overtime").enable();
                        } else {
                            HTMLForms.getField("rounds_number").disable();
                            HTMLForms.getField("has_overtime").disable();
                        }
                    ']
                ]
            ));

            $bracket_fieldset->add_field(new FormFieldNumberEditor('rounds_number', $this->lang['scm.rounds.number'], $this->get_params()->get_rounds_number(),
                [
                    'description' => $this->lang['scm.rounds.number.clue'],
                    'class' => $warning_class, 'min' => 0, 'required' => true,
                    'hidden' => $this->get_params()->get_finals_type() == ScmParams::FINALS_RANKING
                ]
            ));

            $bracket_fieldset->add_field(new FormFieldCheckbox('has_overtime', $this->lang['scm.has.overtime'], $this->get_params()->get_has_overtime(),
				[
                    'hidden' => $this->get_params()->get_finals_type() == ScmParams::FINALS_RANKING,
                    'events' => ['click' => '
                        if (HTMLForms.getField("has_overtime").getValue()) {
                            HTMLForms.getField("overtime_duration").enable();
                        } else {
                            HTMLForms.getField("overtime_duration").disable();
                        }
                    ']
                ]
			));
			$bracket_fieldset->add_field(new FormFieldNumberEditor('overtime_duration', $this->lang['scm.overtime.duration'], $this->get_params()->get_overtime_duration(),
				[
                    'min' => 0, 'required' => true,
                    'description' => $this->lang['scm.minutes.clue'],
                    'hidden' => !$this->get_params()->get_has_overtime()
                ]
			));

            $bracket_fieldset->add_field(new FormFieldCheckbox('draw_games', $this->lang['scm.draw.games'], $this->get_params()->get_draw_games()));

            $bracket_fieldset->add_field(new FormFieldCheckbox('third_place', $this->lang['scm.third.place'], $this->get_params()->get_third_place(),
                [
                    'class' => $warning_class,
                    'hidden' => $this->is_tournament && $this->get_params()->get_looser_bracket()
                ]
            ));
			// $bracket_fieldset->add_field(new FormFieldCheckbox('golden_goal', $this->lang['scm.golden.goal'], $this->get_params()->get_golden_goal()));
			// $bracket_fieldset->add_field(new FormFieldCheckbox('silver_goal', $this->lang['scm.silver.goal'], $this->get_params()->get_silver_goal()));
		}

		if ($this->is_championship || $this->is_tournament)
		{
			$ranking_fieldset = new FormFieldsetHTML('ranking', $this->lang['scm.params.ranking']);
            $form->add_fieldset($ranking_fieldset);

            $ranking_fieldset->add_field(new FormFieldNumberEditor('victory_points', $this->lang['scm.victory.points'], $this->get_params()->get_victory_points(), ['min' => 0]));
			$ranking_fieldset->add_field(new FormFieldNumberEditor('draw_points', $this->lang['scm.draw.points'], $this->get_params()->get_draw_points(), ['min' => 0]));
			$ranking_fieldset->add_field(new FormFieldNumberEditor('loss_points', $this->lang['scm.loss.points'], $this->get_params()->get_loss_points(), ['min' => 0]));

			$ranking_fieldset->add_field(new FormFieldNumberEditor('promotion', $this->lang['scm.promotion'], $this->get_params()->get_promotion(), ['min' => 0]));
			$ranking_fieldset->add_field(new FormFieldNumberEditor('playoff_prom', $this->lang['scm.playoff.prom'], $this->get_params()->get_playoff_prom(), ['min' => 0]));
			$ranking_fieldset->add_field(new FormFieldNumberEditor('playoff_releg', $this->lang['scm.playoff.releg'], $this->get_params()->get_playoff_releg(), ['min' => 0]));
			$ranking_fieldset->add_field(new FormFieldNumberEditor('relegation', $this->lang['scm.relegation'], $this->get_params()->get_relegation(), ['min' => 0]));
			$ranking_fieldset->add_field(new FormFieldNumberEditor('fairplay_yellow', $this->lang['scm.fairplay.yellow'], $this->get_params()->get_fairplay_yellow(), ['min' => 0]));
			$ranking_fieldset->add_field(new FormFieldNumberEditor('fairplay_red', $this->lang['scm.fairplay.red'], $this->get_params()->get_fairplay_red(), ['min' => 0]));

			$ranking_type_fieldset = new FormFieldsetHTML('ranking', $this->lang['scm.ranking.type']);
			$ranking_type_fieldset->set_description($this->lang['scm.ranking.type.clue']);
            $form->add_fieldset($ranking_type_fieldset);

			$ranking_type_fieldset->add_field(new FormFieldSimpleSelectChoice('ranking_crit_1', $this->lang['scm.ranking.criterion'] . 1, $this->get_params()->get_ranking_crit_1(), $this->ranking_criterion_list()));
			$ranking_type_fieldset->add_field(new FormFieldSimpleSelectChoice('ranking_crit_2', $this->lang['scm.ranking.criterion'] . 2, $this->get_params()->get_ranking_crit_2(), $this->ranking_criterion_list()));
			$ranking_type_fieldset->add_field(new FormFieldSimpleSelectChoice('ranking_crit_3', $this->lang['scm.ranking.criterion'] . 3, $this->get_params()->get_ranking_crit_3(), $this->ranking_criterion_list()));
			$ranking_type_fieldset->add_field(new FormFieldSimpleSelectChoice('ranking_crit_4', $this->lang['scm.ranking.criterion'] . 4, $this->get_params()->get_ranking_crit_4(), $this->ranking_criterion_list()));
			$ranking_type_fieldset->add_field(new FormFieldSimpleSelectChoice('ranking_crit_5', $this->lang['scm.ranking.criterion'] . 5, $this->get_params()->get_ranking_crit_5(), $this->ranking_criterion_list()));
			$ranking_type_fieldset->add_field(new FormFieldSimpleSelectChoice('ranking_crit_6', $this->lang['scm.ranking.criterion'] . 6, $this->get_params()->get_ranking_crit_6(), $this->ranking_criterion_list()));
			$ranking_type_fieldset->add_field(new FormFieldSimpleSelectChoice('ranking_crit_7', $this->lang['scm.ranking.criterion'] . 7, $this->get_params()->get_ranking_crit_7(), $this->ranking_criterion_list()));
			$ranking_type_fieldset->add_field(new FormFieldSimpleSelectChoice('ranking_crit_8', $this->lang['scm.ranking.criterion'] . 8, $this->get_params()->get_ranking_crit_8(), $this->ranking_criterion_list()));
			$ranking_type_fieldset->add_field(new FormFieldSimpleSelectChoice('ranking_crit_9', $this->lang['scm.ranking.criterion'] . 9, $this->get_params()->get_ranking_crit_9(), $this->ranking_criterion_list()));
			$ranking_type_fieldset->add_field(new FormFieldSimpleSelectChoice('ranking_crit_10', $this->lang['scm.ranking.criterion'] . 10, $this->get_params()->get_ranking_crit_10(), $this->ranking_criterion_list()));
		}

		if ($this->is_championship)
		{
			$penalties_fieldset = new FormFieldsetHTML('penalties', $this->lang['scm.params.penalties']);
            $form->add_fieldset($penalties_fieldset);

            $teams = ScmTeamService::get_teams($this->event_id());

            foreach ($teams as $team)
            {
                $penalties_fieldset->add_field(new FormFieldNumberEditor('penalty_' . $team['id_team'], $team['club_name'], $team['team_penalty'],
                    ['max' => 0]
                ));
                $penalties_fieldset->add_field(new FormFieldSimpleSelectChoice('status_' . $team['id_team'], '<span class="small text-italic">' . $this->lang['scm.params.status'] . '</span>', $team['team_status'],
                    [
                        new FormFieldSelectChoiceOption($this->lang['scm.params.status.play'], ''),
                        new FormFieldSelectChoiceOption($this->lang['scm.params.status.forfeit'], ScmParams::FORFEIT),
                        new FormFieldSelectChoiceOption($this->lang['scm.params.status.exempt'], ScmParams::EXEMPT)
                    ]
                ));
                $penalties_fieldset->add_field(new FormFieldSpacer('team_separator_' . $team['id_team'], '<hr />'));
            }
        }

		$option_fieldset = new FormFieldsetHTML('options', $this->lang['scm.params.options']);
		$form->add_fieldset($option_fieldset);

        $option_fieldset->add_field(new FormFieldNumberEditor('game_duration', $this->lang['scm.game.duration'], $this->get_params()->get_game_duration(),
			[
                'required' => true,
                'description' => $this->lang['scm.game.duration.clue'], 'min' => 0
            ]
		));

        $option_fieldset->add_field(new FormFieldSimpleSelectChoice('favorite_team_id', $this->lang['scm.favorite.team'], $this->get_params()->get_favorite_team_id(), $this->fav_teams_list(),
            ['description' => $this->lang['scm.favorite.clue']]
        ));

		$option_fieldset->add_field(new FormFieldSimpleSelectChoice('bonus', $this->lang['scm.bonus.param'], $this->get_params()->get_bonus(),
            [
                new FormFieldSelectChoiceOption($this->lang['common.none'], ''),
                new FormFieldSelectChoiceOption($this->lang['scm.bonus.single'], ScmParams::BONUS_SINGLE),
                new FormFieldSelectChoiceOption($this->lang['scm.bonus.double'], ScmParams::BONUS_DOUBLE)
            ]
        ));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function save()
	{
		$params = $this->get_params();
        $params->set_params_event_id($this->event_id());

        if ($this->is_tournament)
        {
            $params->set_groups_number($this->form->get_value('groups_number'));
            $params->set_teams_per_group($this->form->get_value('teams_per_group'));
            $params->set_hat_ranking($this->form->get_value('hat_ranking'));
            $params->set_hat_days($this->form->get_value('hat_days'));
            $params->set_fill_games($this->form->get_value('fill_games'));
            $params->set_looser_bracket($this->form->get_value('looser_bracket'));
            if ($this->form->get_value('looser_bracket'))
                $params->set_brackets_number($this->form->get_value('brackets_number'));
            else 
                $params->set_brackets_number(0);
            $params->set_display_playgrounds($this->form->get_value('display_playgrounds'));
        }

        if ($this->is_cup || $this->is_tournament)
        {
            if ($this->form->get_value('looser_bracket'))
                $params->set_third_place(0);
            else
                $params->set_third_place($this->form->get_value('third_place'));
            $params->set_finals_type($this->form->get_value('finals_type')->get_raw_value());
            if ($params->get_finals_type() == ScmParams::FINALS_ROUND)
                $params->set_rounds_number($this->form->get_value('rounds_number'));
            $params->set_draw_games($this->form->get_value('draw_games'));
            $params->set_has_overtime($this->form->get_value('has_overtime'));
            $params->set_overtime_duration($this->form->get_value('overtime_duration'));
        }

        if ($this->is_championship || $this->is_tournament)
        {
            $params->set_victory_points($this->form->get_value('victory_points'));
            $params->set_draw_points($this->form->get_value('draw_points'));
            $params->set_loss_points($this->form->get_value('loss_points'));

            $params->set_promotion($this->form->get_value('promotion'));
            $params->set_playoff_prom($this->form->get_value('playoff_prom'));
            $params->set_playoff_releg($this->form->get_value('playoff_releg'));
            $params->set_relegation($this->form->get_value('relegation'));
            $params->set_fairplay_yellow($this->form->get_value('fairplay_yellow'));
            $params->set_fairplay_red($this->form->get_value('fairplay_red'));

            $params->set_ranking_crit_1($this->form->get_value('ranking_crit_1')->get_raw_value());
            $params->set_ranking_crit_2($this->form->get_value('ranking_crit_2')->get_raw_value());
            $params->set_ranking_crit_3($this->form->get_value('ranking_crit_3')->get_raw_value());
            $params->set_ranking_crit_4($this->form->get_value('ranking_crit_4')->get_raw_value());
            $params->set_ranking_crit_5($this->form->get_value('ranking_crit_5')->get_raw_value());
            $params->set_ranking_crit_6($this->form->get_value('ranking_crit_6')->get_raw_value());
            $params->set_ranking_crit_7($this->form->get_value('ranking_crit_7')->get_raw_value());
            $params->set_ranking_crit_8($this->form->get_value('ranking_crit_8')->get_raw_value());
            $params->set_ranking_crit_9($this->form->get_value('ranking_crit_9')->get_raw_value());
            $params->set_ranking_crit_10($this->form->get_value('ranking_crit_10')->get_raw_value());
        }

        if ($this->is_championship)
        {
            $teams = ScmTeamService::get_teams($this->event_id());
            foreach ($teams as $team)
            {
                ScmTeamService::update_team_penalty($team['id_team'], $this->form->get_value('penalty_' . $team['id_team']));
                ScmTeamService::update_team_status($team['id_team'], $this->form->get_value('status_' . $team['id_team'])->get_raw_value());
            }
        }

        $params->set_game_duration($this->form->get_value('game_duration'));
        $params->set_bonus($this->form->get_value('bonus')->get_raw_value());
        $params->set_favorite_team_id($this->form->get_value('favorite_team_id')->get_raw_value());

		ScmParamsService::update_params($params);

		ScmEventService::clear_cache();
	}

	private function ranking_criterion_list()
	{
		$options = [];

        $options[] = new FormFieldSelectChoiceOption('', '');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.general.points'], 'points');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.particular.points'], 'points_prtl');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.general.goal.average'], 'goal_average');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.particular.goal.average'], 'goal_average_prtl');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.general.goals.for'], 'goals_for');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.particular.goals.for'], 'goals_for_prtl');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.away.goals.for'], 'goals_for_away');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.general.goals.against'], 'goals_against');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.particular.goals.against'], 'goals_against_prtl');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.win'], 'win');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.win.away'], 'win_away');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.general.fairplay'], 'fairplay');
        $options[] = new FormFieldSelectChoiceOption($this->lang['scm.ranking.particular.fairplay'], 'fairplay_prtl');

		return $options;
	}

	private function fav_teams_list()
	{
		$options = [];

        $options[] = new FormFieldSelectChoiceOption('', 0);
		foreach(ScmTeamService::get_teams($this->event_id()) as $team)
		{
            $options[] = new FormFieldSelectChoiceOption($team['club_name'], $team['id_team']);
		}

		return $options;
	}

	private function get_params()
	{
		if ($this->params === null)
		{
			$id = AppContext::get_request()->get_getint('event_id', 0);
			if (!empty($id))
			{
				try {
					$this->params = ScmParamsService::get_params($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_params = true;
				$this->params = new ScmParams();
				$this->params->init_default_properties();
			}
		}
		return $this->params;
	}

	private function get_event()
	{
		if ($this->event === null)
		{
			$id = AppContext::get_request()->get_getint('event_id', 0);
            try {
                $this->event = ScmEventService::get_event($id);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
		}
		return $this->event;
	}

    private function event_id()
    {
        return $this->get_event()->get_id();
    }

	private function check_authorizations()
	{
		if (!$this->get_event()->is_authorized_to_manage_events())
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

	protected function get_template_string_content()
	{
		return '
            # INCLUDE MESSAGE_HELPER #
            # INCLUDE MENU #
            # INCLUDE HAS_GAMES_WARNING #
            # INCLUDE CONTENT #
        ';
	}

	private function generate_response(View $view)
	{
		$event = $this->get_event();
		$category = $event->get_category();
		$params = $this->get_params();

		$location_id = $params->get_params_event_id() ? 'scm-params-'. $params->get_params_event_id() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		if (!AppContext::get_session()->location_id_already_exists($location_id))
			$graphical_environment->set_location_id($location_id);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->lang['scm.params.management'], $event->get_event_name() . ($category->get_id() != Category::ROOT_CATEGORY ? ' - ' . $category->get_name() : '') . ' - ' . $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
		$graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.params.management']);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());
		$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($event->get_id_category(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
        if ($event->get_is_sub())
            $breadcrumb->add(ScmEventService::get_master_name($event->get_id()), ScmEventService::get_master_url($event->get_id()));
		$breadcrumb->add($event->get_is_sub() ? ScmDivisionService::get_division($event->get_division_id())->get_division_name() : $event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));
		$breadcrumb->add($this->lang['scm.params.management'], ScmUrlBuilder::edit_params($params->get_params_event_id(), $event->get_event_slug()));


		return $response;
	}
}
?>
