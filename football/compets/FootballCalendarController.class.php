<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballCalendarController extends DefaultModuleController
{
    private $compet;

	protected function get_template_to_use()
	{
		return new FileTemplate('football/FootballCalendarController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_view();
		$this->count_views_number($request);
		$this->check_authorizations();

		return $this->generate_response();
	}

	private function build_view()
	{
		$compet = $this->get_compet();
        $teams_number = FootballTeamService::get_teams_number($this->compet_id());
        $teams_per_group = FootballParamsService::get_params($this->compet_id())->get_teams_per_group();

		$this->view->put_all(array(
            'C_CHAMPIONSHIP' => FootballDivisionService::get_division($this->get_compet()->get_compet_division_id())->get_division_compet_type() == FootballDivision::CHAMPIONSHIP,
            'C_CUP' => FootballDivisionService::get_division($this->get_compet()->get_compet_division_id())->get_division_compet_type() == FootballDivision::CUP,
            'C_TOURNAMENT' => FootballDivisionService::get_division($this->get_compet()->get_compet_division_id())->get_division_compet_type() == FootballDivision::TOURNAMENT,
            'C_HAS_MATCHES' => FootballMatchService::has_matches($this->compet_id())
        ));

        $this->view->put_all(array_merge(
            $compet->get_template_vars(),
            array(
                'MENU' => FootballMenuService::build_compet_menu($this->compet_id()),
                // tournament
                'TOURNAMENT_CALENDAR' => FootballCalendarService::build_tournament_calendar($this->compet_id()),
                'JS_DOC' => FootballTournamentService::build_bracket_js_matches($this->compet_id(), $teams_number, $teams_per_group),

                'NOT_VISIBLE_MESSAGE' => MessageHelper::display($this->lang['warning.element.not.visible'], MessageHelper::WARNING),
            )
        ));
	}

	private function get_compet()
	{
		if ($this->compet === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
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

	private function count_views_number(HTTPRequestCustom $request)
	{
		if (!$this->compet->is_published())
		{
			$this->view->put('NOT_VISIBLE_MESSAGE', MessageHelper::display($this->lang['warning.element.not.visible'], MessageHelper::WARNING));
		}
		else
		{
			if ($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), FootballUrlBuilder::calendar($this->compet->get_id_compet())->rel()))
			{
				$this->compet->set_views_number($this->compet->get_views_number() + 1);
				FootballCompetService::update_views_number($this->compet);
			}
		}
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
		$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::calendar($compet->get_id_compet()));

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
		$breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::calendar($compet->get_id_compet()));
		$breadcrumb->add($this->lang['football.calendar']);

		return $response;
	}
}
?>
