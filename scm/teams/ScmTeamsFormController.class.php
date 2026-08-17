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

                // Remove doubles
                const seen = new Set();
                const elements = document.querySelectorAll(".form-field-checkbox");

                elements.forEach(el => {
                    const id = el.id;
                    if (!id) return; // ignore elements without id

                    if (seen.has(id)) {
                        el.remove(); // double => removed from the DOM
                    } else {
                        seen.add(id); // first occurrence => kept and marked as seen
                    }
                });

                // Reorder checkboxes with district categories
                   // Select the main container
                    const container = document.getElementById("onblurContainerResponseScmTeamsFormController_teams");
                    container.style.columns = "unset";

                    // Function to find the most precise parent for a checkbox
                    const findPreciseParent = (checkbox) => {
                        const checkboxSort = checkbox.getAttribute("data-sort");
                        if (!checkboxSort) return null;

                        // Extract the part after the first "_" (e.g., "fr_lfna_dordogne-perigord" from "1_fr_lfna_dordogne-perigord")
                        const parts = checkboxSort.split("_").slice(1);
                        let parentSort = `checkbox-title_${parts.join("_")}`;

                        // Search for the most precise parent that exists in the DOM
                        while (parentSort) {
                            const parentDetails = container.querySelector(`details[data-sort="${parentSort}"]`);
                            if (parentDetails) {
                                return parentDetails;
                            }
                            // If the parent does not exist, go up one level (e.g., "checkbox-title_fr_lfna_dordogne-perigord" -> "checkbox-title_fr_lfna")
                            parts.pop();
                            parentSort = parts.length ? `checkbox-title_${parts.join("_")}` : null;
                        }


                        return null;
                    };


                    // Function to check if an element is an ancestor of another
                    const isAncestor = (parent, child) => {
                        while (child) {
                            if (child === parent) return true;
                            child = child.parentNode;
                        }
                        return false;
                    };

                    // First pass: Convert all checkbox-title elements to <details>
                    const checkboxTitles = Array.from(container.querySelectorAll("[id$=_checkbox-title]"));
                    checkboxTitles.forEach(element => {
                        const details = document.createElement("details");
                        details.id = element.id;
                        details.setAttribute("open", "");

                        // Copy the data-sort attribute from the original element to the new details element
                        const sortValue = element.getAttribute("data-sort");
                        if (sortValue) {
                            details.setAttribute("data-sort", sortValue);
                        }

                        const heading = element.querySelector("h2, h3, h4");
                        const summary = document.createElement("summary");
                        summary.classList.add("summary-title");
                        if (heading) {
                            summary.appendChild(heading);
                        }

                        // Create a div.col-v-4 for checkboxes only
                        const columnsDiv = document.createElement("div");
                        columnsDiv.className = "col-v-4";

                        details.appendChild(summary);
                        details.appendChild(columnsDiv);

                        container.insertBefore(details, element);
                        container.removeChild(element);
                    });

                    // Second pass: Nest all <details> elements in their respective parent containers
                    const allDetails = Array.from(container.querySelectorAll("details"));
                    for (let i = allDetails.length - 1; i >= 0; i--) {
                        const details = allDetails[i];
                        const sortValue = details.getAttribute("data-sort");
                        if (!sortValue) continue;

                        const sortParts = sortValue.split("_");
                        if (sortParts.length <= 2) continue; // Skip root-level details (e.g., "checkbox-title_de")

                        // For nested details like "checkbox-title_fr_lfna_dordogne-perigord"
                        // Find the parent by removing the last part of the sort value
                        const parentSortValue = `checkbox-title_${sortParts.slice(1, -1).join("_")}`;
                        const parentElement = container.querySelector(`details[data-sort="${parentSortValue}"]`);

                        if (parentElement) {
                            const columnsDiv = parentElement.querySelector(".col-v-4");
                            if (columnsDiv && !isAncestor(details, parentElement)) {
                                parentElement.insertBefore(details, columnsDiv.nextSibling);
                            }
                        }
                    }


                    // Third pass: Move .form-field-checkbox into their most precise parent
                    const checkboxesList = Array.from(container.querySelectorAll(".form-field-checkbox"));
                    checkboxesList.forEach(checkbox => {
                        const parentDetails = findPreciseParent(checkbox);
                        if (!parentDetails) return;


                        const columnsDiv = parentDetails.querySelector(".col-v-4");
                        if (columnsDiv && !isAncestor(checkbox, columnsDiv)) {
                            columnsDiv.appendChild(checkbox);
                        }
                    });
                // Reorder checkboxes with district categories
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

        // 1. Sort countries by their interpreted names
        $otherKeysWithNames = [];
        foreach (array_keys($clubs) as $key) {
            if ($key === 'all' || $key === 'other') {
                continue; // Handle 'all' and 'other' separately
            }
            $interpretedName = LangLoader::get_message($key, 'countries');
            $otherKeysWithNames[$key] = $interpretedName;
        }
        asort($otherKeysWithNames);

        // 2. Rebuild $reordered_clubs with sorted countries
        $reordered_clubs = [];
        if (!empty($allKey)) {
            $reordered_clubs = $allKey;
        }

        foreach (array_keys($otherKeysWithNames) as $countryKey) {
            $reordered_clubs[$countryKey] = $clubs[$countryKey];
        }

        if (!empty($otherKey)) {
            $reordered_clubs = array_merge($reordered_clubs, $otherKey);
        }

        // 3. Iterate through $reordered_clubs and recursively sort leagues and districts
        foreach ($reordered_clubs as $country => $countries)
        {
            if ($country === 'all')
            {
                // Special case for 'all' (unchanged)
                $options[] = new ScmFormFieldMultipleCheckboxOption(
                    'checkbox-title_root',
                    '<h2>' . $this->lang['scm.clubs.countries.team'] . '</h2>',
                    'checkbox-title_root',
                );
                foreach ($countries as $country_club)
                {
                    $options[] = new ScmFormFieldMultipleCheckboxOption(
                        $country_club['id_club'],
                        $country_club['club_name'],
                        $country_club['id_club'] . '_root',
                    );
                }
            }
            else
            {
                $data = ScmClubService::get_district_data($countries['file']);

                // Add country title
                $options[] = new ScmFormFieldMultipleCheckboxOption(
                    'checkbox-title_' . $country,
                    '<h2>' . LangLoader::get_message($country, 'countries') . '</h2>',
                    'checkbox-title_' . $country,
                );

                // 4. Sort leagues by their interpreted names
                $leaguesWithNames = [];
                foreach ($countries as $leagueKey => $leagues)
                {
                    if ($leagueKey === 'file') {
                        continue;
                    }
                    $leagueName = ScmClubService::get_league($data, $leagueKey);
                    $leaguesWithNames[$leagueKey] = $leagueName;
                }
                asort($leaguesWithNames);

                // 5. Iterate through sorted leagues
                foreach (array_keys($leaguesWithNames) as $leagueKey)
                {
                    $leagues = $countries[$leagueKey];

                    // Add league title
                    $options[] = new ScmFormFieldMultipleCheckboxOption(
                        'checkbox-title_' . $country . '_' . $leagueKey,
                        '<h3>' . ScmClubService::get_league($data, $leagueKey) . '</h3>',
                        'checkbox-title_' . $country . '_' . $leagueKey,
                    );

                    // 6. Sort districts by their interpreted names
                    $districtsWithNames = [];
                    foreach ($leagues as $districtKey => $districts)
                    {
                        if (empty($districtKey)) {
                            // Case where there are no districts (e.g., leagues without subcategories)
                            foreach ($districts as $district_club) {
                                $options[] = new ScmFormFieldMultipleCheckboxOption(
                                    $district_club['id_club'],
                                    $district_club['club_name'],
                                    $district_club['id_club'] . '_' . $country . '_' . $leagueKey,
                                );
                            }
                            continue;
                        }

                        $districtName = ScmClubService::get_district($data, $districtKey);
                        $districtsWithNames[$districtKey] = $districtName;
                    }

                    if (!empty($districtsWithNames)) {
                        asort($districtsWithNames);

                        // 7. Iterate through sorted districts
                        foreach (array_keys($districtsWithNames) as $districtKey)
                        {
                            $districts = $leagues[$districtKey];

                            // Add district title
                            $options[] = new ScmFormFieldMultipleCheckboxOption(
                                'checkbox-title_' . $country . '_' . $leagueKey . '_' . $districtKey,
                                '<h4>' . ScmClubService::get_district($data, $districtKey) . '</h4>',
                                'checkbox-title_' . $country . '_' . $leagueKey . '_' . $districtKey,
                            );

                            // Add clubs in the district
                            foreach ($districts as $district_club) {
                                $options[] = new ScmFormFieldMultipleCheckboxOption(
                                    $district_club['id_club'],
                                    $district_club['club_name'],
                                    $district_club['id_club'] . '_' . $country . '_' . $leagueKey . '_' . $districtKey,
                                );
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
