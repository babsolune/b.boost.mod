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
    private $brackets_number;
    private $finals_form;
    private $finals_submit_button;
    private $finals_ranking;

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
		$this->check_authorizations();

        if ($this->finals_ranking)
        {
            $this->build_finals_form();

            if ($this->finals_submit_button->has_been_submited() && $this->finals_form->validate())
            {
                $this->finals_save();
                $this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['scm.warning.games.update'], MessageHelper::SUCCESS, 4));
            }
        }
        else
        {
            $this->build_form();

            if ($this->submit_button->has_been_submited() && $this->form->validate())
            {
                $this->save();
                $this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['scm.warning.games.update'], MessageHelper::SUCCESS, 4));
            }
        }

		$this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),
            'CONTENT' => $this->finals_ranking ? $this->finals_form->display() : $this->form->display(),
        ]);

		return $this->generate_response($this->view);
	}

    private function init()
    {
        $this->hat_ranking = $this->get_params()->get_hat_ranking();
        $this->brackets_number = $this->get_params()->get_looser_bracket() ? $this->get_params()->get_brackets_number() + 1 : 1;
        $this->teams_number = ScmTeamService::get_teams_number($this->event_id());
        $this->teams_per_group = $this->get_params()->get_teams_per_group();
        $this->return_games = ScmEventService::get_event_game_type($this->event_id()) == ScmDivision::RETURN_GAMES;
        $this->finals_ranking = $this->get_params()->get_finals_type() == ScmParams::FINALS_RANKING;
    }

	private function build_form()
	{
        $rounds_number = $this->get_params()->get_rounds_number();
        $gr = AppContext::get_request()->get_getint('round', 0);
        $round_name = ($this->hat_ranking && $gr == $rounds_number + 1) ? $this->lang['scm.playoff.games'] : $this->lang['scm.round.' . $gr . ''];

        $form = new HTMLForm(__CLASS__);
        $form->set_css_class('floating-submit');
        $form->set_layout_title(
            '<div class="align-center small">' . $this->lang['scm.games.management'] . '</div>'
            . '<div class="align-center smaller">' . $this->lang['scm.games.brackets.stage'] . ' - ' . $round_name . '</div>'
        );

        foreach ($this->get_brackets($gr) as $br => $games)
        {
            $fieldset_stage_tile = $br == 1 ? $this->lang['scm.winner.bracket'] : ($this->brackets_number == 2 ? $this->lang['scm.looser.bracket'] : $this->lang['scm.looser.bracket'] . ' ' . ScmBracketService::ntl($br));

            ${'fieldset'.$br} = new FormFieldsetHTML('bracket' . $br, $this->get_params()->get_looser_bracket() ? $fieldset_stage_tile : '');
            $form->add_fieldset(${'fieldset'.$br});

            $games_number = count($games);

            foreach ($games as $game)
            {
                $or = $game['game_order'];
                ${'bracket_fieldset'.$br.$or} = new FormFieldsetHTML('bracket' . $br . $or, '');
                ${'bracket_fieldset'.$br.$or}->set_css_class('grouped-fields round-fields');
                $form->add_fieldset(${'bracket_fieldset'.$br.$or});
                $empty_teams = '';
                if ($this->get_game('B', $gr, $br, $or)->get_game_home_empty())
                    $empty_teams = ' - ' . $this->get_game('B', $gr, $br, $or)->get_game_home_empty() . '|' . $this->get_game('B', $gr, $br, $or)->get_game_away_empty();
                $game_number     = '<strong>B' . $gr . $br . $or . '</strong>' . $empty_teams;
                $game_date       = $this->get_game('B', $gr, $br, $or)->get_game_date();
                $game_playground = $this->get_game('B', $gr, $br, $or)->get_game_playground();
                $game_home_id    = $this->get_game('B', $gr, $br, $or)->get_game_home_id();
                $game_home_score = $this->get_game('B', $gr, $br, $or)->get_game_home_score();
                $game_away_score = $this->get_game('B', $gr, $br, $or)->get_game_away_score();
                $game_away_id    = $this->get_game('B', $gr, $br, $or)->get_game_away_id();
                $bonus = $this->get_params()->get_bonus() &&
                    ($game->get_game_home_off_bonus() ||
                    $game->get_game_home_def_bonus() ||
                    $game->get_game_away_off_bonus() ||
                    $game->get_game_away_def_bonus()) ? ' ' . $this->lang['scm.bonus.param'] 
                    : '';

                switch ($game['game_status']) {
                    case ScmGame::DELAYED :
                        $status = ' ' . $this->lang['scm.event.status.delayed'];
                        break;
                    case ScmGame::STOPPED :
                        $status = ' ' . $this->lang['scm.event.status.stopped'];
                        break;
                    case '' :
                        $status = '';
                        break;
                }

                if ($this->return_games && $or == 1)
                    ${'bracket_fieldset'.$br.$or}->add_field(new FormFieldSpacer('first_leg_' . $gr . $br, $this->lang['scm.first.leg']));
                ${'bracket_fieldset'.$br.$or}->add_field(new FormFieldFree('game_number_' . $gr . $br . $or, '', $game_number . $bonus . $status,
                    ['class' => 'game-name small text-italic form-B-' . $gr . $br . $or]
                ));
                ${'bracket_fieldset'.$br.$or}->add_field(new FormFieldActionLink('details', $this->lang['scm.game.details'] , ScmUrlBuilder::edit_details_game($this->event_id(), $this->get_event()->get_event_slug(), 'B', $gr, $br, $or), 'small game-details align-right text-italic'));
                ${'bracket_fieldset'.$br.$or}->add_field(new FormFieldDateTime('game_date_' . $gr . $br . $or, '', $game_date,
                    ['class' => 'game-date']
                ));
                if($this->get_params()->get_display_playgrounds())
                    ${'bracket_fieldset'.$br.$or}->add_field(new FormFieldTextEditor('game_playground_' . $gr . $br . $or, '', $game_playground,
                        ['class' => 'game-playground', 'placeholder' => $this->lang['scm.field']]
                    ));
                ${'bracket_fieldset'.$br.$or}->add_field(new FormFieldSimpleSelectChoice('home_team_' . $gr . $br . $or, '', $game_home_id,
                    $this->get_teams_list(),
                    ['class' => 'home-team game-team']
                ));
                ${'bracket_fieldset'.$br.$or}->add_field(new FormFieldNumberEditor('home_score_' . $gr . $br . $or, '', $game_home_score,
                    ['class' => 'home-team game-score', 'pattern' => '[0-9]*']
                ));
                ${'bracket_fieldset'.$br.$or}->add_field(new FormFieldNumberEditor('away_score_' . $gr . $br . $or, '', $game_away_score,
                    ['class' => 'away-team game-score', 'pattern' => '[0-9]*']
                ));
                ${'bracket_fieldset'.$br.$or}->add_field(new FormFieldSimpleSelectChoice('away_team_' . $gr . $br . $or, '', $game_away_id,
                    $this->get_teams_list(),
                    ['class' => 'away-team game-team']
                ));
                if ($this->return_games && $or == $games_number / 2)
                    ${'bracket_fieldset'.$br.$or}->add_field(new FormFieldSpacer('winner_second_leg_' . $gr, '<hr />' . $this->lang['scm.second.leg']));
            }
        }

        $this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$this->form = $form;
	}

	private function save()
	{
        $gr = AppContext::get_request()->get_getint('round', 0);

        foreach ($this->get_brackets($gr) as $br => $games)
        {
            foreach($games as $game)
            {
                $or = $game['game_order'];
                $item = $this->get_game('B', $gr, $br, $or);
                $item->set_game_date($this->form->get_value('game_date_' . $gr . $br . $or));
                if($this->get_params()->get_display_playgrounds())
                    $item->set_game_playground($this->form->get_value('game_playground_' . $gr . $br . $or));
                $item->set_game_home_id((int)$this->form->get_value('home_team_' . $gr . $br . $or)->get_raw_value());
                $item->set_game_home_score($this->form->get_value('home_score_' . $gr . $br . $or));
                $item->set_game_home_pen($this->form->get_value('home_pen_' . $gr . $br . $or));
                $item->set_game_away_pen($this->form->get_value('away_pen_' . $gr . $br . $or));
                $item->set_game_away_score($this->form->get_value('away_score_' . $gr . $br . $or));
                $item->set_game_away_id((int)$this->form->get_value('away_team_' . $gr . $br . $or)->get_raw_value());

                ScmGameService::update_game($item, $item->get_id_game());
            }
        }

		ScmEventService::clear_cache();
	}

    private function build_finals_form()
    {
        $group = AppContext::get_request()->get_getint('round', 0);
        $games_number = count(ScmGroupService::games_list_from_group($this->event_id(), 'B', $group));

        $finals_form = new HTMLForm(__CLASS__);
        $finals_form->set_css_class('class');
        $finals_form->set_layout_title(
            '<div class="align-center small">' . $this->lang['scm.games.management'] . '</div>'
            . '<div class="align-center smaller">' . $this->lang['scm.games.brackets.stage'] . ' - ' . $this->lang['scm.group'] . ' ' . $group . '</div>'
        );

        foreach (ScmGroupService::games_list_from_group($this->event_id(), 'B', $group) as $group_game)
        {
            $game = new ScmGame();
            $game->set_properties($group_game);
            $gr = $game->get_game_group();
            $or = $game->get_game_order();
            $round = $game->get_game_round();
            $fieldset = new FormFieldsetHTML('round_' . $or, '');
            $fieldset->set_css_class('grouped-fields round-fields');
            $finals_form->add_fieldset($fieldset);

            if ($this->return_games && $or == 1)
                $fieldset->add_field(new FormFieldSpacer('first_leg_' . $gr, $this->lang['scm.first.leg']));
            $fieldset->add_field(new FormFieldFree('game_number', '', '<strong>B' . $gr . $or . '</strong>'. ' - ' . $this->lang['scm.round'] . ' ' . $round,
                ['class' => 'game-name small text-italic form-G' . $gr . $or]
            ));
            $fieldset->add_field(new FormFieldActionLink('details', $this->lang['scm.game.details'] , ScmUrlBuilder::edit_details_game($this->event_id(), $this->get_event()->get_event_slug(), 'G', $gr, $round, $or), 'small text-italic'));
            $fieldset->add_field(new FormFieldDateTime('game_date_' . $gr . $or, '', $game->get_game_date(),
                ['class' => 'game-date']
            ));
            if($this->get_params()->get_display_playgrounds())
                $fieldset->add_field(new FormFieldTextEditor('game_playground_' . $gr . $or, '', $game->get_game_playground(),
                    ['class' => 'game-playground', 'placeholder' => $this->lang['scm.field']]
                ));
            $fieldset->add_field(new FormFieldSimpleSelectChoice('home_team_' . $gr . $or, '', $game->get_game_home_id(),
                $this->get_teams_list(),
                ['class' => 'home-team game-team']
            ));
            $fieldset->add_field(new FormFieldNumberEditor('home_score_' . $gr . $or, '', $game->get_game_home_score(),
                ['class' => 'home-team game-score', 'pattern' => '[0-9]*']
            ));
            $fieldset->add_field(new FormFieldNumberEditor('away_score_' . $gr . $or, '', $game->get_game_away_score(),
                ['class' => 'away-team game-score', 'pattern' => '[0-9]*']
            ));
            $fieldset->add_field(new FormFieldSimpleSelectChoice('away_team_' . $gr . $or, '', $game->get_game_away_id(),
                $this->get_teams_list(),
                ['class' => 'away-team game-team']
            ));

            if ($this->return_games && $or == $games_number / 2)
                $fieldset->add_field(new FormFieldSpacer('second_leg_' . $gr, '<hr />' . $this->lang['scm.second.leg']));
        }

        $this->finals_submit_button = new FormButtonDefaultSubmit();
        $finals_form->add_button($this->finals_submit_button);

        $this->finals_form = $finals_form;
    }

    private function finals_save()
    {
        $gr = AppContext::get_request()->get_getint('round', 0);
        foreach($this->get_group_games($gr) as $games)
        {
            foreach ($games as $group_game)
            {
                $game = new ScmGame();
                $game->set_properties($group_game);
                $or = $game->get_game_order();
                $game->set_game_event_id($this->event_id());
                $game->set_game_date($this->finals_form->get_value('game_date_' . $gr . $or));
                if($this->get_params()->get_display_playgrounds())
                    $game->set_game_playground($this->finals_form->get_value('game_playground_' . $gr . $or));
                $game->set_game_home_id((int)$this->finals_form->get_value('home_team_' . $gr . $or)->get_raw_value());
                $game->set_game_home_score($this->finals_form->get_value('home_score_' . $gr . $or));
                $game->set_game_away_score($this->finals_form->get_value('away_score_' . $gr . $or));
                $game->set_game_away_id((int)$this->finals_form->get_value('away_team_' . $gr . $or)->get_raw_value());

                ScmGameService::update_game($game, $game->get_id_game());
            }
        }
    }

    private function get_group_games($gr)
    {
        $games = ScmGroupService::games_list_from_group($this->event_id(), 'B', $gr);

        usort($games, function($a, $b) {
            if ($a['game_round'] == $b['game_round']) {
                return $a['game_order'] - $b['game_order'];
            } else {
                return $a['game_round'] - $b['game_round'];
            }
        });

        $rounds = [];
        foreach($games as $game)
        {
            $rounds[$game['game_round']][] = $game;
        }

        return $rounds;
    }

    private function get_brackets($gr)
    {
        $games = ScmGroupService::games_list_from_group($this->event_id(), 'B', $gr);

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
            $brackets[$game['game_round']][] = $game;
        }

        return $brackets;
    }

    private function get_group_teams_list($type, $group)
    {
        $teams_list = [];
        foreach (ScmTeamService::get_teams($this->event_id()) as $team)
        {
            $team_group = $team['team_group'];
            $team_group = $team_group ? TextHelper::substr($team_group, 0, 1) : '';
            if ($team_group == $group)
                $teams_list[] = $team;
        }
        $options = [];

        $clubs = ScmClubCache::load();
        $options[] = new FormFieldSelectChoiceOption('', 0);
		foreach($teams_list as $team)
		{
			$options[] = new FormFieldSelectChoiceOption($clubs->get_club_name($team['team_club_id']), $team['id_team']);
		}

		return $options;
    }

	private function get_game($type, $group, $round, $order)
	{
        $event_id = $this->event_id();
        try {
            $this->game = ScmGameService::get_game($event_id, $type, $group, $round, $order);
        } catch (RowNotFoundException $e) {
            $error_controller = PHPBoostErrors::unexisting_page();
            DispatchManager::redirect($error_controller);
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
