<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 12 22
 * @since       PHPBoost 6.0 - 2022 12 22
*/

class FootballClubsManagerController extends DefaultModuleController
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
			new HTMLTableColumn($this->lang['football.club.name'], 'club_name'),
			new HTMLTableColumn($this->lang['football.club.acronym'], 'club_acronym'),
			new HTMLTableColumn($this->lang['football.club.logo'], 'club_logo'),
			new HTMLTableColumn('<a class="offload" href="' . FootballUrlBuilder::add_club()->rel() . '" aria-label="' . $this->lang['football.add.club'] . '"><i class="far fa-square-plus" aria-hidden="true"></i></a>')
		);

		$table_model = new SQLHTMLTableModel(FootballSetup::$football_club_table, 'clubs-manager', $columns, new HTMLTableSortingRule('club_name', HTMLTableSortingRule::ASC));

		$table_model->set_layout_title($this->lang['football.clubs.manager']);

		$table = new HTMLTable($table_model);
		$table->set_filters_fieldset_class_HTML();

		$results = array();
		$result = $table_model->get_sql_results('club',
			array('*', 'club.id_club')
		);
		foreach ($result as $row)
		{
			$club = new FootballClub();
			$club->set_properties($row);

			$this->elements_number++;
			$this->ids[$this->elements_number] = $club->get_id_club();

			$edit_link = new EditLinkHTMLElement(FootballUrlBuilder::edit_club($club->get_id_club()));
			$delete_link = new DeleteLinkHTMLElement(FootballUrlBuilder::delete_club($club->get_id_club()), '', array('data-confirmation' => $this->lang['football.warning.delete.club']));

			$row = array(
				new HTMLTableRowCell(new LinkHTMLElement(FootballUrlBuilder::display_club($club->get_id_club()), $club->get_club_name())),
				new HTMLTableRowCell(new SpanHTMLElement($club->get_club_acronym())),
				new HTMLTableRowCell(new ImgHTMLElement(
					Url::to_rel($club->get_club_logo()),
					array('alt' => !empty($club->get_club_logo()) ? StringVars::replace_vars($this->lang['football.alt.logo'], array('name' => $club->get_club_name())) : $this->lang['football.clubs.no.logo']), 
					'small-logo'
				)),
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
						$club = FootballClubService::get_club($this->ids[$i]);
                        FootballClubService::delete_club($this->ids[$i]);
						HooksService::execute_hook_action('delete', self::$module_id, $club->get_properties());
                    }
                }
            }
            FootballCompetService::clear_cache();

            AppContext::get_response()->redirect(FootballUrlBuilder::manage_clubs(), $this->lang['warning.process.success']);
        }
    }

	private function check_authorizations()
	{
		if (!FootballAuthorizationsService::check_authorizations()->manage_clubs())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}

	private function generate_response($page = 1)
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->lang['football.clubs.manager'], $this->lang['football.module.title'], $page);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::manage_clubs());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());
		$breadcrumb->add($this->lang['football.clubs.manager'], FootballUrlBuilder::manage_clubs());

		return $response;
	}
}
?>
