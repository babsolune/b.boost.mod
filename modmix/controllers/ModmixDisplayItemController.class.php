<?php
/*##################################################
 *		       ModmixDisplayItemController.class.php
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

class ModmixDisplayItemController extends ModuleController
{
	private $lang;
	private $tpl;
	private $itemmix;
	private $category;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->check_pending_itemmix($request);

		$this->build_view($request);

		return $this->generate_response();
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'modmix');
		$this->tpl = new FileTemplate('modmix/ModmixDisplayItemController.tpl');
		$this->tpl->add_lang($this->lang);
	}

	private function get_itemmix()
	{
		if ($this->itemmix === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try
				{
					$this->itemmix = ModmixService::get_itemmix('WHERE modmix.id=:id', array('id' => $id));
				}
				catch (RowNotFoundException $e)
				{
					$error_controller = PHPBoostErrors::unexisting_page();
   					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->itemmix = new Itemmix();
		}
		return $this->itemmix;
	}

	private function check_pending_itemmix(HTTPRequestCustom $request)
	{
		if (!$this->itemmix->is_published())
		{
			$this->tpl->put('NOT_VISIBLE_MESSAGE', MessageHelper::display(LangLoader::get_message('element.not_visible', 'status-messages-common'), MessageHelper::WARNING));
		}
		else
		{
			if ($request->get_url_referrer() && !TextHelper::strstr($request->get_url_referrer(), ModmixUrlBuilder::display_item($this->itemmix->get_category()->get_id(), $this->itemmix->get_category()->get_rewrited_name(), $this->itemmix->get_id(), $this->itemmix->get_rewrited_title())->rel()))
			{
				$this->itemmix->set_views_number($this->itemmix->get_views_number() + 1);
				ModmixService::update_views_number($this->itemmix);
			}
		}
	}

	private function build_view(HTTPRequestCustom $request)
	{
		$config = ModmixConfig::load();
		$comments_config = new ModmixComments();
		$notation_config = new ModmixNotation();

		$this->category = $this->itemmix->get_category();

		$this->build_sources_view();
		$this->build_carousel_view();
		$this->build_keywords_view();
		$this->build_suggested_items($this->itemmix);
		$this->build_navigation_links($this->itemmix);

		$this->tpl->put_all(array_merge($this->itemmix->get_array_tpl_vars(), array(
			'C_COMMENTS_ENABLED' => $comments_config->are_comments_enabled(),
			'C_NOTATION_ENABLED' => $notation_config->is_notation_enabled(),
			'KERNEL_NOTATION'    => NotationService::display_active_image($this->itemmix->get_notation()),
			'CONTENTS'           => FormatingHelper::second_parse($this->itemmix->get_contents()),
			'U_EDIT_ITEM'     => ModmixUrlBuilder::edit_item($this->itemmix->get_id())->rel()
		)));

		//Affichage commentaires
		if ($comments_config->are_comments_enabled())
		{
			$comments_topic = new ModmixCommentsTopic($this->itemmix);
			$comments_topic->set_id_in_module($this->itemmix->get_id());
			$comments_topic->set_url(ModmixUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemmix->get_id(), $this->itemmix->get_rewrited_title()));

			$this->tpl->put('COMMENTS', $comments_topic->display());
		}
	}

	private function build_sources_view()
	{
		$sources = $this->itemmix->get_sources();
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
		$carousel = $this->itemmix->get_carousel();
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
		$keywords = $this->itemmix->get_keywords();
		$nbr_keywords = count($keywords);
		$this->tpl->put('C_KEYWORDS', $nbr_keywords > 0);

		$i = 1;
		foreach ($keywords as $keyword)
		{
			$this->tpl->assign_block_vars('keywords', array(
				'C_SEPARATOR' => $i < $nbr_keywords,
				'NAME' => $keyword->get_name(),
				'URL' => ModmixUrlBuilder::display_tag($keyword->get_rewrited_name())->rel(),
			));
			$i++;
		}
	}

	private function build_suggested_items(Itemmix $itemmix)
	{
		$now = new Date();

		$result = PersistenceContext::get_querier()->select('
		SELECT id, title, category_id, rewrited_title, thumbnail_url,
		(2 * FT_SEARCH_RELEVANCE(title, :search_content) + FT_SEARCH_RELEVANCE(contents, :search_content) / 3) AS relevance
		FROM ' . ModmixSetup::$modmix_table . '
		WHERE (FT_SEARCH(title, :search_content) OR FT_SEARCH(contents, :search_content)) AND id <> :excluded_id
		AND (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0)))
		ORDER BY relevance DESC LIMIT 0, :limit_nb', array(
			'excluded_id' => $itemmix->get_id(),
			'search_content' => $itemmix->get_title() .','. $itemmix->get_contents(),
			'timestamp_now' => $now->get_timestamp(),
			'limit_nb' => (int) ModmixConfig::load()->get_suggested_items_nb()
		));

		$this->tpl->put_all(array(
			'C_SUGGESTED_ITEMS' => $result->get_rows_count() > 0 && ModmixConfig::load()->get_enabled_items_suggestions(),
			'SUGGESTED_COLUMNS' => ModmixConfig::load()->get_cols_number_displayed_per_line()
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
				'U_ITEM' => ModmixUrlBuilder::display_item($row['category_id'], ModmixService::get_categories_manager()->get_categories_cache()->get_category($row['category_id'])->get_rewrited_name(), $row['id'], $row['rewrited_title'])->rel()
			));
		}
		$result->dispose();
	}

	private function build_navigation_links(Itemmix $itemmix)
	{
		$now = new Date();
		$timestamp_itemmix = $itemmix->get_creation_date()->get_timestamp();

		$result = PersistenceContext::get_querier()->select('
		(SELECT id, title, category_id, rewrited_title, thumbnail_url, \'PREVIOUS\' as type
		FROM '. ModmixSetup::$modmix_table .'
		WHERE (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0))) AND creation_date < :timestamp_itemmix AND category_id IN :authorized_categories ORDER BY creation_date DESC LIMIT 1 OFFSET 0)
		UNION
		(SELECT id, title, category_id, rewrited_title, thumbnail_url, \'NEXT\' as type
		FROM '. ModmixSetup::$modmix_table .'
		WHERE (published = 1 OR (published = 2 AND publication_start_date < :timestamp_now AND (publication_end_date > :timestamp_now OR publication_end_date = 0))) AND creation_date > :timestamp_itemmix AND category_id IN :authorized_categories ORDER BY creation_date ASC LIMIT 1 OFFSET 0)
		', array(
			'timestamp_now' => $now->get_timestamp(),
			'timestamp_itemmix' => $timestamp_itemmix,
			'authorized_categories' => array($itemmix->get_category_id())
		));

		$this->tpl->put_all(array(
			'C_NAVIGATION_LINKS' => $result->get_rows_count() > 0 && ModmixConfig::load()->get_enabled_navigation_links(),
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
				'U_'. $row['type'] .'_ITEM' => ModmixUrlBuilder::display_item($row['category_id'], ModmixService::get_categories_manager()->get_categories_cache()->get_category($row['category_id'])->get_rewrited_name(), $row['id'], $row['rewrited_title'])->rel(),
			));
		}
		$result->dispose();
	}

	private function check_authorizations()
	{
		$itemmix = $this->get_itemmix();

		$current_user = AppContext::get_current_user();
		$not_authorized = !ModmixAuthorizationsService::check_authorizations($itemmix->get_category_id())->moderation() && !ModmixAuthorizationsService::check_authorizations($itemmix->get_category_id())->write() && (!ModmixAuthorizationsService::check_authorizations($itemmix->get_category_id())->contribution() || $itemmix->get_author_user()->get_id() != $current_user->get_id());

		switch ($itemmix->get_publication_state())
		{
			case Itemmix::PUBLISHED_NOW:
				if (!ModmixAuthorizationsService::check_authorizations($itemmix->get_category_id())->read())
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
		   			DispatchManager::redirect($error_controller);
				}
			break;
			case Itemmix::NOT_PUBLISHED:
				if ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
		   			DispatchManager::redirect($error_controller);
				}
			break;
			case Itemmix::PUBLICATION_DATE:
				if (!$itemmix->is_published() && ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL)))
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

	private function get_pagination($nbr_pages, $current_page)
	{
		$pagination = new ModulePagination($current_page, $nbr_pages, 1, Pagination::LIGHT_PAGINATION);
		$pagination->set_url(ModmixUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemmix->get_id(), $this->itemmix->get_rewrited_title(), '%d'));

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
		$graphical_environment->set_page_title($this->itemmix->get_title(), $this->lang['modmix.module.title']);
		$graphical_environment->get_seo_meta_data()->set_description($this->itemmix->get_description());
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ModmixUrlBuilder::display_item($this->category->get_id(), $this->category->get_rewrited_name(), $this->itemmix->get_id(), $this->itemmix->get_rewrited_title(), AppContext::get_request()->get_getint('page', 1)));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['modmix.module.title'], ModmixUrlBuilder::home());

		$categories = array_reverse(ModmixService::get_categories_manager()->get_parents($this->itemmix->get_category_id(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ModmixUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($this->itemmix->get_title(), ModmixUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $this->itemmix->get_id(), $this->itemmix->get_rewrited_title()));

		return $response;
	}
}
?>
