<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmEventHomeController extends DefaultModuleController
{
    private $event;

	protected function get_template_to_use()
	{
		return new FileTemplate('scm/ScmEventHomeController.tpl');
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
		$event           = $this->get_event();
        $teams_number    = ScmTeamService::get_teams_number($this->event_id());
        $teams_per_group = ScmParamsService::get_params($this->event_id())->get_teams_per_group();

        $c_has_games    = ScmGameService::has_games($this->event_id());
        $c_championship = $c_has_games && ScmEventService::get_event_type($this->event_id()) == ScmDivision::CHAMPIONSHIP;
        $c_cup          = $c_has_games && ScmEventService::get_event_type($this->event_id()) == ScmDivision::CUP;
        $c_tournament   = $c_has_games && ScmEventService::get_event_type($this->event_id()) == ScmDivision::TOURNAMENT;

        $this->view->put_all([
            'C_CHAMPIONSHIP' => $c_championship,
            'C_CUP'          => $c_cup,
            'C_TOURNAMENT'   => $c_tournament,
            'C_HAS_GAMES'    => $c_has_games 
        ]);

        $this->view->put_all(array_merge(
            $event->get_template_vars(),
            [
                'MENU'                => ScmMenuService::build_event_menu($this->event_id()),
                // Rounds
                'DAYS_CALENDAR'       => $c_championship ? ScmEventHomeService::build_days_calendar($this->event_id()) : '',
                'ROUNDS_CALENDAR'     => $c_tournament ? ScmEventHomeService::build_rounds_calendar($this->event_id()) : '',

                'NOT_VISIBLE_MESSAGE' => MessageHelper::display($this->lang['warning.element.not.visible'], MessageHelper::WARNING),
            ]
        ));
	}

	private function get_event()
	{
		if ($this->event === null)
		{
			$id = AppContext::get_request()->get_getint('event_id', 0);
			if (!empty($id))
			{
				try {
					$this->event = ScmEventService::get_event($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->event = new ScmEvent();
		}
		return $this->event;
	}

    private function event_id()
    {
        return $this->get_event()->get_id();
    }

	private function count_views_number(HTTPRequestCustom $request)
	{
		if (!$this->event->is_published())
		{
			$this->view->put('NOT_VISIBLE_MESSAGE', MessageHelper::display($this->lang['warning.element.not.visible'], MessageHelper::WARNING));
		}
		else
		{
			if ($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), ScmUrlBuilder::event_home($this->event->get_id(), $this->event->get_event_slug())->rel()))
			{
				$this->event->set_views_number($this->event->get_views_number() + 1);
				ScmEventService::update_views_number($this->event);
			}
		}
	}

	private function check_authorizations()
	{
		$event = $this->get_event();

		$current_user = AppContext::get_current_user();
		$not_authorized = !ScmAuthorizationsService::check_authorizations($event->get_id_category())->moderation() && !ScmAuthorizationsService::check_authorizations($event->get_id_category())->write();

		switch ($event->get_publishing_state()) {
			case ScmEvent::PUBLISHED:
				if (!ScmAuthorizationsService::check_authorizations($event->get_id_category())->read())
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case ScmEvent::NOT_PUBLISHED:
				if ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case ScmEvent::DEFERRED_PUBLICATION:
				if (!$event->is_published() && ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL)))
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
		$event = $this->get_event();
		$category = $event->get_category();
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($event->get_event_name(), ($category->get_id() != Category::ROOT_CATEGORY ? $category->get_name() . ' - ' : '') . $this->lang['scm.module.title']);
		$graphical_environment->get_seo_meta_data()->set_description('');
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));

		// if ($event->has_thumbnail())
		// 	$graphical_environment->get_seo_meta_data()->set_picture_url($event->get_thumbnail());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'],ScmUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($event->get_id_category(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));
		$breadcrumb->add($this->lang['scm.infos']);

		return $response;
	}
}
?>
