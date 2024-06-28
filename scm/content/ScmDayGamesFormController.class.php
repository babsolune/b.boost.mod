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
        $i = AppContext::get_request()->get_getint('round', 0);
		$form = new HTMLForm(__CLASS__);
        $form->set_css_class('floating-submit');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['scm.games.management'] . '</div>');

        $fieldset = new FormFieldsetHTML('days', $this->lang['scm.day'].' ' . $i);
		$form->add_fieldset($fieldset);

        for($j = 1; $j <= $this->teams_number / 2; $j++)
        {
            $game_fieldset = new FormFieldsetHTML('game' . $j, '');
            $game_fieldset->set_css_class('grouped-fields matchdays-game');
            $form->add_fieldset($game_fieldset);
            $game_number = '<strong>D' . $i . $j . '</strong>';
            $game_date = $this->get_game('D', $i, $j) ? $this->get_game('D', $i, $j)->get_game_date() : new Date();
            $game_playground = $this->get_game('D', $i, $j) ? $this->get_game('D', $i, $j)->get_game_playground() : '';
            $game_home_id = $this->get_game('D', $i, $j) ? $this->get_game('D', $i, $j)->get_game_home_id() : 0;
            $game_home_score = $this->get_game('D', $i, $j) ? $this->get_game('D', $i, $j)->get_game_home_score() : '';
            $game_away_score = $this->get_game('D', $i, $j) ? $this->get_game('D', $i, $j)->get_game_away_score() : '';
            $game_away_id = $this->get_game('D', $i, $j) ? $this->get_game('D', $i, $j)->get_game_away_id() : 0;

            $game_fieldset->add_field(new FormFieldFree('day_game_number_' . $i . $j, '', $game_number,
                ['class' => 'game-name small text-italic form-D' . $i . $j]
            ));
            $game_fieldset->add_field(new FormFieldDateTime('day_game_date_' . $i . $j, '', $game_date,
                ['class' => 'game-date']
            ));
            if($this->get_params()->get_display_playgrounds())
                $game_fieldset->add_field(new FormFieldTextEditor('day_game_playground_' . $i . $j, '', $game_playground,
                    ['class' => 'game-playground', 'placeholder' => $this->lang['scm.field']]
                ));
            $game_fieldset->add_field(new FormFieldSimpleSelectChoice('day_home_team_' . $i . $j, '', $game_home_id,
                $this->get_teams_list(),
                ['class' => 'home-team game-team']
            ));
            $game_fieldset->add_field(new FormFieldTextEditor('day_home_score_' . $i . $j, '', $game_home_score,
                ['class' => 'game-score', 'pattern' => '[0-9]*']
            ));
            $game_fieldset->add_field(new FormFieldTextEditor('day_away_score_' . $i . $j, '', $game_away_score,
                ['class' => 'game-score', 'pattern' => '[0-9]*']
            ));
            $game_fieldset->add_field(new FormFieldSimpleSelectChoice('day_away_team_' . $i . $j, '', $game_away_id,
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
        $i = AppContext::get_request()->get_getint('round', 0);

        for($j = 1; $j <= $this->teams_number / 2; $j++)
        {
            $game = $this->get_game('D', $i, $j);
            $game->set_game_event_id($this->event_id());
            $game->set_game_type('D');
            $game->set_game_group($i);
            $game->set_game_order($j);
            $game->set_game_date($this->form->get_value('day_game_date_' . $i . $j));
            if($this->get_params()->get_display_playgrounds())
                $game->set_game_playground($this->form->get_value('day_game_playground_' . $i . $j));
            $game->set_game_home_id((int)$this->form->get_value('day_home_team_' . $i . $j)->get_raw_value());
            $game->set_game_home_score($this->form->get_value('day_home_score_' . $i . $j));
            $game->set_game_away_score($this->form->get_value('day_away_score_' . $i . $j));
            $game->set_game_away_id((int)$this->form->get_value('day_away_team_' . $i . $j)->get_raw_value());

            if ($game->get_id_game() == null)
            {
                $id = ScmGameService::add_game($game);
                $game->set_id_game($id);
            }
            else {
                ScmGameService::update_game($game, $game->get_id_game());
            }
            if ($game->get_game_home_score())
                ScmDayService::update_day_played($this->event_id(), $game->get_game_group(), 1);
        }

		ScmEventService::clear_cache();
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
