<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 07 23
 * @since       PHPBoost 6.0 - 2024 07 23
*/

class ScmDetailsGameFormController extends DefaultModuleController
{
    private $event;
    private $params;
    private $games_number;
    private $bracket_games;
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
        $this->games_number = ScmGameService::get_games($this->event_id());
        $this->return_games = ScmEventService::get_event_game_type($this->event_id()) == ScmDivision::RETURN_GAMES;
        $this->bracket_games = $this->get_game()->get_game_type() === 'B';
    }

	private function build_form()
	{
        if($this->get_game()->get_game_type() == 'D')
            $url = ScmUrlBuilder::edit_days_games($this->event_id(), $this->get_event()->get_event_slug(), $this->get_game()->get_game_group())->rel();
        if($this->get_game()->get_game_type() == 'G')
            $url = ScmUrlBuilder::edit_groups_games($this->event_id(), $this->get_event()->get_event_slug(), $this->get_game()->get_game_group())->rel();
        if($this->get_game()->get_game_type() == 'B')
            $url = ScmUrlBuilder::edit_brackets_games($this->event_id(), $this->get_event()->get_event_slug(), $this->get_game()->get_game_group())->rel();

        $form = new HTMLForm(__CLASS__);
        $form->set_css_class('floating-submit');
		$form->set_layout_title(
            '<div class="align-center small">' . $this->lang['scm.game.details'] . '</div>'
            . '<div class="align-center smaller">' . $this->get_game()->get_game_type() . $this->get_game()->get_game_group() . $this->get_game()->get_game_round() . $this->get_game()->get_game_order() . ' - <a href="' . $url . '" class="small offload"><i class="fa fa-share-from-square fa-flip-horizontal"></i> ' . $this->lang['scm.event.back'] . '</a></div>'
        );

        $fieldset = new FormFieldsetHTML('game', '');
        $fieldset->set_css_class('game-details');
        $form->add_fieldset($fieldset);

        $fieldset->add_field(new FormFieldSpacer('empty', ''));
        $fieldset->add_field(new FormFieldSpacer('team_1', $this->get_game()->get_game_home_id() == 0 ? $this->lang['scm.th.team'] . ' 1' : ScmTeamService::get_team_name($this->get_game()->get_game_home_id())));
        $fieldset->add_field(new FormFieldSpacer('team_2', $this->get_game()->get_game_away_id() == 0 ? $this->lang['scm.th.team'] . ' 2' : ScmTeamService::get_team_name($this->get_game()->get_game_away_id())));

        if ($this->bracket_games && $this->return_games) {
            if (($this->get_game()->get_game_order() > count($this->games_number) / 2)) {
                $fieldset->add_field(new FormFieldSpacer('penalties', $this->lang['scm.event.penalties']));
                $fieldset->add_field(new FormFieldTextEditor('home_pen', '', $this->get_game()->get_game_home_pen(),
                    ['class' => 'game-details home-details', 'pattern' => '[0-9]*']
                ));
                $fieldset->add_field(new FormFieldTextEditor('away_pen', '', $this->get_game()->get_game_away_pen(),
                    ['class' => 'game-details away-details', 'pattern' => '[0-9]*']
                ));
            }
        }
        elseif ($this->bracket_games && !$this->return_games) {
            $fieldset->add_field(new FormFieldSpacer('penalties', $this->lang['scm.event.penalties']));
            $fieldset->add_field(new FormFieldTextEditor('home_pen', '', $this->get_game()->get_game_home_pen(),
                ['class' => 'game-details home-details', 'pattern' => '[0-9]*']
            ));
            $fieldset->add_field(new FormFieldTextEditor('away_pen', '', $this->get_game()->get_game_away_pen(),
                ['class' => 'game-details away-details', 'pattern' => '[0-9]*']
            ));
        }

        if($this->get_params()->get_bonus())
        {
            $fieldset->add_field(new FormFieldSpacer('offensive_bonus', $this->get_params()->get_bonus() == ScmParams::BONUS_DOUBLE ? $this->lang['scm.event.off.bonus'] : $this->lang['scm.event.bonus']));
            $fieldset->add_field(new FormFieldTextEditor('home_off_bonus', '', $this->get_game()->get_game_home_off_bonus(),
                ['class' => 'game-details home-details', 'pattern' => '[0-9]*']
            ));
            $fieldset->add_field(new FormFieldTextEditor('away_off_bonus', '', $this->get_game()->get_game_away_off_bonus(),
                ['class' => 'game-details away-details', 'pattern' => '[0-9]*']
            ));
            if($this->get_params()->get_bonus() == ScmParams::BONUS_DOUBLE)
            {
                $fieldset->add_field(new FormFieldSpacer('defensive_bonus', $this->lang['scm.event.def.bonus']));
                $fieldset->add_field(new FormFieldTextEditor('home_def_bonus', '', $this->get_game()->get_game_home_def_bonus(),
                    ['class' => 'game-details home-details', 'pattern' => '[0-9]*']
                ));
                $fieldset->add_field(new FormFieldTextEditor('away_def_bonus', '', $this->get_game()->get_game_away_def_bonus(),
                    ['class' => 'game-details away-details', 'pattern' => '[0-9]*']
                ));
            }
        }

        $fieldset->add_field(new FormFieldSpacer('game_goals', $this->lang['scm.event.goals']));
        $fieldset->add_field(new ScmFormFieldGameEvents('home_goals', '', $this->get_game()->get_game_home_goals()));
        $fieldset->add_field(new ScmFormFieldGameEvents('away_goals', '', $this->get_game()->get_game_away_goals()));

        $fieldset->add_field(new FormFieldSpacer('yellow_card', $this->lang['scm.event.yellow.cards']));
        $fieldset->add_field(new ScmFormFieldGameEvents('home_yellow', '', $this->get_game()->get_game_home_yellow()));
        $fieldset->add_field(new ScmFormFieldGameEvents('away_yellow', '', $this->get_game()->get_game_away_yellow()));

        $fieldset->add_field(new FormFieldSpacer('red_card', $this->lang['scm.event.red.cards']));
        $fieldset->add_field(new ScmFormFieldGameEvents('home_red', '', $this->get_game()->get_game_home_red()));
        $fieldset->add_field(new ScmFormFieldGameEvents('away_red', '', $this->get_game()->get_game_away_red()));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('status', $this->lang['scm.event.status'], $this->get_game()->get_game_status(),
            [
                new FormFieldSelectChoiceOption('', ''),
                new FormFieldSelectChoiceOption($this->lang['scm.event.status.delayed'], ScmGame::DELAYED),
                new FormFieldSelectChoiceOption($this->lang['scm.event.status.stopped'], ScmGame::STOPPED)
            ]
        ));

        if ($this->bracket_games) {
            $fieldset->add_field(new FormFieldSpacer('empty_field', $this->lang['scm.event.empty.field']));
            $fieldset->add_field(new FormFieldTextEditor('home_empty', '', $this->get_game()->get_game_home_empty()));
            $fieldset->add_field(new FormFieldTextEditor('away_empty', '', $this->get_game()->get_game_away_empty()));
        }

        $fieldset->add_field(new FormFieldUrlEditor('video', $this->lang['scm.event.video'], $this->get_game()->get_game_video()->relative()));

        $fieldset->add_field(new FormFieldRichTextEditor('summary', $this->lang['scm.event.summary'], $this->get_game()->get_game_summary()));

        $this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$this->form = $form;
	}

	private function save()
	{
        $game = $this->get_game();
        if ($this->bracket_games)
        {
            $game->set_game_home_pen($this->form->get_value('home_pen'));
            $game->set_game_away_pen($this->form->get_value('away_pen'));
        }
        $game->set_game_home_goals($this->form->get_value('home_goals'));
        $game->set_game_home_yellow($this->form->get_value('home_yellow'));
        $game->set_game_home_red($this->form->get_value('home_red'));
        $game->set_game_home_empty($this->form->get_value('home_empty'));
        $game->set_game_away_goals($this->form->get_value('away_goals'));
        $game->set_game_away_yellow($this->form->get_value('away_yellow'));
        $game->set_game_away_red($this->form->get_value('away_red'));
        $game->set_game_away_empty($this->form->get_value('away_empty'));

        $game->set_game_video(new Url($this->form->get_value('video')));
        $game->set_game_summary($this->form->get_value('summary'));
        $game->set_game_status($this->form->get_value('status')->get_raw_value());

        if($this->get_params()->get_bonus())
        {
            $game->set_game_home_off_bonus($this->form->get_value('home_off_bonus'));
            $game->set_game_away_off_bonus($this->form->get_value('away_off_bonus'));
            if($this->get_params()->get_bonus() == ScmParams::BONUS_DOUBLE)
            {
                $game->set_game_home_def_bonus($this->form->get_value('home_def_bonus'));
                $game->set_game_away_def_bonus($this->form->get_value('away_def_bonus'));
            }
        }

        ScmGameService::update_game($game, $game->get_id_game());

		ScmEventService::clear_cache();
	}

    private function get_game()
    {
        $request = AppContext::get_request();
        $ty = $request->get_getstring('type', '');
        $gr = $request->get_getint('group', 0);
        $ro = $request->get_getint('round', 0);
        $or = $request->get_getint('order', 0);
        $game = ScmGameService::get_game($this->event_id(), $ty, $gr, $ro, $or);

        return $game;
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

    private function get_group_teams_list($group)
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

    private function get_teams_list()
    {
        $options = [];

        $clubs = ScmClubCache::load();
        $options[] = new FormFieldSelectChoiceOption('', '');
		foreach(ScmTeamService::get_teams($this->event_id()) as $team)
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
        $request = AppContext::get_request();
		$location_id = $event->get_id() ? 'scm-edit-details-'. $event->get_id() : '';

		// $response = new SiteDisplayResponse($view, $location_id);
		$response = new SiteDisplayResponse($view);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());

		if (!AppContext::get_session()->location_id_already_exists($location_id))
            $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['scm.games.management'], $this->lang['scm.module.title']);
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.games.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_groups_games($event->get_id(), $event->get_event_slug()));

        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($event->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        $category = $event->get_category();
        $breadcrumb->add($event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));
        if ($request->get_value('type') == 'G')
            $link = ScmUrlBuilder::edit_groups_games($event->get_id(), $event->get_event_slug(), $request->get_value('group'));
        elseif ($request->get_value('type') == 'B')
            $link = ScmUrlBuilder::edit_brackets_games($event->get_id(), $event->get_event_slug(), $request->get_value('group'));
        elseif ($request->get_value('type') == 'D')
            $link = ScmUrlBuilder::edit_days_games($event->get_id(), $event->get_event_slug(), $request->get_value('group'));
        $breadcrumb->add($this->lang['scm.games.management'], $link);
        $breadcrumb->add($this->lang['scm.game.details'], ScmUrlBuilder::edit_details_game($this->event_id(), $this->get_event()->get_event_slug(), $request->get_value('type'), $request->get_value('group'), $request->get_value('round'), $request->get_value('order')));

		return $response;
	}
}
?>
