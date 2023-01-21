<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class DocumentationTagController extends DefaultModuleController
{
	private $keyword;
	private $comments_config;
	private $content_management_config;

	protected function get_template_to_use()
	{
		return new FileTemplate('documentation/DocumentationSeveralItemsController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->build_view($request);

		return $this->generate_response($request);
	}

	public function init()
	{
		$this->comments_config = CommentsConfig::load();
		$this->content_management_config = ContentManagementConfig::load();
	}

	public function build_view(HTTPRequestCustom $request)
	{
		$now = new Date();

		$authorized_categories = CategoriesService::get_authorized_categories(Category::ROOT_CATEGORY, $this->config->is_summary_displayed_to_guests());
		$condition = 'WHERE relation.id_keyword = :id_keyword
		AND id_category IN :authorized_categories
		AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))';
		$parameters = array(
			'id_keyword' => $this->get_keyword()->get_id(),
			'authorized_categories' => $authorized_categories,
			'timestamp_now' => $now->get_timestamp()
		);

		$page = $request->get_getint('page', 1);
		$pagination = $this->get_pagination($condition, $parameters, $page);

		$result = PersistenceContext::get_querier()->select('SELECT i.*, c.*, member.*, f.id AS fav_id, com.comments_number, notes.average_notes, notes.notes_number, note.note
		FROM ' . DocumentationSetup::$documentation_items_table . ' i
		LEFT JOIN ' . DocumentationSetup::$documentation_contents_table . ' c ON c.item_id = i.id
		LEFT JOIN ' . DocumentationSetup::$documentation_favs_table .' f ON f.item_id = i.id
		LEFT JOIN ' . DB_TABLE_KEYWORDS_RELATIONS . ' relation ON relation.module_id = \'documentation\' AND relation.id_in_module = i.id
		LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = c.author_user_id
		LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = i.id AND com.module_id = \'documentation\'
		LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = i.id AND notes.module_name = \'documentation\'
		LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = i.id AND note.module_name = \'documentation\' AND note.user_id = :user_id
		' . $condition . '
		AND c.active_content = 1
		ORDER BY c.update_date
		LIMIT :number_items_per_page OFFSET :display_from', array_merge($parameters, array(
			'user_id' => AppContext::get_current_user()->get_id(),
			'number_items_per_page' => $pagination->get_number_items_per_page(),
			'display_from' => $pagination->get_display_from()
		)));

		$this->view->put_all(array(
			'C_TAG_ITEMS'            => true,
			'C_ITEMS'                => $result->get_rows_count() > 0,
			'C_SEVERAL_ITEMS'        => $result->get_rows_count() > 1,
			'C_GRID_VIEW'            => $this->config->get_display_type() == DocumentationConfig::GRID_VIEW,
			'C_LIST_VIEW'            => $this->config->get_display_type() == DocumentationConfig::LIST_VIEW,
			'C_TABLE_VIEW'           => $this->config->get_display_type() == DocumentationConfig::TABLE_VIEW,
			'C_ENABLED_COMMENTS'     => $this->comments_config->module_comments_is_enabled('documentation'),
			'C_ENABLED_NOTATION'     => $this->content_management_config->module_notation_is_enabled('documentation'),
			'C_AUTHOR_DISPLAYED'     => $this->config->is_author_displayed(),
			'C_PAGINATION'           => $pagination->has_several_pages(),
			'C_CATEGORY_DESCRIPTION' => !empty($category_description),

			'CATEGORIES_PER_ROW' => $this->config->get_categories_per_row(),
			'ITEMS_PER_ROW'      => $this->config->get_items_per_row(),
			'PAGINATION'         => $pagination->display(),
			'TABLE_COLSPAN'      => 4 + (int)$this->comments_config->module_comments_is_enabled('documentation') + (int)$this->content_management_config->module_notation_is_enabled('documentation'),
			'CATEGORY_NAME'      => $this->get_keyword()->get_name()
		));

		while ($row = $result->fetch())
		{
			$item = new DocumentationItem();
			$item->set_properties($row);

			$keywords = $item->get_keywords();
			$has_keywords = count($keywords) > 0;

			$this->view->assign_block_vars('items', array_merge($item->get_template_vars(), array(
				'C_KEYWORDS' => $has_keywords
			)));

			if ($has_keywords)
				$this->build_keywords_view($keywords);

			foreach ($item->get_item_content()->get_sources() as $name => $url)
			{
				$this->view->assign_block_vars('items.sources', $item->get_array_tpl_source_vars($name));
			}
		}
		$result->dispose();
	}

	private function get_keyword()
	{
		if ($this->keyword === null)
		{
			$rewrited_name = AppContext::get_request()->get_getstring('tag', '');
			if (!empty($rewrited_name))
			{
				try {
					$this->keyword = KeywordsService::get_keywords_manager()->get_keyword('WHERE rewrited_name=:rewrited_name', array('rewrited_name' => $rewrited_name));
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}
		}
		return $this->keyword;
	}

	private function get_pagination($condition, $parameters, $page)
	{
		$result = PersistenceContext::get_querier()->select_single_row_query('SELECT COUNT(*) AS items_number
		FROM '. DocumentationSetup::$documentation_items_table .' documentation
		LEFT JOIN '. DB_TABLE_KEYWORDS_RELATIONS .' relation ON relation.module_id = \'documentation\' AND relation.id_in_module = documentation.id
		' . $condition, $parameters);

		$pagination = new ModulePagination($page, $result['items_number'], (int)DocumentationConfig::load()->get_items_per_page());
		$pagination->set_url(DocumentationUrlBuilder::display_tag($this->get_keyword()->get_rewrited_name(), '%d'));

		if ($pagination->current_page_is_empty() && $page > 1)
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}

		return $pagination;
	}

	private function build_keywords_view($keywords)
	{
		$nbr_keywords = count($keywords);

		$i = 1;
		foreach ($keywords as $keyword)
		{
			$this->view->assign_block_vars('items.keywords', array(
				'C_SEPARATOR' => $i < $nbr_keywords,
				'NAME' => $keyword->get_name(),
				'URL'  => DocumentationUrlBuilder::display_tag($keyword->get_rewrited_name())->rel(),
			));
			$i++;
		}
	}

	private function check_authorizations()
	{
		if (!DocumentationAuthorizationsService::check_authorizations()->read())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}

	private function generate_response(HTTPRequestCustom $request)
	{
		$page = $request->get_getint('page', 1);
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->get_keyword()->get_name(), $this->lang['documentation.module.title'], $page);
		$graphical_environment->get_seo_meta_data()->set_description(StringVars::replace_vars($this->lang['documentation.seo.description.tag'], array('subject' => $this->get_keyword()->get_name())), $page);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(DocumentationUrlBuilder::display_tag($this->get_keyword()->get_rewrited_name(), $page));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['documentation.module.title'], DocumentationUrlBuilder::home());
		$breadcrumb->add($this->get_keyword()->get_name(), DocumentationUrlBuilder::display_tag($this->get_keyword()->get_rewrited_name(), $page));

		return $response;
	}
}
?>
