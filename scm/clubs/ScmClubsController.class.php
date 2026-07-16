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

        $clubs = ScmClubService::sort_club_list($cache->get_clubs());

        foreach ($clubs as $country => $countries)
        {
            if($country == 'all')
            {
                $this->view->assign_block_vars('countries', [
                    'COUNTRY_NAME' => $this->lang['scm.clubs.countries.team']
                ]);

                foreach ($countries as $country_club)
                {
                    $item = new ScmClub();
                    $item->set_properties($country_club);
                    $this->view->assign_block_vars('countries.items', $item->get_template_vars());
                }
            }
            else
            {
                $data = ScmClubService::get_district_data($countries['file']);
                $this->view->assign_block_vars('countries', [
                    'COUNTRY_NAME' => LangLoader::get_message($country, 'countries')
                ]);

                foreach ($countries as $league => $leagues)
                {
                    $this->view->assign_block_vars('countries.leagues', [
                        'C_LEAGUE' => !empty($league) && $league !== 'file',
                        'LEAGUE_NAME' => ScmClubService::get_league($data, $league),
                    ]);

                    if (empty($league))
                    {
                        foreach ($leagues as $root_league_clubs)
                        {
                            foreach ($root_league_clubs as $root_league_club)
                            {
                                $item = new ScmClub();
                                $item->set_properties($root_league_club);
                                $this->view->assign_block_vars('countries.items', $item->get_template_vars());
                            }
                        }
                    }
                    elseif ($league !== 'file')
                    {
                        foreach ($leagues as $district => $districts)
                        {
                            if (empty($district))
                            {
                                foreach ($districts as $root_district_club)
                                {
                                    $item = new ScmClub();
                                    $item->set_properties($root_district_club);
                                    $this->view->assign_block_vars('countries.leagues.items', $item->get_template_vars());
                                }
                            }
                            else
                            {
                                $this->view->assign_block_vars('countries.leagues.districts', [
                                    'C_DISTRICT_NAME' => $district,
                                    'DISTRICT_NAME' => ScmClubService::get_district($data, $district)
                                ]);
                                foreach ($districts as $district_club)
                                {
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
		$object->build_view(AppContext::get_request());
		return $object->view;
	}
}
?>
