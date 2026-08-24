<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2021 08 22
 * @since       PHPBoost 5.0 - 2017 06 21
*/

class ScmFormFieldDistrictSelect extends AbstractFormField
{
    private $club;
	private $require = true;

	public function __construct($id, $label, array $value = [], array $field_options = [], array $constraints = [])
	{
		parent::__construct($id, $label, $value, $field_options, $constraints);
	}

    /**
     * @return string The html code for the select.
     */
    public function display()
    {
        $template = $this->get_template_to_use();
        $view = new FileTemplate('scm/fields/ScmFormFieldDistrictSelect.tpl');

        $view->put_all([
            'NAME' => $this->get_html_id(),
            'ID' => $this->get_id(),
            'HTML_ID' => $this->get_html_id(),
            'C_DISABLED' => $this->is_disabled()
        ]);

        $this->assign_common_template_variables($template);

        foreach ($this->get_countries_list() as $option)
        {
            $label = LangLoader::get_message($option['code'], 'countries');
            $view->assign_block_vars('options', [
                'CODE' => $option['code'],
                'FILE' => $option['file'],
                'LABEL' => $label,
                'PROTECTED_LABEL' => TextHelper::ucfirst(Url::encode_rewrite($label)),
            ]);
        }

		$i = 0;
		foreach ($this->get_value() as $option)
		{
            $data = ScmClubService::get_district_data($option['file']);
			$view->put_all([
				'COUNTRY' => !empty($option['code']) ? LangLoader::get_message($option['code'], 'countries') : '',
				'CODE' => $option['code'],
				'LEAGUE' => !empty($option['league']) ? ScmClubService::get_league($data, $option['league']) : '',
				'LREF' => $option['lref'],
				'DISTRICT' => !empty($option['district']) ? ScmClubService::get_district($data, $option['district']) : '',
				'DREF' => $option['dref'],
				'FILE' => $option['file'],
			]);
			$i++;
		}

        if ($i == 0)
		{
			$view->put_all([
				'COUNTRY' => '== Sélectionner un pays ==',
				'CODE' => '',
				'LEAGUE' => '== Sélectionner une ligue ==',
				'LREF' => '',
				'DISTRICT' => '== Sélectionner un district ==',
				'DREF' => '',
				'FILE' => '',
			]);
		}

        $template->assign_block_vars('fieldelements', [
            'ELEMENT' => $view->render(),
        ]);

        return $template;
    }

    private function get_countries_list()
    {
        $options = [];
        $leagues_dir = ModulesManager::get_module_path('scm/data/leagues/');
        $json_files = glob($leagues_dir . '*.json');

        usort($json_files, function($fileA, $fileB) {
            $dataA = json_decode(file_get_contents($fileA), true);
            $dataB = json_decode(file_get_contents($fileB), true);

            if (basename($fileA) === 'other.json') {
                return -1;
            }
            if (basename($fileB) === 'other.json') {
                return 1;
            }
            $codeA = LangLoader::get_message($dataA['country']['code'], 'countries');
            $codeB = LangLoader::get_message($dataB['country']['code'], 'countries');
            return strcmp($codeA, $codeB);
        });

        foreach ($json_files as $file) {
            if ($this->get_club()->get_id_club() === null) {
                $data_file = '../' . $file;
            }
            else {
                $data_file = '../../' . $file;
            }
            $json = file_get_contents($file);
            $data = json_decode($json, true);
            if (isset($data['country']['code'])) {
                $options[] =
                    [
                        'code' => $data['country']['code'],
                        'file' => $data_file
                    ];
            }
        }
        return $options;
    }

	public function retrieve_value()
	{
		$request = AppContext::get_request();
		$values = [];
		$field_country_id = $this->get_html_id() . '-country';
        if ($request->has_postparameter($field_country_id))
        {
            $field_league_id = $this->get_html_id() . '-league';
            $field_district_id = $this->get_html_id() . '-district';
            $field_file_id = $this->get_html_id() . '-file';
            $field_file = $request->get_poststring($field_file_id);

            if (!empty($request->get_poststring($field_country_id)))
                $values[] = [
                    'code' => $request->get_poststring($field_country_id),
                    'lref' => $request->get_poststring($field_league_id),
                    'dref' => $request->get_poststring($field_district_id),
                    'file' => $field_file,
                ];
        }
		$this->set_value($values);
	}

    private function get_club()
	{
		if ($this->club === null)
		{
			$id = AppContext::get_request()->get_getint('club_id', 0);
			if (!empty($id))
			{
				try {
					$this->club = ScmClubService::get_club($id);
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->club = new ScmClub();
			}
		}
		return $this->club;
	}

    public function validate()
    {
        try
		{
			$this->retrieve_value();
			return true;
		}
		catch(Exception $ex)
		{
			return $this->is_required() ? false : true;
		}
    }

	protected function compute_options(array &$field_options)
	{
		foreach($field_options as $attribute => $value)
		{
			$attribute = TextHelper::strtolower($attribute);
			switch ($attribute)
			{
				case 'require':
					$this->require = $value;
					unset($field_options['require']);
					break;
            }
        }
		parent::compute_options($field_options);
    }

    protected function get_default_template()
    {
        return new FileTemplate('framework/builder/form/FormField.tpl');
    }

    protected function get_js_specialization_code()
    {
        return ($this->is_required() ? '
        jQuery("#'. $this->get_html_id() .'_field").change(function() {
            HTMLForms.get("' . $this->get_form_id() . '").getField("'. $this->get_id() . '").enableValidationMessage();
            HTMLForms.get("' . $this->get_form_id() . '").getField("'. $this->get_id() . '").liveValidate();
        });' : '');
    }
}
?>