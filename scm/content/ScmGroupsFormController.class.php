<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmGroupsFormController extends DefaultModuleController
{
    private $event;
    private $params;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

        $this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
            $this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['scm.warning.group.update'], MessageHelper::SUCCESS, 4));
		    // AppContext::get_response()->redirect(ScmUrlBuilder::edit_groups($this->get_event()->get_id(), $this->get_event()->get_event_slug(), AppContext::get_request()->get_getint('cluster', 0)));
        }

		$this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),
            'CONTENT' => $this->form->display(),
            'HAS_GAMES_WARNING' => ScmGameService::has_games($this->event_id()) ? MessageHelper::display($this->lang['scm.warning.has.games'], MessageHelper::NOTICE) : MessageHelper::display('', '')
        ]);

		return $this->generate_response($this->view);
	}

	private function build_form()
	{
		$form = new HTMLForm(self::class);
        $form->set_css_class('cell-flex cell-columns-4');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['scm.groups.management'] . '</div>');

        $groups_number = $this->get_params()->get_groups_number();
        $teams = ScmTeamService::get_teams($this->event_id());
        $groups = array_fill(0, $groups_number, []);
        for ($i = 0; $i < count($teams); $i++) {
            $group_index = floor($i / (count($teams) / count($groups)));
            $groups[$group_index][] = $teams[$i];
        }

        for ($i = 1; $i <= count($groups); $i++)
        {
            $fieldset = new FormFieldsetHTML('group_' . $i, $this->lang['scm.group'] . ' ' . ScmGroupService::ntl($i));
            $form->add_fieldset($fieldset);

            for ($j = 1; $j <= count($groups[$i-1]); $j++)
            {
                $team_id = ScmTeamService::get_team_in_group($this->event_id(), $i, $j) ? ScmTeamService::get_team_in_group($this->event_id(), $i, $j)->get_id_team() : '';
                $fieldset->add_field(new FormFieldSimpleSelectChoice('group_teams_' . $i . $j, '', $team_id,
                    $this->get_teams_list($this->event_id()),
                    ['class' => 'groups-select']
                ));
            }
        }

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function save()
	{
        $groups_number = $this->get_params()->get_groups_number();
        $teams = ScmTeamService::get_teams($this->event_id());
        $groups = array_fill(0, $groups_number, []);
        for ($i = 0; $i < count($teams); $i++) {
            $group_index = floor($i / (count($teams) / count($groups)));
            $groups[$group_index][] = $teams[$i];
        }

        // unselect all teams to manage changes
        foreach(ScmTeamService::get_teams($this->event_id()) as $team)
        {
            ScmTeamService::update_team_group($team['id_team'], null, null);
        }

        $count_teams = 0;
        for ($i = 1; $i <= count($groups); $i++)
        {
            for ($j = 1; $j <= count($groups[$i-1]); $j++)
            {
                $id = $this->form->get_value('group_teams_' . $i . $j)->get_raw_value();
                ScmTeamService::update_team_group($id, $i, $j);
                if ($this->form->get_value('group_teams_' . $i . $j)->get_raw_value())
                    $count_teams++;
            }
        }

        if (ScmGameService::has_games($this->event_id()))
            ScmGameService::delete_games($this->event_id());
        if ($this->get_params()->get_finals_type() == ScmParams::FINALS_RANKING)
        {
            ScmGroupService::set_groups_games($this->event_id());
            ScmGroupService::set_groups_finals_games($this->event_id());
        }
        elseif ($this->get_params()->get_finals_type() == ScmParams::FINALS_ROUND)
        {
            if ($this->get_params()->get_hat_ranking())
                ScmGroupService::set_hat_days_games($this->event_id(), $this->get_params()->get_hat_days(), ScmTeamService::get_teams_number($this->event_id()));
            else
                ScmGroupService::set_groups_games($this->event_id());
            ScmBracketService::set_bracket_games($this->event_id(), ScmParamsService::get_params($this->event_id())->get_rounds_number());
        }
        else
            ScmBracketService::set_bracket_games($this->event_id(), ScmParamsService::get_params($this->event_id())->get_rounds_number());

		ScmEventService::clear_cache();
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

        $location_id = $event->get_id() ? 'scm-group-'. $event->get_id() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		if (!AppContext::get_session()->location_id_already_exists($location_id))
            $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['scm.groups.management'], $event->get_event_name() . ($category->get_id() != Category::ROOT_CATEGORY ? ' - ' . $category->get_name() : '') . ' - ' . $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.groups.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_groups($event->get_id(), $event->get_event_slug()));

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
		$breadcrumb->add($event->get_is_sub() ? ScmDivisionService::get_division($event->get_division_id())->get_division_name() : $event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug(),  AppContext::get_request()->get_getint('cluster', 0)));
		$breadcrumb->add($this->lang['scm.groups.management'], ScmUrlBuilder::edit_groups($event->get_id(), $event->get_event_slug(),  AppContext::get_request()->get_getint('cluster', 0)));

		return $response;
	}
}
?>
