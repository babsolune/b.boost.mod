<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmPracticeController extends DefaultModuleController
{
    private $event;
    private $params;
    private $division;
    private $return_games;

    protected function get_template_to_use()
	{
		return new FileTemplate('scm/content/ScmPracticeController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
        if ($this->return_games) 
        {
            $this->build_round_trip_view();
        }
        else
        {
            $this->build_single_view();
        }
        $this->check_authorizations();

        $this->view->put_all([
            'MENU'             => ScmMenuService::build_event_menu($this->event_id()),
            'C_RETURN_GAMES'   => $this->return_games,
            'C_ONE_DAY'        => $this->get_event()->get_oneday(),
            'C_HAS_GAMES'      => ScmGameService::has_games($this->event_id()),
            'C_DISPLAY_PLAYGROUNDS' => $this->get_params($this->event_id())->get_display_playgrounds()
        ]);

		return $this->generate_response();
	}

    private function init()
    {
		$this->division = ScmDivisionCache::load()->get_division($this->get_event()->get_division_id());
        $this->return_games = $this->get_event()->get_event_game_type() == ScmEvent::RETURN_GAMES;
    }

	/** Bracket with return matches */
	private function build_round_trip_view()
	{
        $games = ScmGameService::get_games($this->event_id());

        if($games)
        {
            $chunks = array_chunk($games, ceil(count($games) / 2));
            foreach ($chunks[0] as $game_a)
            {
                if ($game_a['game_home_id'] != 0 && $game_a['game_away_id'] != 0)
                {
                    foreach ($chunks[1] as $game_b)
                    {
                        if(
                            $game_b['game_away_id'] != 0
                            && $game_b['game_home_id'] != 0
                            && $game_a['game_home_id'] == $game_b['game_away_id']
                            && $game_a['game_away_id'] == $game_b['game_home_id']
                        )
                        {
                            $game = new ScmGame();
                            $game->set_properties($game_a);

                            $total_home = (int)$game_a['game_home_score'] + (int)$game_b['game_away_score'];
                            $total_away = (int)$game_a['game_away_score'] + (int)$game_b['game_home_score'];

                            $this->view->assign_block_vars('games', array_merge(
                                $game->get_template_vars(),
                                Date::get_array_tpl_vars($game_a['game_date'], 'game_date_a'),
                                Date::get_array_tpl_vars($game_b['game_date'], 'game_date_b'),
                                [
                                    'C_HOME_WIN' => $total_home > $total_away || $game_b['game_away_pen'] > $game_b['game_home_pen'],
                                    'C_AWAY_WIN' => $total_away > $total_home || $game_b['game_home_pen'] > $game_b['game_away_pen'],
                                    'C_HAS_PEN' => $game_b['game_home_pen'] != '' && $game_b['game_away_pen'] != '',
                                    'GAME_DATE_A_DAY_MONTH' => Date::to_format($game_a['game_date'], Date::FORMAT_DAY_MONTH),
                                    'GAME_DATE_A_YEAR' => date('Y', $game_a['game_date']),
                                    'GAME_DATE_B_DAY_MONTH' => Date::to_format($game_b['game_date'], Date::FORMAT_DAY_MONTH),
                                    'GAME_DATE_B_YEAR' => date('Y', $game_b['game_date']),
                                    'HOME_SCORE_B' => $game_b['game_away_score'],
                                    'HOME_PEN' => $game_b['game_away_pen'],
                                    'AWAY_SCORE_B' => $game_b['game_home_score'],
                                    'AWAY_PEN' => $game_b['game_home_pen'],
                                ]
                            ));
                        }
                    }
                }
                elseif ($game_a['game_home_empty'] != '' && $game_a['game_away_empty'] != '')
                {
                    foreach ($chunks[1] as $game_b)
                    {
                        if(
                            $game_b['game_away_empty'] != ''
                            && $game_b['game_home_empty'] != ''
                            && $game_a['game_home_empty'] == $game_b['game_away_empty']
                            && $game_a['game_away_empty'] == $game_b['game_home_empty']
                        )
                        {
                            $game = new ScmGame();
                            $game->set_properties($game_a);
    
                            $total_home = (int)$game_a['game_home_score'] + (int)$game_b['game_away_score'];
                            $total_away = (int)$game_a['game_away_score'] + (int)$game_b['game_home_score'];
    
                            $this->view->assign_block_vars('games', array_merge(
                                $game->get_template_vars(),
                                Date::get_array_tpl_vars($game_a['game_date'], 'game_date_a'),
                                Date::get_array_tpl_vars($game_b['game_date'], 'game_date_b'),
                                [
                                    'C_HOME_WIN' => $total_home > $total_away || $game_b['game_away_pen'] > $game_b['game_home_pen'],
                                    'C_AWAY_WIN' => $total_away > $total_home || $game_b['game_home_pen'] > $game_b['game_away_pen'],
                                    'C_HAS_PEN' => $game_b['game_home_pen'] != '' && $game_b['game_away_pen'] != '',
                                    'GAME_DATE_A_DAY_MONTH' => Date::to_format($game_a['game_date'], Date::FORMAT_DAY_MONTH),
                                    'GAME_DATE_A_YEAR' => date('Y', $game_a['game_date']),
                                    'GAME_DATE_B_DAY_MONTH' => Date::to_format($game_b['game_date'], Date::FORMAT_DAY_MONTH),
                                    'GAME_DATE_B_YEAR' => date('Y', $game_b['game_date']),
                                    'HOME_SCORE_B' => $game_b['game_away_score'],
                                    'HOME_PEN' => $game_b['game_away_pen'],
                                    'AWAY_SCORE_B' => $game_b['game_home_score'],
                                    'AWAY_PEN' => $game_b['game_home_pen'],
                                ]
                            ));
                        }
                    }
                }
                else {
                    $game = new ScmGame();
                    $game->set_properties($game_a);

                    $this->view->assign_block_vars('games', $game->get_template_vars());
                }
            }
        }
    }

	/** Bracket with single matches */
	private function build_single_view()
	{
        $games = ScmGameService::get_games($this->event_id());

        usort($games, function($a, $b) {
            if ($a['game_date'] == $b['game_date'])
                return $a['game_order'] - $b['game_order'];
            else
                return $a['game_date'] - $b['game_date'];
        });

        $dates = [];
        foreach($games as $game)
        {
            $dates[Date::to_format($game['game_date'], Date::FORMAT_DAY_MONTH_YEAR_TEXT)][] = $game;
        }

        foreach($dates as $date => $games)
        {
            $this->view->assign_block_vars('dates', [
                'DATE' => $date
            ]);

            foreach($games as $game)
            {
                    $item = new ScmGame();
                    $item->set_properties($game);

                    $this->view->assign_block_vars('dates.games', $item->get_template_vars());
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

    private function get_params()
	{
        if (!empty($this->event_id()))
        {
            try {
                $this->params = ScmParamsService::get_params($this->event_id());
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
		return $this->params;
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
		$graphical_environment->set_page_title($this->lang['scm.games.list'], $event->get_event_name() . ($category->get_id() != Category::ROOT_CATEGORY ? ' - ' . $category->get_name() : '') . ' - ' . $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
		$graphical_environment->get_seo_meta_data()->set_description(StringVars::replace_vars($this->lang['scm.seo.description.event.bracket'], ['event' => $event->get_event_name()]));
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
		$breadcrumb->add($this->lang['scm.games.list'], ScmUrlBuilder::display_practice($event->get_id(), $event->get_event_slug()));

		return $response;
	}
}
?>
