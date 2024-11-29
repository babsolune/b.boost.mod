<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmEvent
{
	private $id;
	private $id_category;
	private $event_slug;
	private $season_id;
	private $division_id;
	private $start_date;
	private $end_date;
	private $views_number;
	private $scoring_type;
	private $is_sub;
	private $master_id;
	private $sub_order;

	private $published;
	private $publishing_start_date;
	private $publishing_end_date;
	private $end_date_enabled;

	private $creation_date;
	private $update_date;

	private $sources;

	const THUMBNAIL_URL = '/templates/__default__/images/default_item.webp';

	const NOT_PUBLISHED        = 0;
	const PUBLISHED            = 1;
	const DEFERRED_PUBLICATION = 2;

    const SCORING_GOALS  = "scoring_goals";
    const SCORING_TRIES  = "scoring_tries";
    const SCORING_POINTS = "scoring_points";
    const SCORING_SETS   = "scoring_sets";

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

	public function get_event_slug()
	{
		return $this->event_slug;
	}

	public function set_event_slug($event_slug)
	{
		$this->event_slug = $event_slug;
	}

	public function get_category()
	{
		return CategoriesService::get_categories_manager()->get_categories_cache()->get_category($this->id_category);
	}

	public function get_season_id()
	{
		return $this->season_id;
	}

	public function set_season_id($season_id)
	{
		$this->season_id = $season_id;
	}

	public function get_division_id()
	{
		return $this->division_id;
	}

	public function set_division_id($division_id)
	{
		$this->division_id = $division_id;
	}

	public function get_start_date()
	{
		return $this->start_date;
	}

	public function set_start_date(Date $start_date)
	{
		$this->start_date = $start_date;
	}

	public function get_end_date()
	{
		return $this->end_date;
	}

	public function set_end_date(Date $end_date)
	{
		$this->end_date = $end_date;
	}

	public function get_views_number()
	{
		return $this->views_number;
	}

	public function set_views_number($views_number)
	{
		$this->views_number = $views_number;
	}

	public function get_scoring_type()
	{
		return $this->scoring_type;
	}

	public function set_scoring_type($scoring_type)
	{
		$this->scoring_type = $scoring_type;
	}

	public function get_is_sub()
	{
		return $this->is_sub;
	}

	public function set_is_sub($is_sub)
	{
		$this->is_sub = $is_sub;
	}

	public function get_master_id()
	{
		return $this->master_id;
	}

	public function set_master_id($master_id)
	{
		$this->master_id = $master_id;
	}

	public function get_sub_order()
	{
		return $this->sub_order;
	}

	public function set_sub_order($sub_order)
	{
		$this->sub_order = $sub_order;
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
		return ScmAuthorizationsService::check_authorizations($this->id_category)->read() && ($this->get_publishing_state() == self::PUBLISHED || ($this->get_publishing_state() == self::DEFERRED_PUBLICATION && $this->get_publishing_start_date()->is_anterior_to($now) && ($this->end_date_enabled ? $this->get_publishing_end_date()->is_posterior_to($now) : true)));
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
		return ScmAuthorizationsService::check_authorizations($this->id_category)->write() || ScmAuthorizationsService::check_authorizations($this->id_category)->contribution();
	}

	public function is_authorized_to_edit()
	{
		return ScmAuthorizationsService::check_authorizations($this->id_category)->moderation() || ScmAuthorizationsService::check_authorizations($this->id_category)->write();
	}

	public function is_authorized_to_delete()
	{
		return ScmAuthorizationsService::check_authorizations($this->id_category)->moderation() || ScmAuthorizationsService::check_authorizations($this->id_category)->write();
	}

	public function is_authorized_to_manage_events()
	{
		return ScmAuthorizationsService::check_authorizations($this->id_category)->moderation() || ScmAuthorizationsService::check_authorizations($this->id_category)->manage_events();
	}

	public function get_event_name()
	{
        $season_name = ScmSeasonService::get_season($this->get_season_id())->get_season_name();
        $division_name = ScmDivisionService::get_division($this->get_division_id())->get_division_name();
		return $division_name . ' - ' . $season_name;
	}

	public function get_properties()
	{
		return [
			'id'                    => $this->get_id(),
			'id_category'           => $this->get_id_category(),
			'event_slug'            => $this->get_event_slug(),
			'season_id'             => $this->get_season_id(),
			'division_id'           => $this->get_division_id(),
			'start_date'            => $this->get_start_date() !== null ? $this->get_start_date()->get_timestamp() : 0,
			'end_date'              => $this->get_end_date() !== null ? $this->get_end_date()->get_timestamp() : 0,
			'views_number'          => $this->get_views_number(),
			'scoring_type'          => $this->get_scoring_type(),
			'is_sub'                => $this->get_is_sub(),
			'master_id'             => $this->get_master_id(),
			'sub_order'             => $this->get_sub_order(),
			'published'             => $this->get_publishing_state(),
			'publishing_start_date' => $this->get_publishing_start_date() !== null ? $this->get_publishing_start_date()->get_timestamp() : 0,
			'publishing_end_date'   => $this->get_publishing_end_date() !== null ? $this->get_publishing_end_date()->get_timestamp() : 0,
			'creation_date'         => $this->get_creation_date()->get_timestamp(),
			'update_date'           => $this->get_update_date() !== null ? $this->get_update_date()->get_timestamp() : $this->get_creation_date()->get_timestamp(),
			'sources'               => TextHelper::serialize($this->get_sources())
        ];
	}

	public function set_properties(array $properties)
	{
		$this->id                    = $properties['id'];
		$this->id_category           = $properties['id_category'];
		$this->event_slug            = $properties['event_slug'];
		$this->season_id             = $properties['season_id'];
		$this->division_id           = $properties['division_id'];
		$this->start_date            = !empty($properties['start_date']) ? new Date($properties['start_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->end_date              = !empty($properties['end_date']) ? new Date($properties['end_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->views_number          = $properties['views_number'];
		$this->scoring_type          = $properties['scoring_type'];
		$this->is_sub                = $properties['is_sub'];
		$this->master_id             = $properties['master_id'];
		$this->sub_order             = $properties['sub_order'];
		$this->published             = $properties['published'];
		$this->publishing_start_date = !empty($properties['publishing_start_date']) ? new Date($properties['publishing_start_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->publishing_end_date   = !empty($properties['publishing_end_date']) ? new Date($properties['publishing_end_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->end_date_enabled      = !empty($properties['publishing_end_date']);
		$this->creation_date         = new Date($properties['creation_date'], Timezone::SERVER_TIMEZONE);
		$this->update_date           = !empty($properties['update_date']) ? new Date($properties['update_date'], Timezone::SERVER_TIMEZONE) : null;
		$this->sources               = !empty($properties['sources']) ? TextHelper::unserialize($properties['sources']) : [];
    }

	public function init_default_properties($id_category = Category::ROOT_CATEGORY)
	{
		$this->id_category           = $id_category;
		$this->start_date            = new Date();
		$this->end_date              = new Date();
		$this->publishing_start_date = new Date();
		$this->publishing_end_date   = new Date();
        $this->published             = self::PUBLISHED;
		$this->creation_date         = new Date();
		$this->end_date_enabled      = false;
		$this->sources               = [];
	}

	public function clean_publishing_start_and_end_date()
	{
		$this->publishing_start_date = null;
		$this->publishing_end_date   = null;
		$this->end_date_enabled      = false;
	}

	public function clean_publishing_end_date()
	{
		$this->publishing_end_date = null;
		$this->end_date_enabled    = false;
	}

	public function get_event_url()
	{
		return ScmUrlBuilder::event_home($this->id, $this->event_slug)->rel();
	}

	public function get_template_vars()
	{
		$category = $this->get_category();

		return array_merge(
			Date::get_array_tpl_vars($this->creation_date, 'date'),
			Date::get_array_tpl_vars($this->start_date, 'start_date'),
			Date::get_array_tpl_vars($this->end_date, 'end_date'),
			Date::get_array_tpl_vars($this->update_date, 'update_date'),
			Date::get_array_tpl_vars($this->publishing_start_date, 'differed_publishing_start_date'),
			[
				// Conditions
				'C_VISIBLE'         => $this->is_published(),
				'C_CONTROLS'        => $this->is_authorized_to_edit() || $this->is_authorized_to_delete() || $this->is_authorized_to_manage_events(),
				'C_EDIT'            => $this->is_authorized_to_edit(),
				'C_DELETE'          => $this->is_authorized_to_delete(),
				'C_PARAMETERS'      => $this->is_authorized_to_manage_events(),
				'C_HAS_UPDATE_DATE' => $this->has_update_date(),
				'C_DIFFERED'        => $this->published == self::DEFERRED_PUBLICATION,
                'C_IS_MASTER'       => ScmEventService::is_master($this->id),
                'C_IS_SUB'          => $this->is_sub,

				// Item
				'ID'            => $this->id,
				'TITLE'         => $this->get_event_name(),
                'SEASON_NAME'   => ScmSeasonService::get_season($this->get_season_id())->get_season_name(),
                'DIVISION_NAME' => ScmDivisionService::get_division($this->get_division_id())->get_division_name(),
				'STATUS'        => $this->get_publishing_state(),

				// Category
				'C_ROOT_CATEGORY' => $category->get_id() == Category::ROOT_CATEGORY,
				'CATEGORY_ID'     => $category->get_id(),
				'CATEGORY_NAME'   => $category->get_name(),
				'U_CATEGORY'      => ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel(),
				'U_EDIT_CATEGORY' => $category->get_id() == Category::ROOT_CATEGORY ? ScmUrlBuilder::configuration()->rel() : CategoriesUrlBuilder::edit($category->get_id(), 'scm')->rel(),

				// Links

				'U_SYNDICATION'  => SyndicationUrlBuilder::rss('scm', $this->id_category)->rel(),
				'U_EVENT'        => $this->get_event_url(),
				'U_MASTER_EVENT' => ScmEventService::get_master_url($this->id),
				'U_EDIT'         => ScmUrlBuilder::edit($this->id, $this->event_slug)->rel(),
				'U_DELETE'       => ScmUrlBuilder::delete($this->id)->rel(),
            ]
		);
	}

	public function get_array_tpl_source_vars($source_name)
	{
		$vars = [];
		$sources = $this->get_sources();

		if (isset($sources[$source_name]))
		{
			$vars = [
				'C_SEPARATOR' => array_search($source_name, array_keys($sources)) < count($sources) - 1,
				'NAME' => $source_name,
				'URL'  => $sources[$source_name]
            ];
		}

		return $vars;
	}
}
?>
