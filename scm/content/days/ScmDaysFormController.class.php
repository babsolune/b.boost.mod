<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

class ScmDaysFormController extends DefaultModuleController
{
    private $event;
    private $day;
    private $oneday;
    private $days_number;

	public function execute(HTTPRequestCustom $request)
	{
        $this->init();
		$this->check_authorizations();

        $this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
            $this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['scm.warning.bracket.update'], MessageHelper::SUCCESS, 4));
		}

		$this->view->put_all([
            'MENU'              => ScmMenuService::build_event_menu($this->event_id()),
            'CONTENT'           => $this->form->display(),
            'HAS_GAMES_WARNING' => ScmGameService::has_games($this->event_id()) ? MessageHelper::display($this->lang['scm.warning.has.games'], MessageHelper::NOTICE) : MessageHelper::display('', '')
        ]);

		return $this->generate_response($this->view);
	}

    private function init()
    {
        $teams_number = ScmTeamService::get_teams_number($this->event_id());
        $c_return_games = $this->get_event()->get_event_game_type() == ScmEvent::RETURN_GAMES;
        $this->days_number = $c_return_games ? ($teams_number - 1) * 2 : $teams_number - 1;
        $this->oneday = $this->get_event()->get_oneday();
    }

	private function build_form()
	{
		$form = new HTMLForm(self::class);
		$form->set_layout_title('<div class="align-center small">' . $this->lang['scm.days.management'] . '</div>');

        $fieldset = new FormFieldsetHTML('days', $this->oneday ? $this->lang['scm.day.date'] : $this->lang['scm.days.date']);
        $fieldset->set_css_class('days-form');
        $form->add_fieldset($fieldset);

        for ($i = 1; $i <= $this->days_number; $i++)
        {
            $day_date = $this->get_day($i)->get_day_date() ?? ScmEventService::get_event($this->event_id())->get_start_date();
            $fieldset->add_field(new FormFieldDate('day_date_' . $i, $this->oneday ? '' : $this->lang['scm.day'] . ' ' . $i, $day_date,
                ['class' => 'groups-select']
            ));
            if ($this->get_event()->get_oneday() && $i == 1) {
                break;
            }
        }

        $fieldset->add_field(new ScmFormFieldClock('general_time', $this->lang['scm.day.general.time'], $this->get_day(1)->get_general_time(),
            ['class' => 'cell-100']
        ));

        $fieldset->add_field(new FormFieldFree('inset', '', '
            <script>
                const inset = document.querySelector(".days-form .fieldset-inset");
                inset.classList.add("cell-flex", "cell-columns-4");
            </script>
        '));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function save()
	{
        if (!ScmGameService::has_games($this->event_id()))
        {
            ScmDayService::set_days_games($this->event_id());
        }
        else
        {
            ScmGameService::delete_games($this->event_id());
            ScmDayService::set_days_games($this->event_id());
        }

        for ($i = 1; $i <= $this->days_number; $i++)
        {
            $day = $this->get_day($i);
            $day->set_day_event_id($this->event_id());
            $day->set_day_round($i);
            $day->set_general_time($this->form->get_value('general_time'));
            if ($this->oneday) {
                $day->set_day_date($this->form->get_value('day_date_1'));
            }
            else {
                $day->set_day_date($this->form->get_value('day_date_' . $i));
            }

            if ($day->get_id_day() == null)
            {
                $id = ScmDayService::add_day($day);
                $day->set_id_day($id);
            }
            else {
                ScmDayService::update_day($day, $day->get_id_day());
            }

            $general_time = explode(':', $this->form->get_value('general_time'));
            $general_time = ($general_time[0] * 3600) + ($general_time[1] * 60);
            $real_date = $day->get_day_date()->get_timestamp() + $general_time;
            ScmGameService::update_game_date($this->event_id(), $i, $real_date);
        }

		ScmEventService::clear_cache();
	}

	private function get_day($day_round)
	{
        $event_id = $this->event_id();
        $id = ScmDayService::get_day($event_id, $day_round) ? ScmDayService::get_day($event_id, $day_round)->get_id_day() : null;

        if($id !== null)
        {
            try {
                $this->day = ScmDayService::get_day($event_id, $day_round);
            } catch (RowNotFoundException $e) {
                $error_controller = PHPBoostErrors::unexisting_page();
                DispatchManager::redirect($error_controller);
            }
        }
        else
        {
            $this->day = new ScmDay();
        }
		return $this->day;
	}

	private function get_event()
	{
		$id = AppContext::get_request()->get_getint('event_id', 0);
		try {
            $this->event = ScmEventService::get_event($id);
        } catch (RowNotFoundException $e) {
            $error_controller = PHPBoostErrors::unexisting_page();
            DispatchManager::redirect($error_controller);
        }
		return $this->event;
	}

    private function event_id()
    {
        return $this->get_event()->get_id();
    }

	private function check_authorizations()
	{
		if (!$this->get_event()->is_authorized_to_manage_events())
        {
            $error_controller = PHPBoostErrors::user_not_authorized();
            DispatchManager::redirect($error_controller);
        }

		if (AppContext::get_current_user()->is_readonly())
		{
			$controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($controller);
		}
	}

	protected function get_template_string_content()
	{
		return '
            # INCLUDE MESSAGE_HELPER #
            # INCLUDE MENU #
            # INCLUDE HAS_GAMES_WARNING #
            # INCLUDE CONTENT #
        ';
	}

	private function generate_response(View $view)
	{
		$event = $this->get_event();
        $category = $event->get_category();
		$location_id = $event->get_id() ? 'scm-day-'. $event->get_id() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		if (!AppContext::get_session()->location_id_already_exists($location_id))
            $graphical_environment->set_location_id($location_id);

        $graphical_environment->set_page_title($this->lang['scm.days.games.creation'], $event->get_event_name() . ($category->get_id() != Category::ROOT_CATEGORY ? ' - ' . $category->get_name() : '') . ' - ' . $this->lang['scm.module.title'] . ' - ' . GeneralConfig::load()->get_site_name());
        $graphical_environment->get_seo_meta_data()->set_description($this->lang['scm.days.games.creation']);
        $graphical_environment->get_seo_meta_data()->set_canonical_url(ScmUrlBuilder::edit_days($event->get_id(), $event->get_event_slug()));


		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['scm.module.title'], ScmUrlBuilder::home());
        $categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($event->get_id_category(), true));
        foreach ($categories as $id => $category)
        {
            if ($category->get_id() != Category::ROOT_CATEGORY)
                $breadcrumb->add($category->get_name(), ScmUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
        }
        if ($event->get_is_sub())
            $breadcrumb->add(ScmEventService::get_master_name($event->get_id()), ScmEventService::get_master_url($event->get_id()));
		$breadcrumb->add($event->get_is_sub() ? ScmDivisionService::get_division($event->get_division_id())->get_division_name() : $event->get_event_name(), ScmUrlBuilder::event_home($event->get_id(), $event->get_event_slug()));
		$breadcrumb->add($this->lang['scm.days.games.creation'], ScmUrlBuilder::edit_days($event->get_id(), $event->get_event_slug()));

		return $response;
	}
}
?>
