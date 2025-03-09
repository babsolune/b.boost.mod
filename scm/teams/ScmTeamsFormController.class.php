<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmTeamsFormController extends DefaultModuleController
{
    private $event;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
            $event_name = $this->get_event()->get_event_name();
            $teams_number = ScmTeamService::get_teams_number($this->event_id());
            if ($this->event->get_event_type() == ScmEvent::TOURNAMENT || $this->event->get_event_type() == ScmEvent::CUP)
            {
                if (is_numeric($teams_number) && $teams_number % 2 == 0)
                    $this->view->put('MESSAGE_HELPER', MessageHelper::display(StringVars::replace_vars($this->lang['scm.warning.add.teams'], ['teams_number' => $teams_number, 'event_name' => $event_name]), MessageHelper::SUCCESS, 4));
                else
                    $this->view->put('MESSAGE_HELPER', MessageHelper::display(StringVars::replace_vars($this->lang['scm.warning.add.teams.odd'], ['teams_number' => $teams_number, 'event_name' => $event_name]), MessageHelper::SUCCESS, 10));
            }
            else
                $this->view->put('MESSAGE_HELPER', MessageHelper::display(StringVars::replace_vars($this->lang['scm.warning.add.teams'], ['teams_number' => $teams_number, 'event_name' => $event_name]), MessageHelper::SUCCESS, 4));
        }

        $this->view->put_all([
            'MENU'              => ScmMenuService::build_event_menu($this->event_id()),
            'CONTENT'           => $this->form->display(),
            'HAS_GAMES_WARNING' => ScmGameService::has_games($this->event_id()) ? MessageHelper::display($this->lang['scm.warning.teams.has.games'], MessageHelper::NOTICE) : MessageHelper::display('', '')
        ]);

		return $this->generate_response($this->view);
	}

	private function build_form()
	{
		$form = new HTMLForm(self::class);
        $form->set_css_class('teams-checkbox floating-submit');
		$form->set_layout_title('<div class="align-center small">' . $this->lang['scm.associate.clubs'] . '</div>');

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$fieldset = new FormFieldsetHTML('event', '');
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldMultipleCheckbox('teams', '', $this->get_teams_list(), $this->get_clubs_list()));

        $fieldset->add_field(new FormFieldFree('teams_counter', '', '
			<script>
					// Select all checkboxes
                    let checkboxes = document.querySelectorAll("input[type=checkbox]");
                    // Add span to display the counter
                    let target = document.getElementById("'. self::class.'_event");
                    let span = document.createElement("span");
                    span.classList.add("checkbox-counter");
                    target.prepend(span);

                    // Initialize checked count
                    let checked_count = 0;

                    // Function to count checked checkboxes
                    function count_checked_checkboxes() {
                        checked_count = document.querySelectorAll("input[type=checkbox]:checked").length;
                        const counter = checked_count + " ' . $this->lang['scm.selected.teams'] . '";
                        const span_target = document.querySelector(".checkbox-counter");
                        span_target.innerHTML = counter;
                    }
                    // Add event listener to each checkbox
                    checkboxes.forEach((checkbox) => {
                        checkbox.addEventListener("change", count_checked_checkboxes);
                    });

                    // Call the function initially to get the current count
                    count_checked_checkboxes();
			</script>
        '));

		$this->form = $form;
	}

	private function save()
	{
        $team_list = [];
        // Add clubs in teams list
        foreach($this->form->get_value('teams') as $key => $options)
        {
            $team = new ScmTeam;
            $team_list[] = $options->get_id();

            if (!in_array($options->get_id(), $this->get_team_ids()))
            {
                $team->set_team_event_id($this->event_id());
                $team->set_team_club_id($options->get_id());
                $id = ScmTeamService::add_team($team);
                $team->set_id_team($id);
            }
        }

        // Delete team if it's unchecked
        foreach(array_diff($this->get_team_ids(), $team_list) as $club_id)
        {
            ScmTeamService::delete_team($this->event_id(), $club_id);
        }

        ScmEventService::clear_cache();
	}

    private function get_clubs_list()
    {
        $options = [];
		$cache = ScmClubCache::load();
		$clubs_list = $cache->get_clubs();

		$i = 1;
		foreach($clubs_list as $club)
		{
			$options[] = new FormFieldMultipleCheckboxOption($club['id_club'], ($club['club_name']));
			$i++;
		}

		return $options;
    }

    private function get_teams_list()
    {
        $teams = [];
        foreach(ScmTeamService::get_teams($this->event_id()) as $team)
        {
            $teams[] = $team['team_club_id'];
        }
		return $teams;
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

    private function get_team_ids()
    {
        $teams = ScmTeamService::get_teams($this->event_id());
        $team_ids = [];
        foreach($teams as $id => $team_id)
        {
            $team_ids[] = $team_id['team_club_id'];
        }
        return $team_ids;
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
		$location_id = $event->get_id() ? 'scm-team-'. $event->get_id() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		if (!AppContext::get_session()->location_id_already_exists($location_id))
            $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['scm.teams.management'], $event->get_event_name() . ($category->get_id() != Category::ROOT_CATEGORY ? ' - ' . $category->get_name() : '') . ' - ' . $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.teams.management']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_teams($this->event_id(), $event->get_event_slug()));

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
		$breadcrumb->add($this->lang['scm.teams.management'], ScmUrlBuilder::edit_teams($this->event_id(), $event->get_event_slug()));

		return $response;
	}
}
?>
