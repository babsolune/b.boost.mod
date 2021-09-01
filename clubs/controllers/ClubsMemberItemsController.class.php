<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ClubsMemberItemsController extends ModuleController
{
	private $view;
	private $lang;
	private $member;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->build_view($request);

		return $this->generate_response($request);
	}

	public function init()
	{
		$this->lang = LangLoader::get('common', 'clubs');
		$this->view = new FileTemplate('clubs/ClubsSeveralItemsController.tpl');
		$this->view->add_lang($this->lang);
	}

	public function build_view(HTTPRequestCustom $request)
	{
		$now = new Date();
		$config = ClubsConfig::load();
		$authorized_categories = CategoriesService::get_authorized_categories(Category::ROOT_CATEGORY);

		$condition = 'WHERE id_category IN :authorized_categories
		AND author_user_id = :user_id
		AND published = 1';
		$parameters = array(
			'user_id' => $this->get_member()->get_id(),
			'authorized_categories' => $authorized_categories,
			'timestamp_now' => $now->get_timestamp()
		);

		$page = AppContext::get_request()->get_getint('page', 1);
		$pagination = $this->get_pagination($condition, $parameters, $page);

		$result = PersistenceContext::get_querier()->select('SELECT clubs.*, member.*
		FROM '. ClubsSetup::$clubs_table .' clubs
		LEFT JOIN '. DB_TABLE_MEMBER .' member ON member.user_id = clubs.author_user_id
		' . $condition . '
		ORDER BY clubs.creation_date DESC
		LIMIT :number_items_per_page OFFSET :display_from', array_merge($parameters, array(
			'number_items_per_page' => $pagination->get_number_items_per_page(),
			'display_from' => $pagination->get_display_from()
		)));

		$this->view->put_all(array(
			'C_MEMBER_ITEMS'     => true,
			'C_MY_ITEMS'         => $this->is_current_member_displayed(),
			'C_ITEMS'            => $result->get_rows_count() > 0,
			'C_SEVERAL_ITEMS'    => $result->get_rows_count() > 1,
			'C_GRID_VIEW'        => $config->get_display_type() == ClubsConfig::GRID_VIEW,
			'C_TABLE_VIEW'       => $config->get_display_type() == ClubsConfig::TABLE_VIEW,
			'CATEGORIES_PER_ROW' => $config->get_categories_per_row(),
			'ITEMS_PER_ROW'      => $config->get_items_per_row(),
			'C_PAGINATION'       => $pagination->has_several_pages(),
			'PAGINATION'         => $pagination->display(),
			'MEMBER_NAME'        => $this->get_member()->get_display_name()
		));

		while ($row = $result->fetch())
		{
			$item = new ClubsItem();
			$item->set_properties($row);

			$this->view->assign_block_vars('items', array_merge($item->get_array_tpl_vars()));
		}
		$result->dispose();
	}

	protected function get_member()
	{
		if ($this->member === null)
		{
			$this->member = UserService::get_user(AppContext::get_request()->get_getint('user_id', AppContext::get_current_user()->get_id()));
			if (!$this->member)
				DispatchManager::redirect(PHPBoostErrors::unexisting_element());
		}
		return $this->member;
	}

	protected function is_current_member_displayed()
	{
		return $this->member && $this->member->get_id() == AppContext::get_current_user()->get_id();
	}

	private function get_pagination($condition, $parameters, $page)
	{
		$items_number = ClubsService::count($condition, $parameters);

		$pagination = new ModulePagination($page, $items_number, (int)ClubsConfig::load()->get_items_per_page());
		$pagination->set_url(ClubsUrlBuilder::display_pending('%d'));

		if ($pagination->current_page_is_empty() && $page > 1)
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}

		return $pagination;
	}

	private function check_authorizations()
	{
		if (!(CategoriesAuthorizationsService::check_authorizations()->write() || CategoriesAuthorizationsService::check_authorizations()->contribution() || CategoriesAuthorizationsService::check_authorizations()->moderation()))
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}

	private function generate_response(HTTPRequestCustom $request)
	{
		$page = $request->get_getint('page', 1);
		$page_title = $this->is_current_member_displayed() ? $this->lang['clubs.my.items'] : $this->lang['clubs.member.items'] . ' ' . $this->get_member()->get_display_name();
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($page_title, $this->lang['clubs.module.title']);
		$graphical_environment->get_seo_meta_data()->set_description($this->lang['clubs.seo.description.pending']);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ClubsUrlBuilder::display_member_items($this->get_member()->get_id(), $page));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['clubs.module.title'], ClubsUrlBuilder::home());
		$breadcrumb->add($page_title, ClubsUrlBuilder::display_member_items($this->get_member()->get_id(), $page));

		return $response;
	}
}
?>
