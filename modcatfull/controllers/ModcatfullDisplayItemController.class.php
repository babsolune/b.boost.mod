<?php
/*##################################################
 *		       ModcatfullDisplayItemController.class.php
 *                            -------------------
 *   begin                : Month XX, 2017
 *   copyright            : (C) 2017 Firstname LASTNAME
 *   email                : nickname@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

/**
 * @author Firstname LASTNAME <nickname@phpboost.com>
 */

class ModcatfullDisplayItemController extends ModuleController
{
	private $lang;
	private $tpl;
	private $itemcatfull;
	private $category;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->check_pending_itemcatfull($request);

		$this->build_view($request);

		return $this->generate_response();
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'modcatfull');
		$this->tpl = new FileTemplate('modcatfull/ModcatfullDisplayItemController.tpl');
		$this->tpl->add_lang($this->lang);
	}

	private function get_itemcatfull()
	{
		if ($this->itemcatfull === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try
				{
					$this->itemcatfull = ModcatfullService::get_itemcatfull('WHERE modcatfull.id=:id', array('id' => $id));
				}
				catch (RowNotFoundException $e)
				{
					$error_controller = PHPBoostErrors::unexisting_page();
   					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->itemcatfull = new Itemcatfull();
		}
		return $this->itemcatfull;
	}

	private function check_pending_itemcatfull(HTTPRequestCustom $request)
	{
		if (!$this->itemcatfull->is_published())
		{
			$this->tpl->put('NOT_VISIBLE_MESSAGE', MessageHelper::display(LangLoader::get_message('element.not_visible', 'status-messages-common'), MessageHelper::WARNING));
		}
		else
		{
			if ($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), ModcatfullUrlBuilder::display_item($this->itemcatfull->get_category()->get_id(), $this->itemcatfull->get_category()->get_rewrited_name(), $this->itemcatfull->get_id(), $this->itemcatfull->get_rewrited_title())->rel()))
			{
				$this->itemcatfull->set_views_number($this->itemcatfull->get_views_number() + 1);
				ModcatfullService::update_views_number($this->itemcatfull);
			}
		}
	}

	private function build_view(HTTPRequestCustom $request)
	{
		$current_page = $request->get_getint('page', 1);
		$config = ModcatfullConfig::load();
		$comments_config = new ModcatfullComments();
		$notation_config = new ModcatfullNotation();

		$this->category = $this->itemcatfull->get_category();

		$itemcatfull_contents = $this->itemcatfull->get_contents();

		//If itemcatfull doesn't begin with a page, we insert one
		if (TextHelper::substr(trim($itemcatfull_contents), 0, 6) != '[page]')
		{
			$itemcatfull_contents = '[page]&nbsp;[/page]' . $itemcatfull_contents;
		}

		//Removing [page] bbcode
		$itemcatfull_contents_clean = preg_split('`\[page\].+\[/page\](.*)`usU', $itemcatfull_contents, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		//Retrieving pages
		preg_match_all('`\[page\]([^[]+)\[/page\]`uU', $itemcatfull_contents, $pages_array);

		$page_nbr = count($pages_array[1]);

		if ($page_nbr > 1)
			$this->build_form($pages_array, $current_page);

		$this->build_sources_view();
		$this->build_carousel_view();
		$this->build_keywords_view();
		$this->build_suggested_items($this->itemcatfull);
		$this->build_navigation_links($this->itemcatfull);

		$page_name = (isset($pages_array[1][$current_page-1]) && $pages_array[1][$current_page-1] != '&nbsp;') ? $pages_array[1][($current_page-1)] : '';

		$this->tpl->put_all(array_merge($this->itemcatfull->get_array_tpl_vars(), array(
			'C_COMMENTS_ENABLED' => $comments_config->are_comments_enabled(),
			'C_NOTATION_ENABLED' => $notation_config->is_notation_enabled(),
			'KERNEL_NOTATION'    => NotationService::display_active_image($this->itemcatfull->get_notation()),
			'CONTENTS'           => isset($itemcatfull_contents_clean[$current_page-1]) ? FormatingHelper::second_parse($itemcatfull_contents_clean[$current_page-1]) : '',
			'PAGE_NAME'          => $page_name,
			'U_EDIT_ITEM'     	 => $page_name !== '' ? ModcatfullUrlBuilder::edit_item($this->itemcatfull->get_id(), $current_page)->rel() : ModcatfullUrlBuilder::edit_item($this->itemcatfull->get_id())->rel()
		)));

		$this->build_pages_pagination($current_page, $page_nbr, $pages_array);

		//Affichage commentaires
		if ($comments_config->are_comments_enabled())
		{
			$comments_topic = new ModcatfullCommentsTopic($this->itemcatfull);
			$comments_topic->set_id_in_module($this->itemcatfull->get_id());
			$comments_topic->set_url(ModcatfullUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemcatfull->get_id(), $this->itemcatfull->get_rewrited_title()));

			$this->tpl->put('COMMENTS', $comments_topic->display());
		}
	}

	private function build_form($pages_array, $current_page)
	{
		$form = new HTMLForm(__CLASS__, '', false);
		$form->set_css_class('options');

		$fieldset = new FormFieldsetHorizontal('pages', array('description' => $this->lang['modcatfull.summary']));

		$form->add_fieldset($fieldset);

		$itemcatfull_pages = $this->list_itemcatfull_pages($pages_array);

		$fieldset->add_field(new FormFieldSimpleSelectChoice('itemcatfull_pages', '', $current_page, $itemcatfull_pages,
			array('class' => 'summary', 'events' => array('change' => 'document.location = "' . ModcatfullUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemcatfull->get_id(), $this->itemcatfull->get_rewrited_title())->rel() . '" + HTMLForms.getField("itemcatfull_pages").getValue();'))
		));

		$this->tpl->put('FORM', $form->display());
	}

	private function build_pages_pagination($current_page, $page_nbr, $pages_array)
	{
		$this->tpl->put_all(array(
			'C_FIRST_PAGE' => $current_page >= 0 && $current_page<= 1,
		));

		if ($page_nbr > 1)
		{
			$pagination = $this->get_pagination($page_nbr, $current_page);

			if ($current_page > 1 && $current_page <= $page_nbr)
			{
				$previous_page = ModcatfullUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemcatfull->get_id(), $this->itemcatfull->get_rewrited_title())->rel() . ($current_page - 1);

				$this->tpl->put_all(array(
					'U_PREVIOUS_PAGE' => $previous_page,
					'L_PREVIOUS_TITLE' => $pages_array[1][$current_page-2]
				));
			}

			if ($current_page > 0 && $current_page < $page_nbr)
			{
				$next_page = ModcatfullUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemcatfull->get_id(), $this->itemcatfull->get_rewrited_title())->rel() . ($current_page + 1);

				$this->tpl->put_all(array(
					'U_NEXT_PAGE' => $next_page,
					'L_NEXT_TITLE' => $pages_array[1][$current_page]
				));
			}

			$this->tpl->put_all(array(
				'C_PAGINATION' => true,
				'C_PREVIOUS_PAGE' => ($current_page != 1) ? true : false,
				'C_NEXT_PAGE' => ($current_page != $page_nbr) ? true : false,
				'ITEMS_PAGINATION' => $pagination->display()
			));
		}
	}

	private function list_itemcatfull_pages($pages_array)
	{
		$options = array();

		$i = 1;
		foreach ($pages_array[1] as $page_name)
		{
			$options[] = new FormFieldSelectChoiceOption($page_name, $i++);
		}

		return $options;
	}

	private function build_sources_view()
	{
		$sources = $this->itemcatfull->get_sources();
		$nbr_sources = count($sources);
		$this->tpl->put('C_SOURCES', $nbr_sources > 0);

		$i = 1;
		foreach ($sources as $name => $url)
		{
			$this->tpl->assign_block_vars('sources', array(
				'C_SEPARATOR' => $i < $nbr_sources,
				'NAME' => $name,
				'URL' => $url,
			));
			$i++;
		}
	}

	private function build_carousel_view()
	{
		$carousel = $this->itemcatfull->get_carousel();
		$nbr_pictures = count($carousel);
		$this->tpl->put('C_CAROUSEL', $nbr_pictures > 0);


		$i = 1;
		foreach ($carousel as $name => $url)
		{
			if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
				$ptr = false;
			else
				$ptr = true;

			$this->tpl->assign_block_vars('carousel', array(
				'C_PTR' => $ptr,
				'NAME' => $name,
				'URL' => $url,
			));
			$i++;
		}
	}

	private function build_keywords_view()
	{
		$keywords = $this->itemcatfull->get_keywords();
		$nbr_keywords = count($keywords);
		$this->tpl->put('C_KEYWORDS', $nbr_keywords > 0);

		$i = 1;
		foreach ($keywords as $keyword)
		{
			$this->tpl->assign_block_vars('keywords', array(
				'C_SEPARATOR' => $i < $nbr_keywords,
				'NAME' => $keyword->get_name(),
				'URL' => ModcatfullUrlBuilder::display_tag($keyword->get_rewrited_name())->rel(),
			));
			$i++;
		}
	}

	private function build_suggested_items(Itemcatfull $itemcatfull)
	{
		$now = new Date();

		$result = PersistenceContext::get_querier()->select('
		SELECT id, title, category_id, rewrited_title, thumbnail_url,
		(2 * FT_SEARCH_RELEVANCE(title, :search_content) + FT_SEARCH_RELEVANCE(contents, :search_content) / 3) AS relevance
		FROM ' . ModcatfullSetup::$modcatfull_table . '
		WHERE (FT_SEARCH(title, :search_content) OR FT_SEARCH(contents, :search_content)) AND id <> :excluded_id
		AND (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0)))
		ORDER BY relevance DESC LIMIT 0, :limit_nb', array(
			'excluded_id' => $itemcatfull->get_id(),
			'search_content' => $itemcatfull->get_title() .','. $itemcatfull->get_contents(),
			'timestamp_now' => $now->get_timestamp(),
			'limit_nb' => (int) ModcatfullConfig::load()->get_suggested_items_nb()
		));

		$this->tpl->put_all(array(
			'C_SUGGESTED_ITEMS' => $result->get_rows_count() > 0 && ModcatfullConfig::load()->get_enabled_items_suggestions(),
			'SUGGESTED_COLUMNS' => ModcatfullConfig::load()->get_cols_number_displayed_per_line()
		));

		while ($row = $result->fetch())
		{
			if(filter_var($row['thumbnail_url'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
				$ptr = false;
			else
				$ptr = true;

			$this->tpl->assign_block_vars('suggested_items', array(
				'C_PTR' => $ptr,
				'C_HAS_THUMBNAIL' => !empty($row['thumbnail_url']),
				'TITLE' => $row['title'],
				'THUMBNAIL' => $row['thumbnail_url'],
				'U_ITEM' => ModcatfullUrlBuilder::display_item($row['category_id'], ModcatfullService::get_categories_manager()->get_categories_cache()->get_category($row['category_id'])->get_rewrited_name(), $row['id'], $row['rewrited_title'])->rel()
			));
		}
		$result->dispose();
	}

	private function build_navigation_links(Itemcatfull $itemcatfull)
	{
		$now = new Date();
		$timestamp_itemcatfull = $itemcatfull->get_creation_date()->get_timestamp();

		$result = PersistenceContext::get_querier()->select('
		(SELECT id, title, category_id, rewrited_title, thumbnail_url, \'PREVIOUS\' as type
		FROM '. ModcatfullSetup::$modcatfull_table .'
		WHERE (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0))) AND creation_date < :timestamp_itemcatfull AND category_id IN :authorized_categories ORDER BY creation_date DESC LIMIT 1 OFFSET 0)
		UNION
		(SELECT id, title, category_id, rewrited_title, thumbnail_url, \'NEXT\' as type
		FROM '. ModcatfullSetup::$modcatfull_table .'
		WHERE (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0))) AND creation_date > :timestamp_itemcatfull AND category_id IN :authorized_categories ORDER BY creation_date ASC LIMIT 1 OFFSET 0)
		', array(
			'timestamp_now' => $now->get_timestamp(),
			'timestamp_itemcatfull' => $timestamp_itemcatfull,
			'authorized_categories' => array($itemcatfull->get_category_id())
		));

		$this->tpl->put_all(array(
			'C_NAVIGATION_LINKS' => $result->get_rows_count() > 0 && ModcatfullConfig::load()->get_enabled_navigation_links(),
		));

		while ($row = $result->fetch())
		{
			if(filter_var($row['thumbnail_url'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
				$ptr = false;
			else
				$ptr = true;

			$this->tpl->put_all(array(
				'C_'. $row['type'] .'_ITEM' => true,
				'C_' . $row['type'] . '_PTR' => $ptr,
				'C_' . $row['type'] . '_HAS_THUMBNAIL' => !empty($row['thumbnail_url']),
				$row['type'] . '_ITEM_TITLE' => $row['title'],
				$row['type'] . '_THUMBNAIL' => $row['thumbnail_url'],
				'U_'. $row['type'] .'_ITEM' => ModcatfullUrlBuilder::display_item($row['category_id'], ModcatfullService::get_categories_manager()->get_categories_cache()->get_category($row['category_id'])->get_rewrited_name(), $row['id'], $row['rewrited_title'])->rel(),
			));
		}
		$result->dispose();
	}

	private function check_authorizations()
	{
		$itemcatfull = $this->get_itemcatfull();

		$current_user = AppContext::get_current_user();
		$not_authorized = !ModcatfullAuthorizationsService::check_authorizations($itemcatfull->get_category_id())->moderation() && !ModcatfullAuthorizationsService::check_authorizations($itemcatfull->get_category_id())->write() && (!ModcatfullAuthorizationsService::check_authorizations($itemcatfull->get_category_id())->contribution() || $itemcatfull->get_author_user()->get_id() != $current_user->get_id());

		switch ($itemcatfull->get_publication_state())
		{
			case Itemcatfull::PUBLISHED_NOW:
				if (!ModcatfullAuthorizationsService::check_authorizations($itemcatfull->get_category_id())->read())
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
		   			DispatchManager::redirect($error_controller);
				}
			break;
			case Itemcatfull::NOT_PUBLISHED:
				if ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
		   			DispatchManager::redirect($error_controller);
				}
			break;
			case Itemcatfull::PUBLICATION_DATE:
				if (!$itemcatfull->is_published() && ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL)))
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

	private function get_pagination($page_nbr, $current_page)
	{
		$pagination = new ModulePagination($current_page, $page_nbr, 1, Pagination::LIGHT_PAGINATION);
		$pagination->set_url(ModcatfullUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemcatfull->get_id(), $this->itemcatfull->get_rewrited_title(), '%d'));

		if ($pagination->current_page_is_empty() && $current_page > 1)
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}

		return $pagination;
	}

	private function generate_response()
	{
		$response = new SiteDisplayResponse($this->tpl);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->itemcatfull->get_title(), $this->lang['modcatfull.module.title']);
		$graphical_environment->get_seo_meta_data()->set_description($this->itemcatfull->get_description());
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ModcatfullUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemcatfull->get_id(), $this->itemcatfull->get_rewrited_title(), AppContext::get_request()->get_getint('page', 1)));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['modcatfull.module.title'], ModcatfullUrlBuilder::home());

		$categories = array_reverse(ModcatfullService::get_categories_manager()->get_parents($this->itemcatfull->get_category_id(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ModcatfullUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($this->itemcatfull->get_title(), ModcatfullUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $this->itemcatfull->get_id(), $this->itemcatfull->get_rewrited_title()));

		return $response;
	}
}
?>
