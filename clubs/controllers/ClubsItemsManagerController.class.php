<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ClubsItemsManagerController extends AdminModuleController
{
	private $lang;
	private $view;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->build_table();

		return $this->generate_response();
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'clubs');
		$this->view = new StringTemplate('# INCLUDE table #');
	}

	private function build_table()
	{
		$common_lang = LangLoader::get('common-lang');
		$display_categories = CategoriesService::get_categories_manager()->get_categories_cache()->has_categories();

		$columns = array(
			new HTMLTableColumn($common_lang['common.title'], 'title'),
			new HTMLTableColumn($common_lang['common.category'], 'id_category'),
			new HTMLTableColumn($common_lang['common.author'], 'display_name'),
			new HTMLTableColumn($common_lang['common.creation.date'], 'creation_date'),
			new HTMLTableColumn($common_lang['common.status'], 'published'),
			new HTMLTableColumn($common_lang['common.moderation'], '', array('sr-only' => true))
		);

		if (!$display_categories)
			unset($columns[1]);

		$table_model = new SQLHTMLTableModel(ClubsSetup::$clubs_table, 'table', $columns, new HTMLTableSortingRule('creation_date', HTMLTableSortingRule::DESC));

		$table_model->set_caption($this->lang['clubs.management']);

		$table = new HTMLTable($table_model);

		$results = array();
		$result = $table_model->get_sql_results('clubs
			LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = clubs.author_user_id',
			array('*', 'clubs.id')
		);
		foreach ($result as $row)
		{
			$item = new ClubsItem();
			$item->set_properties($row);
			$category = $item->get_category();
			$user = $item->get_author_user();

			$edit_item = new LinkHTMLElement(ClubsUrlBuilder::edit($item->get_id()), '', array('title' => LangLoader::get_message('common.edit', 'common-lang')), 'fa fa-edit');
			$delete_item = new LinkHTMLElement(ClubsUrlBuilder::delete($item->get_id()), '', array('title' => LangLoader::get_message('common.delete', 'common-lang'), 'data-confirmation' => 'delete-element'), 'far fa-trash-alt');

			$user_group_color = User::get_group_color($user->get_groups(), $user->get_level(), true);
			$author = $user->get_id() !== User::VISITOR_LEVEL ? new LinkHTMLElement(UserUrlBuilder::profile($user->get_id()), $user->get_display_name(), (!empty($user_group_color) ? array('style' => 'color: ' . $user_group_color) : array()), UserService::get_level_class($user->get_level())) : $user->get_display_name();

			$row = array(
				new HTMLTableRowCell(new LinkHTMLElement(ClubsUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()), $item->get_title()), 'left'),
				new HTMLTableRowCell(new LinkHTMLElement(ClubsUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()), $category->get_name())),
				new HTMLTableRowCell($author),
				new HTMLTableRowCell($item->get_creation_date()->format(Date::FORMAT_DAY_MONTH_YEAR)),
				new HTMLTableRowCell($item->get_status()),
				new HTMLTableRowCell($edit_item->display() . $delete_item->display())
			);

			if (!$display_categories)
				unset($row[1]);

			$results[] = new HTMLTableRow($row);
		}
		$table->set_rows($table_model->get_number_of_matching_rows(), $results);

		$this->view->put('table', $table->display());
	}

	private function check_authorizations()
	{
		if (!CategoriesAuthorizationsService::check_authorizations()->moderation())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}

	private function generate_response()
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->lang['clubs.management'], $this->lang['clubs.module.title']);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ClubsUrlBuilder::manage());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['clubs.module.title'], ClubsUrlBuilder::home());

		$breadcrumb->add($this->lang['clubs.management'], ClubsUrlBuilder::manage());

		return $response;
	}
}
?>
