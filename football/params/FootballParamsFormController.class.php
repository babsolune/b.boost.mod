<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 27
 * @since       PHPBoost 6.0 - 2022 12 27
*/

class FootballParamsFormController extends DefaultModuleController
{
	private $params;
	private $is_new_params;
	private $compet;
	private $division;
	private $is_championship;
	private $is_cup;
	private $is_tournament;
	private $compet_type;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		$this->check_authorizations();

		$this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
            $compet_name = $this->get_compet()->get_compet_name();
            $this->view->put('MESSAGE_HELPER', MessageHelper::display(StringVars::replace_vars($this->lang['football.warning.params.update'], array('compet_name' => $compet_name)), MessageHelper::SUCCESS, 4));
        }

		$this->view->put_all(array(
            'MENU' => FootballMenuService::build_compet_menu($this->compet_id()),
            'CONTENT' => $this->form->display()
        ));

		return $this->generate_response($this->view);
	}

	private function init()
	{
		$this->division = FootballDivisionCache::load()->get_division($this->get_compet()->get_compet_division_id());
		$this->is_championship = $this->division['division_compet_type'] == FootballDivision::CHAMPIONSHIP;
		$this->is_cup = $this->division['division_compet_type'] == FootballDivision::CUP;
		$this->is_tournament = $this->division['division_compet_type'] == FootballDivision::TOURNAMENT;
	}

	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);
        $form->set_css_class('params-form cell-flex cell-columns-2');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['football.params'] . '</div>');

		if ($this->is_tournament)
		{
            $tournament_fieldset = new FormFieldsetHTML('tournament', $this->lang['football.params.tournament']);
            $form->add_fieldset($tournament_fieldset);

            $tournament_fieldset->add_field(new FormFieldNumberEditor('teams_per_group', $this->lang['football.teams.per.group'], $this->get_params()->get_teams_per_group(),
                array('min' => 0, 'required' => true)
            ));
            $tournament_fieldset->add_field(new FormFieldCheckbox('fill_matches', $this->lang['football.fill.matches'], $this->get_params()->get_fill_matches(),
                array('description' => '<span aria-label="' . $this->lang['football.fill.matches.clue'] . '"><i class="far fa-circle-question"></i></span>')
            ));
            $tournament_fieldset->add_field(new FormFieldCheckbox('looser_bracket', $this->lang['football.looser.bracket'], $this->get_params()->get_looser_bracket(),
                array('events' => array('click' => '
                    if (HTMLForms.getField("looser_bracket").getValue()) {
                        HTMLForms.getField("third_place").disable();
                    } else {
                        HTMLForms.getField("third_place").enable();
                    }
                '))
            ));
            $tournament_fieldset->add_field(new FormFieldCheckbox('display_playgrounds', $this->lang['football.display.playgrounds'], $this->get_params()->get_display_playgrounds()));
        }

		if ($this->is_cup || $this->is_tournament)
		{
			$bracket_fieldset = new FormFieldsetHTML('bracket', $this->lang['football.params.bracket']);
            $form->add_fieldset($bracket_fieldset);

            $bracket_fieldset->add_field(new FormFieldNumberEditor('rounds_number', $this->lang['football.rounds.number'], $this->get_params()->get_rounds_number(),
                array(
                    'description' => '<span aria-label="' . $this->lang['football.rounds.number.clue'] . '"><i class="far fa-circle-question"></i></span>', 
                    'min' => 0, 'max' => 7, 'required' => true
                )
            ));
			$bracket_fieldset->add_field(new FormFieldCheckbox('has_overtime', $this->lang['football.has.overtime'], $this->get_params()->get_has_overtime(),
				array('events' => array('click' => '
                    if (HTMLForms.getField("has_overtime").getValue()) {
                        HTMLForms.getField("overtime_duration").enable();
                    } else {
                        HTMLForms.getField("overtime_duration").disable();
                    }
                '))
			));
			$bracket_fieldset->add_field(new FormFieldNumberEditor('overtime_duration', $this->lang['football.overtime.duration'], $this->get_params()->get_overtime_duration(),
				array(
                    'min' => 0,
                    'description' => $this->lang['football.minutes.clue'],
                    'hidden' => !$this->get_params()->get_has_overtime()
                )
			));
			$bracket_fieldset->add_field(new FormFieldCheckbox('golden_goal', $this->lang['football.golden.goal'], $this->get_params()->get_golden_goal()));
			$bracket_fieldset->add_field(new FormFieldCheckbox('silver_goal', $this->lang['football.silver.goal'], $this->get_params()->get_silver_goal()));
			$bracket_fieldset->add_field(new FormFieldCheckbox('third_place', $this->lang['football.third.place'], $this->get_params()->get_third_place(),
                array('hidden' => $this->is_tournament && $this->get_params()->get_looser_bracket())
            ));
		}

		if ($this->is_championship || $this->is_tournament)
		{
			$ranking_fieldset = new FormFieldsetHTML('ranking', $this->lang['football.params.ranking']);
            $form->add_fieldset($ranking_fieldset);

            $ranking_fieldset->add_field(new FormFieldNumberEditor('victory_points', $this->lang['football.victory.points'], $this->get_params()->get_victory_points(), array('min' => 0)));
			$ranking_fieldset->add_field(new FormFieldNumberEditor('draw_points', $this->lang['football.draw.points'], $this->get_params()->get_draw_points(), array('min' => 0)));
			$ranking_fieldset->add_field(new FormFieldNumberEditor('loss_points', $this->lang['football.loss.points'], $this->get_params()->get_loss_points(), array('min' => 0)));

			$ranking_fieldset->add_field(new FormFieldNumberEditor('promotion', $this->lang['football.promotion'], $this->get_params()->get_promotion(), array('min' => 0)));
			$ranking_fieldset->add_field(new FormFieldColorPicker('promotion_color', $this->lang['football.promotion.color'], $this->get_params()->get_promotion_color()));
			$ranking_fieldset->add_field(new FormFieldNumberEditor('play_off', $this->lang['football.play.off'], $this->get_params()->get_play_off(), array('min' => 0)));
			$ranking_fieldset->add_field(new FormFieldColorPicker('play_off_color', $this->lang['football.play.off.color'], $this->get_params()->get_play_off_color()));
			$ranking_fieldset->add_field(new FormFieldNumberEditor('relegation', $this->lang['football.relegation'], $this->get_params()->get_relegation(), array('min' => 0)));
			$ranking_fieldset->add_field(new FormFieldColorPicker('relegation_color', $this->lang['football.relegation.color'], $this->get_params()->get_relegation_color()));

			$ranking_fieldset->add_field(new FormFieldSimpleSelectChoice('ranking_type', $this->lang['football.ranking.type'], $this->get_params()->get_ranking_type(), $this->ranking_list()));
		}

		$option_fieldset = new FormFieldsetHTML('options', $this->lang['football.params.options']);
		$form->add_fieldset($option_fieldset);
		$option_fieldset->add_field(new FormFieldNumberEditor('match_duration', $this->lang['football.match.duration'], $this->get_params()->get_match_duration(),
			array('description' => $this->lang['football.minutes.clue'], 'min' => 0)
		));

		$option_fieldset->add_field(new FormFieldCheckbox('sets_mode', $this->lang['football.sets.mode'], $this->get_params()->get_sets_mode(),
			array(
				'events' => array('click' => '
					if (HTMLForms.getField("sets_mode").getValue()) {
						HTMLForms.getField("sets_number").enable();
					} else {
						HTMLForms.getField("sets_number").disable();
					}'
				)
			)
		));

		$option_fieldset->add_field(new FormFieldNumberEditor('sets_number', $this->lang['football.sets.number'], $this->get_params()->get_sets_number(),
			array('hidden' => !$this->get_params()->get_sets_mode(), 'min' => 0)
		));

		$option_fieldset->add_field(new FormFieldCheckbox('bonus', $this->lang['football.bonus'], $this->get_params()->get_sets_mode(),
			array('description' => $this->lang['football.bonus.clue'])
		));

		$option_fieldset->add_field(new FormFieldSimpleSelectChoice('favorite_team_id', $this->lang['football.favorite.team'], $this->get_params()->get_favorite_team_id(), $this->teams_list()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function save()
	{
		$params = $this->get_params();
        $params->set_params_compet_id($this->compet_id());

        if ($this->is_tournament)
        {
            $params->set_teams_per_group($this->form->get_value('teams_per_group'));
            $params->set_fill_matches($this->form->get_value('fill_matches'));
            $params->set_looser_bracket($this->form->get_value('looser_bracket'));
            $params->set_display_playgrounds($this->form->get_value('display_playgrounds'));
        }

        if ($this->is_cup || $this->is_tournament)
        {
            $params->set_third_place($this->form->get_value('third_place'));
            $params->set_rounds_number($this->form->get_value('rounds_number'));
            $params->set_has_overtime($this->form->get_value('has_overtime'));
            $params->set_overtime_duration($this->form->get_value('overtime_duration'));
        }

        if ($this->is_championship || $this->is_tournament)
        {
            $params->set_victory_points($this->form->get_value('victory_points'));
            $params->set_draw_points($this->form->get_value('draw_points'));
            $params->set_loss_points($this->form->get_value('loss_points'));

            $params->set_promotion($this->form->get_value('promotion'));
            $params->set_promotion_color($this->form->get_value('promotion_color'));
            $params->set_play_off($this->form->get_value('play_off'));
            $params->set_play_off_color($this->form->get_value('play_off_color'));
            $params->set_relegation($this->form->get_value('relegation'));
            $params->set_relegation_color($this->form->get_value('relegation_color'));
            $params->set_ranking_type($this->form->get_value('ranking_type'));
        }
        else
        {

        }

        $params->set_match_duration($this->form->get_value('match_duration'));
        $params->set_bonus($this->form->get_value('bonus'));
        $params->set_sets_mode($this->form->get_value('sets_mode'));
        $params->set_sets_number($this->form->get_value('sets_number'));
        $params->set_favorite_team_id($this->form->get_value('favorite_team_id')->get_raw_value());

		if ($this->is_new_params)
		{
			// $id = AppContext::get_request()->get_getint('id', 0);
            $id = FootballParamsService::add_params($params);
                $params->set_id_params($id);
        }
		else
		{
			FootballParamsService::update_params($params);
        }

		FootballCompetService::clear_cache();
	}

	private function ranking_list()
	{
		$options = array();
		// $cache = FootballSeasonCache::load();
		// $seasons_list = $cache->get_seasons();

		// $i = 1;
		// foreach($seasons_list as $season)
		// {
		// 	$options[] = new FormFieldSelectChoiceOption($season['season_name'], $season['id_season']);
		// 	$i++;
		// }

		return $options;
	}

	private function teams_list()
	{
		$options = array();

        $options[] = new FormFieldSelectChoiceOption('', 0);
		foreach(FootballTeamService::get_teams($this->compet_id()) as $team)
		{
			$options[] = new FormFieldSelectChoiceOption($team['team_club_name'], $team['id_team']);
		}

		return $options;
	}

	private function get_params()
	{
		if ($this->params === null)
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
			else
			{
				$this->is_new_params = true;
				$this->params = new FootballParams();
				$this->params->init_default_properties();
			}
		}
		return $this->params;
	}

	private function get_compet()
	{
		if ($this->compet === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
            try {
                $this->compet = FootballCompetService::get_compet($id);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
		}
		return $this->compet;
	}

    private function compet_id()
    {
        return $this->get_compet()->get_id_compet();
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

	private function redirect()
	{
		AppContext::get_response()->redirect(FootballUrlBuilder::params($this->compet_id()));
	}

	protected function get_template_string_content()
	{
		return '
            # INCLUDE MESSAGE_HELPER #
            # INCLUDE MENU #
            # INCLUDE CONTENT #
        ';
	}

	private function generate_response(View $view)
	{
		$compet = $this->get_compet();
		$category = $compet->get_category();
		$params = $this->get_params();

		$location_id = $params->get_params_compet_id() ? 'param-edit-'. $params->get_params_compet_id() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());

		if (!AppContext::get_session()->location_id_already_exists($location_id))
			$graphical_environment->set_location_id($location_id);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($compet->get_compet_name(), ($category->get_id() != Category::ROOT_CATEGORY ? $category->get_name() . ' - ' : '') . $this->lang['football.module.title']);
		// $graphical_environment->get_seo_meta_data()->set_description($compet->get_real_summary());
		$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::calendar($compet->get_id_compet()));

		$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($compet->get_id_category(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), FootballUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::calendar($compet->get_id_compet()));
		$breadcrumb->add($this->lang['football.params'], FootballUrlBuilder::params($params->get_params_compet_id()));


		return $response;
	}
}
?>
