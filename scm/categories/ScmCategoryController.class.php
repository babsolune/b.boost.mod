<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmCategoryController extends DefaultModuleController
{
	private $comments_config;
	private $content_management_config;

	private $category;

	protected function get_template_to_use()
	{
		return new FileTemplate('scm/ScmCategoryController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		$this->check_authorizations();

		$this->build_view($request);

		return $this->generate_response($request);
	}

	private function init()
	{
		$this->comments_config = CommentsConfig::load();
		$this->content_management_config = ContentManagementConfig::load();
	}

	private function build_view(HTTPRequestCustom $request)
	{
		$now = new Date();
		$page = $request->get_getint('page', 1);
		$subcategories_page = $request->get_getint('subcategories_page', 1);

		$subcategories = CategoriesService::get_categories_manager('scm')->get_categories_cache()->get_children($this->get_category()->get_id(), CategoriesService::get_authorized_categories($this->get_category()->get_id(), '', 'scm'));
		$categories_number_displayed = 0;
		foreach ($subcategories as $id => $category)
		{
			$categories_number_displayed++;

            $this->view->assign_block_vars('sub_categories_list',
            [
                'C_SEVERAL_ITEMS'      => $category->get_elements_number() > 1,

                'CATEGORY_ID'          => $category->get_id(),
                'CATEGORY_NAME'        => $category->get_name(),
                'CATEGORY_PARENT_ID'   => $category->get_id_parent(),
                'CATEGORY_SUB_ORDER'   => $category->get_order(),
                'ITEMS_NUMBER'         => $category->get_elements_number(),
                'U_CATEGORY'           => ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel()
            ]);
		}

		$condition = 'WHERE id_category = :id_category
		AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))';
		$params = [
			'id_category' => $this->get_category()->get_id(),
			'timestamp_now' => $now->get_timestamp()
        ];

		$result = PersistenceContext::get_querier()->select('SELECT event.*
            FROM ' . ScmSetup::$scm_event_table . ' event
            ' . $condition . '
            ORDER BY event.id DESC', 
            $params
        );

		$this->view->put_all([
			'C_ITEMS'                    => $result->get_rows_count() > 0,
			'C_SEVERAL_ITEMS'            => $result->get_rows_count() > 1,
			'C_ENABLED_COMMENTS'         => $this->comments_config->module_comments_is_enabled('scm'),
			'C_ENABLED_NOTATION'         => $this->content_management_config->module_notation_is_enabled('scm'),
			'C_CONTROLS'                 => ScmAuthorizationsService::check_authorizations($this->get_category()->get_id())->moderation(),
			'C_CATEGORY'                 => true,
			'C_ROOT_CATEGORY'            => $this->get_category()->get_id() == Category::ROOT_CATEGORY,
			'C_HIDE_NO_ITEM_MESSAGE'     => $this->get_category()->get_id() == Category::ROOT_CATEGORY && ($categories_number_displayed != 0 || !empty($category_description)),
			'C_SUB_CATEGORIES'           => $categories_number_displayed > 0,

			'CATEGORY_ID'              => $this->get_category()->get_id(),
			'CATEGORY_NAME'            => $this->get_category()->get_name(),
			'CATEGORY_PARENT_ID' 	   => $this->get_category()->get_id_parent(),
			'CATEGORY_SUB_ORDER' 	   => $this->get_category()->get_order(),

            'U_EDIT_CATEGORY'  	   => $this->get_category()->get_id() == Category::ROOT_CATEGORY ? ScmUrlBuilder::configuration()->rel() : CategoriesUrlBuilder::edit($this->get_category()->get_id(), 'scm')->rel()
		]);

		while ($row = $result->fetch())
		{
			$item = new ScmEvent();
			$item->set_properties($row);
            $c_is_master = ScmEventService::is_master($item->get_id()) || count(ScmEventService::get_sub_list($item->get_id())) == 0;
            $c_is_not_sub = empty($item->get_master_id());

            if ($c_is_master && $c_is_not_sub)
                $this->view->assign_block_vars('items', $item->get_template_vars());

            if ($c_is_master)
            {
                $now = new Date();
                foreach (ScmEventService::get_sub_list($item->get_id()) as $sub_event)
                {
                    $sub_item = new ScmEvent();
                    $sub_item->set_properties($sub_event);
                    $this->view->assign_block_vars('items.sub_items', array_merge($sub_item->get_template_vars(), [
                        'C_IS_ENDED' => $sub_item->get_end_date() < $now
                    ]));
                }
            }
		}
		$result->dispose();
	}

	private function get_category()
	{
		if ($this->category === null)
		{
			$id = AppContext::get_request()->get_getint('id_category', 0);
			if (!empty($id))
			{
				try {
					$this->category = CategoriesService::get_categories_manager('scm')->get_categories_cache()->get_category($id);
				} catch (CategoryNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->category = CategoriesService::get_categories_manager('scm')->get_categories_cache()->get_category(Category::ROOT_CATEGORY);
			}
		}
		return $this->category;
	}

	private function check_authorizations()
	{
		// if (AppContext::get_current_user()->is_guest())
		// {
		// 	if (!ScmAuthorizationsService::check_authorizations($this->get_category()->get_id())->manage_events())
		// 	{
		// 		$error_controller = PHPBoostErrors::user_not_authorized();
		// 		DispatchManager::redirect($error_controller);
		// 	}
		// }
		// else
		// {
			if (!ScmAuthorizationsService::check_authorizations($this->get_category()->get_id())->read())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		// }
	}

	private function generate_response(HTTPRequestCustom $request)
	{
		$page = $request->get_getint('page', 1);
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();

		if ($this->get_category()->get_id() != Category::ROOT_CATEGORY)
			$graphical_environment->set_page_title($this->get_category()->get_name(), $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name(), $page);
		else
			$graphical_environment->set_page_title($this->lang['category.categories'], $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name(), $page);
        $description = StringVars::replace_vars($this->lang['scm.seo.description.categories'], ['site' => GeneralConfig::load()->get_site_name()]);
        $graphical_environment->get_seo_meta_data()->set_description($description);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name(), $page));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager('scm')->get_parents($this->get_category()->get_id(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name(), ($category->get_id() == $this->get_category()->get_id() ? $page : 1)));
		}

		return $response;
	}

	public static function get_view()
	{
		$object = new self('scm');
		$object->init();
		$object->check_authorizations();
		$object->build_view(AppContext::get_request());
		return $object->view;
	}
}
?>
