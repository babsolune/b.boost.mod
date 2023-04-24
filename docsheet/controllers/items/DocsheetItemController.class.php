<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 03 27
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class DocsheetItemController extends DefaultModuleController
{
	protected function get_template_to_use()
	{
		return new FileTemplate('docsheet/DocsheetItemController.tpl');
	}

	public function execute(HTTPRequestCustom $request)
	{
		$this->build_view();
		$this->check_pending_items($request);

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
					$this->item = DocsheetService::get_item($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->item = new DocsheetItem();
		}
		return $this->item;
	}

	private function check_pending_items(HTTPRequestCustom $request)
	{
		if (!$this->item->is_published())
		{
			$this->view->put('NOT_VISIBLE_MESSAGE', MessageHelper::display(LangLoader::get_message('warning.element.not.visible', 'warning-lang'), MessageHelper::WARNING));
		}
		else
		{
			if ($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), DocsheetUrlBuilder::display($this->item->get_category()->get_id(), $this->item->get_category()->get_rewrited_name(), $this->item->get_id(), $this->item->get_rewrited_title())->rel()))
			{
				$this->item->set_views_number($this->item->get_views_number() + 1);
				DocsheetService::update_views_number($this->item);
			}
		}
	}

	private function build_view()
	{
		$comments_config = CommentsConfig::load();
		$content_management_config = ContentManagementConfig::load();
		$item = $this->get_item();
		$item_content = $this->get_item()->get_item_content();
		$category = $item->get_category();
		$this->build_suggested_items($item);
		$this->build_navigation_links($item);

		$keywords = $item->get_keywords();
		$has_keywords = count($keywords) > 0;

		if($item_content->has_content_level())
		{
			switch ($item_content->get_content_level()) {
				case DocsheetItemContent::WIP_LEVEL:
					$this->view->put('LEVEL_MESSAGE', MessageHelper::display($this->lang['docsheet.level.wip.message'], MessageHelper::NOTICE));
				break;
				case DocsheetItemContent::SKETCH_LEVEL:
					$this->view->put('LEVEL_MESSAGE', MessageHelper::display($this->lang['docsheet.level.sketch.message'], MessageHelper::WARNING));
				break;
				case DocsheetItemContent::REDO_LEVEL:
					$this->view->put('LEVEL_MESSAGE', MessageHelper::display($this->lang['docsheet.level.redo.message'], MessageHelper::ERROR));
				break;
				case DocsheetItemContent::CLAIM_LEVEL:
					$this->view->put('LEVEL_MESSAGE', MessageHelper::display($this->lang['docsheet.level.claim.message'], MessageHelper::ERROR));
				break;
				case DocsheetItemContent::TRUST_LEVEL:
					$this->view->put('LEVEL_MESSAGE', MessageHelper::display($this->lang['docsheet.level.trust.message'], MessageHelper::SUCCESS));
				break;
				case DocsheetItemContent::CUSTOM_LEVEL:
					$this->view->put('LEVEL_MESSAGE', MessageHelper::display($item_content->get_custom_level(), MessageHelper::QUESTION));
				break;
			}
		}

		$this->view->put_all(array_merge($item->get_template_vars(), array(
            'MODULE_NAME'           => $this->config->get_module_name(),
			'C_AUTHOR_DISPLAYED'    => $this->config->is_author_displayed(),
			'C_ENABLED_COMMENTS'    => $comments_config->module_comments_is_enabled('docsheet'),
			'C_ENABLED_NOTATION'    => $content_management_config->module_notation_is_enabled('docsheet'),
			'C_KEYWORDS'            => $has_keywords,
		)));

		if ($comments_config->module_comments_is_enabled('docsheet'))
		{
			$comments_topic = new DocsheetCommentsTopic($item);
			$comments_topic->set_id_in_module($item->get_id());
			$comments_topic->set_url(DocsheetUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()));

			$this->view->put('COMMENTS', $comments_topic->display());
		}

		if ($has_keywords)
			$this->build_keywords_view($keywords);

		foreach ($item_content->get_sources() as $name => $url)
		{
			$this->view->assign_block_vars('sources', $item->get_array_tpl_source_vars($name));
		}
	}

	private function build_suggested_items(DocsheetItem $item)
	{
		$now = new Date();

		$result = PersistenceContext::get_querier()->select('SELECT
			i.id, c.item_id, c.title, i.id_category, i.rewrited_title, c.thumbnail, c.content, c.active_content, i.creation_date, c.update_date,
			(2 * FT_SEARCH_RELEVANCE(c.title, :search_content) + FT_SEARCH_RELEVANCE(c.content, :search_content) / 3) AS relevance
		FROM ' . DocsheetSetup::$docsheet_articles_table . ' i
        LEFT JOIN ' . DocsheetSetup::$docsheet_contents_table . ' c ON c.item_id = i.id
		WHERE (FT_SEARCH(c.title, :search_content) OR FT_SEARCH(c.content, :search_content)) AND i.id <> :excluded_id
		AND (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0)))
		AND c.active_content = 1
		ORDER BY relevance DESC LIMIT 0, :limit_nb', array(
			'excluded_id' => $item->get_id(),
			'search_content' => $item->get_item_content()->get_title() .','. $item->get_item_content()->get_content(),
			'timestamp_now' => $now->get_timestamp(),
			'limit_nb' => (int)DocsheetConfig::load()->get_suggested_items_nb()
		));

		$this->view->put('C_SUGGESTED_ITEMS', $result->get_rows_count() > 0 && DocsheetConfig::load()->get_enabled_items_suggestions());

		while ($row = $result->fetch())
		{
			$date = $row['creation_date'] <= $row['update_date'] ? $row['update_date'] : $row['creation_date'];
			$this->view->assign_block_vars('suggested', array(
				'C_HAS_THUMBNAIL' => !empty($row['thumbnail']),
				'TITLE'           => $row['title'],
				'DATE'            => Date::to_format($date, Date::FORMAT_DAY_MONTH_YEAR),
				'U_THUMBNAIL'     => Url::to_rel($row['thumbnail']),
				'U_ITEM'          => DocsheetUrlBuilder::display($row['id_category'], CategoriesService::get_categories_manager()->get_categories_cache()->get_category($row['id_category'])->get_rewrited_name(), $row['id'], $row['rewrited_title'])->rel()
			));
		}
		$result->dispose();
	}

	private function build_navigation_links(DocsheetItem $item)
	{
		$now = new Date();
		$item_timestamp = $item->get_creation_date()->get_timestamp();

		$result = PersistenceContext::get_querier()->select('
		(SELECT i.id, c.title, i.id_category, i.rewrited_title, c.thumbnail, \'PREVIOUS\' as type
		FROM '. DocsheetSetup::$docsheet_articles_table .' i
		LEFT JOIN ' . DocsheetSetup::$docsheet_contents_table . ' c ON c.item_id = i.id
        WHERE (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0))) AND creation_date < :item_timestamp AND id_category IN :authorized_categories ORDER BY creation_date DESC LIMIT 1 OFFSET 0)
		UNION
		(SELECT i.id, c.title, i.id_category, i.rewrited_title, c.thumbnail, \'NEXT\' as type
		FROM '. DocsheetSetup::$docsheet_articles_table .' i
		LEFT JOIN ' . DocsheetSetup::$docsheet_contents_table . ' c ON c.item_id = i.id
        WHERE (published = 1 OR (published = 2 AND publishing_start_date < :timestamp_now AND (publishing_end_date > :timestamp_now OR publishing_end_date = 0))) AND creation_date > :item_timestamp AND id_category IN :authorized_categories ORDER BY creation_date ASC LIMIT 1 OFFSET 0)
		', array(
			'timestamp_now' => $now->get_timestamp(),
			'item_timestamp' => $item_timestamp,
			'authorized_categories' => array($item->get_id_category())
		));

		$this->view->put_all(array(
			'C_RELATED_LINKS' => $result->get_rows_count() > 0 && DocsheetConfig::load()->get_enabled_navigation_links(),
		));

		while ($row = $result->fetch())
		{
			$this->view->put_all(array(
				'C_'. $row['type'] .'_ITEM' => true,
				'C_' . $row['type'] . '_HAS_THUMBNAIL' => !empty($row['thumbnail']),
				$row['type'] . '_ITEM' => $row['title'],
				'U_'. $row['type'] . '_THUMBNAIL' => Url::to_rel($row['thumbnail']),
				'U_'. $row['type'] .'_ITEM' => DocsheetUrlBuilder::display($row['id_category'], CategoriesService::get_categories_manager()->get_categories_cache()->get_category($row['id_category'])->get_rewrited_name(), $row['id'], $row['rewrited_title'])->rel(),
			));
		}
		$result->dispose();
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
				'URL'  => DocsheetUrlBuilder::display_tag($keyword->get_rewrited_name())->rel(),
			));
			$i++;
		}
	}

	private function check_authorizations()
	{
		$item = $this->get_item();

		$current_user = AppContext::get_current_user();
		$not_authorized = !DocsheetAuthorizationsService::check_authorizations($item->get_id_category())->moderation() && !DocsheetAuthorizationsService::check_authorizations($item->get_id_category())->write() && (!DocsheetAuthorizationsService::check_authorizations($item->get_id_category())->contribution() || $item->get_item_content()->get_author_user()->get_id() != $current_user->get_id());

		switch ($item->get_publishing_state()) {
			case DocsheetItem::PUBLISHED:
				if (!DocsheetAuthorizationsService::check_authorizations($item->get_id_category())->read())
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case DocsheetItem::NOT_PUBLISHED:
				if ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case DocsheetItem::DEFERRED_PUBLICATION:
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
		$graphical_environment->set_page_title($item_content->get_title(), ($category->get_id() != Category::ROOT_CATEGORY ? $category->get_name() . ' - ' : '') . $this->config->get_module_name());
		$graphical_environment->get_seo_meta_data()->set_description($item_content->get_real_summary());
		$graphical_environment->get_seo_meta_data()->set_canonical_url(DocsheetUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()));

		if ($item_content->has_thumbnail())
			$graphical_environment->get_seo_meta_data()->set_picture_url($item_content->get_thumbnail());

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->config->get_module_name(),DocsheetUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($item->get_id_category(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), DocsheetUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($item_content->get_title(), DocsheetUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()));

		return $response;
	}
}
?>
