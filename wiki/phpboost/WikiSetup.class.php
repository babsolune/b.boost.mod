<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 03 27
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class WikiSetup extends DefaultModuleSetup
{
	public static $wiki_articles_table;
	public static $wiki_cats_table;
	public static $wiki_contents_table;
	public static $wiki_favorites_table;

	/**
	 * @var string[string] localized messages
	 */
	private $messages;
	private $querier;

	private $articles_column;
	private $cats_column;
	private $contents_column;
	private $favorites_column;

	public static function __static()
	{
		self::$wiki_articles_table = PREFIX . 'wiki_articles';
		self::$wiki_cats_table = PREFIX . 'wiki_cats';
		self::$wiki_contents_table = PREFIX . 'wiki_contents';
		self::$wiki_favorites_table = PREFIX . 'wiki_favorites';
	}

    public function upgrade($installed_version)
    {
		$this->querier = PersistenceContext::get_querier();
		$this->articles_column = PersistenceContext::get_dbms_utils()->desc_table(self::$wiki_articles_table);
		$this->cats_column = PersistenceContext::get_dbms_utils()->desc_table(self::$wiki_cats_table);
		$this->contents_column = PersistenceContext::get_dbms_utils()->desc_table(self::$wiki_contents_table);
		$this->favorites_column = PersistenceContext::get_dbms_utils()->desc_table(self::$wiki_favorites_table);

        $this->disable_mini_menu();
		$this->delete_files();

		$this->modify_fields();
		$this->add_fields();

		$this->modify_content();

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
		ConfigManager::delete('wiki', 'config');
		CacheManager::invalidate('module', 'wiki');
		KeywordsService::get_keywords_manager()->delete_module_relations();
	}

	private function drop_tables()
	{
		PersistenceContext::get_dbms_utils()->drop(array(
			self::$wiki_articles_table,
			self::$wiki_cats_table,
			self::$wiki_contents_table,
			self::$wiki_favorites_table
		));
	}

	private function create_tables()
	{
		$this->create_wiki_articles_table();
		$this->create_wiki_cats_table();
		$this->create_wiki_contents_table();
		$this->create_wiki_favorites_table();
	}

	private function create_wiki_articles_table()
	{
		$fields = array(
			'id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'id_category' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'title' => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
			'rewrited_title' => array('type' => 'string', 'length' => 255, 'default' => "''"),
			'i_order' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'published' => array('type' => 'integer', 'length' => 1, 'notnull' => 1, 'default' => 0),
			'publishing_start_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'publishing_end_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'creation_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'views_number' => array('type' => 'integer', 'length' => 11, 'default' => 0),
		);
		$options = array(
			'primary' => array('id'),
			'indexes' => array(
				'title' => array('type' => 'fulltext', 'fields' => 'title'),
				'id_category' => array('type' => 'key', 'fields' => 'id_category'),
				'i_order' => array('type' => 'key', 'fields' => 'i_order'),
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$wiki_articles_table, $fields, $options);
	}

	private function create_wiki_contents_table()
	{
		$fields = array(
			'content_id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'item_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
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
				'item_id' => array('type' => 'key', 'fields' => 'item_id'),
				'content' => array('type' => 'fulltext', 'fields' => 'content'),
			)
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$wiki_contents_table, $fields, $options);
	}

	private function create_wiki_cats_table()
	{
		RichCategory::create_categories_table(self::$wiki_cats_table);
	}

	private function create_wiki_favorites_table()
	{
		$fields = array(
			'track_id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'notnull' => 1),
			'track_item_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
			'track_user_id' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0)
		);
		$options = array(
			'primary' => array('track_id')
		);
		PersistenceContext::get_dbms_utils()->create_table(self::$wiki_favorites_table, $fields, $options);
	}

	private function insert_data()
	{
		$this->messages = LangLoader::get('install', 'wiki');
		$this->insert_wiki_cats_data();
		$this->insert_wiki_data();
		$this->insert_wiki_contents_data();
	}

	private function insert_wiki_cats_data()
	{
		PersistenceContext::get_querier()->insert(self::$wiki_cats_table, array(
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

	private function insert_wiki_data()
	{
		PersistenceContext::get_querier()->insert(self::$wiki_articles_table, array(
			'id'               		=> 1,
			'id_category'           => 1,
			'title'                 => $this->messages['default.sheet.name'],
			'rewrited_title'        => Url::encode_rewrite($this->messages['default.sheet.name']),
			'i_order'               => 1,
			'published'             => WikiItem::PUBLISHED,
			'publishing_start_date' => 0,
			'publishing_end_date'   => 0,
			'creation_date'  	    => time(),
			'views_number'          => 0
		));
	}

	private function insert_wiki_contents_data()
	{
		PersistenceContext::get_querier()->insert(self::$wiki_contents_table, array(
			'content_id'     	    => 1,
			'item_id'        	    => 1,
			'active_content'        => 1,
			'summary'        	    => '',
			'author_custom_name'    => '',
			'thumbnail'             => FormFieldThumbnail::DEFAULT_VALUE,
			'content'        	    => $this->messages['default.sheet.content'],
			'content_level'    	    => WikiItemContent::NO_LEVEL,
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
		$file = new File(PATH_TO_ROOT . '/wiki/templates/wiki_js_tools.tpl');                   $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/wiki_search_form.tpl');                $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/templates/wiki_tools.tpl');                      $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/action.php');                                    $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/admin_wiki_groups.php');                         $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/admin_wiki.php');                                $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/explorer.php');                                  $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/favorites.php');                                 $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/history.php');                                   $file->delete();
		$file = new File(PATH_TO_ROOT . '/wiki/post_js_tools.php');                             $file->delete();
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

    private function disable_mini_menu()
	{
		$wikitreemenu_id = 0;
		try {
			$wikitreemenu_id = PersistenceContext::get_querier()->get_column_value(DB_TABLE_MENUS, 'id', 'WHERE title = "wikitree/WikitreeModuleMiniMenu"');
		} catch (RowNotFoundException $e) {}
		if ($wikitreemenu_id)
        {
			$menu = MenuService::load($wikitreemenu_id);
			MenuService::delete($menu);
			MenuService::generate_cache();
		}
		$wikistatusmenu_id = 0;
		try {
			$wikistatusmenu_id = PersistenceContext::get_querier()->get_column_value(DB_TABLE_MENUS, 'id', 'WHERE title = "wikistatus/WikiStatusModuleMiniMenu"');
		} catch (RowNotFoundException $e) {}
		if ($wikistatusmenu_id)
        {
			$menu = MenuService::load($wikistatusmenu_id);
			MenuService::delete($menu);
			MenuService::generate_cache();
		}
	}

	private function modify_fields()
	{
		$articles_change = array(
            'id_cat'        => 'id_category INT(11) NOT NULL DEFAULT 0',
            'encoded_title' => 'rewrited_title VARCHAR(255) NOT NULL DEFAULT ""',
            'hits'          => 'views_number INT(11) NOT NULL DEFAULT 0'
		);
		foreach ($articles_change as $old_name => $new_name)
		{
			if (isset($this->articles_column[$old_name]))
				PersistenceContext::get_querier()->inject('ALTER TABLE ' . PREFIX . 'wiki_articles CHANGE ' . $old_name . ' ' . $new_name);
		}

		$contents_change = array(
            'id_contents' => 'content_id INT(11) AUTO_INCREMENT NOT NULL',
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
            'id'         => 'track_id INT(11) AUTO_INCREMENT NOT NULL',
            'id_article' => 'track_item_id INT(11) NOT NULL DEFAULT 0',
            'user_id'    => 'track_user_id INT(11) NOT NULL DEFAULT 0'
		);
		foreach ($favorites_change as $old_name => $new_name)
		{
			if (isset($this->favorites_column[$old_name]))
				PersistenceContext::get_querier()->inject('ALTER TABLE ' . PREFIX . 'wiki_favorites CHANGE ' . $old_name . ' ' . $new_name);
		}
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

        if (!isset($this->articles_column['id_category']['key']) || !$this->articles_column['id_category']['key'])
            PersistenceContext::get_querier()->inject('ALTER TABLE ' . PREFIX . 'wiki_articles ADD KEY `id_category` (`id_category`)');
        if (!isset($this->articles_column['i_order']['key']) || !$this->articles_column['i_order']['key'])
            PersistenceContext::get_querier()->inject('ALTER TABLE ' . PREFIX . 'wiki_articles ADD KEY `i_order` (`i_order`)');

        if (!isset($this->contents_column['thumbnail']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_contents', 'thumbnail', array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"));
        if (!isset($this->contents_column['custom_level']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_contents', 'custom_level', array('type' => 'string', 'length' => 65000));

        if (!isset($this->contents_column['sources']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_contents', 'sources', array('type' => 'string', 'length' => 65000));

        if (!isset($this->contents_column['item_id']['key']) || !$this->contents_column['item_id']['key'])
            PersistenceContext::get_querier()->inject('ALTER TABLE ' . PREFIX . 'wiki_contents ADD KEY `item_id` (`item_id`)');

        if (!isset($this->cats_column['name']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_cats', 'name', array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"));
        if (!isset($this->cats_column['rewrited_name']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_cats', 'rewrited_name', array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"));
        if (!isset($this->cats_column['thumbnail']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_cats', 'thumbnail', array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"));
        if (!isset($this->cats_column['description']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_cats', 'description', array('type' => 'string', 'length' => 65000));
        if (!isset($this->cats_column['special_authorizations']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_cats', 'special_authorizations', array('type' => 'string', 'length' => 65000));
        if (!isset($this->cats_column['auth']))
			PersistenceContext::get_dbms_utils()->add_column(PREFIX . 'wiki_cats', 'auth', array('type' => 'string', 'length' => 65000));
    }

	private function modify_content()
	{
        // Set categories `name`, `rewrited_name`, `auth` from old `ìs_cat`
        // Set content from old article
			$result = $this->querier->select('SELECT i.id, i.title, i.rewrited_title, i.auth, i.is_cat, i.defined_status, cat.id as cat_id
            FROM ' . PREFIX . 'wiki_articles i
            LEFT JOIN ' . PREFIX . 'wiki_cats cat ON cat.article_id = i.id
            LEFT JOIN ' . PREFIX . 'wiki_contents c ON c.item_id = i.id
            WHERE i.is_cat = 1'
        );

        while ($row = $result->fetch())
        {
            $this->querier->update(PREFIX . 'wiki_cats', array('name' => $row['title'], 'rewrited_name' => $row['rewrited_title'], 'auth' => $row['auth']), 'WHERE id = :id', array('id' => $row['cat_id']));
            $this->querier->update(PREFIX . 'wiki_contents', array('title' => $row['title'], 'custom_level' => $row['defined_status']), 'WHERE item_id = :id', array('id' => $row['id']));
        }
        $result->dispose();

        // TODO change `-- title --` to `[title=1]title[/title]`
        // TODO change `--- title ---` to `[title=2]title[/title]`
        // TODO change `---- title ----` to `[title=3]title[/title]`
        // TODO change `----- title -----` to `[title=4]title[/title]`
        // TODO change `------ title ------` to `[title=5]title[/title]`
    }

	private function delete_fields()
	{
		if (isset($this->articles_column['id_contents']))
			PersistenceContext::get_dbms_utils()->drop_column(PREFIX . 'wiki_articles', 'id_contents');
		if (isset($this->articles_column['is_cat']))
			PersistenceContext::get_dbms_utils()->drop_column(PREFIX . 'wiki_articles', 'is_cat');
		if (isset($this->articles_column['undefined_status']))
			PersistenceContext::get_dbms_utils()->drop_column(PREFIX . 'wiki_articles', 'undefined_status');
		if (isset($this->articles_column['redirect']))
			PersistenceContext::get_dbms_utils()->drop_column(PREFIX . 'wiki_articles', 'redirect');
		if (isset($this->articles_column['auth']))
			PersistenceContext::get_dbms_utils()->drop_column(PREFIX . 'wiki_articles', 'auth');

		if (isset($this->contents_column['menu']))
			PersistenceContext::get_dbms_utils()->drop_column(PREFIX . 'wiki_contents', 'menu');
		if (isset($this->contents_column['user_ip']))
			PersistenceContext::get_dbms_utils()->drop_column(PREFIX . 'wiki_contents', 'user_ip');

		if (isset($this->cats_column['article_id']))
			PersistenceContext::get_dbms_utils()->drop_column(PREFIX . 'wiki_cats', 'article_id');
	}
}
?>
