<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class DocumentationPendingItemsController extends DefaultModuleController
{
	protected function get_template_to_use()
	{
		return new FileTemplate('documentation/DocumentationSeveralItemsController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->build_view($request);

		return $this->generate_response($request);
	}

	public function build_view(HTTPRequestCustom $request)
	{
		$now = new Date();
		$comments_config = CommentsConfig::load();
		$content_management_config = ContentManagementConfig::load();
		$authorized_categories = CategoriesService::get_authorized_categories(Category::ROOT_CATEGORY, $this->config->is_summary_displayed_to_guests());
		$condition = 'WHERE id_category IN :authorized_categories
		AND (published = 0 OR (published = 2 AND (publishing_start_date > :timestamp_now OR (publishing_end_date != 0 AND publishing_end_date < :timestamp_now))))';
		$parameters = array(
			'user_id' => AppContext::get_current_user()->get_id(),
			'authorized_categories' => $authorized_categories,
			'timestamp_now' => $now->get_timestamp()
		);

		$page = $request->get_getint('page', 1);
		$pagination = $this->get_pagination($condition, $parameters, $page);

		$result = PersistenceContext::get_querier()->select('SELECT i.*, c.*, member.*, f.id AS fav_id, com.comments_number, notes.average_notes, notes.notes_number, note.note
		FROM ' . DocumentationSetup::$documentation_items_table . ' i
		LEFT JOIN ' . DocumentationSetup::$documentation_contents_table . ' c ON c.item_id = i.id
		LEFT JOIN ' . DocumentationSetup::$documentation_favs_table . ' f ON f.item_id = i.id
		LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = c.author_user_id
		LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = i.id AND com.module_id = \'documentation\'
		LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = i.id AND notes.module_name = \'documentation\'
		LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = i.id AND note.module_name = \'documentation\' AND note.user_id = :user_id
		' . $condition .'
		' . (!DocumentationAuthorizationsService::check_authorizations()->moderation() ? ' AND c.author_user_id = :user_id' : '') . '
		AND c.active_content = 1
		ORDER BY c.update_date
		LIMIT :number_items_per_page OFFSET :display_from', array_merge($parameters, array(
			'number_items_per_page' => $pagination->get_number_items_per_page(),
			'display_from' => $pagination->get_display_from()
		)));

		$this->view->put_all(array(
			'C_PENDING'              => true,
			'C_ITEMS'                => $result->get_rows_count() > 0,
			'C_SEVERAL_ITEMS'        => $result->get_rows_count() > 1,
			'C_GRID_VIEW'            => $this->config->get_display_type() == DocumentationConfig::GRID_VIEW,
			'C_LIST_VIEW'            => $this->config->get_display_type() == DocumentationConfig::LIST_VIEW,
			'C_TABLE_VIEW'           => $this->config->get_display_type() == DocumentationConfig::TABLE_VIEW,
			'C_CATEGORY_DESCRIPTION' => !empty($category_description),
			'C_ENABLED_COMMENTS'     => $comments_config->module_comments_is_enabled('documentation'),
			'C_ENABLED_NOTATION'     => $content_management_config->module_notation_is_enabled('documentation'),
			'C_AUTHOR_DISPLAYED'     => $this->config->is_author_displayed(),
			'C_PAGINATION'           => $pagination->has_several_pages(),

			'CATEGORIES_PER_ROW' => $this->config->get_categories_per_row(),
			'ITEMS_PER_ROW'      => $this->config->get_items_per_row(),
			'PAGINATION'         => $pagination->display(),
			'TABLE_COLSPAN'      => 4 + (int)$comments_config->module_comments_is_enabled('documentation') + (int)$content_management_config->module_notation_is_enabled('documentation')
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

	private function get_pagination($condition, $parameters, $page)
	{
		$items_number = DocumentationService::count($condition, $parameters);

		$pagination = new ModulePagination($page, $items_number, (int)DocumentationConfig::load()->get_items_per_page());
		$pagination->set_url(DocumentationUrlBuilder::display_pending('%d'));

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
		if (!(DocumentationAuthorizationsService::check_authorizations()->write() || DocumentationAuthorizationsService::check_authorizations()->contribution() || DocumentationAuthorizationsService::check_authorizations()->moderation()))
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
		$graphical_environment->set_page_title($this->lang['documentation.pending.items'], $this->lang['documentation.module.title'], $page);
		$graphical_environment->get_seo_meta_data()->set_description($this->lang['documentation.seo.description.pending'], $page);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(DocumentationUrlBuilder::display_pending($page));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['documentation.module.title'], DocumentationUrlBuilder::home());
		$breadcrumb->add($this->lang['documentation.pending.items'], DocumentationUrlBuilder::display_pending($page));

		return $response;
	}
}
?>
