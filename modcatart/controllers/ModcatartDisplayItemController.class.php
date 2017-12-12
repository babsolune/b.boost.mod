<?php
/*##################################################
 *		       ModcatartDisplayItemController.class.php
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

class ModcatartDisplayItemController extends ModuleController
{
	private $lang;
	private $tpl;
	private $itemcatart;
	private $category;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->check_pending_itemcatart($request);

		$this->build_view($request);

		return $this->generate_response();
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'modcatart');
		$this->tpl = new FileTemplate('modcatart/ModcatartDisplayItemController.tpl');
		$this->tpl->add_lang($this->lang);
	}

	private function get_itemcatart()
	{
		if ($this->itemcatart === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try
				{
					$this->itemcatart = ModcatartService::get_itemcatart('WHERE modcatart.id=:id', array('id' => $id));
				}
				catch (RowNotFoundException $e)
				{
					$error_controller = PHPBoostErrors::unexisting_page();
   					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->itemcatart = new Itemcatart();
		}
		return $this->itemcatart;
	}

	private function check_pending_itemcatart(HTTPRequestCustom $request)
	{
		if (!$this->itemcatart->is_published())
		{
			$this->tpl->put('NOT_VISIBLE_MESSAGE', MessageHelper::display(LangLoader::get_message('element.not_visible', 'status-messages-common'), MessageHelper::WARNING));
		}
		else
		{
			if ($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), ModcatartUrlBuilder::display_item($this->itemcatart->get_category()->get_id(), $this->itemcatart->get_category()->get_rewrited_name(), $this->itemcatart->get_id(), $this->itemcatart->get_rewrited_title())->rel()))
			{
				$this->itemcatart->set_views_number($this->itemcatart->get_views_number() + 1);
				ModcatartService::update_views_number($this->itemcatart);
			}
		}
	}

	private function build_view(HTTPRequestCustom $request)
	{
		$current_page = $request->get_getint('page', 1);
		$config = ModcatartConfig::load();
		$comments_config = new ModcatartComments();
		$notation_config = new ModcatartNotation();

		$this->category = $this->itemcatart->get_category();

		$itemcatart_contents = $this->itemcatart->get_contents();

		//If itemcatart doesn't begin with a page, we insert one
		if (TextHelper::substr(trim($itemcatart_contents), 0, 6) != '[page]')
		{
			$itemcatart_contents = '[page]&nbsp;[/page]' . $itemcatart_contents;
		}

		//Removing [page] bbcode
		$itemcatart_contents_clean = preg_split('`\[page\].+\[/page\](.*)`usU', $itemcatart_contents, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		//Retrieving pages
		preg_match_all('`\[page\]([^[]+)\[/page\]`uU', $itemcatart_contents, $pages_array);

		$page_nbr = count($pages_array[1]);

		if ($page_nbr > 1)
			$this->build_form($pages_array, $current_page);

		$this->build_sources_view();
		$this->build_carousel_view();
		$this->build_keywords_view();
		$this->build_suggested_items($this->itemcatart);
		$this->build_navigation_links($this->itemcatart);

		$page_name = (isset($pages_array[1][$current_page-1]) && $pages_array[1][$current_page-1] != '&nbsp;') ? $pages_array[1][($current_page-1)] : '';

		$this->tpl->put_all(array_merge($this->itemcatart->get_array_tpl_vars(), array(
			'C_COMMENTS_ENABLED' => $comments_config->are_comments_enabled(),
			'C_NOTATION_ENABLED' => $notation_config->is_notation_enabled(),
			'KERNEL_NOTATION'    => NotationService::display_active_image($this->itemcatart->get_notation()),
			'CONTENTS'           => isset($itemcatart_contents_clean[$current_page-1]) ? FormatingHelper::second_parse($itemcatart_contents_clean[$current_page-1]) : '',
			'PAGE_NAME'          => $page_name,
			'U_EDIT_ITEM'     	 => $page_name !== '' ? ModcatartUrlBuilder::edit_item($this->itemcatart->get_id(), $current_page)->rel() : ModcatartUrlBuilder::edit_item($this->itemcatart->get_id())->rel()
		)));

		$this->build_pages_pagination($current_page, $page_nbr, $pages_array);

		//Affichage commentaires
		if ($comments_config->are_comments_enabled())
		{
			$comments_topic = new ModcatartCommentsTopic($this->itemcatart);
			$comments_topic->set_id_in_module($this->itemcatart->get_id());
			$comments_topic->set_url(ModcatartUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemcatart->get_id(), $this->itemcatart->get_rewrited_title()));

			$this->tpl->put('COMMENTS', $comments_topic->display());
		}
	}

	private function build_form($pages_array, $current_page)
	{
		$form = new HTMLForm(__CLASS__, '', false);
		$form->set_css_class('options');

		$fieldset = new FormFieldsetHorizontal('pages', array('description' => $this->lang['modcatart.summary']));

		$form->add_fieldset($fieldset);

		$itemcatart_pages = $this->list_itemcatart_pages($pages_array);

		$fieldset->add_field(new FormFieldSimpleSelectChoice('itemcatart_pages', '', $current_page, $itemcatart_pages,
			array('class' => 'summary', 'events' => array('change' => 'document.location = "' . ModcatartUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemcatart->get_id(), $this->itemcatart->get_rewrited_title())->rel() . '" + HTMLForms.getField("itemcatart_pages").getValue();'))
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
				$previous_page = ModcatartUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemcatart->get_id(), $this->itemcatart->get_rewrited_title())->rel() . ($current_page - 1);

				$this->tpl->put_all(array(
					'U_PREVIOUS_PAGE' => $previous_page,
					'L_PREVIOUS_TITLE' => $pages_array[1][$current_page-2]
				));
			}

			if ($current_page > 0 && $current_page < $page_nbr)
			{
				$next_page = ModcatartUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemcatart->get_id(), $this->itemcatart->get_rewrited_title())->rel() . ($current_page + 1);

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

	private function list_itemcatart_pages($pages_array)
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
		$sources = $this->itemcatart->get_sources();
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
		$carousel = $this->itemcatart->get_carousel();
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
		$keywords = $this->itemcatart->get_keywords();
		$nbr_keywords = count($keywords);
		$this->tpl->put('C_KEYWORDS', $nbr_keywords > 0);

		$i = 1;
		foreach ($keywords as $keyword)
		{
			$this->tpl->assign_block_vars('keywords', array(
				'C_SEPARATOR' => $i < $nbr_keywords,
				'NAME' => $keyword->get_name(),
				'URL' => ModcatartUrlBuilder::display_tag($keyword->get_rewrited_name())->rel(),
			));
			$i++;
		}
	}

	private function build_suggested_items(Itemcatart $itemcatart)
	{
		$now = new Date();

		$result = PersistenceContext::get_querier()->select('
		SELECT id, title, category_id, rewrited_title, thumbnail_url,
		(2 * FT_SEARCH_RELEVANCE(title, :search_content) + FT_SEARCH_RELEVANCE(contents, :search_content) / 3) AS relevance
		FROM ' . ModcatartSetup::$modcatart_table . '
		WHERE (FT_SEARCH(title, :search_content) OR FT_SEARCH(contents, :search_content)) AND id <> :excluded_id
		AND (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0)))
		ORDER BY relevance DESC LIMIT 0, :limit_nb', array(
			'excluded_id' => $itemcatart->get_id(),
			'search_content' => $itemcatart->get_title() .','. $itemcatart->get_contents(),
			'timestamp_now' => $now->get_timestamp(),
			'limit_nb' => (int) ModcatartConfig::load()->get_suggested_items_nb()
		));

		$this->tpl->put_all(array(
			'C_SUGGESTED_ITEMS' => $result->get_rows_count() > 0 && ModcatartConfig::load()->get_enabled_items_suggestions(),
			'SUGGESTED_COLUMNS' => ModcatartConfig::load()->get_cols_number_displayed_per_line()
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
				'U_ITEM' => ModcatartUrlBuilder::display_item($row['category_id'], ModcatartService::get_categories_manager()->get_categories_cache()->get_category($row['category_id'])->get_rewrited_name(), $row['id'], $row['rewrited_title'])->rel()
			));
		}
		$result->dispose();
	}

	private function build_navigation_links(Itemcatart $itemcatart)
	{
		$now = new Date();
		$timestamp_itemcatart = $itemcatart->get_creation_date()->get_timestamp();

		$result = PersistenceContext::get_querier()->select('
		(SELECT id, title, category_id, rewrited_title, thumbnail_url, \'PREVIOUS\' as type
		FROM '. ModcatartSetup::$modcatart_table .'
		WHERE (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0))) AND creation_date < :timestamp_itemcatart AND category_id IN :authorized_categories ORDER BY creation_date DESC LIMIT 1 OFFSET 0)
		UNION
		(SELECT id, title, category_id, rewrited_title, thumbnail_url, \'NEXT\' as type
		FROM '. ModcatartSetup::$modcatart_table .'
		WHERE (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0))) AND creation_date > :timestamp_itemcatart AND category_id IN :authorized_categories ORDER BY creation_date ASC LIMIT 1 OFFSET 0)
		', array(
			'timestamp_now' => $now->get_timestamp(),
			'timestamp_itemcatart' => $timestamp_itemcatart,
			'authorized_categories' => array($itemcatart->get_category_id())
		));

		$this->tpl->put_all(array(
			'C_NAVIGATION_LINKS' => $result->get_rows_count() > 0 && ModcatartConfig::load()->get_enabled_navigation_links(),
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
				'U_'. $row['type'] .'_ITEM' => ModcatartUrlBuilder::display_item($row['category_id'], ModcatartService::get_categories_manager()->get_categories_cache()->get_category($row['category_id'])->get_rewrited_name(), $row['id'], $row['rewrited_title'])->rel(),
			));
		}
		$result->dispose();
	}

	private function check_authorizations()
	{
		$itemcatart = $this->get_itemcatart();

		$current_user = AppContext::get_current_user();
		$not_authorized = !ModcatartAuthorizationsService::check_authorizations($itemcatart->get_category_id())->moderation() && !ModcatartAuthorizationsService::check_authorizations($itemcatart->get_category_id())->write() && (!ModcatartAuthorizationsService::check_authorizations($itemcatart->get_category_id())->contribution() || $itemcatart->get_author_user()->get_id() != $current_user->get_id());

		switch ($itemcatart->get_publication_state())
		{
			case Itemcatart::PUBLISHED_NOW:
				if (!ModcatartAuthorizationsService::check_authorizations($itemcatart->get_category_id())->read())
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
		   			DispatchManager::redirect($error_controller);
				}
			break;
			case Itemcatart::NOT_PUBLISHED:
				if ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
		   			DispatchManager::redirect($error_controller);
				}
			break;
			case Itemcatart::PUBLICATION_DATE:
				if (!$itemcatart->is_published() && ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL)))
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
		$pagination->set_url(ModcatartUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemcatart->get_id(), $this->itemcatart->get_rewrited_title(), '%d'));

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
		$graphical_environment->set_page_title($this->itemcatart->get_title(), $this->lang['modcatart.module.title']);
		$graphical_environment->get_seo_meta_data()->set_description($this->itemcatart->get_description());
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ModcatartUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemcatart->get_id(), $this->itemcatart->get_rewrited_title(), AppContext::get_request()->get_getint('page', 1)));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['modcatart.module.title'], ModcatartUrlBuilder::home());

		$categories = array_reverse(ModcatartService::get_categories_manager()->get_parents($this->itemcatart->get_category_id(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ModcatartUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($this->itemcatart->get_title(), ModcatartUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $this->itemcatart->get_id(), $this->itemcatart->get_rewrited_title()));

		return $response;
	}
}
?>
