<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballSeasonsManagerController extends DefaultModuleController
{
	private $elements_number = 0;
	private $ids = array();

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_table();

		$this->execute_multiple_delete_if_needed($request);

		return $this->generate_response();
	}

	private function build_table()
	{
		$columns = array(
			new HTMLTableColumn('ID', 'id_season'),
			new HTMLTableColumn($this->lang['common.name'], 'season_name'),
			new HTMLTableColumn('<a class="offload" href="' . FootballUrlBuilder::add_season()->rel() . '" aria-label="' . $this->lang['football.add.season'] . '"><i class="far fa-square-plus" aria-hidden="true"></i></a>')
		);

		$table_model = new SQLHTMLTableModel(FootballSetup::$football_season_table, 'seasons-manager', $columns, new HTMLTableSortingRule('season_name', HTMLTableSortingRule::DESC));

		$table_model->set_layout_title($this->lang['football.seasons.manager']);

		$table = new HTMLTable($table_model);
		$table->set_filters_fieldset_class_HTML();

		$results = array();
		$result = $table_model->get_sql_results('season',
			array('*', 'season.id_season')
		);
		foreach ($result as $row)
		{
			$season = new FootballSeason();
			$season->set_properties($row);

			$this->elements_number++;
			$this->ids[$this->elements_number] = $season->get_id_season();

			$edit_link = new EditLinkHTMLElement(FootballUrlBuilder::edit_season($season->get_id_season()));
			$delete_link = new DeleteLinkHTMLElement(FootballUrlBuilder::delete_season($season->get_id_season()), '', array('data-confirmation' => $this->lang['football.warning.delete.season']));

			$row = array(
				new HTMLTableRowCell('#' . $season->get_id_season()),
				new HTMLTableRowCell($season->get_season_name()),
				new HTMLTableRowCell($edit_link->display() . $delete_link->display(), 'controls')
			);

			$results[] = new HTMLTableRow($row);
		}
		$table->set_rows($table_model->get_number_of_matching_rows(), $results);

		$this->view->put('CONTENT', $table->display());

		return $table->get_page_number();
	}

	private function execute_multiple_delete_if_needed(HTTPRequestCustom $request)
    {
        if ($request->get_string('delete-selected-elements', false))
        {
            for ($i = 1; $i <= $this->elements_number; $i++)
            {
                if ($request->get_value('delete-checkbox-' . $i, 'off') == 'on')
                {
                    if (isset($this->ids[$i]))
                    {
						$season = FootballSeasonService::get_season($this->ids[$i]);
                        FootballSeasonService::delete_season($this->ids[$i]);
						HooksService::execute_hook_action('delete', self::$module_id, $season->get_properties());
                    }
                }
            }
            FootballCompetService::clear_cache();

            AppContext::get_response()->redirect(FootballUrlBuilder::manage_seasons(), $this->lang['warning.process.success']);
        }
    }

	private function check_authorizations()
	{
		if (!FootballAuthorizationsService::check_authorizations()->read())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}

	private function generate_response($page = 1)
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->lang['football.seasons.manager'], $this->lang['football.module.title'], $page);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::manage_seasons());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());

		$breadcrumb->add($this->lang['football.seasons.manager'], FootballUrlBuilder::manage_seasons());

		return $response;
	}
}
?>
