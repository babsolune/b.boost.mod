<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmEventsManagerController extends DefaultModuleController
{
	private $elements_number = 0;
	private $ids = [];

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$current_page = $this->build_table();

		$this->execute_multiple_delete_if_needed($request);

		return $this->generate_response($current_page);
	}

	private function build_table()
	{
		$display_categories = CategoriesService::get_categories_manager()->get_categories_cache()->has_categories();

		$columns = [
			new HTMLTableColumn($this->lang['category.category'], 'id_category'),
			new HTMLTableColumn($this->lang['scm.division'], 'division_name'),
			new HTMLTableColumn($this->lang['scm.season'], 'season_name'),
			new HTMLTableColumn($this->lang['common.status'], 'published'),
			new HTMLTableColumn('<a class="offload" href="' . ScmUrlBuilder::add()->rel() . '" aria-label="' . $this->lang['scm.add.event'] . '"><i class="far fa-square-plus" aria-hidden="true"></i></a>', '', ['css_class' => 'bgc-full success'])
        ];

		if (!$display_categories)
			unset($columns[1]);

		$table_model = new SQLHTMLTableModel(
            ScmSetup::$scm_event_table,
            'events-manager',
            $columns, new HTMLTableSortingRule('start_date', HTMLTableSortingRule::DESC),
            20
        );

		$table_model->set_layout_title($this->lang['scm.events.management']);

		$table_model->set_filters_menu_title($this->lang['scm.filter.events']);
		$table_model->add_filter(new HTMLTableDateGreaterThanOrEqualsToSQLFilter('start_date', 'filter1', $this->lang['scm.event.start.date'] . ' ' . TextHelper::lcfirst($this->lang['common.minimum'])));
		$table_model->add_filter(new HTMLTableDateLessThanOrEqualsToSQLFilter('start_date', 'filter2', $this->lang['scm.event.start.date'] . ' ' . TextHelper::lcfirst($this->lang['common.maximum'])));
		if ($display_categories)
			$table_model->add_filter(new HTMLTableCategorySQLFilter('filter4'));

		$status_list = [
            Item::PUBLISHED => $this->lang['common.status.published'],
            Item::NOT_PUBLISHED => $this->lang['common.status.draft'],
            Item::DEFERRED_PUBLICATION => $this->lang['common.status.deffered.date']
        ];
		$table_model->add_filter(new HTMLTableEqualsFromListSQLFilter('published', 'filter5', $this->lang['common.status.publication'], $status_list));

		$table = new HTMLTable($table_model);
		$table->set_filters_fieldset_class_HTML();

		$results = [];
		$result = $table_model->get_sql_results('event
			LEFT JOIN ' . ScmSetup::$scm_season_table . ' season ON season.id_season = event.season_id
			LEFT JOIN ' . ScmSetup::$scm_division_table . ' division ON division.id_division = event.division_id',
			['*', 'event.id']
		);
		foreach ($result as $row)
		{
			$item = new ScmEvent();
			$item->set_properties($row);
			$category = $item->get_category();

			$this->elements_number++;
			$this->ids[$this->elements_number] = $item->get_id();

			$edit_link = new EditLinkHTMLElement(ScmUrlBuilder::edit($item->get_id(), $item->get_event_slug()));
			$delete_link = new DeleteLinkHTMLElement(ScmUrlBuilder::delete($item->get_id()));

            $real_event = $item->get_is_sub()
                ? '<span class="smaller">' . ScmEventService::get_master_division($item->get_id()) . '</span>'
                    . ' - '
                    . $row['division_name']
                    . '<span class="smaller">' . (!empty($item->get_pool()) ? ' - ' . $item->get_pool() : '') . '</span>'
                : $row['division_name']
                    . '<span class="smaller">' . (!empty($item->get_pool()) ? ' - ' . $item->get_pool() : '') . '</span>';

			$row = [
				new HTMLTableRowCell(new LinkHTMLElement(ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()), ($category->get_id() == Category::ROOT_CATEGORY ? $this->lang['common.none.alt'] : $category->get_name()))),
				new HTMLTableRowCell(new LinkHTMLElement(ScmUrlBuilder::event_home($item->get_id(), $item->get_event_slug()), $real_event), 'align-left'),
				new HTMLTableRowCell($row['season_name']),
				new HTMLTableRowCell($item->get_status()),
				new HTMLTableRowCell($edit_link->display() . $delete_link->display(), 'controls')
            ];

			if (!$display_categories)
				unset($row[1]);

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
						$event = ScmEventService::get_event($this->ids[$i]);
                        ScmEventService::delete($this->ids[$i]);
						HooksService::execute_hook_action('delete', self::$module_id, $event->get_properties());
                    }
                }
            }
            ScmEventService::clear_cache();

            AppContext::get_response()->redirect(ScmUrlBuilder::manage(), $this->lang['warning.process.success']);
        }
    }

	private function check_authorizations()
	{
		if (!ScmAuthorizationsService::check_authorizations()->moderation())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}

	private function generate_response($page = 1)
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->lang['scm.events.management'], $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name(), $page);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::manage());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());

		$breadcrumb->add($this->lang['scm.events.management'], ScmUrlBuilder::manage());

		return $response;
	}
}
?>
