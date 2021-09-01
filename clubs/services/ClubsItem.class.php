<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ClubsItem
{
	private $id;
	private $id_category;
	private $title;
	private $short_title;
	private $rewrited_title;
	private $website_url;
    private $location;
    private $stadium_address;
    private $stadium_latitude;
    private $stadium_longitude;
    private $phone;
    private $club_email;
	private $content;

	private $published;

	private $creation_date;
	private $author_user;
	private $views_number;
	private $logo_url;
	private $logo_mini_url;
    private $colors_enabled;
    private $colors;

    private $facebook;
    private $twitter;
    private $instagram;
    private $youtube;

	private $notation;

	const NOT_PUBLISHED = 0;
	const PUBLISHED = 1;

	const DEFAULT_LOGO = '';

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

	public function get_category()
	{
		return CategoriesService::get_categories_manager()->get_categories_cache()->get_category($this->id_category);
	}

	public function get_title()
	{
		return $this->title;
	}

	public function set_title($title)
	{
		$this->title = $title;
	}

	public function get_short_title()
	{
		return $this->short_title;
	}

	public function set_short_title($short_title)
	{
		$this->short_title = $short_title;
	}

	public function get_rewrited_title()
	{
		return $this->rewrited_title;
	}

	public function set_rewrited_title($rewrited_title)
	{
		$this->rewrited_title = $rewrited_title;
	}

	public function get_website_url()
	{
		if (!$this->website_url instanceof Url)
			return new Url('');

		return $this->website_url;
	}

	public function set_website_url(Url $website_url)
	{
		$this->website_url = $website_url;
	}

	public function get_location()
	{
		return $this->location;
	}

	public function set_location($location)
	{
		$this->location = $location;
	}

	public function get_stadium_address()
	{
		return $this->stadium_address;
	}

	public function set_stadium_address($stadium_address)
	{
		$this->stadium_address = $stadium_address;
	}

	public function get_stadium_latitude()
	{
		return $this->stadium_latitude;
	}

	public function set_stadium_latitude($stadium_latitude)
	{
		$this->stadium_latitude = $stadium_latitude;
	}

	public function get_stadium_longitude()
	{
		return $this->stadium_longitude;
	}

	public function set_stadium_longitude($stadium_longitude)
	{
		$this->stadium_longitude = $stadium_longitude;
	}

	public function get_phone()
	{
		return $this->phone;
	}

	public function set_phone($phone)
	{
		$this->phone = $phone;
	}

	public function get_club_email()
	{
		return $this->club_email;
	}

	public function set_club_email($club_email)
	{
		$this->club_email = $club_email;
	}

	public function get_content()
	{
		return $this->content;
	}

	public function set_content($content)
	{
		$this->content = $content;
	}

	public function set_colors_enabled($colors_enabled)
	{
		$this->colors_enabled = $colors_enabled;
	}

	public function has_colors()
	{
		return $this->colors_enabled;
	}

	public function add_colors_pic($colors_pic)
	{
		$this->colors[] = $colors_pic;
	}

	public function set_colors($colors)
	{
		$this->colors = $colors;
	}

	public function get_colors()
	{
		return $this->colors;
	}

	public function get_published()
	{
		return $this->published;
	}

	public function set_published($published)
	{
		$this->published = $published;
	}

	public function is_published()
	{
		$now = new Date();
		return CategoriesAuthorizationsService::check_authorizations($this->id_category)->read() && ($this->get_published() == self::PUBLISHED );
	}

	public function get_status()
	{
		switch ($this->published) {
			case self::PUBLISHED:
				return LangLoader::get_message('common.status.approved', 'common-lang');
			break;
			case self::NOT_PUBLISHED:
				return LangLoader::get_message('common.status.draft', 'common-lang');
			break;
		}
	}

	public function get_creation_date()
	{
		return $this->creation_date;
	}

	public function set_creation_date(Date $creation_date)
	{
		$this->creation_date = $creation_date;
	}

	public function get_author_user()
	{
		return $this->author_user;
	}

	public function set_author_user(User $user)
	{
		$this->author_user = $user;
	}

	public function get_views_number()
	{
		return $this->views_number;
	}

	public function set_views_number($views_number)
	{
		$this->views_number = $views_number;
	}

	public function get_logo()
	{
		return $this->logo_url;
	}

	public function set_logo(Url $logo)
	{
		$this->logo_url = $logo;
	}

	public function has_logo()
	{
		$logo = $this->logo_url->rel();
		return !empty($logo);
	}

	public function get_logo_mini()
	{
		return $this->logo_mini_url;
	}

	public function set_logo_mini(Url $logo_mini)
	{
		$this->logo_mini_url = $logo_mini;
	}

	public function has_logo_mini()
	{
		$logo_mini = $this->logo_mini_url->rel();
		return !empty($logo_mini);
	}

	public function get_facebook()
	{
		if (!$this->facebook instanceof Url)
			return new Url('');

		return $this->facebook;
	}

	public function set_facebook(Url $facebook)
	{
		$this->facebook = $facebook;
	}

	public function get_twitter()
	{
		if (!$this->twitter instanceof Url)
			return new Url('');

		return $this->twitter;
	}

	public function set_twitter(Url $twitter)
	{
		$this->twitter = $twitter;
	}

	public function get_instagram()
	{
		if (!$this->instagram instanceof Url)
			return new Url('');

		return $this->instagram;
	}

	public function set_instagram(Url $instagram)
	{
		$this->instagram = $instagram;
	}

	public function get_youtube()
	{
		if (!$this->youtube instanceof Url)
			return new Url('');

		return $this->youtube;
	}

	public function set_youtube(Url $youtube)
	{
		$this->youtube = $youtube;
	}

	public function get_notation()
	{
		return $this->notation;
	}

	public function set_notation(Notation $notation)
	{
		$this->notation = $notation;
	}

	public function is_authorized_to_add()
	{
		return CategoriesAuthorizationsService::check_authorizations($this->id_category)->write() || CategoriesAuthorizationsService::check_authorizations($this->id_category)->contribution();
	}

	public function is_authorized_to_edit()
	{
		return CategoriesAuthorizationsService::check_authorizations($this->id_category)->moderation() || ((CategoriesAuthorizationsService::check_authorizations($this->id_category)->write() || (CategoriesAuthorizationsService::check_authorizations($this->id_category)->contribution() && !$this->is_published())) && $this->get_author_user()->get_id() == AppContext::get_current_user()->get_id() && AppContext::get_current_user()->check_level(User::MEMBER_LEVEL));
	}

	public function is_authorized_to_delete()
	{
		return CategoriesAuthorizationsService::check_authorizations($this->id_category)->moderation() || ((CategoriesAuthorizationsService::check_authorizations($this->id_category)->write() || (CategoriesAuthorizationsService::check_authorizations($this->id_category)->contribution() && !$this->is_published())) && $this->get_author_user()->get_id() == AppContext::get_current_user()->get_id() && AppContext::get_current_user()->check_level(User::MEMBER_LEVEL));
	}

	public function get_properties()
	{
		return array(
			'id' => $this->get_id(),
			'id_category' => $this->get_id_category(),
			'title' => $this->get_title(),
			'short_title' => $this->get_short_title(),
			'rewrited_title' => $this->get_rewrited_title(),
			'website_url' => $this->get_website_url()->absolute(),
			'phone' => $this->get_phone(),
			'club_email' => $this->get_club_email(),
			'location' => TextHelper::serialize($this->get_location()),
			'stadium_address' => $this->get_stadium_address(),
			'latitude' => $this->get_stadium_latitude(),
			'longitude' => $this->get_stadium_longitude(),
			'facebook' => $this->get_facebook()->absolute(),
			'twitter' => $this->get_twitter()->absolute(),
			'instagram' => $this->get_instagram()->absolute(),
			'youtube' => $this->get_youtube()->absolute(),
			'content' => $this->get_content(),
			'published' => $this->get_published(),
			'creation_date' => $this->get_creation_date()->get_timestamp(),
			'author_user_id' => $this->get_author_user()->get_id(),
			'views_number' => $this->get_views_number(),
			'logo_url' => $this->get_logo()->relative(),
			'logo_mini_url' => $this->get_logo_mini()->relative(),
			'colors_enabled' => (int)$this->has_colors(),
			'colors' => TextHelper::serialize($this->get_colors()),
		);
	}

	public function set_properties(array $properties)
	{
		$this->id = $properties['id'];
		$this->id_category = $properties['id_category'];
		$this->title = $properties['title'];
		$this->short_title = $properties['short_title'];
		$this->rewrited_title = $properties['rewrited_title'];
		$this->website_url = new Url($properties['website_url']);
		$this->phone = $properties['phone'];
		$this->club_email = $properties['club_email'];
		$this->location = !empty($properties['location']) ? TextHelper::unserialize($properties['location']) : array();
        $this->stadium_address = $properties['stadium_address'];
        $this->stadium_latitude = $properties['latitude'];
        $this->stadium_longitude = $properties['longitude'];
        $this->facebook = new Url($properties['facebook']);
        $this->twitter = new Url($properties['twitter']);
        $this->instagram = new Url($properties['instagram']);
        $this->youtube = new Url($properties['youtube']);
		$this->content = $properties['content'];
		$this->published = $properties['published'];
		$this->creation_date = new Date($properties['creation_date'], Timezone::SERVER_TIMEZONE);
		$this->views_number = $properties['views_number'];
		$this->logo_url = new Url($properties['logo_url']);
		$this->logo_mini_url = new Url($properties['logo_mini_url']);
		$this->colors_enabled      = (bool)$properties['colors_enabled'];
		$this->colors = !empty($properties['colors']) ? TextHelper::unserialize($properties['colors']) : array();

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
		$this->creation_date = new Date();
		$this->views_number = 0;
		$this->logo_url = new Url('');
		$this->logo_mini_url = new Url('');
		$this->website_url = new Url('');
		$this->location = array();
        $this->facebook = new Url('');
        $this->twitter = new Url('');
        $this->instagram = new Url('');
        $this->youtube = new Url('');
		$this->colors = array();
	}

	public function get_array_tpl_vars()
	{
		$category = $this->get_category();
		$content = FormatingHelper::second_parse($this->content);
		$user = $this->get_author_user();
		$user_group_color = User::get_group_color($user->get_groups(), $user->get_level(), true);
        $config = ClubsConfig::load();

		$colors_number = count($this->get_colors());

        // Convertisseur degres decimaux -> derges, minutes, secondes
        // Latitude
        $stad_lat = $this->stadium_latitude;

        if($stad_lat > 0)
            $card_lat = 'N';
        else
            $card_lat = 'S';

        $stad_lat = abs($stad_lat);
        $stad_lat_deg = intval($stad_lat);
        $stad_lat_min = ($stad_lat - $stad_lat_deg)*60;
        $stad_lat_sec = ($stad_lat_min - intval($stad_lat))*60;

        // Longitude
        $stad_lng = $this->stadium_longitude;

        if($stad_lng > 0)
            $card_lng = 'E';
        else
            $card_lng = 'W';

        $stad_lng = abs($stad_lng);
        $stad_lng_deg = intval($stad_lng);
        $stad_lng_min = ($stad_lng - $stad_lng_deg)*60;
        $stad_lng_sec = ($stad_lng_min - intval($stad_lng))*60;

		return array_merge(
			Date::get_array_tpl_vars($this->creation_date, 'date'),
			array(
            'C_NEW_WINDOW'         => $config->get_new_window(true),
            'C_GMAP_ENABLED'       => ClubsService::is_gmap_enabled(),
            'C_LOCATION'           => !empty($this->location),
            'C_STADIUM_LOCATION'   => ($this->stadium_latitude) && ($this->stadium_longitude),
            'C_CONTENT'            => !empty($content),
			'C_VISIBLE'            => $this->is_published(),
			'C_CONTROLS'           => $this->is_authorized_to_edit() || $this->is_authorized_to_delete(),
			'C_EDIT'               => $this->is_authorized_to_edit(),
			'C_DELETE'             => $this->is_authorized_to_delete(),
			'C_AUTHOR_GROUP_COLOR' => !empty($user_group_color),
            'C_VISIT'              => !empty($this->website_url->absolute()),
			'C_LOGO'               => $this->has_logo(),
			'C_LOGO_MINI'          => $this->has_logo_mini(),
            'C_PHONE'              => !empty($this->phone),
            'C_EMAIL'              => !empty($this->club_email),
			'C_FACEBOOK'           => !empty($this->facebook->absolute()),
			'C_TWITTER'            => !empty($this->twitter->absolute()),
			'C_INSTAGRAM'          => !empty($this->instagram->absolute()),
			'C_YOUTUBE'            => !empty($this->youtube->absolute()),
			'C_DEFAULT_ADDRESS'    => GoogleMapsConfig::load()->get_default_marker_address(),
			'C_CONTACT'			   => !empty($this->phone) || !empty($this->club_email) || !empty($this->facebook->absolute()) || !empty($this->twitter->absolute()) || !empty($this->instagram->absolute()) || !empty($this->youtube->absolute()),
			'C_COLORS'		   	   => $this->colors_enabled && $colors_number > 0,

            // Deafult values
            'GMAP_API_KEY' => GoogleMapsConfig::load()->get_api_key(),
            'C_GMAP_API'   => ModulesManager  ::is_module_installed('GoogleMaps') && ModulesManager::is_module_activated('GoogleMaps'),

			// Category
			'C_ROOT_CATEGORY'      => $category->get_id() == Category::ROOT_CATEGORY,
			'CATEGORY_ID'          => $category->get_id(),
			'CATEGORY_NAME'        => $category->get_name(),
			'CATEGORY_DESCRIPTION' => $category->get_description(),
			'CATEGORY_THUMBNAIL'   => $category->get_thumbnail()->rel(),
			'U_EDIT_CATEGORY'      => $category->get_id() == Category::ROOT_CATEGORY ? ClubsUrlBuilder::configuration()->rel() : CategoriesUrlBuilder::edit($category->get_id())->rel(),

			// Item
			'ID'                  => $this->id,
			'TITLE'               => $this->title,
			'REWRITED_TITLE'      => $this->rewrited_title,
			'WEBSITE_URL'         => $this->website_url->absolute(),
			'CONTENT'             => $content,
			'STATUS'              => $this->get_status(),
			'C_AUTHOR_EXIST'      => $user->get_id() !== User::VISITOR_LEVEL,
			'AUTHOR_DISPLAY_NAME' => $user->get_display_name(),
			'AUTHOR_LEVEL_CLASS'  => UserService::get_level_class($user->get_level()),
			'AUTHOR_GROUP_COLOR'  => $user_group_color,
			'VIEWS_NUMBER'        => $this->views_number,
            'LOCATION'            => $this->location,
            'PHONE'               => $this->phone,
            'EMAIL'               => $this->club_email,
			'DEFAULT_LAT'         => GoogleMapsConfig::load()->get_default_marker_latitude(),
			'DEFAULT_LNG'         => GoogleMapsConfig::load()->get_default_marker_longitude(),
            'STADIUM_ADDRESS'     => $this->stadium_address,
            'LATITUDE'            => $this->stadium_latitude,
            'LONGITUDE'           => $this->stadium_longitude,
            'STAD_LAT'            => str_pad($stad_lat_deg, 2, '0', STR_PAD_LEFT) . '° ' . intval($stad_lat_min) . "' " . number_format($stad_lat_sec, 2) . '" ' . $card_lat,
            'STAD_LNG'            => str_pad($stad_lng_deg, 2, '0', STR_PAD_LEFT) . '° ' . intval($stad_lng_min) . "' " . number_format($stad_lng_sec, 2) . '" ' . $card_lng,

			'U_SYNDICATION'    => SyndicationUrlBuilder::rss('clubs', $this->id_category)->rel(),
			'U_AUTHOR_PROFILE' => UserUrlBuilder::profile($this->get_author_user()->get_id())->rel(),
			'U_ITEM'           => ClubsUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $this->id, $this->rewrited_title)->rel(),
			'U_VISIT'          => ClubsUrlBuilder::visit($this->id)->rel(),
			'U_DEADLINK'       => ClubsUrlBuilder::dead_link($this->id)->rel(),
			'U_CATEGORY'       => ClubsUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name())->rel(),
			'U_EDIT'           => ClubsUrlBuilder::edit($this->id)->rel(),
			'U_DELETE'         => ClubsUrlBuilder::delete($this->id)->rel(),
			'U_LOGO'           => $this->get_logo()->rel(),
			'U_LOGO_MINI'      => $this->get_logo_mini()->rel(),
			'U_FACEBOOK'       => $this->facebook->absolute(),
			'U_TWITTER'        => $this->twitter->absolute(),
			'U_INSTAGRAM'      => $this->instagram->absolute(),
			'U_YOUTUBE'        => $this->youtube->absolute()
			)
		);

	}
}
?>
