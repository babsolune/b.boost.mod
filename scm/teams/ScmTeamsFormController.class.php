<?php
/**
 * @copyright   &copy; 2005-2026 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2024 06 12
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

		$fieldset->add_field(new ScmFormFieldMultipleCheckbox('teams', '', $this->get_teams_list(), $this->get_clubs_list()));

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
                    const counter = checked_count > 1 ? checked_count + " ' . $this->lang['scm.selected.teams'] . '" : checked_count + " ' . $this->lang['scm.selected.team'] . '";
                    const span_target = document.querySelector(".checkbox-counter");
                    span_target.innerHTML = counter;
                }
                // Add event listener to each checkbox
                checkboxes.forEach((checkbox) => {
                    checkbox.addEventListener("change", count_checked_checkboxes);
                });

                // Call the function initially to get the current count
                count_checked_checkboxes();

                // Reorder checkboxes with district categories
                const container = document.getElementById("onblurContainerResponse'. self::class.'_teams");
                container.style.columns = "unset";

                // 1. Récupérer tous les éléments enfants (directs) du conteneur
                const children = Array.from(container.children);

                // 2. Trouver les indices des éléments avec la classe "checkbox-title"
                const initIndices = [];
                children.forEach((child, index) => {
                    if (child.classList.contains("checkbox-title")) {
                        initIndices.push(index);
                    }
                });

                // 4. Construire les groupes
                const groups = [];
                let start = 0;
                for (let i = 0; i < initIndices.length; i++) {
                    const end = initIndices[i];
                    // Groupe précédent (entre deux "checkbox-title") → on le prend si non vide
                    if (start < end) {
                        groups.push({
                            init: null, // pas de titre pour ce groupe (normalement ne devrait pas arriver)
                            items: children.slice(start, end)
                        });
                    }
                    // Groupe commençant par cet "init"
                    const nextStart = (i + 1 < initIndices.length) ? initIndices[i + 1] : children.length;
                    groups.push({
                        init: children[end],
                        items: children.slice(end + 1, nextStart)
                    });
                    start = nextStart;
                }

                // 5. Vider le conteneur
                container.innerHTML = "";

                // 6. Pour chaque groupe, créer un <details> et l"ajouter au conteneur
                groups.forEach(group => {
                    // Créer le <details>
                    const details = document.createElement("details");
                    details.open = true; // ouvert par défaut

                    // Créer le <summary>
                    const summary = document.createElement("summary");
                    summary.classList.add("summary-title");
                    if (group.init) {
                        // On déplace l"élément "init" à l"intérieur du summary
                        summary.appendChild(group.init);
                    } else {
                        // Cas particulier (normalement pas de groupe sans init)
                        summary.textContent = "Groupe";
                    }
                    details.appendChild(summary);

                    // Créer une div interne pour le contenu avec columns:4
                    const innerDiv = document.createElement("div");
                    innerDiv.style.columns = "4";

                    // Ajouter les éléments du groupe dans innerDiv
                    group.items.forEach(item => {
                        innerDiv.appendChild(item);
                    });

                    details.appendChild(innerDiv);

                    // Ajouter le details au conteneur
                    container.appendChild(details);
                });
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

        $clubs = ScmClubService::sort_club_list($cache->get_clubs());

        foreach ($clubs as $country => $countries)
        {
            if($country == 'all')
            {
                $options[] = new FormFieldMultipleCheckboxOption('checkbox_title', '<h2>' . $this->lang['scm.clubs.countries.team'] . '</h2>');
                foreach ($countries as $country_club)
                {
                    $options[] = new FormFieldMultipleCheckboxOption($country_club['id_club'], ($country_club['club_name']));
                }
            }
            else
            {
                $data = ScmClubService::get_district_data($countries['file']);
                $options[] = new FormFieldMultipleCheckboxOption('checkbox_title', '<h2>' . LangLoader::get_message($country, 'countries') . '</h2>');
                foreach ($countries as $league => $leagues)
                {
                    if ($league !== 'file') {
                        $options[] = new FormFieldMultipleCheckboxOption('checkbox_title', empty($league) ? '<h3>' . $this->lang['scm.clubs.leagues.none'] . '</h3>' : '<h3>' . ScmClubService::get_league($data, $league) . '</h3>');
                    }
                    if (empty($league))
                    {
                        foreach ($leagues as $root_league_clubs)
                        {
                            foreach ($root_league_clubs as $root_league_club)
                            {
                                $options[] = new FormFieldMultipleCheckboxOption($root_league_club['id_club'], ($root_league_club['club_name']));
                            }
                        }
                    }
                    elseif ($league !== 'file')
                    {
                        foreach ($leagues as $district => $districts)
                        {
                            $options[] = new FormFieldMultipleCheckboxOption('checkbox_title', empty($district) ? '<h4>' . $this->lang['scm.clubs.districts.none'] . '</h4>' : '<h4>' . ScmClubService::get_district($data, $district) . '</h4>');
                            if (empty($district))
                            {
                                foreach ($districts as $root_district_club)
                                {
                                    $options[] = new FormFieldMultipleCheckboxOption($root_district_club['id_club'], ($root_district_club['club_name']));
                                }
                            }
                            else
                            {
                                foreach ($districts as $district_club)
                                {
                                    $options[] = new FormFieldMultipleCheckboxOption($district_club['id_club'], ($district_club['club_name']));
                                }
                            }
                        }
                    }
                }
            }
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
