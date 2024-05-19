<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballClubController extends DefaultModuleController
{
    private $club;

	protected function get_template_to_use()
	{
		return new FileTemplate('football/FootballClubController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_view();
		$this->check_authorizations();

		return $this->generate_response();
	}

	private function get_club()
	{
		if ($this->club === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->club = FootballClubService::get_club($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->club = new FootballClub();
		}
		return $this->club;
	}

	private function build_view()
	{
		$config = FootballConfig::load();
		$club = $this->get_club();

		$this->view->put_all($club->get_template_vars());
	}

	private function check_authorizations()
	{
		$club = $this->get_club();
	}

	private function generate_response()
	{
		$club = $this->get_club();
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		// $graphical_environment->set_page_title($club->get_club_name(), ($category->get_id() != Category::ROOT_CATEGORY ? $category->get_name() . ' - ' : '') . $this->lang['football.module.title']);
		// $graphical_environment->get_seo_meta_data()->set_description('');
		// $graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $club->get_id_club(), $club->get_slug()));

		// if ($club->has_thumbnail())
		// 	$graphical_environment->get_seo_meta_data()->set_picture_url($club->get_thumbnail());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'],FootballUrlBuilder::home());
		$breadcrumb->add($this->lang['football.clubs.manager'], FootballUrlBuilder::manage_clubs());
		$breadcrumb->add($club->get_club_name(), FootballUrlBuilder::display_club($club->get_id_club()));

		return $response;
	}
}
?>
