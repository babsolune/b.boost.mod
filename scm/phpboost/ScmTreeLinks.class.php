<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmTreeLinks implements ModuleTreeLinksExtensionPoint
{
	public function get_actions_tree_links()
	{
		$module_id = 'scm';
        $config = ScmConfig::load();

		$lang = LangLoader::get_all_langs($module_id);
		$tree = new ModuleTreeLinks();

		$categories = new ModuleLink($lang['category.categories.management'], CategoriesUrlBuilder::manage($module_id), ScmAuthorizationsService::check_authorizations()->manage_events());
			$categories->add_sub_link(new ModuleLink($lang['category.add'], CategoriesUrlBuilder::add(AppContext::get_request()->get_getint('id_category', Category::ROOT_CATEGORY), $module_id), ScmAuthorizationsService::check_authorizations()->manage_events()));
        $tree->add_link($categories);

        $tree->add_link(new ModuleLink($lang['category.categories'], ScmUrlBuilder::display_category(0, 'root'), $config->get_homepage() !== ScmConfig::CATEGORIES && ScmAuthorizationsService::check_authorizations()->read()));
        $tree->add_link(new ModuleLink($lang['scm.around.games'], ScmUrlBuilder::display_game_list(), $config->get_homepage() !== ScmConfig::GAME_LIST && ScmAuthorizationsService::check_authorizations()->read()));
        $tree->add_link(new ModuleLink($lang['scm.current.events'], ScmUrlBuilder::display_event_list(), $config->get_homepage() !== ScmConfig::EVENT_LIST && ScmAuthorizationsService::check_authorizations()->read()));

        $change_event = new ModuleLink($lang['scm.change.event'], '#change_event', ScmAuthorizationsService::check_authorizations()->moderation(), 'bgc moderator');
            foreach ($this->current_event_list($change_event) as $event)
            {
                $event['sub_link'];
            }
        $tree->add_link($change_event);

        $tree->add_link(new ModuleLink($lang['scm.clubs'], ScmUrlBuilder::display_clubs(), ScmAuthorizationsService::check_authorizations()->read()));
        $club = new ModuleLink($lang['scm.clubs.manager'], ScmUrlBuilder::manage_clubs(), ScmAuthorizationsService::check_authorizations()->manage_clubs());
            $club->add_sub_link(new ModuleLink($lang['scm.club.add'], ScmUrlBuilder::add_club(), ScmAuthorizationsService::check_authorizations()->manage_clubs()));
        $tree->add_link($club);

		$event = new ModuleLink($lang['scm.events.management'], ScmUrlBuilder::manage(), ScmAuthorizationsService::check_authorizations()->manage_events());
			$event->add_sub_link(new ModuleLink($lang['scm.add.event'], ScmUrlBuilder::add()));
		$tree->add_link($event);

        $division = new ModuleLink($lang['scm.divisions.manager'], ScmUrlBuilder::manage_divisions(), ScmAuthorizationsService::check_authorizations()->manage_divisions());
            $division->add_sub_link(new ModuleLink($lang['scm.add.division'], ScmUrlBuilder::add_division(), ScmAuthorizationsService::check_authorizations()->manage_divisions()));
        $tree->add_link($division);

        $season = new ModuleLink($lang['scm.seasons.manager'], ScmUrlBuilder::manage_seasons(), ScmAuthorizationsService::check_authorizations()->manage_seasons());
            $season->add_sub_link(new ModuleLink($lang['scm.add.season'], ScmUrlBuilder::add_season(), ScmAuthorizationsService::check_authorizations()->manage_seasons()));
        $tree->add_link($season);

		$tree->add_link(new AdminModuleLink($lang['form.configuration'], ScmUrlBuilder::configuration()));

		if (ModulesManager::get_module($module_id)->get_configuration()->get_documentation())
			$tree->add_link(new ModuleLink($lang['form.documentation'], ModulesManager::get_module('scm')->get_configuration()->get_documentation(), ScmAuthorizationsService::check_authorizations()->moderation()));

		return $tree;
	}

    private static function current_event_list($master_item)
    {
        $events = ScmEventService::get_running_events();
        usort($events, function($a, $b) {
            return strcmp($a['id_category'], $b['id_category']);
        });

        $current_events = [];
        foreach ($events as $event)
        {
            $item = new ScmEvent();
            $item->set_properties($event);
            if (ScmEventService::check_event_display($item->get_id()))
                $current_events[] = [
                    'category' => $item->get_category()->get_name(),
                    'sub_link' => $master_item->add_sub_link(new ModuleLink(
                        $item->get_category()->get_name()
                            . ' - ' . ScmDivisionService::get_division($item->get_division_id())->get_division_name()
                            . ($item->get_pool() ? ' - ' . $item->get_pool() : '')
                            . ' - ' . ScmSeasonService::get_season($item->get_season_id())->get_season_name(),
                        ScmUrlBuilder::event_home($item->get_id(), $item->get_event_slug()),
                        ScmAuthorizationsService::check_authorizations()->moderation(),
                        'bgc-full moderator'
                    ))
                ];
        }
        return $current_events;
    }
}
?>
