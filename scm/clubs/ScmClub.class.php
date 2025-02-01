<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmClub
{
	private $id_club;
	private $club_name;
	private $club_slug;
    private $club_affiliate;
    private $club_affiliation;
	private $club_full_name;
	private $club_flag;
	private $club_logo;
	private $club_website;
	private $club_email;
	private $club_phone;
	private $club_colors;
	private $club_locations;
	private $club_map_display;

	const CLUB_LOGO = '/scm/templates/images/badges/club.webp';

	public function get_id_club()
	{
		return $this->id_club;
	}

	public function set_id_club($id_club)
	{
		$this->id_club = $id_club;
	}

	public function get_club_name()
	{
		return $this->club_name;
	}

	public function set_club_name($club_name)
	{
		$this->club_name = $club_name;
	}

	public function get_club_slug()
	{
		return $this->club_slug;
	}

	public function set_club_slug($club_slug)
	{
		$this->club_slug = $club_slug;
	}

	public function get_club_affiliate()
	{
		return $this->club_affiliate;
	}

	public function set_club_affiliate($club_affiliate)
	{
		$this->club_affiliate = $club_affiliate;
	}

	public function get_club_affiliation()
	{
		return $this->club_affiliation;
	}

	public function set_club_affiliation($club_affiliation)
	{
		$this->club_affiliation = $club_affiliation;
	}

	public function get_club_full_name()
	{
		return $this->club_full_name;
	}

	public function set_club_full_name($club_full_name)
	{
		$this->club_full_name = $club_full_name;
	}

	public function get_club_flag()
	{
		return $this->club_flag;
	}

	public function set_club_flag($club_flag)
	{
		$this->club_flag = $club_flag;
	}

	public function get_club_logo()
	{
		return $this->club_logo;
	}

	public function set_club_logo($club_logo)
	{
		$this->club_logo = $club_logo;
	}

	public function get_club_website()
	{
		if (!$this->club_website instanceof Url)
			return new Url('');

		return $this->club_website;
	}

	public function set_club_website(Url $club_website)
	{
		$this->club_website = $club_website;
	}

	public function get_club_email()
	{
		return $this->club_email;
	}

	public function set_club_email($club_email)
	{
		$this->club_email = $club_email;
	}

	public function get_club_phone()
	{
		return $this->club_phone;
	}

	public function set_club_phone($club_phone)
	{
		$this->club_phone = $club_phone;
	}

	public function add_club_color($club_color)
	{
		$this->club_colors[] = $club_color;
	}

	public function set_club_colors($club_colors)
	{
		$this->club_colors = $club_colors;
	}

	public function get_club_colors()
	{
		return $this->club_colors;
	}

	public function get_club_locations()
	{
		return $this->club_locations;
	}

	public function set_club_locations($club_locations)
	{
		$this->club_locations = $club_locations;
	}

	public function get_club_map_display()
	{
		return $this->club_map_display;
	}

	public function set_club_map_display($club_map_display)
	{
		$this->club_map_display = $club_map_display;
	}

	public function is_authorized_to_manage()
	{
		return ScmAuthorizationsService::check_authorizations()->manage_clubs();
	}

	public function get_properties()
	{
        $real_club = ScmClubCache::load()->get_club($this->club_affiliation);
		return [
			'id_club'          => $this->id_club,
			'club_name'        => $this->club_name,
			'club_slug'        => $real_club ? $real_club['club_slug'] : $this->club_slug,
			'club_affiliate'   => $this->club_affiliate,
			'club_affiliation' => $this->club_affiliation,
			'club_full_name'   => $real_club ? $real_club['club_full_name'] : $this->club_full_name,
			'club_flag'        => $this->club_flag,
			'club_logo'        => $this->club_logo,
			'club_website'     => $this->club_website->absolute(),
			'club_email'       => $this->club_email,
			'club_phone'       => $this->club_phone,
			'club_locations'   => $this->club_locations,
			'club_map_display' => $this->club_map_display,
			'club_colors'      => TextHelper::serialize($this->club_colors)
        ];
	}

	public function set_properties(array $properties)
	{
        $real_club = ScmClubCache::load()->get_club($properties['club_affiliation']);
		$this->id_club          = $properties['id_club'];
		$this->club_name        = $properties['club_name'];
		$this->club_slug        = $this->club_affiliate ? $real_club['club_slug'] : $properties['club_slug'];
		$this->club_affiliate   = $properties['club_affiliate'];
		$this->club_affiliation = $properties['club_affiliation'];
		$this->club_full_name   = $this->club_affiliate ? $real_club['club_full_name'] : $properties['club_full_name'];
		$this->club_flag        = $properties['club_flag'];
		$this->club_logo        = $properties['club_logo'];
		$this->club_website     = new Url($properties['club_website']);
		$this->club_email       = $properties['club_email'];
		$this->club_phone       = $properties['club_phone'];
		$this->club_locations   = $properties['club_locations'];
		$this->club_map_display = $properties['club_map_display'];
        $this->club_colors      = !empty($properties['club_colors']) ? TextHelper::unserialize($properties['club_colors']) : [];
    }

	public function init_default_properties()
	{
		$this->club_map_display = true;
		$this->club_colors      = [];
		$this->club_website     = new Url('');
	}

	public function get_club_url()
	{
		return ScmUrlBuilder::display_club($this->id_club, $this->club_slug)->rel();
	}

	public function get_template_vars()
	{
        $club_locations_value = TextHelper::deserialize($this->get_club_locations());
        $club_locations = '';
		if (is_array($club_locations_value) && isset($club_locations_value['address']))
			$club_locations = $club_locations_value['address'];
		else if (!is_array($club_locations_value))
			$club_locations = $club_locations_value;

		$club_locations_map = '';
		$c_display_map = false;
		if (ScmConfig::load()->is_googlemaps_available())
		{
			$map = new GoogleMapsDisplayMap($this->get_club_locations(), 'club_locations', $this->get_club_name());
			$club_locations_map = $map->display();
			$c_display_map = $this->get_club_map_display();
		}

		return [
            // Conditions
            'C_CONTROLS'      => $this->is_authorized_to_manage(),
            'C_DISPLAY_MAP'   => $c_display_map && $this->has_locations(),
            'C_HAS_SHIELD'    => !empty($this->club_flag) || !empty($this->club_logo),
            'C_HAS_FLAG'      => !empty($this->club_flag),
            'C_HAS_LOGO'      => !empty($this->club_logo),
            'C_HAS_WEBSITE'   => !empty($this->club_website->rel()),
            'C_HAS_EMAIL'     => !empty($this->club_email),
            'C_HAS_PHONE'     => !empty($this->club_phone),
            'C_HAS_NAME'      => !empty($this->club_name),
            'C_HAS_FULL_NAME' => !empty($this->club_full_name),
			// Item
			'ID'           => $this->id_club,
			'NAME'         => $this->club_name,
			'FULL_NAME'    => $this->club_full_name,
			'EMAIL'        => $this->club_email,
			'PHONE'        => $this->club_phone,
			'LOCATION'     => $club_locations,
			'LOCATION_MAP' => $club_locations_map,

			// Links
			'U_LOGO'         => Url::to_rel(ScmClubCache::load()->get_affiliate_club_shield($this->id_club)),
			'U_FLAG'         => TPL_PATH_TO_ROOT . '/images/stats/countries/' . $this->club_flag . '.png',
			'U_CLUB_WEBSITE' => ScmUrlBuilder::visit_club($this->id_club)->rel(),
			'U_CLUB'         => $this->get_club_url(),
			'U_EDIT'         => ScmUrlBuilder::edit_club($this->id_club, $this->club_slug)->rel(),
			'U_DELETE'       => ScmUrlBuilder::delete_club($this->id_club)->rel(),
        ];
	}

    private function has_locations()
    {
        $club_locations_value = TextHelper::deserialize($this->get_club_locations());
        $locations = [];
        foreach ($club_locations_value as $options)
        {
            if ($options['name'])
                $locations[] = $options;
        }
        return count($locations) > 0;
    }
}
?>
