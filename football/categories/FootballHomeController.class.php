<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 04 08
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class FootballHomeController extends DefaultModuleController
{
	private $category;

	protected function get_template_to_use()
	{
		return new FileTemplate('football/FootballHomeController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_view();

		return $this->generate_response();
	}

	private function build_view()
	{
		$now = new Date();
		$categories = CategoriesService::get_categories_manager(self::$module_id)->get_categories_cache()->get_categories();
		$authorized_categories = CategoriesService::get_authorized_categories(Category::ROOT_CATEGORY, true, self::$module_id);

		foreach ($categories as $id => $category)
		{
			if ($id == Category::ROOT_CATEGORY)
			{
				$this->view->put_all(array(
					'C_ROOT_CONTROLS'      => FootballAuthorizationsService::check_authorizations($id)->moderation(),
                    'C_ROOT_ITEMS'         => $category->get_elements_number() > 0,
					'C_SEVERAL_ROOT_ITEMS' => $category->get_elements_number() > 1,
				));

				$result = PersistenceContext::get_querier()->select('SELECT i.*, member.*
				FROM ' . FootballSetup::$football_compet_table . ' i
				LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = i.author_user_id
				WHERE i.id_category = :id_category
				AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
                ORDER BY i.id_category', array(
					'user_id' => AppContext::get_current_user()->get_id(),
					'timestamp_now' => $now->get_timestamp(),
					'id_category' => $category->get_id()
				));

				while ($row = $result->fetch()) {
					$item = new FootballCompet();
					$item->set_properties($row);

					$this->view->assign_block_vars('root_items', $item->get_template_vars());
				}
				$result->dispose();
			}

			if ($id != Category::ROOT_CATEGORY && in_array($id, $authorized_categories))
			{
				$category_elements_number = isset($categories_elements_number[$id]) ? $categories_elements_number[$id] : $category->get_elements_number();
                $this->view->assign_block_vars('categories', array(
                    'C_CONTROLS'            => FootballAuthorizationsService::check_authorizations()->moderation(),
					'C_ITEMS'               => $category_elements_number > 0,
					'C_SEVERAL_ITEMS'       => $category_elements_number > 1,
                    // 'C_CATEGORY_THUMBNAIL'  => !empty($category->get_thumbnail()),
					'ITEMS_NUMBER'          => $category->get_elements_number(),
					'CATEGORY_ID'           => $category->get_id(),
					'CATEGORY_SUB_ORDER'    => $category->get_order(),
					'CATEGORY_PARENT_ID'    => $category->get_id_parent(),
					'CATEGORY_NAME'         => $category->get_name(),

                    // 'U_CATEGORY_THUMBNAIL' => $category->get_thumbnail()->rel(),
					'U_CATEGORY'           => FootballUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel(),
				));

				$result = PersistenceContext::get_querier()->select('SELECT i.*, member.*
				FROM ' . FootballSetup::$football_compet_table . ' i
				LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = i.author_user_id
				WHERE id_category = :id_category
                AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
                ORDER BY i.id_category', array(
					'user_id' => AppContext::get_current_user()->get_id(),
                    'id_category' => $category->get_id(),
                    'timestamp_now' => $now->get_timestamp()
				));

				while ($row = $result->fetch()) {
					$item = new FootballCompet();
					$item->set_properties($row);

                    if ($this->check_season($item->get_compet_season_id()))
                    $this->view->assign_block_vars('categories.items', $item->get_template_vars());
				}
				$result->dispose();
			}
		}
	}

    private function check_season(int $season_id)
    {
        $now = new Date();
        $season_name = FootballSeasonService::get_season($season_id)->get_season_name();
        $season_parts = explode('-', $season_name);

        return in_array($now->get_year(),$season_parts);
    }

	private function get_category()
	{
		if ($this->category === null)
		{
			$id = AppContext::get_request()->get_getint('id_category', 0);
			if (!empty($id))
			{
				try {
					$this->category = CategoriesService::get_categories_manager('football')->get_categories_cache()->get_category($id);
				} catch (CategoryNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->category = CategoriesService::get_categories_manager('football')->get_categories_cache()->get_category(Category::ROOT_CATEGORY);
			}
		}
		return $this->category;
	}

	private function check_authorizations()
	{
        if (!FootballAuthorizationsService::check_authorizations($this->get_category()->get_id())->read())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }
	}

	private function generate_response()
	{
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
        $graphical_environment->set_page_title($this->lang['football.module.title']);
		// $description = StringVars::replace_vars($this->lang['football.seo.description.root'], array('site' => GeneralConfig::load()->get_site_name()));
		// $graphical_environment->get_seo_meta_data()->set_description($description);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(FootballUrlBuilder::display_category($this->get_category()->get_id(), $this->get_category()->get_rewrited_name()));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['football.module.title'], FootballUrlBuilder::home());

		return $response;
	}

	public static function get_view()
	{
		$object = new self('football');
		$object->check_authorizations();
		$object->build_view(AppContext::get_request());
		return $object->view;
	}
}
?>
