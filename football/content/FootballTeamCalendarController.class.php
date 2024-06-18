<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballTeamCalendarController extends DefaultModuleController
{
    private $compet;
    private $team_id;
    private $team_name;

	protected function get_template_to_use()
	{
		return new FileTemplate('football/FootballTeamCalendarController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
        $this->init($request);
		$this->build_view();
		$this->check_authorizations();

        $this->view->put('C_ONE_DAY', FootballMatchService::one_day_compet($this->compet_id()));

		return $this->generate_response();
	}

    private function init(HTTPRequestCustom $request)
    {
        $this->team_id = $request->get_getint('team_id', 0);
        $team_club_id = FootballTeamService::get_team($this->team_id)->get_team_club_id();
        $this->team_name = FootballClubCache::load()->get_club_full_name($team_club_id);
    }

	private function build_view()
	{
        $matches = FootballMatchService::get_team_matches($this->compet_id(), $this->team_id);

        foreach ($matches as $match)
        {
            $item = new FootballMatch();
            $item->set_properties($match);

            $this->view->assign_block_vars('matches', $item->get_array_tpl_vars());
        }

        $this->view->put_all(array(
            'MENU' => FootballMenuService::build_compet_menu($this->compet_id()),
            'C_HAS_MATCHES' => FootballMatchService::has_matches($this->compet_id()),
            'TEAM_NAME' => $this->team_name
        ));
	}

	private function get_compet()
	{
		if ($this->compet === null)
		{
			$id = AppContext::get_request()->get_getint('compet_id', 0);
			if (!empty($id))
			{
				try {
					$this->compet = FootballCompetService::get_compet($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->compet = new FootballCompet();
		}
		return $this->compet;
	}

    private function compet_id()
    {
        return $this->get_compet()->get_id_compet();
    }

	private function check_authorizations()
	{
		$compet = $this->get_compet();

		$current_user = AppContext::get_current_user();
		$not_authorized = !FootballAuthorizationsService::check_authorizations($compet->get_id_category())->moderation() && !FootballAuthorizationsService::check_authorizations($compet->get_id_category())->write() && (!FootballAuthorizationsService::check_authorizations($compet->get_id_category())->contribution() || $compet->get_author_user()->get_id() != $current_user->get_id());

		switch ($compet->get_publishing_state()) {
			case FootballCompet::PUBLISHED:
				if (!FootballAuthorizationsService::check_authorizations($compet->get_id_category())->read())
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case FootballCompet::NOT_PUBLISHED:
				if ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case FootballCompet::DEFERRED_PUBLICATION:
				if (!$compet->is_published() && ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL)))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			default:
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			break;
		}
	}

	private function generate_response()
	{
		$compet = $this->get_compet();
		$category = $compet->get_category();
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($compet->get_compet_name(), ($category->get_id() != Category::ROOT_CATEGORY ? $category->get_name() . ' - ' : '') . $this->lang['football.module.title']);
		$graphical_environment->get_seo_meta_data()->set_description('');
		$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::compet_home($compet->get_id_compet()));

		// if ($compet->has_thumbnail())
		// 	$graphical_environment->get_seo_meta_data()->set_picture_url($compet->get_thumbnail());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'],FootballUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($compet->get_id_category(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), FootballUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::compet_home($compet->get_id_compet()));
        $breadcrumb->add($this->team_name, FootballUrlBuilder::display_team_calendar($compet->get_id_compet(), $this->team_id));

		return $response;
	}
}
?>
