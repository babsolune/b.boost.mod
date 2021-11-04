<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ClubsItemFormController extends ModuleController
{
	/**
	 * @var HTMLForm
	 */
	private $form;
	/**
	 * @var FormButtonSubmit
	 */
	private $submit_button;

	private $lang;
	private $form_lang;
    private $config;

	private $item;
	private $is_new_item;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		$this->check_authorizations();

		$this->build_form($request);

		$view = new StringTemplate('# INCLUDE FORM #');

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->redirect();
		}

		$view->put('FORM', $this->form->display());

		return $this->build_response($view);
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'clubs');
		$this->form_lang = LangLoader::get('form-lang');
        $this->config = ClubsConfig::load();
	}

	private function build_form(HTTPRequestCustom $request)
	{
		$form = new HTMLForm(__CLASS__);
		$form->set_layout_title($this->get_item()->get_id() === null ? $this->lang['clubs.add'] : ($this->lang['clubs.edit']));

		$fieldset = new FormFieldsetHTML('clubs', $this->form_lang['form.parameters']);
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldTextEditor('title', $this->form_lang['form.name'], $this->get_item()->get_title(),
			array('required' => true)
		));

		if(ModulesManager::is_module_installed('scm') && ModulesManager::is_module_activated('scm'))
		{
			$fieldset->add_field(new FormFieldTextEditor('short_title', $this->lang['clubs.short.title'], $this->get_item()->get_short_title(),
				array(
					'required' => true,
					'description' => $this->lang['clubs.short.title.clue']
				)
			));
		}

		if (CategoriesService::get_categories_manager()->get_categories_cache()->has_categories())
		{
			$search_category_children_options = new SearchCategoryChildrensOptions();
			$search_category_children_options->add_authorizations_bits(Category::CONTRIBUTION_AUTHORIZATIONS);
			$search_category_children_options->add_authorizations_bits(Category::WRITE_AUTHORIZATIONS);
			$fieldset->add_field(CategoriesService::get_categories_manager()->get_select_categories_form_field('id_category', $this->form_lang['form.category'], $this->get_item()->get_id_category(), $search_category_children_options));
		}

        $fieldset->add_field(new FormFieldUploadPictureFile('logo', $this->lang['clubs.logo'], $this->get_item()->get_logo()->relative()));

        $fieldset->add_field(new FormFieldUploadPictureFile('logo_mini', $this->lang['clubs.logo.mini'], $this->get_item()->get_logo_mini()->relative(),
			array('description' => $this->lang['clubs.logo.mini.desc'])
		));

		$fieldset->add_field(new FormFieldCheckbox('colors_enabled', $this->lang['clubs.add.colors'], $this->get_item()->has_colors(),
			array(
				'events' => array('click' => '
					if (HTMLForms.getField("colors_enabled").getValue()) {
						HTMLForms.getField("colors").enable();
					} else {
						HTMLForms.getField("colors").disable();
					}'
				)
			)
		));

        $fieldset->add_field(new ClubsFormFieldColors('colors', $this->lang['clubs.colors'], $this->get_item()->get_colors(),
			array(
				'description' => $this->lang['clubs.colors.desc'],
				'hidden' => ($request->is_post_method() ? !$request->get_postbool(__CLASS__ . '_colors_enabled', false) : !$this->get_item()->has_colors())
			)
		));

		$fieldset->add_field(new FormFieldUrlEditor('website_url', $this->lang['clubs.website.url'], $this->get_item()->get_website_url()->absolute()));

        $fieldset->add_field(new FormFieldTelEditor('phone', $this->lang['clubs.labels.phone'], $this->get_item()->get_phone()));

        $fieldset->add_field(new FormFieldMailEditor('club_email', $this->lang['clubs.labels.email'], $this->get_item()->get_club_email()));

		$unserialized_value = @unserialize($this->item->get_location());
		$location_value = $unserialized_value !== false ? $unserialized_value : $this->item->get_location();

		$location = $street_number = $route = $postal_code = $city = $department = $state = $country = '';
		if (is_array($location_value) && (isset($location_value['address']) || isset($location_value['city'])))
        {
            if (isset($location_value['address']))
            {
                $location = $location_value['address'];
            }
			elseif (isset($location_value['city']))
            {
                $location = $location_value['street_number'] . $location_value['city'];
                $street_number = $location_value['street_number'];
                $route = $location_value['route'];
                $postal_code = $location_value['postal_code'];
                $city = $location_value['city'];
                $department = $location_value['department'];
                $state = $location_value['state'];
                $country = $location_value['country'];
            }
        }
		// else if (!is_array($location_value))
		// 	$location = $location_value;

        if(ClubsService::is_gmap_enabled()) {
            $fieldset->add_field(new ClubsFormFieldLocation('location', $this->lang['clubs.headquarter.address'], $this->item->get_location(),
				array('description' => $this->lang['clubs.headquarter.address.clue'])
			));
            // $fieldset->add_field(new LeafletFormFieldCompleteAddress('location', $this->lang['clubs.headquarter.address'], $location, array(
            //     'description' => $this->lang['clubs.headquarter.address.clue']
            // )));

            $fieldset->add_field(new GoogleMapsFormFieldMapAddress('gps', $this->lang['clubs.stadium.location'], new GoogleMapsMarker($this->get_item()->get_stadium_address(), $this->get_item()->get_stadium_latitude(), $this->get_item()->get_stadium_longitude()),
    			array('description' => $this->lang['clubs.stadium.location.desc'], 'always_display_marker' => true)
    		));
        } else {
            $fieldset->add_field(new FormFieldFree('location', $this->lang['clubs.headquarter.address'], $this->lang['clubs.no.gmap']));
            $fieldset->add_field(new FormFieldFree('stadium_location', $this->lang['clubs.stadium.location'], $this->lang['clubs.no.gmap']));
        }

		$fieldset->add_field(new FormFieldRichTextEditor('content', $this->form_lang['form.description'], $this->get_item()->get_content()));

        $social_fieldset = new FormFieldsetHTML('social_network', $this->lang['clubs.social.network']);
        $form->add_fieldset($social_fieldset);

        $social_fieldset->add_field(new FormFieldUrlEditor('facebook', $this->lang['clubs.labels.facebook'], $this->get_item()->get_facebook()->absolute(),
			array('placeholder' => $this->lang['clubs.placeholder.facebook'])
		));

        $social_fieldset->add_field(new FormFieldUrlEditor('twitter', $this->lang['clubs.labels.twitter'], $this->get_item()->get_twitter()->absolute(),
			array('placeholder' => $this->lang['clubs.placeholder.twitter'])
		));

        $social_fieldset->add_field(new FormFieldUrlEditor('instagram', $this->lang['clubs.labels.instagram'], $this->get_item()->get_instagram()->absolute(),
			array('placeholder' => $this->lang['clubs.placeholder.instagram'])
		));

        $social_fieldset->add_field(new FormFieldUrlEditor('youtube', $this->lang['clubs.labels.youtube'], $this->get_item()->get_youtube()->absolute(),
			array('placeholder' => $this->lang['clubs.placeholder.youtube'])
		));

        if (CategoriesAuthorizationsService::check_authorizations($this->get_item()->get_id_category())->moderation())
		{
			$publication_fieldset = new FormFieldsetHTML('publication', $this->form_lang['form.publication']);
			$form->add_fieldset($publication_fieldset);

			$publication_fieldset->add_field(new FormFieldDateTime('creation_date', $this->form_lang['form.creation.date'], $this->get_item()->get_creation_date(),
				array('required' => true)
			));

			if (!$this->get_item()->is_published())
			{
				$publication_fieldset->add_field(new FormFieldCheckbox('update_creation_date', $this->form_lang['form.update.creation.date'], false,
					array('hidden' => $this->get_item()->get_status() != ClubsItem::NOT_PUBLISHED)
				));
			}

			$publication_fieldset->add_field(new FormFieldSimpleSelectChoice('published', $this->form_lang['form.publication'], $this->get_item()->get_published(),
				array(
					new FormFieldSelectChoiceOption($this->form_lang['form.publication.draft'], ClubsItem::NOT_PUBLISHED),
					new FormFieldSelectChoiceOption($this->form_lang['form.publication.now'], ClubsItem::PUBLISHED),
				)
			));
		}

		$this->build_contribution_fieldset($form);

		$fieldset->add_field(new FormFieldHidden('referrer', $request->get_url_referrer()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function build_contribution_fieldset($form)
	{
		$contribution = LangLoader::get('contribution-lang');
		if ($this->get_item()->get_id() === null && $this->is_contributor_member())
		{
			$fieldset = new FormFieldsetHTML('contribution', $contribution['contribution.contribution']);
			$fieldset->set_description(MessageHelper::display($contribution['contribution.extended.warning'], MessageHelper::WARNING)->render());
			$form->add_fieldset($fieldset);

			$fieldset->add_field(new FormFieldRichTextEditor('contribution_description', $contribution['contribution.description'], '',
				array('description' => $contribution['contribution.description.clue'])
			));
		}
		elseif ($this->get_item()->is_published() && $this->get_item()->is_authorized_to_edit() && !AppContext::get_current_user()->check_level(User::ADMINISTRATOR_LEVEL))
		{
			$fieldset = new FormFieldsetHTML('member_edition', $contribution['contribution.member.edition']);
			$fieldset->set_description(MessageHelper::display($contribution['contribution.edition.warning'], MessageHelper::WARNING)->render());
			$form->add_fieldset($fieldset);

			$fieldset->add_field(new FormFieldRichTextEditor('edition_description', $contribution['contribution.edition.description'], '',
				array('description' => $contribution['contribution.edition.description.clue'])
			));
		}
	}

	private function is_contributor_member()
	{
		return (!CategoriesAuthorizationsService::check_authorizations()->write() && CategoriesAuthorizationsService::check_authorizations()->contribution());
	}

	private function get_item()
	{
		if ($this->item === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->item = ClubsService::get_item('WHERE clubs.id=:id', array('id' => $id));
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_item = true;
				$this->item = new ClubsItem();
				$this->item->init_default_properties(AppContext::get_request()->get_getint('id_category', Category::ROOT_CATEGORY));
			}
		}
		return $this->item;
	}

	private function check_authorizations()
	{
		$item = $this->get_item();

		if ($item->get_id() === null)
		{
			if (!$item->is_authorized_to_add())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!$item->is_authorized_to_edit())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		if (AppContext::get_current_user()->is_readonly())
		{
			$controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($controller);
		}
	}

	private function save()
	{
		$item = $this->get_item();

		$item->set_title($this->form->get_value('title'));
		if(ModulesManager::is_module_installed('scm') && ModulesManager::is_module_activated('scm'))
			$item->set_short_title($this->form->get_value('short_title'));
		$item->set_rewrited_title(Url::encode_rewrite($item->get_title()));

		if (CategoriesService::get_categories_manager()->get_categories_cache()->has_categories())
			$item->set_id_category($this->form->get_value('id_category')->get_raw_value());

		$item->set_website_url(new Url($this->form->get_value('website_url')));
		$item->set_logo(new Url($this->form->get_value('logo')));
		$item->set_logo_mini(new Url($this->form->get_value('logo_mini')));
		if($this->form->get_value('colors_enabled'))
		{
        	$item->set_colors_enabled($this->form->get_value('colors_enabled'));
        	$item->set_colors($this->form->get_value('colors'));
		}
		else
			$item->set_colors_enabled(false);

		$item->set_phone($this->form->get_value('phone'));
		$item->set_club_email($this->form->get_value('club_email'));
		$item->set_facebook(new Url($this->form->get_value('facebook')));
		$item->set_twitter(new Url($this->form->get_value('twitter')));
		$item->set_instagram(new Url($this->form->get_value('instagram')));
		$item->set_youtube(new Url($this->form->get_value('youtube')));
		$item->set_content($this->form->get_value('content'));

        $item->set_location($this->form->get_value('location'));

        if(ClubsService::is_gmap_enabled()) {
            $stadium = new GoogleMapsMarker();
			$stadium->set_properties(TextHelper::unserialize($this->form->get_value('gps')));

			$item->set_stadium_address($stadium->get_address());
			$item->set_stadium_latitude($stadium->get_latitude());
			$item->set_stadium_longitude($stadium->get_longitude());
        }

		if (!CategoriesAuthorizationsService::check_authorizations($item->get_id_category())->moderation())
		{
			if ($item->get_id() === null )
				$item->set_creation_date(new Date());

			if (CategoriesAuthorizationsService::check_authorizations($item->get_id_category())->contribution() && !CategoriesAuthorizationsService::check_authorizations($item->get_id_category())->write())
				$item->set_published(ClubsItem::NOT_PUBLISHED);
		}
		else
		{
			if ($this->form->get_value('update_creation_date'))
			{
				$item->set_creation_date(new Date());
			}
			else
			{
				$item->set_creation_date($this->form->get_value('creation_date'));
			}
			$item->set_published($this->form->get_value('published')->get_raw_value());
		}

		if ($item->get_id() === null)
		{
			$id = ClubsService::add($item);
		}
		else
		{
			$id = $item->get_id();
			ClubsService::update($item);
		}

		$this->contribution_actions($item, $id);

		ClubsService::clear_cache();
	}

	private function contribution_actions(ClubsItem $item, $id)
	{
		if ($this->is_contributor_member())
		{
			$contribution = new Contribution();
			$contribution->set_id_in_module($id);
			if ($item->get_id() === null)
				$contribution->set_description(stripslashes($this->form->get_value('contribution_description')));
			else
				$contribution->set_description(stripslashes($this->form->get_value('edition_description')));
			$contribution->set_entitled($item->get_title());
			$contribution->set_fixing_url(ClubsUrlBuilder::edit($id)->relative());
			$contribution->set_poster_id(AppContext::get_current_user()->get_id());
			$contribution->set_module('clubs');
			$contribution->set_auth(
				Authorizations::capture_and_shift_bit_auth(
					CategoriesService::get_categories_manager()->get_heritated_authorizations($item->get_id_category(), Category::MODERATION_AUTHORIZATIONS, Authorizations::AUTH_CHILD_PRIORITY),
					Category::MODERATION_AUTHORIZATIONS, Contribution::CONTRIBUTION_AUTH_BIT
				)
			);
			ContributionService::save_contribution($contribution);
		}
		else
		{
			$corresponding_contributions = ContributionService::find_by_criteria('clubs', $id);
			if (count($corresponding_contributions) > 0)
			{
				foreach ($corresponding_contributions as $contribution)
				{
					$contribution->set_status(Event::EVENT_STATUS_PROCESSED);
					ContributionService::save_contribution($contribution);
				}
			}
		}
		$item->set_id($id);
	}

	private function redirect()
	{
		$item = $this->get_item();
		$category = $item->get_category();

		if ($this->is_new_item && $this->is_contributor_member() && !$item->is_published())
		{
			DispatchManager::redirect(new UserContributionSuccessController());
		}
		elseif ($item->is_published())
		{
			if ($this->is_new_item)
				AppContext::get_response()->redirect(ClubsUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()), StringVars::replace_vars($this->lang['clubs.message.success.add'], array('name' => $item->get_title())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ClubsUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title())), StringVars::replace_vars($this->lang['clubs.message.success.edit'], array('name' => $item->get_title())));
		}
		else
		{
			if ($this->is_new_item)
				AppContext::get_response()->redirect(ClubsUrlBuilder::display_pending(), StringVars::replace_vars($this->lang['clubs.message.success.add'], array('name' => $item->get_title())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : ClubsUrlBuilder::display_pending()), StringVars::replace_vars($this->lang['clubs.message.success.edit'], array('name' => $item->get_title())));
		}
	}

	private function build_response(View $view)
	{
		$item = $this->get_item();

		$response = new SiteDisplayResponse($view);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['clubs.module.title'], ClubsUrlBuilder::home());

		if ($item->get_id() === null)
		{
			$graphical_environment->set_page_title($this->lang['clubs.add']);
			$breadcrumb->add($this->lang['clubs.add'], ClubsUrlBuilder::add($item->get_id_category()));
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['clubs.add'], $this->lang['clubs.module.title']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ClubsUrlBuilder::add($item->get_id_category()));
		}
		else
		{
			$graphical_environment->set_page_title($this->lang['clubs.edit']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['clubs.edit'], $this->lang['clubs.module.title']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(ClubsUrlBuilder::edit($item->get_id()));

			$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($item->get_id_category(), true));
			foreach ($categories as $id => $category)
			{
				if ($category->get_id() != Category::ROOT_CATEGORY)
					$breadcrumb->add($category->get_name(), ClubsUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
			}
			$category = $item->get_category();
			$breadcrumb->add($item->get_title(), ClubsUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()));
			$breadcrumb->add($this->lang['clubs.edit'], ClubsUrlBuilder::edit($item->get_id()));
		}

		return $response;
	}
}
?>
