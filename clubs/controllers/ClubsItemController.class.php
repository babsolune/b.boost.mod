<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ClubsItemController extends ModuleController
{
	private $lang;
	private $common_lang;
	private $view;

	private $item;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();

		$this->init();

		$this->build_view();

		return $this->generate_response();
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'clubs');
		$this->common_lang = LangLoader::get('common-lang');
		$this->view = new FileTemplate('clubs/ClubsItemController.tpl');
		$this->view->add_lang(array_merge($this->lang, $this->common_lang, LangLoader::get('contribution-lang')));
	}

	private function get_item()
	{
		if ($this->item === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->item = ClubsService::get_item('WHERE clubs.id = :id', array('id' => $id));
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
				$this->item = new ClubsItem();
		}
		return $this->item;
	}

	private function build_view()
	{
		$item = $this->get_item();
		$category = $item->get_category();

		$this->build_location_view();
		$this->build_colors_view();

		$this->view->put_all(array_merge($item->get_array_tpl_vars(), array(
			'NOT_VISIBLE_MESSAGE' => MessageHelper::display(LangLoader::get_message('warning.element.not.visible', 'warning-lang'), MessageHelper::WARNING)
		)));
	}

	private function check_authorizations()
	{
		$item = $this->get_item();

		$current_user = AppContext::get_current_user();
		$not_authorized = !CategoriesAuthorizationsService::check_authorizations($item->get_id_category())->moderation() && !CategoriesAuthorizationsService::check_authorizations($item->get_id_category())->write() && (!CategoriesAuthorizationsService::check_authorizations($item->get_id_category())->contribution() || $item->get_author_user()->get_id() != $current_user->get_id());

		switch ($item->get_published()) {
			case ClubsItem::PUBLISHED:
				if (!CategoriesAuthorizationsService::check_authorizations($item->get_id_category())->read())
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			case ClubsItem::NOT_PUBLISHED:
				if ($not_authorized || ($current_user->get_id() == User::VISITOR_LEVEL))
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
					DispatchManager::redirect($error_controller);
				}
			break;
			default:
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			break;
		}
	}

	private function build_location_view()
	{
        $location = $this->item->get_location();

		foreach ($location as $name => $options)
		{
			$this->view->assign_block_vars('location', array(
                'C_STREET_NUMBER' => !empty($options['street_number']),
    			'STREET_NUMBER' => $options['street_number'],
    			'ROUTE' => $options['route'],
    			'CITY' => $options['city'],
    			'POSTAL_CODE' => $options['postal_code'],
			));
		}
	}

	private function build_colors_view()
	{
		$colors = $this->item->get_colors();
		$colors_number = count($colors);
        $this->view->put('C_COLORS', $colors_number > 0);

		$i = 1;
		foreach ($colors as $name => $color)
		{
			$this->view->assign_block_vars('colors', array(
				'C_SEPARATOR' => $i < $colors_number,
				'NAME' => $name,
				'COLOR' => $color,
			));
			$i++;
		}
	}

	private function generate_response()
	{
		$item = $this->get_item();
		$category = $item->get_category();
		$response = new SiteDisplayResponse($this->view);

		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($item->get_title(), $this->lang['clubs.module.title']);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(ClubsUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()));

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['clubs.module.title'],ClubsUrlBuilder::home());

		$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($item->get_id_category(), true));
		foreach ($categories as $id => $category)
		{
			if ($category->get_id() != Category::ROOT_CATEGORY)
				$breadcrumb->add($category->get_name(), ClubsUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
		}
		$breadcrumb->add($item->get_title(), ClubsUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()));

		return $response;
	}
}
?>
