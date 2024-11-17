<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmDayGamesFormController extends DefaultModuleController
{
    private $event;
    private $params;
    private $game;
    private $teams_number;
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
        ]);

		return $this->generate_response($this->view);
	}

    private function init()
    {
        $this->teams_number = ScmTeamService::get_teams_number($this->event_id());
        $this->return_games = ScmEventService::get_event_game_type($this->event_id()) == ScmDivision::RETURN_GAMES;
    }

	private function build_form()
	{
        $gr = AppContext::get_request()->get_getint('round', 0);
		$form = new HTMLForm(__CLASS__);
        $form->set_css_class('floating-submit');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['scm.games.management'] . '</div>');

        $fieldset = new FormFieldsetHTML('days', $this->lang['scm.day'].' ' . $gr);
		$form->add_fieldset($fieldset);

        foreach($this->get_day_games($gr) as $day_game)
        {
            $game = new ScmGame();
            $game->set_properties($day_game);
            $or = $game->get_game_order();
            $ro = 0;
            $game_fieldset = new FormFieldsetHTML('game_' . $gr . $or, '');
            $game_fieldset->set_css_class('grouped-fields matchdays-game');
            $form->add_fieldset($game_fieldset);
            $bonus = $this->get_params()->get_bonus() &&
                ($game->get_game_home_off_bonus() ||
                $game->get_game_home_def_bonus() ||
                $game->get_game_away_off_bonus() ||
                $game->get_game_away_def_bonus()) 
                    ? ' ' . $this->lang['scm.bonus.param']
                    : '';

            switch ($game->get_game_status()) {
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

            $forfeit = $game->get_game_home_forfeit() || $game->get_game_away_forfeit() ?  ' ' . $this->lang['scm.event.forfeit'] : '';

            $game_fieldset->add_field(new FormFieldFree(
                'game_number_' . $gr . $or,
                '',
                '<strong>D' . $gr . $or . '</strong><span class="warning">' . $bonus . $status . $forfeit . '</span>',
                ['class' => 'game-name small text-italic form-D' . $gr . $or]
            ));
            $game_fieldset->add_field(new FormFieldActionLink('details', $this->lang['scm.game.details'] , ScmUrlBuilder::edit_details_game($this->event_id(), $this->get_event()->get_event_slug(), 'D', $gr, $ro, $or), 'small text-italic'));
            $game_fieldset->add_field(new FormFieldDateTime('game_date_' . $gr . $or, '', $game->get_game_date(),
                ['class' => 'game-date']
            ));
            if($this->get_params()->get_display_playgrounds())
                $game_fieldset->add_field(new FormFieldTextEditor('game_playground_' . $gr . $or, '', $game->get_game_playground(),
                    ['class' => 'game-playground', 'placeholder' => $this->lang['scm.field']]
                ));
            $game_fieldset->add_field(new FormFieldSimpleSelectChoice('home_team_' . $gr . $or, '', $game->get_game_home_id(),
                $this->get_teams_list(),
                ['class' => 'home-team game-team']
            ));
            $game_fieldset->add_field(new FormFieldTextEditor('home_score_' . $gr . $or, '', $game->get_game_home_score(),
                ['class' => 'game-score', 'pattern' => '[0-9]*']
            ));
            $game_fieldset->add_field(new FormFieldTextEditor('away_score_' . $gr . $or, '', $game->get_game_away_score(),
                ['class' => 'game-score', 'pattern' => '[0-9]*']
            ));
            $game_fieldset->add_field(new FormFieldSimpleSelectChoice('away_team_' . $gr . $or, '', $game->get_game_away_id(),
                $this->get_teams_list(),
                ['class' => 'away-team game-team']
            ));
        }

        $this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$this->form = $form;
	}

	private function save()
	{
        $gr = AppContext::get_request()->get_getint('round', 0);

        $games = [];
        foreach($this->get_day_games($gr) as $day_game)
        {
            $game = new ScmGame();
            $game->set_properties($day_game);
            $or = $game->get_game_order();
            $game->set_game_event_id($this->event_id());
            $game->set_game_date($this->form->get_value('game_date_' . $gr . $or));
            if($this->get_params()->get_display_playgrounds())
                $game->set_game_playground($this->form->get_value('game_playground_' . $gr . $or));
            $game->set_game_home_id((int)$this->form->get_value('home_team_' . $gr . $or)->get_raw_value());
            $game->set_game_home_score($this->form->get_value('home_score_' . $gr . $or));
            $game->set_game_away_score($this->form->get_value('away_score_' . $gr . $or));
            $game->set_game_away_id((int)$this->form->get_value('away_team_' . $gr . $or)->get_raw_value());

            ScmGameService::update_game($game, $game->get_id_game());
            $games[] = $game->get_game_home_score();
        }

        foreach($this->get_day_games($gr) as $day_game)
        {
            $game = new ScmGame();
            $game->set_properties($day_game);

            if (ScmDayService::day_has_scores($games))
                ScmDayService::update_day_played($this->event_id(), $game->get_game_group(), 1);
            else
                ScmDayService::update_day_played($this->event_id(), $game->get_game_group(), 0);
        }

		ScmEventService::clear_cache();
	}

    private function get_day_games($gr)
    {
        $games = ScmGroupService::games_list_from_group($this->event_id(), 'D', $gr);

        return $games;
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
        $options[] = new FormFieldSelectChoiceOption('', 0);
        foreach (ScmTeamService::get_teams($this->event_id()) as $team)
        {
			$options[] = new FormFieldSelectChoiceOption($team['club_name'], $team['id_team']);
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

		$location_id = $event->get_id() ? 'scm-edit-'. $event->get_id() : '';

		// $response = new SiteDisplayResponse($view, $location_id);
		$response = new SiteDisplayResponse($view);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());

		if (!AppContext::get_session()->location_id_already_exists($location_id))
            $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['scm.games.management'], $this->lang['scm.module.title']);
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.games.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_days_games($event->get_id(), $event->get_event_slug()));

        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($event->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        $category = $event->get_category();
        $breadcrumb->add($event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));
        $breadcrumb->add($this->lang['scm.games.management'], ScmUrlBuilder::edit_days_games($event->get_id(), $event->get_event_slug(), AppContext::get_request()->get_value('round')));

		return $response;
	}
}
?>
