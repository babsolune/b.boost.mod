<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 01 09
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class GuideSetup extends DefaultModuleSetup
{
	public static $guide_table;
	public static $guide_contents_table;
	public static $guide_cats_table;
	public static $guide_favs_table;

	/**
	 * @var string[string] localized messages
	 */
	private $messages;

	public static function __static()
	{
		self::$guide_table = PREFIX . 'guide';
		self::$guide_contents_table = PREFIX . 'guide_contents';
		self::$guide_cats_table = PREFIX . 'guide_cats';
		self::$guide_favs_table = PREFIX . 'guide_favs';
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
		ConfigManager::delete('guide', 'config');
		CacheManager::invalidate('module', 'guide');
		KeywordsService::get_keywords_manager()->delete_module_relations();
	}

	private function drop_tables()
	{
		PersistenceContext::get_dbms_utils()->drop(array(
			self::$guide_table, 
			self::$guide_contents_table, 
			self::$guide_cats_table,
			self::$guide_favs_table
		));
	}

	private function create_tables()
	{
		$this->create_guide_table();
		$this->create_guide_contents_table();
		$this->create_guide_cats_table();
		$this->create_guide_favs_table();
	}

	private function create_guide_table()
	{
		$fields = array(
			'id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'id_category' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'i_order' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'rewrited_title' => array('type' => 'string', 'length' => 255, 'default' => "''"),
			'published' => array('type' => 'integer', 'length' => 1, 'notnull' => 1, 'default' => 0),
			'publishing_start_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'publishing_end_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'creation_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'views_number' => array('type' => 'integer', 'length' => 11, 'default' => 0),
		);
		$options = array(
			'primary' => array('id'),
			'indexes' => array(
				'id_category' => array('type' => 'key', 'fields' => 'id_category'),
				'i_order' => array('type' => 'key', 'fields' => 'i_order'),
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$guide_table, $fields, $options);
	}

	private function create_guide_contents_table()
	{
		$fields = array(
			'content_id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'item_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'title' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'summary' => array('type' => 'text', 'length' => 65000),
			'active_content' => array('type' => 'boolean', 'length' => 1, 'notnull' => 1, 'default' => 0),
			'content' => array('type' => 'text', 'length' => 16777215),
			'thumbnail' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'author_user_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'author_custom_name' => array('type' =>  'string', 'length' => 255, 'default' => "''"),
			'update_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'change_reason' => array('type' => 'text', 'length' => 100, 'notnull' => 0),
			'content_level' => array('type' => 'integer', 'length' => 1, 'default' => 0),
			'custom_level' => array('type' => 'text', 'length' => 65000),
			'sources' => array('type' => 'text', 'length' => 65000)
		);
		$options = array(
			'primary' => array('content_id'),
			'indexes' => array(
				'title' => array('type' => 'fulltext', 'fields' => 'title'),
				'item_id' => array('type' => 'key', 'fields' => 'item_id'),
				'content' => array('type' => 'fulltext', 'fields' => 'content'),
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$guide_contents_table, $fields, $options);
	}

	private function create_guide_cats_table()
	{
		RichCategory::create_categories_table(self::$guide_cats_table);
	}

	private function create_guide_favs_table()
	{
		$fields = array(
			'id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'user_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'item_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0)
		);
		$options = array(
			'primary' => array('id')
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$guide_favs_table, $fields, $options);
	}

	private function insert_data()
	{
		$this->messages = LangLoader::get('install', 'guide');
		$this->insert_guide_cats_data();
		$this->insert_guide_data();
		$this->insert_guide_contents_data();
	}

	private function insert_guide_cats_data()
	{
		PersistenceContext::get_querier()->insert(self::$guide_cats_table, array(
			'id'            => 1,
			'id_parent'     => 0,
			'c_order'       => 1,
			'auth'          => '',
			'rewrited_name' => Url::encode_rewrite($this->messages['default.cat.name']),
			'name'          => $this->messages['default.cat.name'],
			'description'   => $this->messages['default.cat.description'],
			'thumbnail'     => FormFieldThumbnail::DEFAULT_VALUE
		));
	}

	private function insert_guide_data()
	{
		PersistenceContext::get_querier()->insert(self::$guide_table, array(
			'id'               		=> 1,
			'id_category'           => 1,
			'i_order'               => 1,
			'rewrited_title'        => Url::encode_rewrite($this->messages['default.sheet.name']),
			'published'             => GuideItem::PUBLISHED,
			'publishing_start_date' => 0,
			'publishing_end_date'   => 0,
			'creation_date'  	    => time(),
			'views_number'          => 0
		));
	}

	private function insert_guide_contents_data()
	{
		PersistenceContext::get_querier()->insert(self::$guide_contents_table, array(
			'content_id'     	 => 1,
			'item_id'        	 => 1,
			'title'                 => $this->messages['default.sheet.name'],
			'active_content'     => 1,
			'summary'        	    => '',
			'author_custom_name' 	=> '',
			'thumbnail'             => FormFieldThumbnail::DEFAULT_VALUE,
			'content'        	 => $this->messages['default.sheet.content'],
			'content_level'    		=> GuideItemContent::NO_LEVEL,
			'sources'               => TextHelper::serialize(array()),
			'change_reason'    	 => '',
			'author_user_id' 	 => 1,
			'update_date'    	 => time()
		));
	}
}
?>
