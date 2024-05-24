<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballTourneyFinalsController extends DefaultModuleController
{
    private $compet;
    private $params;
	protected function get_template_to_use()
	{
		return new FileTemplate('football/FootballTourneyFinalsController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_winner_view();
        if ($this->get_params()->get_all_places())
        $this->build_looser_view();
		$this->check_authorizations();

        $teams_number = FootballTeamService::get_compet_teams_number($this->id_compet());
        $teams_per_group = $this->get_params()->get_teams_per_group();

        $this->view->put_all(array(
            'MENU' => FootballCompetMenuService::build_compet_menu($this->id_compet()),
            'C_ROUNDS_' . $teams_number . '_' . $teams_per_group => true,
            'C_LOOSER_BRACKET' => $this->get_params()->get_looser_bracket(),
            'C_ALL_PLACES' => $this->get_params()->get_all_places(),
        ));

		return $this->generate_response();
	}

	private function build_winner_view()
	{
        $matches = FootballGroupService::match_list_from_group($this->id_compet(), 'W');
        $matches = call_user_func_array('array_merge', $matches);

        foreach ($matches as $match)
        {
            $bracket = TextHelper::substr($match['match_number'], 0, 1);
            $round = TextHelper::substr($match['match_number'], 1, 1);
            $order = TextHelper::substr($match['match_number'], 2, 1);

            $this->view->put_all(array(
                'C_WINNER_MATCHES' => count($matches) > 0,
                'C_M_W'.$round.$order => $match['match_number'] == 'W'.$round.$order,
                'MATCH_W'.$round.$order => FootballTourneyService::build_finals_match($this->id_compet(), $bracket, $round, $order),
            ));
        }
	}

	private function build_looser_view()
	{
        $matches = FootballGroupService::match_list_from_group($this->id_compet(), 'L');
        $matches = call_user_func_array('array_merge', $matches);

        foreach ($matches as $match)
        {
            $bracket = TextHelper::substr($match['match_number'], 0, 1);
            $round = TextHelper::substr($match['match_number'], 1, 1);
            $order = TextHelper::substr($match['match_number'], 2, 1);

            $this->view->put_all(array(
                'C_LOOSER_MATCHES' => count($matches) > 0,
                'NB' => $match['match_number'],
                'C_M_L'.$round.$order => $match['match_number'] == 'L'.$round.$order,
                'MATCH_L'.$round.$order => FootballTourneyService::build_finals_match($this->id_compet(), $bracket, $round, $order),
            ));
        }

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

    private function id_compet()
    {
        return $this->get_compet()->get_id_compet();
    }

    private function get_params()
	{
        $id = $this->id_compet();
        if (!empty($id))
        {
            try {
                $this->params = FootballParamsService::get_params($id);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
		return $this->params;
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
				if ($not_authorized || ($current_user->get_id() == User::AWAYOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case FootballCompet::DEFERRED_PUBLICATION:
				if (!$compet->is_published() && ($not_authorized || ($current_user->get_id() == User::AWAYOR_LEVEL)))
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
		$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $compet->get_id_compet(), $compet->get_compet_slug()));

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
		$breadcrumb->add($compet->get_compet_name(), FootballUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $compet->get_id_compet(), $compet->get_compet_slug()));

		return $response;
	}
}
?>
