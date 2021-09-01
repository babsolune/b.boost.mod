<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ClubsCategoryController extends ModuleController
{
	private $lang;
	private $common_lang;
	private $view;
	private $config;

	private $category;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		$this->check_authorizations();

		$this->build_view($request);

		return $this->generate_response();
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'clubs');
		$this->common_lang = LangLoader::get('common-lang');
		$this->view = new FileTemplate('clubs/ClubsSeveralItemsController.tpl');
		$this->view->add_lang(array_merge($this->lang, $this->common_lang, LangLoader::get('contribution-lang')));
		$this->config = ClubsConfig::load();
	}

	private function build_view(HTTPRequestCustom $request)
	{
		$now = new Date();

		$authorized_categories = CategoriesService::get_authorized_categories($this->get_category()->get_id(), '', 'clubs');

		$page = AppContext::get_request()->get_getint('page', 1);
		$subcategories_page = AppContext::get_request()->get_getint('subcategories_page', 1);

		$subcategories = CategoriesService::get_categories_manager('clubs')->get_categories_cache()->get_children($this->get_category()->get_id(), CategoriesService::get_authorized_categories($this->get_category()->get_id(),'', 'clubs'));
		$subcategories_pagination = $this->get_subcategories_pagination(count($subcategories), $this->config->get_categories_per_page(), $page, $subcategories_page);


		$nbr_cat_displayed = 0;
		foreach ($subcategories as $id => $category)
		{
			$nbr_cat_displayed++;

			if ($nbr_cat_displayed > $subcategories_pagination->get_display_from() && $nbr_cat_displayed <= ($subcategories_pagination->get_display_from() + $subcategories_pagination->get_number_items_per_page()))
			{
				$this->view->assign_block_vars('sub_categories_list', array(
					'C_CATEGORY_THUMBNAIL' => !empty($category->get_thumbnail()->rel()),
					'C_SEVERAL_ITEMS'      => $category->get_elements_number() > 1,

					'CATEGORY_ID'   => $category->get_id(),
					'CATEGORY_NAME' => $category->get_name(),
					'ITEMS_NUMBER'  => $category->get_elements_number(),

					'U_CATEGORY_THUMBNAIL' => $category->get_thumbnail()->rel(),
					'U_CATEGORY'           => ClubsUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel()
				));
			}
		}

		if($this->get_category()->get_id() == Category::ROOT_CATEGORY)
			$condition = 'WHERE id_category IN :authorised_categories AND published = 1';
		else
			$condition = 'WHERE id_category = :id_category AND published = 1';

		$parameters = array(
			'authorised_categories' => $authorized_categories,
			'id_category' => $this->get_category()->get_id(),
			'timestamp_now' => $now->get_timestamp()
		);

		$pagination = $this->get_pagination($condition, $parameters, $page, $subcategories_page);

		$result = PersistenceContext::get_querier()->select('SELECT clubs.*, member.*
		FROM '. ClubsSetup::$clubs_table .' clubs
		LEFT JOIN '. DB_TABLE_MEMBER .' member ON member.user_id = clubs.author_user_id
		' . $condition . '
		ORDER BY clubs.title ASC
		LIMIT :number_items_per_page OFFSET :display_from', array_merge($parameters, array(
			'user_id' => AppContext::get_current_user()->get_id(),
			'number_items_per_page' => $pagination->get_number_items_per_page(),
			'display_from' => $pagination->get_display_from()
		)));

		$category_description = FormatingHelper::second_parse($this->get_category()->get_description());

		$this->view->put_all(array(
			'C_CATEGORY'                 => true,
			'C_ITEMS'                    => $result->get_rows_count() > 0,
            'C_GMAP_ENABLED'             => ClubsService::is_gmap_enabled(),
            'C_SEVERAL_ITEMS'            => $result->get_rows_count() > 1,
			'C_GRID_VIEW'                => $this->config->get_display_type() == ClubsConfig::GRID_VIEW,
			'C_TABLE_VIEW'               => $this->config->get_display_type() == ClubsConfig::TABLE_VIEW,
			'C_CATEGORY_DESCRIPTION'     => !empty($category_description),
			'C_CONTROLS'                 => CategoriesAuthorizationsService::check_authorizations($this->get_category()->get_id())->moderation(),
			'C_PAGINATION'               => $pagination->has_several_pages(),
			'C_ROOT_CATEGORY'            => $this->get_category()->get_id() == Category::ROOT_CATEGORY,
			'C_HIDE_NO_ITEM_MESSAGE'     => $this->get_category()->get_id() == Category::ROOT_CATEGORY && ($nbr_cat_displayed != 0 || !empty($category_description)),
			'C_SUB_CATEGORIES'           => $nbr_cat_displayed > 0,
			'C_SUBCATEGORIES_PAGINATION' => $subcategories_pagination->has_several_pages(),

			'CATEGORY_NAME'            => $this->get_category()->get_name(),
			'GMAP_API_KEY'             => GoogleMapsConfig::load()->get_api_key(),
			'DEFAULT_LAT'              => GoogleMapsConfig::load()->get_default_marker_latitude(),
			'DEFAULT_LNG'              => GoogleMapsConfig::load()->get_default_marker_longitude(),
			'SUBCATEGORIES_PAGINATION' => $subcategories_pagination->display(),
			'PAGINATION'               => $pagination->display(),
			'CATEGORIES_PER_ROW'       => $this->config->get_categories_per_row(),
			'ITEMS_PER_ROW'       => $this->config->get_categories_per_row(),
			'ID_CAT'                   => $this->get_category()->get_id(),
			'CATEGORY_DESCRIPTION'     => $category_description,

			'U_EDIT_CATEGORY' => $this->get_category()->get_id() == Category::ROOT_CATEGORY ? ClubsUrlBuilder::configuration()->rel() : CategoriesUrlBuilder::edit($this->get_category()->get_id())->rel()
		));

		while ($row = $result->fetch())
		{
			$item = new ClubsItem();
			$item->set_properties($row);

			if ($this->get_category()->get_id() == Category::ROOT_CATEGORY && $item->get_id_category() == 0)
				$this->view->assign_block_vars('root_items', array_merge($item->get_array_tpl_vars()));
			else
				$this->view->assign_block_vars('items', array_merge($item->get_array_tpl_vars()));

		}
		$result->dispose();
	}

	private function get_pagination($condition, $parameters, $page, $subcategories_page)
	{
		$items_number = ClubsService::count($condition, $parameters);

		$pagination = new ModulePagination($page, $items_number, (int)ClubsConfig::load()->get_items_per_page());
		$pagination->set_url(ClubsUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name(), '%d', $subcategories_page));

		if ($pagination->current_page_is_empty() && $page > 1)
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}

		return $pagination;
	}

	private function get_subcategories_pagination($subcategories_number, $categories_per_page, $page, $subcategories_page)
	{
		$pagination = new ModulePagination($subcategories_page, $subcategories_number, (int)$categories_per_page);
		$pagination->set_url(ClubsUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name(), $page, '%d'));

		if ($pagination->current_page_is_empty() && $subcategories_page > 1)
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}

		return $pagination;
	}

	private function get_category()
	{
		if ($this->category === null)
		{
			$id = AppContext::get_request()->get_getint('id_category', 0);
			if (!empty($id))
			{
				try {
					$this->category = CategoriesService::get_categories_manager('clubs')->get_categories_cache()->get_category($id);
				} catch (CategoryNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
   					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->category = CategoriesService::get_categories_manager('clubs')->get_categories_cache()->get_category(Category::ROOT_CATEGORY);
			}
		}
		return $this->category;
	}

	private function check_authorizations()
	{
		if (AppContext::get_current_user()->is_guest())
		{
			if (!Authorizations::check_auth(RANK_TYPE, User::MEMBER_LEVEL, $this->get_category()->get_authorizations(), Category::READ_AUTHORIZATIONS) || !CategoriesAuthorizationsService::check_authorizations($this->get_category()->get_id())->read())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!CategoriesAuthorizationsService::check_authorizations($this->get_category()->get_id())->read())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
	}

	private function generate_response()
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();

		if ($this->get_category()->get_id() != Category::ROOT_CATEGORY)
			$graphical_environment->set_page_title($this->get_category()->get_name(), $this->lang['clubs.module.title']);
		else
			$graphical_environment->set_page_title($this->lang['clubs.module.title']);

		$graphical_environment->get_seo_meta_data()->set_description($this->get_category()->get_description());
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ClubsUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name(), AppContext::get_request()->get_getint('page', 1)));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['clubs.module.title'], ClubsUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager('clubs')->get_parents($this->get_category()->get_id(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ClubsUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}

		return $response;
	}

	public static function get_view()
	{
		$object = new self();
		$object->init();
		$object->check_authorizations();
		$object->build_view(AppContext::get_request());
		return $object->view;
	}
}
?>
