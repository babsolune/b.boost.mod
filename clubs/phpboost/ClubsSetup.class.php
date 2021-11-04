<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ClubsSetup extends DefaultModuleSetup
{
	public static $clubs_table;
	public static $clubs_cats_table;

	/**
	 * @var string[string] localized messages
	 */
	private $messages;

	public static function __static()
	{
		self::$clubs_table = PREFIX . 'clubs';
		self::$clubs_cats_table = PREFIX . 'clubs_cats';
	}

	public function upgrade($installed_version)
	{
		return '6.0.0';
	}

	public function install()
	{
		$this->drop_tables();
		$this->create_tables();
		$this->insert_data();
	}

	public function uninstall()
	{
		$this->drop_tables();
		ConfigManager::delete('clubs', 'config');
		CacheManager::invalidate('module', 'clubs');
	}

	private function drop_tables()
	{
		PersistenceContext::get_dbms_utils()->drop(array(self::$clubs_table, self::$clubs_cats_table));
	}

	private function create_tables()
	{
		$this->create_clubs_table();
		$this->create_clubs_cats_table();
	}

	private function create_clubs_table()
	{
		$fields = array(
			'id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'id_category' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'title' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'rewrited_title' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'short_title' => array('type' => 'string', 'length' => 255, 'default' => "''"),
			'website_url' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'content' => array('type' => 'text', 'length' => 65000),
			'creation_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'update_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'published' => array('type' => 'integer', 'length' => 1, 'notnull' => 1, 'default' => 0),
			'author_user_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'views_number' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'logo_url' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'logo_mini_url' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'colors_enabled' => array('type' => 'boolean', 'notnull' => 1, 'default' => 0),
			'colors' => array('type' => 'text', 'length' => 65000),
			'location' => array('type' => 'text', 'length' => 65000),
			'stadium_address' => array('type' => 'text', 'length' => 65000),
			'latitude' => array('type' => 'decimal', 'length' => 18, 'scale' => 15, 'notnull' => 1, 'default' => 0),
			'longitude' => array('type' => 'decimal', 'length' => 18, 'scale' => 15, 'notnull' => 1, 'default' => 0),
            'club_email' => array('type' => 'text', 'length' => 65000),
			'phone' => array('type' => 'text', 'length' => 65000),
			'facebook' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'twitter' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'instagram' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'youtube' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
		);
		$options = array(
			'primary' => array('id'),
			'indexes' => array(
				'id_category' => array('type' => 'key', 'fields' => 'id_category'),
				'title' => array('type' => 'fulltext', 'fields' => 'title'),
				'content' => array('type' => 'fulltext', 'fields' => 'content')
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$clubs_table, $fields, $options);
	}

	private function create_clubs_cats_table()
	{
		RichCategory::create_categories_table(self::$clubs_cats_table);
	}

	private function insert_data()
	{
		$this->messages = LangLoader::get('install', 'clubs');
		$this->insert_clubs_cats_data();
		$this->insert_clubs_data();
	}

	private function insert_clubs_cats_data()
	{
		PersistenceContext::get_querier()->insert(self::$clubs_cats_table, array(
			'id'            => 1,
			'id_parent'     => 0,
			'c_order'       => 1,
			'auth'          => '',
			'rewrited_name' => Url::encode_rewrite($this->messages['default.cat.name']),
			'name'          => $this->messages['default.cat.name'],
			'description'   => $this->messages['default.cat.description'],
			'thumbnail'     => FormFieldThumbnail::DEFAULT_VALUE,
		));
	}

	private function insert_clubs_data()
	{
		PersistenceContext::get_querier()->insert(self::$clubs_table, array(
			'id'              => 1,
			'id_category'     => 1,
			'title'           => $this->messages['default.club.title'],
			'rewrited_title'  => Url::encode_rewrite($this->messages['default.club.title']),
			'content'         => $this->messages['default.club.content'],
			'published'       => ClubsItem::PUBLISHED,
			'stadium_address' => $this->messages['default.club.stadium'],
			'latitude'        => $this->messages['default.club.latitude'],
			'longitude'       => $this->messages['default.club.longitude'],
			'creation_date'   => time(),
			'update_date'     => time(),
			'author_user_id'  => 1,
			'views_number'    => 0,
			'logo_url'        => '/clubs/clubs.png',
			'logo_mini_url'   => '/clubs/clubs_mini.png',
			'colors'          => TextHelper::serialize(array()),
			'location'        => TextHelper::serialize(array()),
		));
	}
}
?>
