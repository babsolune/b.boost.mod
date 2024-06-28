<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmTeamCalendarController extends DefaultModuleController
{
    private $event;
    private $team_id;
    private $team_name;

	protected function get_template_to_use()
	{
		return new FileTemplate('scm/ScmTeamCalendarController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
        $this->init($request);
		$this->build_view();
		$this->check_authorizations();

        $this->view->put('C_ONE_DAY', ScmGameService::one_day_event($this->event_id()));

		return $this->generate_response();
	}

    private function init(HTTPRequestCustom $request)
    {
        $this->team_id = $request->get_getint('team_id', 0);
        $team_club_id = ScmTeamService::get_team($this->team_id)->get_team_club_id();
        $this->team_name = ScmClubCache::load()->get_club_full_name($team_club_id);
    }

	private function build_view()
	{
        $games = ScmGameService::get_team_games($this->event_id(), $this->team_id);

        foreach ($games as $game)
        {
            $item = new ScmGame();
            $item->set_properties($game);

            $this->view->assign_block_vars('games', array_merge($item->get_template_vars(),[
                'DAY' => $item->get_game_group()
            ]));
        }

        $this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),
            'C_HAS_GAMES' => ScmGameService::has_games($this->event_id()),
            'TEAM_NAME' => $this->team_name
        ]);
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

	private function check_authorizations()
	{
		$event = $this->get_event();

		$current_user = AppContext::get_current_user();
		$not_authorized = !ScmAuthorizationsService::check_authorizations($event->get_id_category())->moderation() && !ScmAuthorizationsService::check_authorizations($event->get_id_category())->write() && !ScmAuthorizationsService::check_authorizations($event->get_id_category())->read();

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
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::display_team_calendar($event->get_id(), $event->get_event_slug(), $this->team_id));

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
        $breadcrumb->add($this->team_name, ScmUrlBuilder::display_team_calendar($event->get_id(), $event->get_event_slug(), $this->team_id));

		return $response;
	}
}
?>
