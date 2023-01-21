<?php
/**
 * @copyright   &copy; 2005-2022 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 01 09
 * @since       PHPBoost 6.0 - 2022 11 18
 */

class DocumentationService
{
	private static $db_querier;
	protected static $module_id = 'documentation';

	public static function __static()
	{
		self::$db_querier = PersistenceContext::get_querier();
	}

	/**
	 * @desc Count items number.
	 * @param string $condition (optional) : Restriction to apply to the list of items
	 */
	public static function count($condition = '', $parameters = array())
	{
		return self::$db_querier->count(DocumentationSetup::$documentation_items_table, $condition, $parameters);
	}

	/**
	 * @desc Create a new entry in the database table.
	 * @param string[] $item : new DocumentationItem
	 */
	public static function add(DocumentationItem $item)
	{
		$result = self::$db_querier->insert(DocumentationSetup::$documentation_items_table, $item->get_properties());

		return $result->get_last_inserted_id();
	}

	public static function get_last_content_id()
	{
		$result = self::$db_querier->select_single_row_query('SELECT MAX(content_id) FROM ' . DocumentationSetup::$documentation_contents_table);
		return $result;
	}

	/**
	 * @desc Create a new item content.
	 * @param string[] $content new DocumentationItemContent
	 */
	public static function add_content(DocumentationItemContent $content)
	{
		$result = self::$db_querier->insert(DocumentationSetup::$documentation_contents_table, $content->get_properties());

		return $result->get_last_inserted_id();
	}

	/**
	 * @desc Update an entry.
	 * @param string[] $item : DocumentationItem to update
	 */
	public static function update(DocumentationItem $item)
	{
		self::$db_querier->update(DocumentationSetup::$documentation_items_table, $item->get_properties(), 'WHERE id=:id', array('id' => $item->get_id()));
	}

	/**
	 * @desc Update an entry.
	 * @param string[] $item : DocumentationItem to update
	 */
	public static function update_content(DocumentationItemContent $item_content)
	{
		self::$db_querier->update(DocumentationSetup::$documentation_contents_table, $item_content->get_properties(), 'WHERE content_id=:id', array('id' => $item_content->get_content_id()));
	}

	/**
	 * @desc Update the position of an item.
	 * @param string[] $id : id of the item to update
	 * @param string[] $position : new item position
	 */
	public static function update_position($id, $position)
	{
		self::$db_querier->update(DocumentationSetup::$documentation_items_table, array('i_order' => $position), 'WHERE id=:id', array('id' => $id));
	}

	public static function update_views_number(DocumentationItem $item)
	{
		self::$db_querier->update(DocumentationSetup::$documentation_items_table, array('views_number' => $item->get_views_number()), 'WHERE id=:id', array('id' => $item->get_id()));
	}

	/**
	 * @desc Delete an entry with all ite contents.
	 * @param string $condition : Restriction to apply to the list
	 * @param string[] $parameters : Parameters of the condition
	 */
	public static function delete(int $id, $content_id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
		
		if ($content_id == 0)
			self::$db_querier->delete(DocumentationSetup::$documentation_items_table, 'WHERE id=:id', array('id' => $id));
		else
			self::$db_querier->delete(DocumentationSetup::$documentation_contents_table, 'WHERE item_id=:id AND content_id = :content_id', array('id' => $id, 'content_id' => $content_id));

		self::$db_querier->delete(DB_TABLE_EVENTS, 'WHERE module=:module AND id_in_module=:id', array('module' => 'documentation', 'id' => $id));

		CommentsService::delete_comments_topic_module('documentation', $id);
		KeywordsService::get_keywords_manager()->delete_relations($id);
		NotationService::delete_notes_id_in_module('documentation', $id);
	}

	/**
	 * @desc Restore a content of an entry.
	 * @param string $condition : Restriction to apply to the list
	 * @param string[] $parameters : Parameters of the condition
	 */
	public static function restore_content($id, $content_id)
	{
		if (AppContext::get_current_user()->is_readonly())
        {
            $controller = PHPBoostErrors::user_in_read_only();
            DispatchManager::redirect($controller);
        }
		self::$db_querier->update(DocumentationSetup::$documentation_contents_table, array('active_content' => '1'), 'WHERE item_id = :id AND content_id = :content_id', array('id' => $id, 'content_id' => $content_id));
	}

	/**
	 * @desc Return the item with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_item(int $id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT i.*, c.*, member.*, f.id AS fav_id, notes.average_notes, notes.notes_number, note.note
		FROM ' . DocumentationSetup::$documentation_items_table .' i
		LEFT JOIN ' . DocumentationSetup::$documentation_contents_table . ' c ON c.item_id = i.id
		LEFT JOIN ' . DocumentationSetup::$documentation_favs_table . ' f ON f.item_id = i.id
		LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = c.author_user_id
		LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = i.id AND notes.module_name = :module_id
		LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = i.id AND note.module_name = :module_id AND note.user_id = :current_user_id
		WHERE i.id = :id AND i.id = c.item_id AND c.active_content = 1', array(
			'module_id'       => self::$module_id,
			'id'              => $id,
			'current_user_id' => AppContext::get_current_user()->get_id()
		));

		$item = new DocumentationItem();
		$item->set_properties($row);
		return $item;
	}

	public static function get_item_content($item_id)
	{
		$content_items = array();

		$result = self::$db_querier->select('SELECT *
		FROM ' . DocumentationSetup::$documentation_items_table .' i
		LEFT JOIN ' . DocumentationSetup::$documentation_contents_table . ' c ON c.item_id = i.id
		LEFT JOIN ' . DocumentationSetup::$documentation_favs_table . ' f ON f.item_id = i.id
		LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = c.author_user_id
		LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = i.id AND notes.module_name = :module_id
		LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = i.id AND note.module_name = :module_id AND note.user_id = :current_user_id
		WHERE c.item_id = :id', array(
			'module_id'       => self::$module_id,
			'id'              => $item_id,
			'current_user_id' => AppContext::get_current_user()->get_id()
		));

		while ($row = $result->fetch()) {
			$content_item = new DocumentationItemContent();
			$content_item->set_properties($row);
			$content_items[$content_item->get_content_id()] = $content_item;
		}
		$result->dispose();

		return $content_items;
	}

	/**
	 * @desc Return the item with all its properties from its id.
	 * @param int $id Item identifier
	 */
	public static function get_item_archive(int $id, int $content_id)
	{
		$row = self::$db_querier->select_single_row_query('SELECT i.*, c.*, member.*, f.id AS fav_id, notes.average_notes, notes.notes_number, note.note
		FROM ' . DocumentationSetup::$documentation_items_table .' i
		LEFT JOIN ' . DocumentationSetup::$documentation_contents_table . ' c ON c.item_id = i.id
		LEFT JOIN ' . DocumentationSetup::$documentation_favs_table . ' f ON f.item_id = i.id
		LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON member.user_id = c.author_user_id
		LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = i.id AND notes.module_name = :module_id
		LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = i.id AND note.module_name = :module_id AND note.user_id = :current_user_id
		WHERE i.id = :id AND i.id = c.item_id AND c.content_id = :content_id', array(
			'module_id'       => self::$module_id,
			'id'              => $id,
			'content_id'      => $content_id,
			'current_user_id' => AppContext::get_current_user()->get_id()
		));

		$item = new DocumentationItem();
		$item->set_properties($row);
		return $item;
	}

	public static function clear_cache()
	{
		Feed::clear_cache('documentation');
		KeywordsCache::invalidate();
		DocumentationCache::invalidate();
        CategoriesService::get_categories_manager()->regenerate_cache();
	}
}
?>
