<?php
/*##################################################
 *                        Itemmix.class.php
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

class Itemmix
{
	private $id;
	private $category_id;
	private $title;
	private $rewrited_title;
	private $description;
	private $contents;
	private $thumbnail_url;
	private $views_number;

	private $custom_author_name;
	private $enabled_author_name_customization;
	private $displayed_author_name;
	private $author_user;
	private $notation;

	private $published;
	private $publication_start_date;
	private $publication_end_date;
	private $creation_date;
	private $enabled_end_date;
	private $updated_date;

	private $sources;
	private $carousel;
	private $keywords;

	const NOT_PUBLISHED = 0;
	const PUBLISHED_NOW = 1;
	const PUBLICATION_DATE = 2;

	const NOTDISPLAYED_AUTHOR_NAME = 0;
	const DISPLAYED_AUTHOR_NAME = 1;

	const DEFAULT_PICTURE = '/modmix/templates/images/default.png';

	public function set_id($id)
	{
		$this->id = $id;
	}

	public function get_id()
	{
		return $this->id;
	}

	public function set_category_id($category_id)
	{
		$this->category_id = $category_id;
	}

	public function get_category_id()
	{
		return $this->category_id;
	}

	public function get_category()
	{
		return ModmixService::get_categories_manager()->get_categories_cache()->get_category($this->category_id);
	}

	public function set_title($title)
	{
		$this->title = $title;
	}

	public function get_title()
	{
		return $this->title;
	}

	public function set_rewrited_title($rewrited_title)
	{
		$this->rewrited_title = $rewrited_title;
	}

	public function get_rewrited_title()
	{
		return $this->rewrited_title;
	}

	public function rewrited_title_is_personalized()
	{
		return $this->rewrited_title != Url::encode_rewrite($this->title);
	}

	public function set_description($description)
	{
		$this->description = $description;
	}

	public function get_description()
	{
		return $this->description;
	}

	public function get_description_enabled()
	{
		return !empty($this->description);
	}

	public function get_real_description()
	{
		if ($this->get_description_enabled())
		{
			return FormatingHelper::second_parse($this->description);
		}
		else
		{
			$clean_contents = preg_split('`\[page\].+\[/page\](.*)`usU', FormatingHelper::second_parse($this->contents), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			return TextHelper::cut_string(@strip_tags($clean_contents[0], '<br><br/>'), (int)ModmixConfig::load()->get_characters_number_to_cut());
		}
	}

	public function set_contents($contents)
	{
		$this->contents = $contents;
	}

	public function get_contents()
	{
		return $this->contents;
	}

	public function set_thumbnail(Url $thumbnail)
	{
		$this->thumbnail_url = $thumbnail;
	}

	public function get_thumbnail()
	{
		return $this->thumbnail_url;
	}

	public function has_thumbnail()
	{
		$thumbnail = $this->thumbnail_url->rel();
		return !empty($thumbnail);
	}

	public function set_views_number($views_number)
	{
		$this->views_number = $views_number;
	}

	public function get_views_number()
	{
		return $this->views_number;
	}

	public function get_displayed_author_name()
	{
		return $this->displayed_author_name;
	}

	public function set_displayed_author_name($displayed)
	{
		$this->displayed_author_name = $displayed;
	}

	public function set_author_user(User $user)
	{
		$this->author_user = $user;
	}

	public function get_author_user()
	{
	    return $this->author_user;
	}

	public function get_custom_author_name()
	{
		return $this->custom_author_name;
	}

	public function set_custom_author_name($custom_author_name)
	{
		$this->custom_author_name = $custom_author_name;
	}

	public function is_enabled_author_name_customization()
	{
		return $this->enabled_author_name_customization;
	}

	public function set_notation(Notation $notation)
	{
		$this->notation = $notation;
	}

	public function get_notation()
	{
	    return $this->notation;
	}

	public function set_publication_state($published)
	{
		$this->published = $published;
	}

	public function get_publication_state()
	{
		return $this->published;
	}

	public function is_published()
	{
		$now = new Date();
		return ModmixAuthorizationsService::check_authorizations($this->category_id)->read() && ($this->get_publication_state() == self::PUBLISHED_NOW || ($this->get_publication_state() == self::PUBLICATION_DATE && $this->get_publication_start_date()->is_anterior_to($now) && ($this->enabled_end_date ? $this->get_publication_end_date()->is_posterior_to($now) : true)));
	}

	public function get_status()
	{
		switch ($this->published) {
			case self::PUBLISHED_NOW:
				return LangLoader::get_message('status.approved.now', 'common');
			break;
			case self::PUBLICATION_DATE:
				return LangLoader::get_message('status.approved.date', 'common');
			break;
			case self::NOT_PUBLISHED:
				return LangLoader::get_message('status.approved.not', 'common');
			break;
		}
	}

	public function set_publication_start_date(Date $publication_start_date)
	{
		$this->publication_start_date = $publication_start_date;
	}

	public function get_publication_start_date()
	{
		return $this->publication_start_date;
	}

	public function set_publication_end_date(Date $publication_end_date)
	{
		$this->publication_end_date = $publication_end_date;
		$this->enabled_end_date = true;
	}

	public function get_publication_end_date()
	{
		return $this->publication_end_date;
	}

	public function enabled_end_date()
	{
		return $this->enabled_end_date;
	}

	public function set_creation_date(Date $creation_date)
	{
		$this->creation_date = $creation_date;
	}

	public function get_creation_date()
	{
		return $this->creation_date;
	}

	public function get_updated_date()
	{
		return $this->updated_date;
	}

	public function set_updated_date(Date $updated_date)
	{
	    $this->updated_date = $updated_date;
	}

	public function add_source($source)
	{
		$this->sources[] = $source;
	}

	public function set_sources($sources)
	{
		$this->sources = $sources;
	}

	public function get_sources()
	{
		return $this->sources;
	}

	public function add_picture($picture)
	{
		$this->carousel[] = $picture;
	}

	public function set_carousel($carousel)
	{
		$this->carousel = $carousel;
	}

	public function get_carousel()
	{
		return $this->carousel;
	}

	public function get_keywords()
	{
		if ($this->keywords === null)
		{
			$this->keywords = ModmixService::get_keywords_manager()->get_keywords($this->id);
		}
		return $this->keywords;
	}

	public function get_keywords_name()
	{
		return array_keys($this->get_keywords());
	}

	public function is_authorized_to_add()
	{
		return ModmixAuthorizationsService::check_authorizations($this->category_id)->write() || ModmixAuthorizationsService::check_authorizations($this->category_id)->contribution();
	}

	public function is_authorized_to_edit()
	{
		return ModmixAuthorizationsService::check_authorizations($this->category_id)->moderation() || ((ModmixAuthorizationsService::check_authorizations($this->get_category_id())->write() || (ModmixAuthorizationsService::check_authorizations($this->get_category_id())->contribution() && !$this->is_published())) && $this->get_author_user()->get_id() == AppContext::get_current_user()->get_id() && AppContext::get_current_user()->check_level(User::MEMBER_LEVEL));
	}

	public function is_authorized_to_delete()
	{
		return ModmixAuthorizationsService::check_authorizations($this->category_id)->moderation() || ((ModmixAuthorizationsService::check_authorizations($this->get_category_id())->write() || (ModmixAuthorizationsService::check_authorizations($this->get_category_id())->contribution() && !$this->is_published())) && $this->get_author_user()->get_id() == AppContext::get_current_user()->get_id() && AppContext::get_current_user()->check_level(User::MEMBER_LEVEL));
	}

	public function get_properties()
	{
		return array(
			'id'                     => $this->get_id(),
			'category_id'            => $this->get_category_id(),
			'title'                  => $this->get_title(),
			'rewrited_title'         => $this->get_rewrited_title(),
			'description'            => $this->get_description(),
			'contents'               => $this->get_contents(),
			'thumbnail_url'          => $this->get_thumbnail()->relative(),
			'views_number'           => $this->get_views_number(),
			'custom_author_name' 	 => $this->get_custom_author_name(),
			'author_user_id'         => $this->get_author_user()->get_id(),
			'displayed_author_name'  => $this->get_displayed_author_name(),
			'published'              => $this->get_publication_state(),
			'publication_start_date' => $this->get_publication_start_date() !== null ? $this->get_publication_start_date()->get_timestamp() : 0,
			'publication_end_date'   => $this->get_publication_end_date() !== null ? $this->get_publication_end_date()->get_timestamp() : 0,
			'creation_date'          => $this->get_creation_date()->get_timestamp(),
			'updated_date'           => $this->get_updated_date() !== null ? $this->get_updated_date()->get_timestamp() : 0,
			'sources'                => TextHelper::serialize($this->get_sources()),
			'carousel'               => TextHelper::serialize($this->get_carousel())
		);
	}

	public function set_properties(array $properties)
	{
		$this->set_id($properties['id']);
		$this->set_category_id($properties['category_id']);
		$this->set_title($properties['title']);
		$this->set_rewrited_title($properties['rewrited_title']);
		$this->set_description($properties['description']);
		$this->set_contents($properties['contents']);
		$this->set_thumbnail(new Url($properties['thumbnail_url']));
		$this->set_views_number($properties['views_number']);
		$this->set_displayed_author_name($properties['displayed_author_name']);
		$this->set_publication_state($properties['published']);
		$this->publication_start_date = !empty($properties['publication_start_date']) ? new Date($properties['publication_start_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->publication_end_date = !empty($properties['publication_end_date']) ? new Date($properties['publication_end_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->enabled_end_date = !empty($properties['publication_end_date']);
		$this->set_creation_date(new Date($properties['creation_date'], Timezone::SERVER_TIMEZONE));
		$this->updated_date = !empty($properties['updated_date']) ? new Date($properties['updated_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->set_sources(!empty($properties['sources']) ? TextHelper::unserialize($properties['sources']) : array());
		$this->set_carousel(!empty($properties['carousel']) ? TextHelper::unserialize($properties['carousel']) : array());

		$user = new User();
		if (!empty($properties['user_id']))
			$user->set_properties($properties);
		else
			$user->init_visitor_user();

		$this->set_author_user($user);

		$this->custom_author_name = !empty($properties['custom_author_name']) ? $properties['custom_author_name'] : $this->author_user->get_display_name();
		$this->enabled_author_name_customization = !empty($properties['custom_author_name']);

		$notation = new Notation();
		$notation_config = new ModmixNotation();

		$notation->set_module_name('modmix');
		$notation->set_notation_scale($notation_config->get_notation_scale());
		$notation->set_id_in_module($properties['id']);
		$notation->set_number_notes($properties['number_notes']);
		$notation->set_average_notes($properties['average_notes']);
		$notation->set_user_already_noted(!empty($properties['note']));
		$this->notation = $notation;
	}

	public function init_default_properties($category_id = Category::ROOT_CATEGORY)
	{
		$this->category_id = $category_id;
		$this->displayed_author_name = self::DISPLAYED_AUTHOR_NAME;
		$this->author_user = AppContext::get_current_user();
		$this->published = self::PUBLISHED_NOW;
		$this->publication_start_date = new Date();
		$this->publication_end_date = new Date();
		$this->creation_date = new Date();
		$this->sources = array();
		$this->carousel = array();
		$this->thumbnail_url = new Url(self::DEFAULT_PICTURE);
		$this->views_number = 0;
		$this->custom_author_name = $this->author_user->get_display_name();
		$this->enabled_author_name_customization = false;
	}

	public function clean_publication_start_and_end_date()
	{
		$this->publication_start_date = null;
		$this->publication_end_date = null;
		$this->enabled_end_date = false;
	}

	public function clean_publication_end_date()
	{
		$this->publication_end_date = null;
		$this->enabled_end_date = false;
	}

	public function get_array_tpl_vars()
	{
		$category           = $this->get_category();
		$contents	 	    = FormatingHelper::second_parse($this->contents);
		$description        = $this->get_real_description();
		$user               = $this->get_author_user();
		$user_group_color   = User::get_group_color($user->get_groups(), $user->get_level(), true);
		$sources            = $this->get_sources();
		$nbr_sources        = count($sources);
		$carousel           = $this->get_carousel();
		$nbr_pictures		= count($carousel);
		$new_content        = new ModmixNewContent();
		$notation_config    = new ModmixNotation();
		
		return array_merge(
			Date::get_array_tpl_vars($this->creation_date, 'date'),
			Date::get_array_tpl_vars($this->updated_date, 'updated_date'),
			Date::get_array_tpl_vars($this->publication_start_date, 'publication_start_date'),
			Date::get_array_tpl_vars($this->publication_end_date, 'publication_end_date'),
			array(
			//Conditions
			'C_EDIT'                           => $this->is_authorized_to_edit(),
			'C_DELETE'                         => $this->is_authorized_to_delete(),
			'C_HAS_THUMBNAIL'                  => $this->has_thumbnail(),
			'C_USER_GROUP_COLOR'               => !empty($user_group_color),
			'C_PUBLISHED'                      => $this->is_published(),
			'C_PUBLICATION_START_AND_END_DATE' => $this->publication_start_date != null && $this->publication_end_date != null,
			'C_PUBLICATION_START_DATE'         => $this->publication_start_date != null,
			'C_PUBLICATION_END_DATE'           => $this->publication_end_date != null,
			'C_UPDATED_DATE'                   => $this->updated_date != null,
			'C_DISPLAYED_AUTHOR'               => $this->get_displayed_author_name(),
			'C_CUSTOM_AUTHOR_NAME' 			   => $this->is_enabled_author_name_customization(),
			'C_NOTATION_ENABLED'               => $notation_config->is_notation_enabled(),
			'C_READ_MORE'                      => !$this->get_description_enabled() && TextHelper::strlen($contents) > ModmixConfig::load()->get_characters_number_to_cut() && $description != @strip_tags($contents, '<br><br/>'),
			'C_SOURCES'                        => $nbr_sources > 0,
			'C_CAROUSEL'                       => $nbr_pictures > 0,
			'C_DIFFERED'                       => $this->published == self::PUBLICATION_DATE,
			'C_NEW_CONTENT'                    => $new_content->check_if_is_new_content($this->publication_start_date != null ? $this->publication_start_date->get_timestamp() : $this->get_creation_date()->get_timestamp()) && $this->is_published(),

			//Modmix
			'ID'                 => $this->get_id(),
			'TITLE'              => $this->get_title(),
			'STATUS'             => $this->get_status(),
			'L_COMMENTS'         => CommentsService::get_number_and_lang_comments('modmix', $this->get_id()),
			'COMMENTS_NUMBER'    => CommentsService::get_number_comments('modmix', $this->get_id()),
			'VIEWS_NUMBER'       => $this->get_views_number(),
			'NOTE'               => $this->get_notation()->get_number_notes() > 0 ? NotationService::display_static_image($this->get_notation()) : '&nbsp;',
			'CUSTOM_AUTHOR_NAME' => $this->custom_author_name,
			'C_AUTHOR_EXIST'     => $user->get_id() !== User::VISITOR_LEVEL,
			'PSEUDO'             => $user->get_display_name(),
			'DESCRIPTION'        => $description,
			'THUMBNAIL'          => $this->get_thumbnail()->rel(),
			'USER_LEVEL_CLASS'   => UserService::get_level_class($user->get_level()),
			'USER_GROUP_COLOR'   => $user_group_color,

			//Category
			'C_ROOT_CATEGORY'      => $category->get_id() == Category::ROOT_CATEGORY,
			'CATEGORY_ID'          => $category->get_id(),
			'CATEGORY_NAME'        => $category->get_name(),
			'CATEGORY_DESCRIPTION' => $category->get_description(),
			'CATEGORY_IMAGE'       => $category->get_image()->rel(),
			'U_EDIT_CATEGORY'      => $category->get_id() == Category::ROOT_CATEGORY ? ModmixUrlBuilder::configuration()->rel() : ModmixUrlBuilder::edit_category($category->get_id())->rel(),

			//Links
			'U_COMMENTS'    => ModmixUrlBuilder::display_items_comments($category->get_id(), $category->get_rewrited_name(), $this->get_id(), $this->get_rewrited_title())->rel(),
			'U_AUTHOR'      => UserUrlBuilder  ::profile($this->get_author_user()->get_id())->rel(),
			'U_CATEGORY'    => ModmixUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel(),
			'U_ITEM'        => ModmixUrlBuilder::display_item($category->get_id(), $category->get_rewrited_name(), $this->get_id(), $this->get_rewrited_title())->rel(),
			'U_EDIT_ITEM'   => ModmixUrlBuilder::edit_item($this->id, AppContext::get_request()->get_getint('page', 1))->rel(),
			'U_DELETE_ITEM' => ModmixUrlBuilder::delete_item($this->id)->rel(),
			'U_SYNDICATION' => ModmixUrlBuilder::category_syndication($category->get_id())->rel(),
			'U_PRINT_ITEM'  => ModmixUrlBuilder::print_item($this->get_id(), $this->get_rewrited_title())->rel()
			)
		);
	}
}
?>
