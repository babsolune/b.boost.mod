<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmDaysDelayedController extends DefaultModuleController
{
    private $event;
	protected function get_template_to_use()
	{
		return new FileTemplate('scm/ScmDaysDelayedController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_view();
		$this->check_authorizations();

        $this->view->put_all([
            'MENU' => ScmMenuService::build_event_menu($this->event_id()),
        ]);

		return $this->generate_response();
	}

	private function build_view()
	{
        $games = ScmGameService::get_games($this->event_id());

        usort($games, function ($a, $b) {
            return $a['game_date'] - $b['game_date'];
        });

        $matchdays = [];
        foreach($games as $game)
        {
            if($game['game_status'] == ScmGame::DELAYED)
                $matchdays[Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][] = $game;
        }

        $this->view->put_all([
            'C_ITEMS' => count($games) > 0,
        ]);
        $now = new Date();

        foreach ($matchdays as $matchday => $dates)
        {
            $this->view->assign_block_vars('dates', [
                'DATE' => $matchday,
            ]);
            foreach ($dates as $game)
            {
                $item = new ScmGame();
                $item->set_properties($game);
                $this->view->assign_block_vars('dates.items', array_merge($item->get_template_vars(), [
                    'C_OVERTIME' => $item->get_game_date() < $now,
                    'U_DAY' => ScmUrlBuilder::edit_days_games($this->event_id(), $this->get_event()->get_event_slug(), $item->get_game_cluster())->rel()
                ]));
            }
        }
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
		$graphical_environment->set_page_title($this->lang['scm.check.days'], $event->get_event_name() . ($category->get_id() != Category::ROOT_CATEGORY ? ' - ' . $category->get_name() : '') . ' - ' . $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
		$graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.check.days']);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'],ScmUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($event->get_id_category(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
        if ($event->get_is_sub())
            $breadcrumb->add(ScmEventService::get_master_name($event->get_id()), ScmEventService::get_master_url($event->get_id()));
		$breadcrumb->add($event->get_is_sub() ? ScmDivisionService::get_division($event->get_division_id())->get_division_name() : $event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));
		$breadcrumb->add($this->lang['scm.check.days'], ScmUrlBuilder::days_checker($event->get_id(), $event->get_event_slug()));

		return $response;
	}
}
?>
