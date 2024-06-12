<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

// namespace PHPBoost\Football\Controllers\Divisions;

class FootballDivisionsManagerController extends DefaultModuleController
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
			new HTMLTableColumn($this->lang['common.name'], 'division_name'),
			new HTMLTableColumn($this->lang['football.compet.type'], 'division_compet_type'),
			new HTMLTableColumn($this->lang['football.match.type'], 'division_match_type'),
			new HTMLTableColumn('<a class="offload" href="' . FootballUrlBuilder::add_division()->rel() . '" aria-label="' . $this->lang['football.add.division'] . '"><i class="far fa-square-plus" aria-hidden="true"></i></a>')
		);

		$table_model = new SQLHTMLTableModel(FootballSetup::$football_division_table, 'divisions-manager', $columns, new HTMLTableSortingRule('division_name', HTMLTableSortingRule::DESC));

		$table_model->set_layout_title($this->lang['football.divisions.manager']);

		$table = new HTMLTable($table_model);
		$table->set_filters_fieldset_class_HTML();

		$results = array();
		$result = $table_model->get_sql_results('division',
			array('*', 'division.id_division')
		);
		foreach ($result as $row)
		{
			$division = new FootballDivision();
			$division->set_properties($row);

			$this->elements_number++;
			$this->ids[$this->elements_number] = $division->get_id_division();

			$edit_link = new EditLinkHTMLElement(FootballUrlBuilder::edit_division($division->get_id_division()));
			$delete_link = new DeleteLinkHTMLElement(FootballUrlBuilder::delete_division($division->get_id_division()), '', array('data-confirmation' => $this->lang['football.warning.delete.division']));

            switch ($division->get_division_compet_type()) {
                case FootballDivision::CUP :
                    $compet_type = $this->lang['football.cup'];
                    break;
                case FootballDivision::CHAMPIONSHIP :
                    $compet_type = $this->lang['football.championship'];
                    break;
                case FootballDivision::TOURNAMENT :
                    $compet_type = $this->lang['football.tournament'];
                    break;
            }

            switch ($division->get_division_match_type()) {
                case FootballDivision::SINGLE_MATCHES :
                    $match_type = $this->lang['football.single.matches'];
                    break;
                case FootballDivision::RETURN_MATCHES :
                    $match_type = $this->lang['football.return.matches'];
                    break;
            }

			$row = array(
				new HTMLTableRowCell($division->get_division_name()),
				new HTMLTableRowCell($compet_type),
				new HTMLTableRowCell($match_type),
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
						$division = FootballDivisionService::get_division($this->ids[$i]);
                        FootballDivisionService::delete_division($this->ids[$i]);
						HooksService::execute_hook_action('delete', self::$module_id, $division->get_properties());
                    }
                }
            }
            FootballCompetService::clear_cache();

            AppContext::get_response()->redirect(FootballUrlBuilder::manage_divisions(), $this->lang['warning.process.success']);
        }
    }

	private function check_authorizations()
	{
		if (!FootballAuthorizationsService::check_authorizations()->manage_divisions())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}

	private function generate_response($page = 1)
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->lang['football.divisions.manager'], $this->lang['football.module.title'], $page);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::manage_divisions());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());

		$breadcrumb->add($this->lang['football.divisions.manager'], FootballUrlBuilder::manage_divisions());

		return $response;
	}
}
?>
