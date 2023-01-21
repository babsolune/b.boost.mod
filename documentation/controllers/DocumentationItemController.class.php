<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 01 09
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class DocumentationItemController extends DefaultModuleController
{
	protected function get_template_to_use()
	{
		return new FileTemplate('documentation/DocumentationItemController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_view();
		$this->count_views_number($request);
		$this->check_authorizations();

		return $this->generate_response();
	}

	private function get_item()
	{
		if ($this->item === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->item = DocumentationService::get_item($id);
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

	private function count_views_number(HTTPRequestCustom $request)
	{
		if (!$this->item->is_published())
		{
			$this->view->put('NOT_VISIBLE_MESSAGE', MessageHelper::display($this->lang['warning.element.not.visible'], MessageHelper::WARNING));
		}
		else
		{
			if ($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), DocumentationUrlBuilder::display($this->item->get_category()->get_id(), $this->item->get_category()->get_rewrited_name(), $this->item->get_id(), $this->item->get_rewrited_title())->rel()))
			{
				$this->item->set_views_number($this->item->get_views_number() + 1);
				DocumentationService::update_views_number($this->item);
			}
		}
	}

	private function build_view()
	{
		$config = DocumentationConfig::load();
		$comments_config = CommentsConfig::load();
		$content_management_config = ContentManagementConfig::load();
		$item = $this->get_item();
		$item_content = $this->get_item()->get_item_content();
		$category = $item->get_category();

		$keywords = $item->get_keywords();
		$has_keywords = count($keywords) > 0;

		if($item_content->has_content_level())
		{
			switch ($item_content->get_content_level()) {
				case DocumentationItemContent::WIP_LEVEL:
					$this->view->put('LEVEL_MESSAGE', MessageHelper::display($this->lang['documentation.level.wip.message'], MessageHelper::NOTICE));
				break;
				case DocumentationItemContent::SKETCH_LEVEL:
					$this->view->put('LEVEL_MESSAGE', MessageHelper::display($this->lang['documentation.level.sketch.message'], MessageHelper::WARNING));
				break;
				case DocumentationItemContent::REDO_LEVEL:
					$this->view->put('LEVEL_MESSAGE', MessageHelper::display($this->lang['documentation.level.redo.message'], MessageHelper::ERROR));
				break;
				case DocumentationItemContent::CLAIM_LEVEL:
					$this->view->put('LEVEL_MESSAGE', MessageHelper::display($this->lang['documentation.level.claim.message'], MessageHelper::ERROR));
				break;
				case DocumentationItemContent::TRUST_LEVEL:
					$this->view->put('LEVEL_MESSAGE', MessageHelper::display($this->lang['documentation.level.trust.message'], MessageHelper::SUCCESS));
				break;
				case DocumentationItemContent::CUSTOM_LEVEL:
					$this->view->put('LEVEL_MESSAGE', MessageHelper::display($item_content->get_custom_level(), MessageHelper::QUESTION));
				break;
			}
		}

		$this->view->put_all(array_merge($item->get_template_vars(), array(
			'C_AUTHOR_DISPLAYED' => $config->is_author_displayed(),
			'C_ENABLED_COMMENTS' => $comments_config->module_comments_is_enabled('documentation'),
			'C_ENABLED_NOTATION' => $content_management_config->module_notation_is_enabled('documentation'),
			'C_KEYWORDS'         => $has_keywords,

			'ARCHIVED_CONTENT'	  => MessageHelper::display($this->lang['documentation.archived.content'], MessageHelper::WARNING),
			'NOT_VISIBLE_MESSAGE' => MessageHelper::display($this->lang['warning.element.not.visible'], MessageHelper::WARNING),
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

		foreach ($item_content->get_sources() as $name => $url)
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
		$item_content = $this->get_item()->get_item_content();
		$category = $item->get_category();
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($item_content->get_title(), ($category->get_id() != Category::ROOT_CATEGORY ? $category->get_name() . ' - ' : '') . $this->lang['documentation.module.title']);
		$graphical_environment->get_seo_meta_data()->set_description($item_content->get_real_summary());
		$graphical_environment->get_seo_meta_data()->set_canonical_url(DocumentationUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()));

		if ($item_content->has_thumbnail())
			$graphical_environment->get_seo_meta_data()->set_picture_url($item_content->get_thumbnail());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['documentation.module.title'],DocumentationUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($item->get_id_category(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), DocumentationUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($item_content->get_title(), DocumentationUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()));
		
		return $response;
	}
}
?>
