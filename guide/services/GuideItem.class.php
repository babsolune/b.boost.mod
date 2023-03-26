<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 01 09
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class GuideItem
{
	private $id;
	private $id_category;
	private $i_order;
	private $item_content;
	private $rewrited_title;

	private $published;
	private $publishing_start_date;
	private $publishing_end_date;
	private $end_date_enabled;
	private $creation_date;
	private $author_user;

	private $views_number;
	private $notation;
	private $keywords;

	const THUMBNAIL_URL = '/templates/__default__/images/default_item.webp';

	const NOT_PUBLISHED        = 0;
	const PUBLISHED            = 1;
	const DEFERRED_PUBLICATION = 2;

	public function get_id()
	{
		return $this->id;
	}

	public function set_id($id)
	{
		$this->id = $id;
	}

	public function get_id_category()
	{
		return $this->id_category;
	}

	public function set_id_category($id_category)
	{
		$this->id_category = $id_category;
	}

	public function get_i_order()
	{
		return $this->i_order;
	}

	public function set_i_order($i_order)
	{
		$this->i_order = $i_order;
	}

	public function get_rewrited_title()
	{
		return $this->rewrited_title;
	}

	public function set_rewrited_title($rewrited_title)
	{
		$this->rewrited_title = $rewrited_title;
	}

	public function set_item_content(GuideItemContent $item_content)
	{
		$this->item_content = $item_content;
	}

	public function get_item_content()
	{
		return $this->item_content;
	}

	public function get_category()
	{
		return CategoriesService::get_categories_manager()->get_categories_cache()->get_category($this->id_category);
	}

	public function get_publishing_state()
	{
		return $this->published;
	}

	public function set_publishing_state($published)
	{
		$this->published = $published;
	}

	public function is_published()
	{
		$now = new Date();
		return GuideAuthorizationsService::check_authorizations($this->id_category)->read() && ($this->get_publishing_state() == self::PUBLISHED || ($this->get_publishing_state() == self::DEFERRED_PUBLICATION && $this->get_publishing_start_date()->is_anterior_to($now) && ($this->end_date_enabled ? $this->get_publishing_end_date()->is_posterior_to($now) : true)));
	}

	public function get_status()
	{
		switch ($this->published) {
			case self::PUBLISHED:
				return LangLoader::get_message('common.status.published', 'common-lang');
			break;
			case self::DEFERRED_PUBLICATION:
				return LangLoader::get_message('common.status.deffered.date', 'common-lang');
			break;
			case self::NOT_PUBLISHED:
				return LangLoader::get_message('common.status.draft', 'common-lang');
			break;
		}
	}

	public function get_publishing_start_date()
	{
		return $this->publishing_start_date;
	}

	public function set_publishing_start_date(Date $publishing_start_date)
	{
		$this->publishing_start_date = $publishing_start_date;
	}

	public function get_publishing_end_date()
	{
		return $this->publishing_end_date;
	}

	public function set_publishing_end_date(Date $publishing_end_date)
	{
		$this->publishing_end_date = $publishing_end_date;
		$this->end_date_enabled = true;
	}

	public function is_end_date_enabled()
	{
		return $this->end_date_enabled;
	}

	public function get_creation_date()
	{
		return $this->creation_date;
	}

	public function set_creation_date(Date $creation_date)
	{
		$this->creation_date = $creation_date;
	}

	public function has_update_date()
	{
		return $this->item_content->get_update_date() !== null && $this->item_content->get_update_date() > $this->creation_date;
	}

	public function get_author_user()
	{
		return $this->author_user;
	}

	public function set_author_user(User $user)
	{
		$this->author_user = $user;
	}

	public function set_views_number($views_number)
	{
		$this->views_number = $views_number;
	}

	public function get_views_number()
	{
		return $this->views_number;
	}

	public function get_notation()
	{
		return $this->notation;
	}

	public function set_notation(Notation $notation)
	{
		$this->notation = $notation;
	}

	public function get_keywords()
	{
		if ($this->keywords === null)
		{
			$this->keywords = KeywordsService::get_keywords_manager()->get_keywords($this->id);
		}
		return $this->keywords;
	}

	public function get_keywords_name()
	{
		return array_keys($this->get_keywords());
	}

	public function is_authorized_to_add()
	{
		return
            GuideAuthorizationsService::check_authorizations($this->id_category)->write()
            || GuideAuthorizationsService::check_authorizations($this->id_category)->contribution()
        ;
	}

	public function is_authorized_to_edit()
	{
		return
            GuideAuthorizationsService::check_authorizations($this->id_category)->moderation()
			|| GuideAuthorizationsService::check_authorizations($this->id_category)->write()
            || (
                GuideAuthorizationsService::check_authorizations($this->id_category)->contribution()
                && $this->get_author_user()->get_id() == AppContext::get_current_user()->get_id()
            )
        ;
	}

	public function is_authorized_to_delete()
	{
		return
            GuideAuthorizationsService::check_authorizations($this->id_category)->moderation()
            || (
                (
                    GuideAuthorizationsService::check_authorizations($this->id_category)->write()
                    || GuideAuthorizationsService::check_authorizations($this->id_category)->contribution()
                )
                && $this->get_author_user()->get_id() == AppContext::get_current_user()->get_id()
            )
        ;
	}

	public function is_authorized_to_restore()
	{
		return
            GuideAuthorizationsService::check_authorizations($this->id_category)->moderation()
            || GuideAuthorizationsService::check_authorizations($this->id_category)->manage_archives()
        ;
	}

	public function get_properties()
	{
		return array(
			'id'                    => $this->get_id(),
			'id_category'           => $this->get_id_category(),
			'i_order'               => $this->get_i_order(),
			'rewrited_title'        => $this->get_rewrited_title(),
			'published'             => $this->get_publishing_state(),
			'publishing_start_date' => $this->get_publishing_start_date() !== null ? $this->get_publishing_start_date()->get_timestamp() : 0,
			'publishing_end_date'   => $this->get_publishing_end_date() !== null ? $this->get_publishing_end_date()->get_timestamp() : 0,
			'creation_date'         => $this->get_creation_date()->get_timestamp(),
			'author_user_id'        => $this->get_author_user()->get_id(),
			'views_number'          => $this->get_views_number(),
		);
	}

	public function set_properties(array $properties)
	{
		$item_content = new GuideItemContent();
		$item_content->set_properties($properties);

		$this->id                      = $properties['id'];
		$this->id_category             = $properties['id_category'];
		$this->i_order                 = $properties['i_order'];
		$this->rewrited_title          = $properties['rewrited_title'];
		$this->item_content            = $item_content;
		$this->views_number            = $properties['views_number'];
		$this->published               = $properties['published'];
		$this->publishing_start_date   = !empty($properties['publishing_start_date']) ? new Date($properties['publishing_start_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->publishing_end_date     = !empty($properties['publishing_end_date']) ? new Date($properties['publishing_end_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->end_date_enabled        = !empty($properties['publishing_end_date']);
		$this->creation_date           = new Date($properties['creation_date'], Timezone::SERVER_TIMEZONE);

		$user = new User();
		if (!empty($properties['user_id']))
			$user->set_properties($properties);
		else
			$user->init_visitor_user();

		$this->set_author_user($user);

		$notation = new Notation();
		$notation->set_module_name('guide');
		$notation->set_id_in_module($properties['id']);
		$notation->set_notes_number($properties['notes_number']);
		$notation->set_average_notes($properties['average_notes']);
		$notation->set_user_already_noted(!empty($properties['note']));
		$this->notation = $notation;
	}

	public function init_default_properties($id_category = Category::ROOT_CATEGORY)
	{
		$this->id_category             = $id_category;
		$this->published               = self::PUBLISHED;
		$this->publishing_start_date   = new Date();
		$this->publishing_end_date     = new Date();
		$this->views_number            = 0;
		$this->end_date_enabled        = false;
		$this->creation_date           = new Date();
		$this->author_user             = AppContext::get_current_user();
	}

	public function clean_publishing_start_and_end_date()
	{
		$this->publishing_start_date   = null;
		$this->publishing_end_date     = null;
		$this->end_date_enabled        = false;
	}

	public function clean_publishing_end_date()
	{
		$this->publishing_end_date = null;
		$this->end_date_enabled = false;
	}

	public function get_item_url()
	{
		$category = $this->get_category();
		return GuideUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $this->id, $this->rewrited_title)->rel();
	}

	public function get_template_vars()
	{
		$category                       = $this->get_category();
		$content                        = FormatingHelper::second_parse($this->item_content->get_content());
		$rich_content                   = HooksService::execute_hook_display_action('guide', $content, $this->get_properties());
		$real_summary                   = $this->item_content->get_real_summary();
		$user                           = $this->author_user;
		$contributor_user               = $this->item_content->get_contributor_user();
		$user_group_color               = User::get_group_color($user->get_groups(), $user->get_level(), true);
		$contributor_user_group_color   = User::get_group_color($contributor_user->get_groups(), $contributor_user->get_level(), true);
		$comments_number                = CommentsService::get_comments_number('guide', $this->id);
		$sources                        = $this->item_content->get_sources();
		$nbr_sources                    = count($sources);
		$config                         = GuideConfig::load();

		return array_merge(
			Date::get_array_tpl_vars($this->creation_date, 'date'),
			Date::get_array_tpl_vars($this->item_content->get_update_date(), 'update_date'),
			Date::get_array_tpl_vars($this->publishing_start_date, 'differed_publishing_start_date'),
			array(
				// Conditions
				'C_VISIBLE'                 => $this->is_published(),
				'C_CONTROLS'			    => $this->is_authorized_to_edit() || $this->is_authorized_to_delete() || $this->is_authorized_to_restore(),
				'C_EDIT'                    => $this->is_authorized_to_edit(),
				'C_DELETE'                  => $this->is_authorized_to_delete(),
				'C_RESTORE'		            => $this->is_authorized_to_restore(),
				'C_READ_MORE'               => !$this->item_content->is_summary_enabled() && TextHelper::strlen($content) > $config->get_auto_cut_characters_number() && $real_summary != @strip_tags($content, '<br><br/>'),
				'C_HAS_THUMBNAIL'           => $this->item_content->has_thumbnail(),
				'C_AUTHOR_CUSTOM_NAME'      => $this->item_content->is_author_custom_name_enabled(),
				'C_ENABLED_VIEWS_NUMBER'    => $config->get_enabled_views_number(),
				'C_AUTHOR_GROUP_COLOR'      => !empty($user_group_color),
				'C_CONTRIBUTOR_GROUP_COLOR' => !empty($contributor_user_group_color),
				'C_HAS_UPDATE_DATE'         => $this->has_update_date(),
				'C_SOURCES'                 => $nbr_sources > 0,
				'C_DIFFERED'                => $this->published == self::DEFERRED_PUBLICATION,
				'C_NEW_CONTENT'             => ContentManagementConfig::load()->module_new_content_is_enabled_and_check_date('guide', $this->get_publishing_start_date() != null ? $this->get_publishing_start_date()->get_timestamp() : $this->creation_date->get_timestamp()) && $this->is_published(),
				'C_INIT'				    => $this->creation_date->get_timestamp() == $this->item_content->get_update_date()->get_timestamp(),
				'C_CHANGE_REASON'		    => !empty($this->item_content->get_change_reason()),

				// Item
				'ID'                        => $this->id,
				'TITLE'                     => $this->item_content->get_title(),
				'CONTENT'                   => $rich_content,
				'SUMMARY' 		            => $real_summary,
				'CHANGE_REASON'             => $this->item_content->get_change_reason(),
				'STATUS'                    => $this->get_publishing_state(),
				'AUTHOR_CUSTOM_NAME'        => $this->item_content->get_author_custom_name(),
				'C_AUTHOR_EXISTS'           => $user->get_id() !== User::VISITOR_LEVEL,
				'C_CONTRIBUTOR_EXISTS'      => $contributor_user->get_id() !== User::VISITOR_LEVEL,
				'AUTHOR_DISPLAY_NAME'       => $user->get_display_name(),
				'AUTHOR_LEVEL_CLASS'        => UserService::get_level_class($user->get_level()),
				'AUTHOR_GROUP_COLOR'        => $user_group_color,
				'CONTRIBUTOR_DISPLAY_NAME'  => $contributor_user->get_display_name(),
				'CONTRIBUTOR_LEVEL_CLASS'   => UserService::get_level_class($contributor_user->get_level()),
				'CONTRIBUTOR_GROUP_COLOR'   => $contributor_user_group_color,
				'VIEWS_NUMBER'              => $this->get_views_number(),
				'STATIC_NOTATION'           => NotationService::display_static_image($this->get_notation()),
				'NOTATION'                  => NotationService::display_active_image($this->get_notation()),

				'C_COMMENTS'      => !empty($comments_number),
				'L_COMMENTS'      => CommentsService::get_lang_comments('guide', $this->id),
				'COMMENTS_NUMBER' => $comments_number,

				// Category
				'C_ROOT_CATEGORY'      => $category->get_id() == Category::ROOT_CATEGORY,
				'CATEGORY_ID'          => $category->get_id(),
				'CATEGORY_NAME'        => $category->get_name(),
				'CATEGORY_DESCRIPTION' => $category->get_description(),
				'U_CATEGORY'           => GuideUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel(),
				'U_CATEGORY_THUMBNAIL' => $category->get_thumbnail()->rel(),
				'U_EDIT_CATEGORY'      => $category->get_id() == Category::ROOT_CATEGORY ? GuideUrlBuilder::configuration()->rel() : CategoriesUrlBuilder::edit($category->get_id(), 'guide')->rel(),

				// Links
				'U_SYNDICATION'         => SyndicationUrlBuilder::rss('guide', $this->id_category)->rel(),
				'U_AUTHOR_PROFILE'      => UserUrlBuilder::profile($this->author_user->get_id())->rel(),
				'U_CONTRIBUTOR_PROFILE' => UserUrlBuilder::profile($this->item_content->get_contributor_user()->get_id())->rel(),
				'U_ITEM'                => $this->get_item_url(),
				'U_HISTORY'             => GuideUrlBuilder::history($this->id)->rel(),
				'U_EDIT'                => GuideUrlBuilder::edit($this->id)->rel(),
				'U_DELETE'              => GuideUrlBuilder::delete($this->id, 0)->rel(),
				'U_THUMBNAIL'           => $this->item_content->get_thumbnail()->rel(),
				'U_COMMENTS'            => GuideUrlBuilder::display_comments($category->get_id(), $category->get_rewrited_name(), $this->id, $this->rewrited_title)->rel()
			)
		);
	}

	public function get_array_tpl_source_vars($source_name)
	{
		$vars = array();
		$sources = $this->item_content->get_sources();

		if (isset($sources[$source_name]))
		{
			$vars = array(
				'C_SEPARATOR' => array_search($source_name, array_keys($sources)) < count($sources) - 1,
				'NAME' => $source_name,
				'URL'  => $sources[$source_name]
			);
		}

		return $vars;
	}
}
?>
