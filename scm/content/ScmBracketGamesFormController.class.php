<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmBracketGamesFormController extends DefaultModuleController
{
    private $event;
    private $params;
    private $hat_ranking;
    private $game;
    private $teams_number;
    private $teams_per_group;
    private $return_games;

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
		$this->check_authorizations();

		$this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
            $this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['scm.warning.games.update'], MessageHelper::SUCCESS, 4));
		}

		$this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),
            'CONTENT' => $this->form->display(),
            'JS_DOC' => $this->teams_number ? ScmBracketService::get_bracket_js_games($this->event_id(), $this->teams_number, $this->teams_per_group) : MessageHelper::display('', '')
        ]);

		return $this->generate_response($this->view);
	}

    private function init()
    {
        $this->hat_ranking = $this->get_params()->get_hat_ranking();
        $this->teams_number = ScmTeamService::get_teams_number($this->event_id());
        $this->teams_per_group = $this->get_params()->get_teams_per_group();
        $this->return_games = ScmEventService::get_event_game_type($this->event_id()) == ScmDivision::RETURN_GAMES;
    }

	private function build_form()
	{
        $i = AppContext::get_request()->get_getint('round', 0);
		$form = new HTMLForm(__CLASS__);
        $form->set_css_class('floating-submit');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['scm.games.management'] . '</div>');

		$bracket_fieldset = new FormFieldsetHTML('looser_bracket', $this->lang['scm.games.brackets.stage']);
		$form->add_fieldset($bracket_fieldset);

        $rounds_number = $this->get_params()->get_rounds_number() ? $this->get_params()->get_rounds_number() : (int)log($this->teams_number / 2, 2);

        if($this->get_params()->get_looser_bracket())
        {
            $looser_fieldset = new FormFieldsetHTML('looser_bracket', $this->lang['scm.looser.bracket']);
            $form->add_fieldset($looser_fieldset);

            $looser_fieldset->add_field(new FormFieldSpacer('round_' . $i, $this->lang['scm.round.' . $i . ''],
                ['class' => 'form-spacer-big']
            ));
            $games_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 4 : $this->round_games_number($i);

            if ($this->return_games)
                $looser_fieldset->add_field(new FormFieldSpacer('looser_first_leg_' . $i, $this->lang['scm.first.leg']));
            for($j = 1; $j <= $games_number; $j++)
            {
                $looser_bracket_fieldset = new FormFieldsetHTML('looser_bracket' . $i, '');
                $looser_bracket_fieldset->set_css_class('grouped-fields round-fields');
                $form->add_fieldset($looser_bracket_fieldset);
                $game_number = '<strong>L' . $i . $j . '</strong>';
                $game_date = $this->get_game('L', $i, $j) ? $this->get_game('L', $i, $j)->get_game_date() : new Date();
                $game_playground = $this->get_game('L', $i, $j) ? $this->get_game('L', $i, $j)->get_game_playground() : '';
                $game_home_id = $this->get_game('L', $i, $j) ? $this->get_game('L', $i, $j)->get_game_home_id() : 0;
                $game_home_score = $this->get_game('L', $i, $j) ? $this->get_game('L', $i, $j)->get_game_home_score() : '';
                $game_home_pen = $this->get_game('L', $i, $j) ? $this->get_game('L', $i, $j)->get_game_home_pen() : '';
                $game_away_pen = $this->get_game('L', $i, $j) ? $this->get_game('L', $i, $j)->get_game_away_pen() : '';
                $game_away_score = $this->get_game('L', $i, $j) ? $this->get_game('L', $i, $j)->get_game_away_score() : '';
                $game_away_id = $this->get_game('L', $i, $j) ? $this->get_game('L', $i, $j)->get_game_away_id() : 0;

                $looser_bracket_fieldset->add_field(new FormFieldFree('l_round_game_number_' . $i . $j, '', $game_number,
                    ['class' => 'game-name small text-italic align-right form-L' . $i . $j]
                ));
                $looser_bracket_fieldset->add_field(new FormFieldDateTime('l_round_game_date_' . $i . $j, '', $game_date,
                    ['class' => 'game-date']
                ));
                if($this->get_params()->get_display_playgrounds())
                    $looser_bracket_fieldset->add_field(new FormFieldTextEditor('l_round_game_playground_' . $i . $j, '', $game_playground,
                        ['class' => 'game-playground', 'placeholder' => $this->lang['scm.field']]
                    ));
                $looser_bracket_fieldset->add_field(new FormFieldSimpleSelectChoice('l_round_home_team_' . $i . $j, '', $game_home_id,
                    $this->get_teams_list(),
                    ['class' => 'home-team game-team']
                ));
                $looser_bracket_fieldset->add_field(new FormFieldTextEditor('l_round_home_score_' . $i . $j, '', $game_home_score,
                    ['class' => 'home-team game-score', 'pattern' => '[0-9]*', 'placeholder' => $this->lang['scm.th.score'] . '1']
                ));
                $looser_bracket_fieldset->add_field(new FormFieldTextEditor('l_round_home_pen_' . $i . $j, '', $game_home_pen,
                    ['class' => 'home-team game-score', 'pattern' => '[0-9]*', 'placeholder' => $this->lang['scm.th.pen'] . '1']
                ));
                $looser_bracket_fieldset->add_field(new FormFieldTextEditor('l_round_away_pen_' . $i . $j, '', $game_away_pen,
                    ['class' => 'away-team game-score', 'pattern' => '[0-9]*', 'placeholder' => $this->lang['scm.th.pen'] . '2']
                ));
                $looser_bracket_fieldset->add_field(new FormFieldTextEditor('l_round_away_score_' . $i . $j, '', $game_away_score,
                    ['class' => 'away-team game-score', 'pattern' => '[0-9]*', 'placeholder' => $this->lang['scm.th.score'] . '2']
                ));
                $looser_bracket_fieldset->add_field(new FormFieldSimpleSelectChoice('l_round_away_team_' . $i . $j, '', $game_away_id,
                    $this->get_teams_list(),
                    ['class' => 'away-team game-team']
                ));
                if ($this->return_games && $j == $games_number / 2)
                    $looser_fieldset->add_field(new FormFieldSpacer('looser_second_leg_' . $i, '<hr />' . $this->lang['scm.second.leg']));
            }
        }

		$winner_fieldset = new FormFieldsetHTML('winner_bracket', $this->get_params()->get_looser_bracket() ? $this->lang['scm.winner.bracket'] : '');
		$form->add_fieldset($winner_fieldset);

        $winner_fieldset->add_field(new FormFieldSpacer('round_' . $i, ($this->hat_ranking && $i == $rounds_number + 1) ? $this->lang['scm.playoff.games'] : $this->lang['scm.round.' . $i . ''],
            ['class' => 'form-spacer-big']
        ));
        if ($this->return_games) {
            if ($this->hat_ranking && $i == $rounds_number + 1)
                $games_number = $this->get_params()->get_playoff() / 2;
            elseif ($i == 1)
                $games_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 4 : $this->round_games_number($i);
            else
                $games_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 2 : $this->round_games_number($i);
        } else {
            $games_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 4 : $this->round_games_number($i);
            if ($i == 1 && $this->get_params()->get_third_place())
                $games_number = 2;
        }

        if (($this->return_games && $i != 1) && ($this->hat_ranking && $i != $rounds_number + 1))
            $winner_fieldset->add_field(new FormFieldSpacer('winner_first_leg_' . $i, $this->lang['scm.first.leg']));
        for($j = 1; $j <= $games_number; $j++)
        {
            $winner_bracket_fieldset = new FormFieldsetHTML('winner_bracket' . $i, '');
            $winner_bracket_fieldset->set_css_class('grouped-fields round-fields');
            $form->add_fieldset($winner_bracket_fieldset);
            $game_number = '<strong>W' . $i . $j . '</strong>';
            $game_date = $this->get_game('W', $i, $j) ? $this->get_game('W', $i, $j)->get_game_date() : new Date();
            $game_playground = $this->get_game('W', $i, $j) ? $this->get_game('W', $i, $j)->get_game_playground() : '';
            $game_home_id = $this->get_game('W', $i, $j) ? $this->get_game('W', $i, $j)->get_game_home_id() : 0;
            $game_home_score = $this->get_game('W', $i, $j) ? $this->get_game('W', $i, $j)->get_game_home_score() : '';
            $game_home_pen = $this->get_game('W', $i, $j) ? $this->get_game('W', $i, $j)->get_game_home_pen() : '';
            $game_away_pen = $this->get_game('W', $i, $j) ? $this->get_game('W', $i, $j)->get_game_away_pen() : '';
            $game_away_score = $this->get_game('W', $i, $j) ? $this->get_game('W', $i, $j)->get_game_away_score() : '';
            $game_away_id = $this->get_game('W', $i, $j) ? $this->get_game('W', $i, $j)->get_game_away_id() : 0;

            $winner_bracket_fieldset->add_field(new FormFieldFree('w_round_game_number_' . $i . $j, '', $game_number,
                ['class' => 'game-name small text-italic align-right form-W' . $i . $j]
            ));
            $winner_bracket_fieldset->add_field(new FormFieldDateTime('w_round_game_date_' . $i . $j, '', $game_date,
                ['class' => 'game-date']
            ));
            if($this->get_params()->get_display_playgrounds())
                $winner_bracket_fieldset->add_field(new FormFieldTextEditor('w_round_game_playground_' . $i . $j, '', $game_playground,
                    ['class' => 'game-playground', 'placeholder' => $this->lang['scm.field']]
                ));
            $winner_bracket_fieldset->add_field(new FormFieldSimpleSelectChoice('w_round_home_team_' . $i . $j, '', $game_home_id,
                $this->get_teams_list(),
                ['class' => 'home-team game-team']
            ));
            $winner_bracket_fieldset->add_field(new FormFieldTextEditor('w_round_home_score_' . $i . $j, '', $game_home_score,
                ['class' => 'home-team game-score', 'pattern' => '[0-9]*']
            ));
            if ((($j <= $games_number / 2) && $this->return_games) && ($i != $rounds_number + 1 && $this->hat_ranking)) {
                $winner_bracket_fieldset->add_field(new FormFieldTextEditor('w_round_home_pen_' . $i . $j, '', '',
                    ['class' => 'home-team game-score', 'disabled' => true]
                ));
                $winner_bracket_fieldset->add_field(new FormFieldTextEditor('w_round_away_pen_' . $i . $j, '', '',
                    ['class' => 'away-team game-score', 'disabled' => true]
                ));
            } elseif (($j <= $games_number / 2) && $this->return_games) {
                $winner_bracket_fieldset->add_field(new FormFieldTextEditor('w_round_home_pen_' . $i . $j, '', '',
                    ['class' => 'home-team game-score', 'disabled' => true]
                ));
                $winner_bracket_fieldset->add_field(new FormFieldTextEditor('w_round_away_pen_' . $i . $j, '', '',
                    ['class' => 'away-team game-score', 'disabled' => true]
                ));
            } else {
                $winner_bracket_fieldset->add_field(new FormFieldTextEditor('w_round_home_pen_' . $i . $j, '', $game_home_pen,
                    ['class' => 'home-team game-score', 'pattern' => '[0-9]*', 'placeholder' => $this->lang['scm.th.pen'] . 1]
                ));
                $winner_bracket_fieldset->add_field(new FormFieldTextEditor('w_round_away_pen_' . $i . $j, '', $game_away_pen,
                    ['class' => 'away-team game-score', 'pattern' => '[0-9]*', 'placeholder' => $this->lang['scm.th.pen'] . 2]
                ));
            }
            $winner_bracket_fieldset->add_field(new FormFieldTextEditor('w_round_away_score_' . $i . $j, '', $game_away_score,
                ['class' => 'away-team game-score', 'pattern' => '[0-9]*']
            ));
            $winner_bracket_fieldset->add_field(new FormFieldSimpleSelectChoice('w_round_away_team_' . $i . $j, '', $game_away_id,
                $this->get_teams_list(),
                ['class' => 'away-team game-team']
            ));
            if (($this->return_games && $j == $games_number / 2) && ($this->hat_ranking && $i != $rounds_number + 1))
                $winner_bracket_fieldset->add_field(new FormFieldSpacer('winner_second_leg_' . $i, '<hr />' . $this->lang['scm.second.leg']));
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
            $games_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 4 : $this->round_games_number($i);

            for($j = 1; $j <= $games_number; $j++)
            {
                $game = $this->get_game('L', $i, $j);
                $game->set_game_event_id($this->event_id());
                $game->set_game_type('L');
                $game->set_game_group($i);
                $game->set_game_order($j);
                $game->set_game_date($this->form->get_value('l_round_game_date_' . $i . $j));
                if($this->get_params()->get_display_playgrounds())
                    $game->set_game_playground($this->form->get_value('l_round_game_playground_' . $i . $j));
                $game->set_game_home_id((int)$this->form->get_value('l_round_home_team_' . $i . $j)->get_raw_value());
                $game->set_game_home_score($this->form->get_value('l_round_home_score_' . $i . $j));
                $game->set_game_home_pen($this->form->get_value('l_round_home_pen_' . $i . $j));
                $game->set_game_away_pen($this->form->get_value('l_round_away_pen_' . $i . $j));
                $game->set_game_away_score($this->form->get_value('l_round_away_score_' . $i . $j));
                $game->set_game_away_id((int)$this->form->get_value('l_round_away_team_' . $i . $j)->get_raw_value());

                if ($game->get_id_game() == null)
                {
                    $id = ScmGameService::add_game($game);
                    $game->set_id_game($id);
                }
                else {
                    ScmGameService::update_game($game, $game->get_id_game());
                }
            }
        }

        // Winner bracket
        if ($this->return_games) {
            if ($this->hat_ranking && $i == $rounds_number) {
                $games_number = $this->get_params()->get_playoff() / 2;
            } elseif ($i == 1) {
                $games_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 4 : $this->round_games_number($i);
            } else {
                $games_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 2 : $this->round_games_number($i);
            }
        } else {
            $games_number = $this->get_params()->get_looser_bracket() ? $this->teams_number / 4 : $this->round_games_number($i);
            if ($i == 1 && $this->get_params()->get_third_place())
                $games_number = 2;
        }

        for($j = 1; $j <= $games_number; $j++)
        {
            $game = $this->get_game('W', $i, $j);
            $game->set_game_event_id($this->event_id());
            $game->set_game_type('W');
            $game->set_game_group($i);
            $game->set_game_order($j);
            $game->set_game_date($this->form->get_value('w_round_game_date_' . $i . $j));
            if($this->get_params()->get_display_playgrounds())
                $game->set_game_playground($this->form->get_value('w_round_game_playground_' . $i . $j));
            $game->set_game_home_id((int)$this->form->get_value('w_round_home_team_' . $i . $j)->get_raw_value());
            $game->set_game_home_score($this->form->get_value('w_round_home_score_' . $i . $j));
            $game->set_game_home_pen($this->form->get_value('w_round_home_pen_' . $i . $j));
            $game->set_game_away_pen($this->form->get_value('w_round_away_pen_' . $i . $j));
            $game->set_game_away_score($this->form->get_value('w_round_away_score_' . $i . $j));
            $game->set_game_away_id((int)$this->form->get_value('w_round_away_team_' . $i . $j)->get_raw_value());

            if ($game->get_id_game() == null)
            {
                $id = ScmGameService::add_game($game);
                $game->set_id_game($id);
            }
            else {
                ScmGameService::update_game($game, $game->get_id_game());
            }
        }

		ScmEventService::clear_cache();
	}

    private function round_games_number($round)
    {
        return ScmBracketService::round_games_number($round, $this->return_games);
    }

	private function get_game($type, $group, $order)
	{
        $event_id = $this->event_id();
        $id = ScmGameService::get_game($event_id, $type, $group, $order) ? ScmGameService::get_game($event_id, $type, $group, $order)->get_id_game() : null;

        if($id !== null)
            try {
                $this->game = ScmGameService::get_game($event_id, $type, $group, $order);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        else
        {
            $this->game = new ScmGame();
        }
		return $this->game;
	}

	private function get_event()
	{
		$id = AppContext::get_request()->get_getint('event_id', 0);
		try {
            $this->event = ScmEventService::get_event($id);
        } catch (RowNotFoundException $e) {
            $error_controller = PHPBoostErrors::unexisting_page();
            DispatchManager::redirect($error_controller);
        }
		return $this->event;
	}

    private function event_id()
    {
        return $this->get_event()->get_id();
    }

    private function get_teams_list()
    {
        $options = [];
        $clubs = ScmClubCache::load();
        $options[] = new FormFieldSelectChoiceOption('', 0);
        foreach (ScmTeamService::get_teams($this->event_id()) as $team)
        {
			$options[] = new FormFieldSelectChoiceOption($clubs->get_club_name($team['team_club_id']), $team['id_team']);
        }

		return $options;
    }

    private function get_params()
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

	private function generate_response(View $view)
	{
		$event = $this->get_event();

		// $location_id = $event->get_id() ? 'scm-edit-'. $event->get_id() : '';

		// $response = new SiteDisplayResponse($view, $location_id);
		$response = new SiteDisplayResponse($view);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());

		// if (!AppContext::get_session()->location_id_already_exists($location_id))
        //     $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['scm.games.management'], $this->lang['scm.module.title']);
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.games.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_brackets_games($event->get_id(), $event->get_event_slug()));

        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($event->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        $category = $event->get_category();
        $breadcrumb->add($event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));
        $breadcrumb->add($this->lang['scm.games.management'], ScmUrlBuilder::edit_brackets_games($event->get_id(), $event->get_event_slug()));

		return $response;
	}
}
?>
