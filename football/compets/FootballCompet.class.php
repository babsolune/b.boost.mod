<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class FootballCompet
{
	private $id_compet;
	private $id_category;
	private $compet_name;
	private $compet_season_id;
	private $compet_division_id;
	private $views_number;

	private $published;
	private $publishing_start_date;
	private $publishing_end_date;
	private $end_date_enabled;

	private $creation_date;
	private $update_date;
	private $author_user;

	private $sources;

	const THUMBNAIL_URL = '/templates/__default__/images/default_item.webp';

	const NOT_PUBLISHED        = 0;
	const PUBLISHED            = 1;
	const DEFERRED_PUBLICATION = 2;

	public function get_id_compet()
	{
		return $this->id_compet;
	}

	public function set_id($id_compet)
	{
		$this->id_compet = $id_compet;
	}

	public function get_id_category()
	{
		return $this->id_category;
	}

	public function set_id_category($id_category)
	{
		$this->id_category = $id_category;
	}

	public function get_category()
	{
		return CategoriesService::get_categories_manager()->get_categories_cache()->get_category($this->id_category);
	}

	public function get_compet_name()
	{
		return $this->compet_name;
	}

	public function set_compet_name($compet_name)
	{
		$this->compet_name = $compet_name;
	}

	public function get_compet_season_id()
	{
		return $this->compet_season_id;
	}

	public function set_compet_season_id($compet_season_id)
	{
		$this->compet_season_id = $compet_season_id;
	}

	public function get_compet_division_id()
	{
		return $this->compet_division_id;
	}

	public function set_compet_division_id($compet_division_id)
	{
		$this->compet_division_id = $compet_division_id;
	}

	public function get_views_number()
	{
		return $this->views_number;
	}

	public function set_views_number($views_number)
	{
		$this->views_number = $views_number;
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
		return FootballAuthorizationsService::check_authorizations($this->id_category)->read() && ($this->get_publishing_state() == self::PUBLISHED || ($this->get_publishing_state() == self::DEFERRED_PUBLICATION && $this->get_publishing_start_date()->is_anterior_to($now) && ($this->end_date_enabled ? $this->get_publishing_end_date()->is_posterior_to($now) : true)));
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

	public function get_update_date()
	{
		return $this->update_date;
	}

	public function set_update_date(Date $update_date)
	{
		$this->update_date = $update_date;
	}

	public function has_update_date()
	{
		return $this->update_date !== null && $this->update_date > $this->creation_date;
	}

	public function get_author_user()
	{
		return $this->author_user;
	}

	public function set_author_user(User $user)
	{
		$this->author_user = $user;
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

	public function is_authorized_to_add()
	{
		return FootballAuthorizationsService::check_authorizations($this->id_category)->write() || FootballAuthorizationsService::check_authorizations($this->id_category)->contribution();
	}

	public function is_authorized_to_edit()
	{
		return FootballAuthorizationsService::check_authorizations($this->id_category)->moderation() || ((FootballAuthorizationsService::check_authorizations($this->id_category)->write() || (FootballAuthorizationsService::check_authorizations($this->id_category)->contribution())) && $this->get_author_user()->get_id() == AppContext::get_current_user()->get_id() && AppContext::get_current_user()->check_level(User::MEMBER_LEVEL));
	}

	public function is_authorized_to_delete()
	{
		return FootballAuthorizationsService::check_authorizations($this->id_category)->moderation() || ((FootballAuthorizationsService::check_authorizations($this->id_category)->write() || (FootballAuthorizationsService::check_authorizations($this->id_category)->contribution() && !$this->is_published())) && $this->get_author_user()->get_id() == AppContext::get_current_user()->get_id() && AppContext::get_current_user()->check_level(User::MEMBER_LEVEL));
	}

	public function is_authorized_to_manage_compets()
	{
		return FootballAuthorizationsService::check_authorizations($this->id_category)->moderation() || FootballAuthorizationsService::check_authorizations($this->id_category)->manage_compets();
	}

	public function get_properties()
	{
		return array(
			'id_compet' => $this->get_id_compet(),
			'id_category' => $this->get_id_category(),
			'compet_name' => $this->get_compet_name(),
			'compet_season_id' => $this->get_compet_season_id(),
			'compet_division_id' => $this->get_compet_division_id(),
			'views_number' => $this->get_views_number(),
			'published' => $this->get_publishing_state(),
			'publishing_start_date' => $this->get_publishing_start_date() !== null ? $this->get_publishing_start_date()->get_timestamp() : 0,
			'publishing_end_date' => $this->get_publishing_end_date() !== null ? $this->get_publishing_end_date()->get_timestamp() : 0,
			'creation_date' => $this->get_creation_date()->get_timestamp(),
			'update_date' => $this->get_update_date() !== null ? $this->get_update_date()->get_timestamp() : $this->get_creation_date()->get_timestamp(),
			'author_user_id' => $this->get_author_user()->get_id(),
			'sources' => TextHelper::serialize($this->get_sources())
		);
	}

	public function set_properties(array $properties)
	{
		$this->id_compet = $properties['id_compet'];
		$this->id_category = $properties['id_category'];
		$this->compet_name = $properties['compet_name'];
		$this->compet_season_id = $properties['compet_season_id'];
		$this->compet_division_id = $properties['compet_division_id'];
		$this->views_number = $properties['views_number'];
		$this->published = $properties['published'];
		$this->publishing_start_date = !empty($properties['publishing_start_date']) ? new Date($properties['publishing_start_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->publishing_end_date = !empty($properties['publishing_end_date']) ? new Date($properties['publishing_end_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->end_date_enabled = !empty($properties['publishing_end_date']);
		$this->creation_date = new Date($properties['creation_date'], Timezone::SERVER_TIMEZONE);
		$this->update_date = !empty($properties['update_date']) ? new Date($properties['update_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->sources = !empty($properties['sources']) ? TextHelper::unserialize($properties['sources']) : array();

		$user = new User();
		if (!empty($properties['user_id']))
			$user->set_properties($properties);
		else
			$user->init_visitor_user();

		$this->set_author_user($user);
	}

	public function init_default_properties($id_category = Category::ROOT_CATEGORY)
	{
		$this->id_category = $id_category;
        $this->published = self::PUBLISHED;
		$this->author_user = AppContext::get_current_user();
		$this->publishing_start_date = new Date();
		$this->publishing_end_date = new Date();
		$this->creation_date = new Date();
		$this->sources = array();
		$this->end_date_enabled = false;
	}

	public function clean_publishing_start_and_end_date()
	{
		$this->publishing_start_date = null;
		$this->publishing_end_date = null;
		$this->end_date_enabled = false;
	}

	public function clean_publishing_end_date()
	{
		$this->publishing_end_date = null;
		$this->end_date_enabled = false;
	}

	public function get_compet_url()
	{
		return FootballUrlBuilder::compet_home($this->id_compet)->rel();
	}

	public function get_template_vars()
	{
		$category = $this->get_category();
		$user = $this->get_author_user();
		$user_group_color = User::get_group_color($user->get_groups(), $user->get_level(), true);

		return array_merge(
			Date::get_array_tpl_vars($this->creation_date, 'date'),
			Date::get_array_tpl_vars($this->update_date, 'update_date'),
			Date::get_array_tpl_vars($this->publishing_start_date, 'differed_publishing_start_date'),
			array(
				// Conditions
				'C_VISIBLE'              => $this->is_published(),
				'C_CONTROLS'			 => $this->is_authorized_to_edit() || $this->is_authorized_to_delete() || $this->is_authorized_to_manage_compets(),
				'C_EDIT'                 => $this->is_authorized_to_edit(),
				'C_DELETE'               => $this->is_authorized_to_delete(),
				'C_PARAMETERS'           => $this->is_authorized_to_manage_compets(),
				'C_HAS_UPDATE_DATE'      => $this->has_update_date(),
				'C_DIFFERED'             => $this->published == self::DEFERRED_PUBLICATION,

				// Item
				'ID'                  => $this->id_compet,
				'TITLE'               => $this->compet_name,
				'STATUS'              => $this->get_publishing_state(),

				// Category
				'C_ROOT_CATEGORY'      => $category->get_id() == Category::ROOT_CATEGORY,
				'CATEGORY_ID'          => $category->get_id(),
				'CATEGORY_NAME'        => $category->get_name(),
				// 'CATEGORY_DESCRIPTION' => $category->get_description(),
				'U_CATEGORY'           => FootballUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel(),
				// 'U_CATEGORY_THUMBNAIL' => $category->get_thumbnail()->rel(),
				'U_EDIT_CATEGORY'      => $category->get_id() == Category::ROOT_CATEGORY ? FootballUrlBuilder::configuration()->rel() : CategoriesUrlBuilder::edit($category->get_id(), 'football')->rel(),

				// Links

				'U_SYNDICATION'    => SyndicationUrlBuilder::rss('football', $this->id_category)->rel(),
				'U_COMPET'         => $this->get_compet_url(),
				'U_EDIT'           => FootballUrlBuilder::edit($this->id_compet)->rel(),
				'U_DELETE'         => FootballUrlBuilder::delete($this->id_compet)->rel(),
			)
		);
	}

	public function get_array_tpl_source_vars($source_name)
	{
		$vars = array();
		$sources = $this->get_sources();

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
