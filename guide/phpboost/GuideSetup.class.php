<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 03 27
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class GuideSetup extends DefaultModuleSetup
{
	public static $guide_articles_table;
	public static $guide_cats_table;
	public static $guide_contents_table;
	public static $guide_favorites_table;

	/**
	 * @var string[string] localized messages
	 */
	private $messages;

	private $articles_column;
	private $contents_column;
	private $favorites_column;

	public static function __static()
	{
		self::$guide_articles_table = PREFIX . 'guide_articles';
		self::$guide_cats_table = PREFIX . 'guide_cats';
		self::$guide_contents_table = PREFIX . 'guide_contents';
		self::$guide_favorites_table = PREFIX . 'guide_favorites';
	}

    public function upgrade($installed_version)
    {
		$this->articles_column = PersistenceContext::get_dbms_utils()->desc_table(self::$guide_articles_table);
		$this->contents_column = PersistenceContext::get_dbms_utils()->desc_table(self::$guide_contents_table);
		$this->favorites_column = PersistenceContext::get_dbms_utils()->desc_table(self::$guide_favorites_table);
		$this->delete_files();
		$this->add_fields();
		$this->modify_fields();
		$this->delete_fields();
        return '6.0.1';
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
			self::$guide_articles_table,
			self::$guide_cats_table,
			self::$guide_contents_table,
			self::$guide_favorites_table
		));
	}

	private function create_tables()
	{
		$this->create_guide_articles_table();
		$this->create_guide_cats_table();
		$this->create_guide_contents_table();
		$this->create_guide_favorites_table();
	}

	private function create_guide_articles_table()
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
		PersistenceContext::get_dbms_utils()->create_table(self::$guide_articles_table, $fields, $options);
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
			'update_date' => array('type' => 'integer', 'length' => 11, 'default' => 0),
			'change_reason' => array('type' => 'text', 'length' => 65000, 'notnull' => 0),
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

	private function create_guide_favorites_table()
	{
		$fields = array(
			'track_id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'track_item_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'track_user_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0)
		);
		$options = array(
			'primary' => array('track_id')
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$guide_favorites_table, $fields, $options);
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
		PersistenceContext::get_querier()->insert(self::$guide_articles_table, array(
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
			'content_id'     	    => 1,
			'item_id'        	    => 1,
			'title'                 => $this->messages['default.sheet.name'],
			'active_content'        => 1,
			'summary'        	    => '',
			'author_custom_name'    => '',
			'thumbnail'             => FormFieldThumbnail::DEFAULT_VALUE,
			'content'        	    => $this->messages['default.sheet.content'],
			'content_level'    	    => GuideItemContent::NO_LEVEL,
			'sources'               => TextHelper::serialize(array()),
			'change_reason'    	    => $this->messages['default.history.init'],
			'author_user_id'        => 1,
			'update_date'    	    => time()
		));
	}

	private function delete_files()
	{
		$file = new File(PATH_TO_ROOT . '/wiki/phpboost/WikiCategoriesCache.class.php');        $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/phpboost/WikiHomePageExtensionPoint.class.php'); $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/phpboost/WikiSitemapExtensionPoint.class.php');  $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/js/bbcode.wiki.js');                   $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/js/bbcode.wiki.min.js');               $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/admin_wiki.tpl');                      $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/admin_wiki_groups.tpl');               $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/explorer.tpl');                        $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/favorites.tpl');                       $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/history.tpl');                         $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/index.tpl');                           $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/post.tpl');                            $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/property.tpl');                        $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/wiki.tpl');                            $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/wiki_js_tool.tpl');                    $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/wiki_search_form.tpl');                $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/wiki_tools.tpl');                      $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/action.php');                                    $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/admin_wiki_groups.php');                         $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/admin_wiki.php');                                $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/explorer.php');                                  $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/favorites.php');                                 $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/history.php');                                   $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/post_js_tool.php');                              $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/post.php');                                      $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/print.php');                                     $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/property.php');                                  $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/wiki_auth.php');                                 $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/wiki_bread_crumb.php');                          $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/wiki_functions.php');                            $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/wiki_tools.php');                                $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/wiki.php');                                      $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/xmlhttprequest.php');                            $file->delete();
	}

	private function add_fields()
	{
		if (!isset($this->articles_column['i_order']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_articles', 'i_order', array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0));
		if (!isset($this->articles_column['creation_date']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_articles', 'creation_date', array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0));
		if (!isset($this->articles_column['published']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_articles', 'published', array('type' => 'integer', 'length' => 1, 'notnull' => 1, 'default' => 0));
		if (!isset($this->articles_column['publishing_start_date']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_articles', 'publishing_start_date', array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0));
		if (!isset($this->articles_column['publishing_end_date']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_articles', 'publishing_end_date', array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0));

        if (!isset($this->contents_column['update_date']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_contents', 'update_date', array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0));
        if (!isset($this->contents_column['thumbnail']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_contents', 'thumbnail', array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"));
        if (!isset($this->contents_column['custom_level']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_contents', 'custom_level', array('type' => 'string', 'length' => 65000));
        if (!isset($this->contents_column['sources']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_contents', 'sources', array('type' => 'string', 'length' => 65000));
    }

	private function modify_fields()
	{
		$articles_change = array(
            'id_cats'       => 'id_category INT(11) NOT NULL DEFAULT 0',
            'encoded_title' => 'rewrited_title VARCHAR(255) NOT NULL DEFAULT ""',
            'hits'          => 'views_number INT(11) NOT NULL DEFAULT 0'
		);
		foreach ($articles_change as $old_name => $new_name)
		{
			if (isset($this->articles_column[$old_name]))
				PersistenceContext::get_querier()->inject('ALTER TABLE ' . PREFIX . 'wiki_articles CHANGE ' . $old_name . ' ' . $new_name);
		}

		$contents_change = array(
            'id_contents' => 'content_id INT(11) AUTO_INCREMENT NOT NULL DEFAULT 0',
            'id_article'  => 'item_id INT(11) NOT NULL DEFAULT 0',
            'timestamp'   => 'update_date INT(11) NOT NULL DEFAULT 0',
            'activ'       => 'active_content INT(11) NOT NULL DEFAULT 0',
            'user_id'     => 'author_user_id INT(11) NOT NULL DEFAULT 0'
		);
		foreach ($contents_change as $old_name => $new_name)
		{
			if (isset($this->contents_column[$old_name]))
				PersistenceContext::get_querier()->inject('ALTER TABLE ' . PREFIX . 'wiki_contents CHANGE ' . $old_name . ' ' . $new_name);
		}

		$favorites_change = array(
            'id'         => 'track_id INT(11) AUTO_INCREMENT NOT NULL DEFAULT 0',
            'id_article' => 'track_item_id INT(11) NOT NULL DEFAULT 0',
            'user_id'    => 'track_user_id INT(11) NOT NULL DEFAULT 0'
		);
		foreach ($favorites_change as $old_name => $new_name)
		{
			if (isset($this->favorites_column[$old_name]))
				PersistenceContext::get_querier()->inject('ALTER TABLE ' . PREFIX . 'wiki_favorites CHANGE ' . $old_name . ' ' . $new_name);
		}
	}

	private function delete_fields()
	{
		if (isset($this->articles_column['id_contents']))
			PersistenceContext::get_dbms_utils()->drop_column(PREFIX . 'wiki_articles', 'id_contents');

		if (isset($this->contents_column['user_ip']))
			PersistenceContext::get_dbms_utils()->drop_column(PREFIX . 'wiki_contents', 'user_ip');
	}
}
?>
