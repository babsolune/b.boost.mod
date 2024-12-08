<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmExplorerController extends DefaultModuleController
{
	private $category;

	protected function get_template_to_use()
	{
		return new FileTemplate('scm/ScmExplorerController.tpl');
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

        $this->view->put_all([
            'C_CURRENT_GAMES_CONFIG' => ScmConfig::load()->get_current_games(),
            'C_CURRENT_GAMES' => count(ScmGameService::get_current_games()) > 0
        ]);
        // Display current games
        foreach(ScmGameService::get_current_games() as $current_game)
        {
            $game = new ScmGame();
            $game->set_properties($current_game);
            $this->view->assign_block_vars('current_games', array_merge($game->get_template_vars(), [
                'C_TYPE_GROUP'   => $game->get_game_type() == 'G',
                'C_TYPE_BRACKET' => $game->get_game_type() == 'B',
                'C_TYPE_DAY'     => $game->get_game_type() == 'D',

                'GROUP'      => ScmGroupService::ntl($game->get_game_cluster()),
                'BRACKET'    => ScmBracketService::ntl($game->get_game_cluster()),
                'DAY'        => $game->get_game_cluster(),
                'EVENT_NAME' => ScmEventService::get_event($game->get_game_event_id())->get_event_name(),
                'U_EVENT'    => ScmUrlBuilder::event_home($game->get_game_event_id(), ScmEventService::get_event_slug($game->get_game_event_id()))->rel()
            ]));
        }

		$now = new Date();
        $cache = ScmGameCache::load();

        // Next games
        $next_events = $next_matchdays = $next_event_games = [];

        $next_matchdays = array_filter($cache->get_games(), function($game) use ($now) {
            return $game['game_date'] > $now->get_timestamp();
        });

        foreach ($next_matchdays as $game)
        {
            $event_id = $game['game_event_id'];
            $favorite_team = ScmParamsService::get_params($event_id)->get_favorite_team_id();
            if ($favorite_team && ($game['game_home_id'] == $favorite_team || $game['game_away_id'] == $favorite_team))
                $next_event_games[$event_id][] = $game;
        }

        foreach ($next_event_games as $games) {
            usort($games, function($a, $b) {
                return $a['game_date'] - $b['game_date'];
            });
            $next_events[] = $games[0];
        }

        $next_categories = [];
        foreach($next_events as $game)
        {
            $category = ScmEventService::get_event($game['game_event_id'])->get_category();
            $next_categories[$category->get_id()][] = $game;
        }
        $this->view->put_all([
			'C_NEXT_ITEMS' => count($next_events) > 0
		]);

        ksort($next_categories);

		foreach ($next_categories as $k => $games)
		{
            $category = CategoriesService::get_categories_manager()->get_categories_cache()->get_category($k);
            $this->view->assign_block_vars('next_categories', [
                'CATEGORY_NAME' => $category->get_name(),
                'U_CATEGORY' => ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel()
            ]);

            foreach ($games as $game)
            {
                $item = new ScmGame();
                $item->set_properties($game);

                $this->view->assign_block_vars('next_categories.next_items', array_merge($item->get_template_vars(), [
                    'C_LATE'         => $item->get_game_cluster() < ScmDayService::get_last_day($item->get_game_event_id()),
                    'YEAR'           => date('y', $item->get_game_date()->get_timestamp()),
                    'C_IS_SUB'       => ScmEventService::is_sub_event($item->get_game_event_id()),
                    'MASTER_EVENT'   => ScmEventService::get_master_division($item->get_game_event_id()),
                    'U_MASTER_EVENT' => ScmEventService::get_master_url($item->get_game_event_id()),
                    'U_EVENT'        => ScmUrlBuilder::event_home($item->get_game_event_id(), ScmEventService::get_event_slug($item->get_game_event_id()))->rel()
                ]));
            }
		}

        // Previous games
        $prev_events = $prev_matchdays = $prev_event_games = [];

        $prev_matchdays = array_filter($cache->get_games(), function($game) use ($now) {
            $is_sub = ScmEventService::get_event($game['game_event_id'])->get_is_sub();
            $real_event_id = $is_sub ? ScmEventService::get_event($game['game_event_id'])->get_master_id() : $game['game_event_id'];
            $is_last_event_id = $is_sub ? ScmEventService::is_last_sub($real_event_id, $game['game_event_id']) : 0;
            if ($is_sub && $is_last_event_id)
                return ( $game['game_date'] < $now->get_timestamp() && $now->get_timestamp() < ScmEventService::get_event($real_event_id)->get_end_date()->get_timestamp());
            else
                return ($now->get_timestamp() < ScmEventService::get_event($game['game_event_id'])->get_end_date()->get_timestamp()) && $game['game_date'] < $now->get_timestamp();
        });

        foreach ($prev_matchdays as $game)
        {
            $event_id = $game['game_event_id'];
            $favorite_team = ScmParamsService::get_params($event_id)->get_favorite_team_id();
            if ($favorite_team && ($game['game_home_id'] == $favorite_team || $game['game_away_id'] == $favorite_team))
                $prev_event_games[$event_id][] = $game;
        }

        foreach ($prev_event_games as $games) {
            usort($games, function($a, $b) {
                return $a['game_date'] - $b['game_date'];
            });
            $prev_events[] = end($games);
        }

        $this->view->put_all([
			'C_PREV_ITEMS' => count($prev_events) > 0
		]);

        $prev_categories = [];
        foreach($prev_events as $game)
        {
            $category = ScmEventService::get_event($game['game_event_id'])->get_category();
            $prev_categories[$category->get_id()][] = $game;
        }

        ksort($prev_categories);

		foreach ($prev_categories as $k => $games)
		{
            $category = CategoriesService::get_categories_manager()->get_categories_cache()->get_category($k);
            $this->view->assign_block_vars('prev_categories', [
                'CATEGORY_NAME' => $category->get_name(),
                'U_CATEGORY' => ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel()
            ]);

            foreach ($games as $game)
            {
                $item = new ScmGame();
                $item->set_properties($game);

                $this->view->assign_block_vars('prev_categories.prev_items', array_merge($item->get_template_vars(), [
                    'C_LATE'         => $item->get_game_cluster() < ScmDayService::get_last_day($item->get_game_event_id()),
                    'YEAR'           => date('y', $item->get_game_date()->get_timestamp()),
                    'C_IS_SUB'       => ScmEventService::is_sub_event($item->get_game_event_id()),
                    'MASTER_EVENT'   => ScmEventService::get_master_division($item->get_game_event_id()),
                    'U_MASTER_EVENT' => ScmEventService::get_master_url($item->get_game_event_id()),
                    'U_EVENT'        => ScmUrlBuilder::event_home($item->get_game_event_id(), ScmEventService::get_event_slug($item->get_game_event_id()))->rel()
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
        if (!ScmAuthorizationsService::check_authorizations($this->get_category()->get_id())->read())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }
	}

	private function generate_response()
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
        $graphical_environment->set_page_title($this->lang['scm.module.title']);
		// $description = StringVars::replace_vars($this->lang['scm.seo.description.root'], ['site' => GeneralConfig::load()->get_site_name()]);
		// $graphical_environment->get_seo_meta_data()->set_description($description);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name()));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());

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
