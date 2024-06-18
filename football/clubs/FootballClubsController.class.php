<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballClubsController extends DefaultModuleController
{
	private $category;

	protected function get_template_to_use()
	{
		return new FileTemplate('football/FootballClubsController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_view();

		return $this->generate_response();
	}

	private function build_view()
	{
		$now = new Date();
		$cache = FootballClubCache::load();

        // Display categories
		foreach ($cache->get_clubs() as $id => $club)
		{
            $item = new FootballClub();
            $item->set_properties($club);

            $this->view->assign_block_vars('clubs', $item->get_template_vars());
		}
	}

	private function check_authorizations()
	{
        if (!FootballAuthorizationsService::check_authorizations()->read())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }
	}

	private function generate_response()
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
        $graphical_environment->set_page_title($this->lang['football.clubs'], $this->lang['football.module.title']);
		
		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());

		return $response;
	}

	public static function get_view()
	{
		$object = new self('football');
		$object->check_authorizations();
		$object->build_view(AppContext::get_request());
		return $object->view;
	}
}
?>
