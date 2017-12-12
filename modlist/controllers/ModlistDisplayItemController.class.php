<?php
/*##################################################
 *		       ModlistDisplayItemController.class.php
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

class ModlistDisplayItemController extends ModuleController
{
	private $lang;
	private $tpl;
	private $itemlist;
	private $category;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->check_pending_itemlist($request);

		$this->build_view($request);

		return $this->generate_response();
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'modlist');
		$this->tpl = new FileTemplate('modlist/ModlistDisplayItemController.tpl');
		$this->tpl->add_lang($this->lang);
	}

	private function get_itemlist()
	{
		if ($this->itemlist === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try
				{
					$this->itemlist = ModlistService::get_itemlist('WHERE modlist.id=:id', array('id' => $id));
				}
				catch (RowNotFoundException $e)
				{
					$error_controller = PHPBoostErrors::unexisting_page();
   					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->itemlist = new Itemlist();
		}
		return $this->itemlist;
	}

	private function check_pending_itemlist(HTTPRequestCustom $request)
	{
		if (!$this->itemlist->is_published())
		{
			$this->tpl->put('NOT_VISIBLE_MESSAGE', MessageHelper::display(LangLoader::get_message('element.not_visible', 'status-messages-common'), MessageHelper::WARNING));
		}
		else
		{
			if ($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), ModlistUrlBuilder::display_item($this->itemlist->get_category()->get_id(), $this->itemlist->get_category()->get_rewrited_name(), $this->itemlist->get_id(), $this->itemlist->get_rewrited_title())->rel()))
			{
				$this->itemlist->set_views_number($this->itemlist->get_views_number() + 1);
				ModlistService::update_views_number($this->itemlist);
			}
		}
	}

	private function build_view(HTTPRequestCustom $request)
	{
		$config = ModlistConfig::load();
		$comments_config = new ModlistComments();
		$notation_config = new ModlistNotation();

		$this->category = $this->itemlist->get_category();

		$this->build_sources_view();
		$this->build_carousel_view();
		$this->build_keywords_view();
		$this->build_suggested_items($this->itemlist);
		$this->build_navigation_links($this->itemlist);

		$this->tpl->put_all(array_merge($this->itemlist->get_array_tpl_vars(), array(
			'C_COMMENTS_ENABLED' => $comments_config->are_comments_enabled(),
			'C_NOTATION_ENABLED' => $notation_config->is_notation_enabled(),
			'KERNEL_NOTATION'    => NotationService::display_active_image($this->itemlist->get_notation()),
			'CONTENTS'           => FormatingHelper::second_parse($this->itemlist->get_contents()),
			'U_EDIT_ITEM'     	 => ModlistUrlBuilder::edit_item($this->itemlist->get_id())->rel()
		)));

		//Affichage commentaires
		if ($comments_config->are_comments_enabled())
		{
			$comments_topic = new ModlistCommentsTopic($this->itemlist);
			$comments_topic->set_id_in_module($this->itemlist->get_id());
			$comments_topic->set_url(ModlistUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemlist->get_id(), $this->itemlist->get_rewrited_title()));

			$this->tpl->put('COMMENTS', $comments_topic->display());
		}
	}

	private function build_sources_view()
	{
		$sources = $this->itemlist->get_sources();
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
		$carousel = $this->itemlist->get_carousel();
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
		$keywords = $this->itemlist->get_keywords();
		$nbr_keywords = count($keywords);
		$this->tpl->put('C_KEYWORDS', $nbr_keywords > 0);

		$i = 1;
		foreach ($keywords as $keyword)
		{
			$this->tpl->assign_block_vars('keywords', array(
				'C_SEPARATOR' => $i < $nbr_keywords,
				'NAME' => $keyword->get_name(),
				'URL' => ModlistUrlBuilder::display_tag($keyword->get_rewrited_name())->rel(),
			));
			$i++;
		}
	}

	private function build_suggested_items(Itemlist $itemlist)
	{
		$now = new Date();

		$result = PersistenceContext::get_querier()->select('
		SELECT id, title, category_id, rewrited_title, thumbnail_url,
		(2 * FT_SEARCH_RELEVANCE(title, :search_content) + FT_SEARCH_RELEVANCE(contents, :search_content) / 3) AS relevance
		FROM ' . ModlistSetup::$modlist_table . '
		WHERE (FT_SEARCH(title, :search_content) OR FT_SEARCH(contents, :search_content)) AND id <> :excluded_id
		AND (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0)))
		ORDER BY relevance DESC LIMIT 0, :limit_nb', array(
			'excluded_id' => $itemlist->get_id(),
			'search_content' => $itemlist->get_title() .','. $itemlist->get_contents(),
			'timestamp_now' => $now->get_timestamp(),
			'limit_nb' => (int) ModlistConfig::load()->get_suggested_items_nb()
		));

		$this->tpl->put_all(array(
			'C_SUGGESTED_ITEMS' => $result->get_rows_count() > 0 && ModlistConfig::load()->get_enabled_items_suggestions(),
			'SUGGESTED_COLUMNS' => ModlistConfig::load()->get_cols_number_displayed_per_line()
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
				'U_ITEM' => ModlistUrlBuilder::display_item($row['category_id'], ModlistService::get_categories_manager()->get_categories_cache()->get_category($row['category_id'])->get_rewrited_name(), $row['id'], $row['rewrited_title'])->rel()
			));
		}
		$result->dispose();
	}

	private function build_navigation_links(Itemlist $itemlist)
	{
		$now = new Date();
		$timestamp_itemlist = $itemlist->get_creation_date()->get_timestamp();

		$result = PersistenceContext::get_querier()->select('
		(SELECT id, title, category_id, rewrited_title, thumbnail_url, \'PREVIOUS\' as type
		FROM '. ModlistSetup::$modlist_table .'
		WHERE (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0))) AND creation_date < :timestamp_itemlist AND category_id IN :authorized_categories ORDER BY creation_date DESC LIMIT 1 OFFSET 0)
		UNION
		(SELECT id, title, category_id, rewrited_title, thumbnail_url, \'NEXT\' as type
		FROM '. ModlistSetup::$modlist_table .'
		WHERE (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0))) AND creation_date > :timestamp_itemlist AND category_id IN :authorized_categories ORDER BY creation_date ASC LIMIT 1 OFFSET 0)
		', array(
			'timestamp_now' => $now->get_timestamp(),
			'timestamp_itemlist' => $timestamp_itemlist,
			'authorized_categories' => array($itemlist->get_category_id())
		));

		$this->tpl->put_all(array(
			'C_NAVIGATION_LINKS' => $result->get_rows_count() > 0 && ModlistConfig::load()->get_enabled_navigation_links(),
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
				'U_'. $row['type'] .'_ITEM' => ModlistUrlBuilder::display_item($row['category_id'], ModlistService::get_categories_manager()->get_categories_cache()->get_category($row['category_id'])->get_rewrited_name(), $row['id'], $row['rewrited_title'])->rel(),
			));
		}
		$result->dispose();
	}

	private function check_authorizations()
	{
		$itemlist = $this->get_itemlist();

		$current_user = AppContext::get_current_user();
		$not_authorized = !ModlistAuthorizationsService::check_authorizations($itemlist->get_category_id())->moderation() && !ModlistAuthorizationsService::check_authorizations($itemlist->get_category_id())->write() && (!ModlistAuthorizationsService::check_authorizations($itemlist->get_category_id())->contribution() || $itemlist->get_author_user()->get_id() != $current_user->get_id());

		switch ($itemlist->get_publication_state())
		{
			case Itemlist::PUBLISHED_NOW:
				if (!ModlistAuthorizationsService::check_authorizations($itemlist->get_category_id())->read())
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
		   			DispatchManager::redirect($error_controller);
				}
			break;
			case Itemlist::NOT_PUBLISHED:
				if ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
		   			DispatchManager::redirect($error_controller);
				}
			break;
			case Itemlist::PUBLICATION_DATE:
				if (!$itemlist->is_published() && ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL)))
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
		$response = new SiteDisplayResponse($this->tpl);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->itemlist->get_title(), $this->lang['modlist.module.title']);
		$graphical_environment->get_seo_meta_data()->set_description($this->itemlist->get_description());
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ModlistUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemlist->get_id(), $this->itemlist->get_rewrited_title(), AppContext::get_request()->get_getint('page', 1)));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['modlist.module.title'], ModlistUrlBuilder::home());

		$categories = array_reverse(ModlistService::get_categories_manager()->get_parents($this->itemlist->get_category_id(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ModlistUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($this->itemlist->get_title(), ModlistUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $this->itemlist->get_id(), $this->itemlist->get_rewrited_title()));

		return $response;
	}
}
?>
