<?php
/**
 * @copyright   &copy; 2005-2023 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 01 31
 * @since       PHPBoost 6.0 - 2021 03 24
*/

class WikiCategoriesManager extends CategoriesManager
{
	/**
	 * Deletes a category and items.
	 * @param int $id Id of the category to delete.
	 */
	public function delete($id)
	{
		if (!$this->get_categories_cache()->category_exists($id) || $id == Category::ROOT_CATEGORY)
		{
			throw new CategoryNotFoundException($id);
		}
		$result = PersistenceContext::get_querier()->select('SELECT i.id
		FROM ' . WikiSetup::$wiki_articles_table . ' i
		LEFT JOIN ' . WikiSetup::$wiki_contents_table . ' c ON c.item_id = i.id
		WHERE id_category = :id_category AND c.item_id = i.id', array('id_category' => $id));
		while ($row = $result->fetch())
		{
            for($i = 0; $i < count(WikiService::get_item_content($row['id'])); $i++ )
			{
				PersistenceContext::get_querier()->delete(WikiSetup::$wiki_contents_table, 'WHERE item_id = :id', array('id' => $row['id']));
			}
			WikiService::delete($row['id'], 0);
		}
		$result->dispose();

		parent::delete($id);
	}
}
?>
