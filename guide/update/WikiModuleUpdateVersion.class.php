<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 03 30
 * @since       PHPBoost 6.0 - 2023 03 30
 */

class WikiModuleUpdateVersion extends ModuleUpdateVersion
{
	public function __construct()
	{
		parent::__construct('wiki');

		$this->content_tables = array(array('name' => PREFIX . 'wiki', 'content_field' => 'content'));

		self::$delete_old_files_list = array(
            '/phpboost/WikiCategoriesCache.class.php',
            '/phpboost/WikiHomePageExtensionPoint.class.php',
            '/phpboost/WikiSitemapExtensionPoint.class.php',
            '/templates/js/bbcode.wiki.js',
            '/templates/js/bbcode.wiki.min.js',
            '/templates/admin_wiki.tpl',
            '/templates/admin_wiki_groups.tpl',
            '/templates/explorer.tpl',
            '/templates/favorites.tpl',
            '/templates/history.tpl',
            '/templates/index.tpl',
            '/templates/post.tpl',
            '/templates/property.tpl',
            '/templates/wiki.tpl',
            '/templates/wiki_js_tool.tpl',
            '/templates/wiki_search_form.tpl',
            '/templates/wiki_tools.tpl',
			'/action.php',
			'/admin_wiki_groups.php',
			'/admin_wiki.php',
			'/explorer.php',
			'/favorites.php',
			'/history.php',
			'/post_js_tool.php',
			'/post.php',
			'/print.php',
			'/property.php',
			'/wiki_auth.php',
			'/wiki_bread_crumb.php',
			'/wiki_functions.php',
			'/wiki_tools.php',
			'/wiki.php',
			'/xmlhttprequest.php',
		);

		$this->database_columns_to_modify = array(
			array(
				'table_name' => PREFIX . 'wiki_articles',
				'columns' => array(
					'id_cats'       => 'id_category INT(11) NOT NULL DEFAULT 0',
					'encoded_title' => 'rewrited_title VARCHAR(255) NOT NULL DEFAULT ""',
					'hits'          => 'views_number INT(11) NOT NULL DEFAULT 0'
				)
			),
			array(
				'table_name' => PREFIX . 'wiki_contents',
				'columns' => array(
					'id_contents' => 'content_id INT(11) AUTO_INCREMENT NOT NULL DEFAULT 0',
					'id_article'  => 'item_id INT(11) NOT NULL DEFAULT 0',
					'timestamp'   => 'update_date INT(11) NOT NULL DEFAULT 0',
					'activ'       => 'active_content INT(11) NOT NULL DEFAULT 0',
					'user_id'     => 'author_user_id INT(11) NOT NULL DEFAULT 0'
				)
            ),
			array(
				'table_name' => PREFIX . 'wiki_favorites',
				'columns' => array(
					'id'         => 'track_id INT(11) AUTO_INCREMENT NOT NULL DEFAULT 0',
					'id_article' => 'track_item_id INT(11) NOT NULL DEFAULT 0',
					'user_id'    => 'track_user_id INT(11) NOT NULL DEFAULT 0'
				)
			)
		);

		$this->database_columns_to_add = array(
			array(
				'table_name' => PREFIX . 'wiki_articles',
				'columns' => array(
                    'i_order'               => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
                    'creation_date'         => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
                    'published'             => array('type' => 'integer', 'length' => 1, 'notnull' => 1, 'default' => 0),
                    'publishing_start_date' => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
                    'publishing_end_date'   => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0)
                )
            ),
			array(
				'table_name' => PREFIX . 'wiki_contents',
				'columns' => array(
					'update_date'   => array('type' => 'integer', 'length' => 11, 'notnull' => 1, 'default' => 0),
					'thumbnail'     => array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''"),
                    'custom_level'  => array('type' => 'text', 'length' => 65000),
					'sources'       => array('type' => 'text', 'length' => 65000)
                )
			)
		);

        // TODO : modify keys/indexes

		$this->database_columns_to_delete = array(
			array(
				'table_name' => PREFIX . 'wiki_articles',
				'columns' => array('id_contents', 'title', 'is_cat', 'undefined_status', 'redirect', 'auth')
            ),
			array(
				'table_name' => PREFIX . 'wiki_contents',
				'columns' => array('menu', 'user_ip')
			)
		);
	}

	protected function execute_module_specific_changes()
	{
        // Set categories from old `ìs_cat` sheet without content
			$result = $this->querier->select('SELECT i.id, c.title, i.rewrited_title, i.auth, i.is_cat, c.content, cat.id as cat_id
                FROM ' . PREFIX . 'wiki_articles i
                LEFT JOIN ' . PREFIX . 'wiki_contents c ON c.id_article = i.id
                LEFT JOIN ' . PREFIX . 'wiki_cats cat ON cat.id_article = i.id
                WHERE i.is_cat = 1
                AND c.active_content = 1'
            );

            while ($row = $result->fetch())
            {
                $this->querier->update(PREFIX . 'wiki_cats', array('name' => $row['title'], 'rewrited_name' => $row['rewrited_title'], 'auth' => $row['auth']), 'WHERE id = :id', array('id' => $row['cat_id']));
            }
            $result->dispose();

        // Change location of sheet title
            $result = $this->querier->select('SELECT i.id, i.title
                FROM ' . PREFIX . 'wiki_articles i
                LEFT JOIN ' . PREFIX . 'wiki_contents c ON c.item_id = i.id
                WHERE i.id = c.item_id'
            );

            while ($row = $result->fetch())
            {
                $this->querier->update(PREFIX . 'wiki_contents', array('title' => $row['title']), 'WHERE item_id = :item_id', array('item_id' => $row['id']));
            }
            $result->dispose();

        // TODO change `-- `title` --` to [title=1]title[/title]
        // TODO change `--- `title` ---` to [title=2]title[/title]
        // TODO change `---- `title` ----` to [title=3]title[/title]
        // TODO change `----- `title` -----` to [title=4]title[/title]
        // TODO change `------ `title` ------` to [title=5]title[/title]
    }
}
?>
