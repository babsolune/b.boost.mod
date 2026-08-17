<?php
/**
 * @copyright   &copy; 2005-2026 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmClubsController extends DefaultModuleController
{
	protected function get_template_to_use()
	{
		return new FileTemplate('scm/clubs/ScmClubsController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_view();

		return $this->generate_response();
	}

	private function build_view()
	{
		$cache = ScmClubCache::load();

        // Remove sub-club from the list
        $clubs_list = [];
        foreach($cache->get_clubs() as $club_id => $club)
        {
            if (!$club['club_sub']) {
                $clubs_list[] = $club;
            }
        }

        $clubs = ScmClubService::sort_club_list($clubs_list);

        // Separate keys into 'all', 'other', and the rest
        $allKey = [];
        $otherKey = [];
        $otherKeysWithNames = [];

        foreach (array_keys($clubs) as $key)
        {
            if ($key === 'all') {
                $allKey = [$key => $clubs[$key]];
            } elseif ($key === 'other') {
                $otherKey = [$key => $clubs[$key]];
            } else {
                $interpretedName = LangLoader::get_message($key, 'countries');
                $otherKeysWithNames[$key] = $interpretedName;
            }
        }

        // Sort countries by their interpreted names
        asort($otherKeysWithNames);

        // Rebuild the clubs array in the desired order
        $reordered_clubs = [];

        // Add 'all' first if it exists
        if (!empty($allKey)) {
            $reordered_clubs = $allKey;
        }

        // Add sorted countries
        foreach (array_keys($otherKeysWithNames) as $key)
        {
            $reordered_clubs[$key] = $clubs[$key];
        }

        // Add 'other' last if it exists
        if (!empty($otherKey)) {
            $reordered_clubs = array_merge($reordered_clubs, $otherKey);
        }

        // Iterate through reordered clubs and sort leagues and districts recursively
        foreach ($reordered_clubs as $country => $countries)
        {
            if ($country == 'all') {
                // Handle 'all' case
                $this->view->assign_block_vars('countries', [
                    'COUNTRY_NAME' => $this->lang['scm.clubs.countries.team']
                ]);

                foreach ($countries as $country_club) {
                    $item = new ScmClub();
                    $item->set_properties($country_club);
                    $this->view->assign_block_vars('countries.items', $item->get_template_vars());
                }
            } else {
                // Get district data for the country
                $data = ScmClubService::get_district_data($countries['file']);

                // Assign country name
                $this->view->assign_block_vars('countries', [
                    'COUNTRY_NAME' => LangLoader::get_message($country, 'countries')
                ]);

                // Sort leagues by their interpreted names
                $leaguesWithNames = [];
                foreach ($countries as $leagueKey => $leagues) {
                    if ($leagueKey === 'file') {
                        continue;
                    }
                    $leagueName = ScmClubService::get_league($data, $leagueKey);
                    $leaguesWithNames[$leagueKey] = $leagueName;
                }
                asort($leaguesWithNames);

                // Iterate through sorted leagues
                foreach (array_keys($leaguesWithNames) as $leagueKey) {
                    $leagues = $countries[$leagueKey];

                    // Assign league data
                    $this->view->assign_block_vars('countries.leagues', [
                        'C_LEAGUE' => !empty($leagueKey) && $leagueKey !== 'file',
                        'LEAGUE_NAME' => ScmClubService::get_league($data, $leagueKey),
                    ]);

                    if (empty($leagueKey)) {
                        // Handle leagues without a key (e.g., root-level clubs)
                        foreach ($leagues as $root_league_clubs) {
                            usort($root_league_clubs, function($a, $b) {
                                return strcmp($a['club_full_name'], $b['club_full_name']);
                            });
                            foreach ($root_league_clubs as $root_league_club) {
                                $item = new ScmClub();
                                $item->set_properties($root_league_club);
                                $this->view->assign_block_vars('countries.items', $item->get_template_vars());
                            }
                        }
                    } else {
                        // Sort districts by their interpreted names
                        $districtsWithNames = [];
                        foreach ($leagues as $districtKey => $districts) {
                            if (empty($districtKey)) {
                                // Handle districts without a key (e.g., root-level clubs in a league)
                                usort($districts, function($a, $b) {
                                    return strcmp($a['club_full_name'], $b['club_full_name']);
                                });
                                foreach ($districts as $root_district_club) {
                                    $item = new ScmClub();
                                    $item->set_properties($root_district_club);
                                    $this->view->assign_block_vars('countries.leagues.items', $item->get_template_vars());
                                }
                                continue;
                            }
                            $districtName = ScmClubService::get_district($data, $districtKey);
                            $districtsWithNames[$districtKey] = $districtName;
                        }

                        // Sort districts by their interpreted names
                        asort($districtsWithNames);

                        // Iterate through sorted districts
                        foreach (array_keys($districtsWithNames) as $districtKey) {
                            $districts = $leagues[$districtKey];

                            // Assign district data
                            $this->view->assign_block_vars('countries.leagues.districts', [
                                'C_DISTRICT_NAME' => $districtKey,
                                'DISTRICT_NAME' => ScmClubService::get_district($data, $districtKey)
                            ]);

                            usort($districts, function($a, $b) {
                                return strcmp($a['club_full_name'], $b['club_full_name']);
                            });

                            // Add clubs in the district
                            foreach ($districts as $district_club) {
                                $item = new ScmClub();
                                $item->set_properties($district_club);
                                $this->view->assign_block_vars('countries.leagues.districts.items', $item->get_template_vars());
                            }
                        }
                    }
                }
            }
        }
	}

	private function check_authorizations()
	{
        if (!ScmAuthorizationsService::check_authorizations()->read())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }
	}

	private function generate_response()
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
        $graphical_environment->set_page_title($this->lang['scm.clubs'], $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
        $description = StringVars::replace_vars($this->lang['scm.seo.description.clubs'], ['site' => GeneralConfig::load()->get_site_name()]);
        $graphical_environment->get_seo_meta_data()->set_description($description);

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());
		$breadcrumb->add($this->lang['scm.clubs'], ScmUrlBuilder::display_clubs());

		return $response;
	}

	public static function get_view()
	{
		$object = new self('scm');
		$object->check_authorizations();
		$object->build_view();
		return $object->view;
	}
}
?>
