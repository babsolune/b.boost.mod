<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2022 11 18
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class DocumentationItemArchiveController extends DefaultModuleController
{
	protected function get_template_to_use()
	{
		return new FileTemplate('documentation/DocumentationItemController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_view();
		$this->check_authorizations();

		return $this->generate_response();
	}

	private function get_item()
	{
		if ($this->item === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			$content_id = AppContext::get_request()->get_getint('content_id', 0);
			if (!empty($id))
			{
				try {
					$this->item = DocumentationService::get_item_archive($id, $content_id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->item = new DocumentationItem();
		}
		return $this->item;
	}

	private function build_view()
	{
		$config = DocumentationConfig::load();
		$comments_config = CommentsConfig::load();
		$content_management_config = ContentManagementConfig::load();
		$item = $this->get_item();
		$category = $item->get_category();

		$keywords = $item->get_keywords();
		$has_keywords = count($keywords) > 0;

		$this->view->put_all(array_merge($item->get_template_vars(), array(
			'C_AUTHOR_DISPLAYED' => $config->is_author_displayed(),
			'C_ENABLED_COMMENTS' => $comments_config->module_comments_is_enabled('documentation'),
			'C_ENABLED_NOTATION' => $content_management_config->module_notation_is_enabled('documentation'),
			'C_KEYWORDS'         => $has_keywords,
			'C_ARCHIVE'          => $item->is_published() && $item->get_item_content()->get_active_content() == 0,
			'C_RESTORE' 		 => DocumentationAuthorizationsService::check_authorizations()->display_restore_link(),

			'ARCHIVED_CONTENT' => MessageHelper::display($this->lang['documentation.archived.content'], MessageHelper::WARNING),

			'U_DELETE_CONTENT' => DocumentationUrlBuilder::delete($item->get_id(), $item->get_item_content()->get_content_id())->rel(),
			'U_RESTORE' 	   => DocumentationUrlBuilder::restore($item->get_id(), $item->get_item_content()->get_content_id())->rel()
		)));

		if ($comments_config->module_comments_is_enabled('documentation'))
		{
			$comments_topic = new DocumentationCommentsTopic($item);
			$comments_topic->set_id_in_module($item->get_id());
			$comments_topic->set_url(DocumentationUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()));

			$this->view->put('COMMENTS', $comments_topic->display());
		}

		if ($has_keywords)
			$this->build_keywords_view($keywords);

		foreach ($item->get_item_content()->get_sources() as $name => $url)
		{
			$this->view->assign_block_vars('sources', $item->get_array_tpl_source_vars($name));
		}
	}

	private function build_keywords_view($keywords)
	{
		$nbr_keywords = count($keywords);

		$i = 1;
		foreach ($keywords as $keyword)
		{
			$this->view->assign_block_vars('keywords', array(
				'C_SEPARATOR' => $i < $nbr_keywords,
				'NAME' => $keyword->get_name(),
				'URL'  => DocumentationUrlBuilder::display_tag($keyword->get_rewrited_name())->rel(),
			));
			$i++;
		}
	}

	private function check_authorizations()
	{
		$item = $this->get_item();

		$current_user = AppContext::get_current_user();
		$not_authorized = !DocumentationAuthorizationsService::check_authorizations($item->get_id_category())->moderation() && !DocumentationAuthorizationsService::check_authorizations($item->get_id_category())->write() && (!DocumentationAuthorizationsService::check_authorizations($item->get_id_category())->contribution() || $item->get_item_content()->get_author_user()->get_id() != $current_user->get_id());

		switch ($item->get_publishing_state()) {
			case DocumentationItem::PUBLISHED:
				if (!DocumentationAuthorizationsService::check_authorizations($item->get_id_category())->read())
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case DocumentationItem::NOT_PUBLISHED:
				if ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case DocumentationItem::DEFERRED_PUBLICATION:
				if (!$item->is_published() && ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL)))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			default:
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			break;
		}
	}

	private function generate_response()
	{
		$item = $this->get_item();
		$category = $item->get_category();
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($item->get_item_content()->get_title(), ($category->get_id() != Category::ROOT_CATEGORY ? $category->get_name() . ' - ' : '') . $this->lang['documentation.module.title']);
		$graphical_environment->get_seo_meta_data()->set_description($item->get_item_content()->get_real_summary());
		$graphical_environment->get_seo_meta_data()->set_canonical_url(DocumentationUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()));

		if ($item->get_item_content()->has_thumbnail())
			$graphical_environment->get_seo_meta_data()->set_picture_url($item->get_item_content()->get_thumbnail());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['documentation.module.title'],DocumentationUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($item->get_id_category(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), DocumentationUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($item->get_item_content()->get_title(), DocumentationUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()));
		$breadcrumb->add($this->lang['documentation.archive'] . ' ' . $item->get_item_content()->get_content_id(), DocumentationUrlBuilder::archive($item->get_id(), $item->get_item_content()->get_content_id()));
		
		return $response;
	}
}
?>
