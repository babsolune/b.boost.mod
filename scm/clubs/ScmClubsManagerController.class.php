<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmClubsManagerController extends DefaultModuleController
{
	private $elements_number = 0;
	private $ids = [];

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_table();

		$this->execute_multiple_delete_if_needed($request);

		return $this->generate_response();
	}

	private function build_table()
	{
		$columns = [
			new HTMLTableColumn($this->lang['scm.club.full.name'], 'club_full_name'),
			new HTMLTableColumn($this->lang['scm.club.name'], 'club_name'),
			new HTMLTableColumn($this->lang['scm.club.flag'], ''),
			new HTMLTableColumn($this->lang['scm.club.logo'], ''),
			new HTMLTableColumn('<a class="offload" href="' . ScmUrlBuilder::add_club()->rel() . '" aria-label="' . $this->lang['scm.club.add'] . '"><i class="far fa-square-plus" aria-hidden="true"></i></a>', '', ['css_class' => 'bgc-full success'])
        ];

		$table_model = new SQLHTMLTableModel(ScmSetup::$scm_club_table, 'clubs-manager', $columns, new HTMLTableSortingRule('club_name', HTMLTableSortingRule::ASC));

		$table_model->set_layout_title($this->lang['scm.clubs.manager']);

		$table_model->set_filters_menu_title($this->lang['scm.clubs.filter']);
		$table_model->add_filter(new HTMLTableContainsTextSQLFilter('club_name', 'filter1', $this->lang['scm.club.name']));
		$table_model->add_filter(new HTMLTableContainsTextSQLFilter('club_full_name', 'filter2', $this->lang['scm.club.full.name']));

        $table = new HTMLTable($table_model);
		$table->set_filters_fieldset_class_HTML();

		$results = [];
		$result = $table_model->get_sql_results('club',
			['*', 'club.id_club']
		);
		foreach ($result as $row)
		{
			$club = new ScmClub();
			$club->set_properties($row);

			$this->elements_number++;
			$this->ids[$this->elements_number] = $club->get_id_club();

			$edit_link = new EditLinkHTMLElement(ScmUrlBuilder::edit_club($club->get_id_club(), $club->get_club_slug()));
			$delete_link = new DeleteLinkHTMLElement(ScmUrlBuilder::delete_club($club->get_id_club()), '', ['data-confirmation' => $this->lang['scm.warning.delete.club']]);

            $real_id = $club->get_club_affiliate() ? $club->get_club_affiliation() : $club->get_id_club();

			$row = [
				new HTMLTableRowCell(new LinkHTMLElement(ScmUrlBuilder::display_club($real_id, $club->get_club_slug()), $club->get_club_full_name())),
				new HTMLTableRowCell(new SpanHTMLElement($club->get_club_name())),
				new HTMLTableRowCell(
                    $club->get_club_flag() ?
                    new ImgHTMLElement(
                        TPL_PATH_TO_ROOT . '/images/stats/countries/' . $club->get_club_flag() . '.png',
                        ['alt' => !empty($club->get_club_flag()) ? StringVars::replace_vars($this->lang['scm.club.logo.alt'], ['name' => $club->get_club_name()]) : ''], 
                        'logo-small'
                    ) : ''
                ),
				new HTMLTableRowCell(
                    $club->get_club_logo() ?
                    new ImgHTMLElement(
                        Url::to_rel($club->get_club_logo()),
                        ['alt' => !empty($club->get_club_logo()) ? StringVars::replace_vars($this->lang['scm.club.logo.alt'], ['name' => $club->get_club_name()]) : ''], 
                        'logo-small'
                    ) : ''
                ),
				new HTMLTableRowCell($edit_link->display() . $delete_link->display(), 'controls')
            ];

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
						$club = ScmClubService::get_club($this->ids[$i]);
                        ScmClubService::delete_club($this->ids[$i]);
						HooksService::execute_hook_action('delete', self::$module_id, $club->get_properties());
                    }
                }
            }
            ScmEventService::clear_cache();

            AppContext::get_response()->redirect(ScmUrlBuilder::manage_clubs(), $this->lang['warning.process.success']);
        }
    }

	private function check_authorizations()
	{
		if (!ScmAuthorizationsService::check_authorizations()->manage_clubs())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}

	private function generate_response($page = 1)
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->lang['scm.clubs.manager'], $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name(), $page);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::manage_clubs());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());
		$breadcrumb->add($this->lang['scm.clubs.manager'], ScmUrlBuilder::manage_clubs());

		return $response;
	}
}
?>
