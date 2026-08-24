<?php
/**
 * @copyright   &copy; 2005-2026 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmSeasonsController extends DefaultModuleController
{
    private $seasons;
    private $events;

    protected function get_template_to_use()
    {
        return new FileTemplate('scm/main/ScmSeasonsController.tpl');
    }

    public function execute(HTTPRequestCustom $request)
    {
        $this->init();

        $this->check_authorizations();

        $this->build_view();

        return $this->generate_response();
    }

    private function init()
    {
        $this->seasons = ScmSeasonCache::load();
        $this->events = ScmEventCache::load();
    }

    private function build_view()
    {
        $season_id = $event_list = 0;

        $groups = $this->groupEventsBySeason($this->events->get_events(), $this->seasons->get_seasons());

        foreach ($groups as $group)
        {
            $season_id++;
            $this->view->assign_block_vars('seasons', array_merge(
                [
                    'SEASON_ID' => $season_id,
                    'SEASON_NAME' => $group['name'],
                ]
            ));
            foreach ($group['events'] as $event)
            {
                $item = ScmEventService::get_event($event['id']);
                $division = ScmDivisionCache::load()->get_division($item->get_division_id());
                $event_list++;
                $this->view->assign_block_vars('seasons.events', array_merge(
                    [
                        'EVENT_ID' => $item->get_id(),
                        'U_EVENT' => ScmUrlBuilder::event_home($item->get_id(), $item->get_event_slug())->rel(),
                        'EVENT_NAME' => $division['division_name'],
                    ]
                ));
            }
        }

        $this->view->put_all([
            'C_SEASONS' => $season_id > 0
        ]);
    }

    /**
     * Groups a list of events by "displayable" season.
     *
     * Rules:
     * - Only groups corresponding to a first_year that has at least
     *   one season with calendar_year = 1 are displayed.
     * - For a given first_year, if a season with calendar_year = 0
     *   exists, its name (season_name) is used as the group title,
     *   and it also collects the events attached to the
     *   calendar_year = 1 season of the same first_year.
     * - If no calendar_year = 0 season exists for that first_year,
     *   the calendar_year = 1 season is displayed instead.
     *
     * @param array $events  List of events (each with a 'season_id' key)
     * @param array $seasons List of seasons (id_season, season_name, first_year, calendar_year)
     * @return array [ first_year => ['name' => string, 'events' => array] ] sorted by first_year
     */
    private function groupEventsBySeason(array $events, array $seasons): array
    {
        // 1. first_year -> calendar_year=1 season / calendar_year=0 season
        $byYear = [];
        foreach ($seasons as $season) {
            $byYear[$season['first_year']]['cal1'] = $byYear[$season['first_year']]['cal1'] ?? null;
            $byYear[$season['first_year']]['cal0'] = $byYear[$season['first_year']]['cal0'] ?? null;

            if ((int)$season['calendar_year'] === 1) {
                $byYear[$season['first_year']]['cal1'] = $season;
            } else {
                $byYear[$season['first_year']]['cal0'] = $season;
            }
        }

        // 2. Determine, for each displayable first_year, which season provides the title
        $displaySeason = []; // first_year => season (array)
        foreach ($byYear as $firstYear => $data) {
            if ($data['cal1'] === null) {
                // no calendar_year=1 -> this group is not displayed
                continue;
            }
            $displaySeason[$firstYear] = $data['cal0'] ?? $data['cal1'];
        }

        // 3. season_id -> first_year (to find the group from an event)
        $yearBySeasonId = [];
        foreach ($seasons as $season) {
            $yearBySeasonId[$season['id_season']] = $season['first_year'];
        }

        // 4. Group the events
        $result = [];
        foreach ($events as $event) {
            $seasonId = $event['season_id'];

            if (!isset($yearBySeasonId[$seasonId])) {
                continue; // unknown season_id, skip
            }

            $firstYear = $yearBySeasonId[$seasonId];

            if (!isset($displaySeason[$firstYear])) {
                continue; // not a displayable group (no calendar_year=1)
            }

            if (!isset($result[$firstYear])) {
                $result[$firstYear] = [
                    'name'   => $displaySeason[$firstYear]['season_name'],
                    'events' => [],
                ];
            }

            $result[$firstYear]['events'][] = $event;
        }

        // 5. Sort by first_year descending
        krsort($result);

        return $result;
    }

    private function check_authorizations()
    {
        if (!ScmAuthorizationsService::check_authorizations()->read())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }
    }

    private function generate_response()
    {
        $response = new SiteDisplayResponse($this->view);

        $graphical_environment = $response->get_graphical_environment();

        $description = StringVars::replace_vars($this->lang['scm.seo.description.season.list'], ['site' => GeneralConfig::load()->get_site_name()]);
        $graphical_environment->get_seo_meta_data()->set_description($description);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::season_list());

        $breadcrumb = $graphical_environment->get_breadcrumb();
        $breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());
        $breadcrumb->add($this->lang['scm.season.list'], ScmUrlBuilder::season_list());

        return $response;
    }
}
?>
