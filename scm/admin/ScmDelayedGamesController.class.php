<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmDelayedGamesController extends DefaultModuleController
{
    private $category;
	protected function get_template_to_use()
	{
		return new FileTemplate('scm/ScmDelayedGamesController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_view($request);

		return $this->generate_response();
	}

	private function build_view(HTTPRequestCustom $request)
	{
		$now = new Date();
		$categories = CategoriesService::get_categories_manager(self::$module_id)->get_categories_cache()->get_categories();
		$authorized_categories = CategoriesService::get_authorized_categories(Category::ROOT_CATEGORY, true, self::$module_id);

        $root_events = $events = $games = [];
		foreach ($categories as $id => $category)
		{
			if ($id == Category::ROOT_CATEGORY)
			{
				$this->view->put_all([
					'C_ROOT_CONTROLS'      => ScmAuthorizationsService::check_authorizations($id)->moderation(),
                    'C_ROOT_ITEMS'         => $category->get_elements_number() > 0,
					'C_SEVERAL_ROOT_ITEMS' => $category->get_elements_number() > 1,
				]);

				$result = PersistenceContext::get_querier()->select('SELECT *
				FROM ' . ScmSetup::$scm_event_table . '
				WHERE id_category = :id_category
				AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
                ORDER BY id_category', [
					'timestamp_now' => $now->get_timestamp(),
					'id_category' => $category->get_id()
				]);

				while ($row = $result->fetch()) {
					$root_event_item = new ScmEvent();
					$root_event_item->set_properties($row);

                    $root_events[] = ScmGameService::get_games($root_event_item->get_id());
				}
				$result->dispose();
			}

			if ($id != Category::ROOT_CATEGORY && in_array($id, $authorized_categories))
			{
				$result = PersistenceContext::get_querier()->select('SELECT *
                    FROM ' . ScmSetup::$scm_event_table . '
                    WHERE id_category = :id_category
                    AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
                    ORDER BY id_category', [
                        'id_category' => $category->get_id(),
                        'timestamp_now' => $now->get_timestamp()
                    ]
				);

				while ($row = $result->fetch()) 
                {
                    $event_item = new ScmEvent();
					$event_item->set_properties($row);

                    $events[] = $row;
                }
            }
        }

        foreach (array_merge($root_events, $events) as $event)
        {
            $games[] = ScmGameService::get_games($event['id']);
        }
        $games = call_user_func_array('array_merge', $games);

        usort($games, function ($a, $b) {
            return $a['game_date'] - $b['game_date'];
        });

        $matchdays = [];
        foreach($games as $game)
        {
            if($game['game_status'] == ScmGame::DELAYED)
                $matchdays[Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][] = $game;
        }
        $now = new Date();

        $this->view->put('C_ITEMS', count($games));

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
                    'U_DAY' => ScmUrlBuilder::edit_days_games($event_item->get_id(), $event_item->get_event_slug(), $item->get_game_cluster())->rel()
                ]));
            }
        }
	}

	private function get_category()
	{
		if ($this->category === null)
		{
			$id = AppContext::get_request()->get_getint('id_category', 0);
			if (!empty($id))
			{
				try {
					$this->category = CategoriesService::get_categories_manager('scm')->get_categories_cache()->get_category($id);
				} catch (CategoryNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->category = CategoriesService::get_categories_manager('scm')->get_categories_cache()->get_category(Category::ROOT_CATEGORY);
			}
		}
		return $this->category;
	}

	private function check_authorizations()
	{
        if (!ScmAuthorizationsService::check_authorizations($this->get_category()->get_id())->moderation())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }
	}

	private function generate_response()
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
        $graphical_environment->set_page_title($this->lang['scm.games.late.list'], $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
		$description = StringVars::replace_vars($this->lang['scm.seo.description.running.events'], ['site' => GeneralConfig::load()->get_site_name()]);
		$graphical_environment->get_seo_meta_data()->set_description($description);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name()));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());
		$breadcrumb->add($this->lang['scm.games.late.list'], ScmUrlBuilder::late_games());

		return $response;
	}
}
?>
