<?php

class FootballClubAjaxCountryController extends AbstractController
{
    public function execute(HTTPRequestCustom $request)
    {
        $token = $request->get_value('token', '');
        $country = $request->get_value('country', '');

        $leagues = [];
        foreach(FootballClubService::get_league_list($country) as $league)
        {
            foreach($league as $code => $name)
            {
                $leagues[] = '<option value="' . $code . '">' . $name . '</option>';
            }
        }

        return new JSONResponse(array('options' => $leagues));
    }
}
?>