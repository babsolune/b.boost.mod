<!-- === ScmFormFieldSelectDistrict.tpl === -->
<div class="grouped-inputs">
    <select
        name="${escape(NAME)}-country"
        id="${escape(HTML_ID)}-country"
        class="grouped-element"
        # IF C_DISABLED # disabled="disabled" # ENDIF #
        data-initial-code="{CODE}">
            <option value="">-- Aucun pays sélectionné --</option>
            # START options #
                <option value="{options.CODE}" label="{options.PROTECTED_LABEL}" data-file="{options.FILE}"# IF options.C_SELECTED # selected="selected"# ENDIF #>{options.LABEL}</option>
            # END options #
    </select>
    <select
        name="${escape(NAME)}-league"
        id="${escape(HTML_ID)}-league"
        class="grouped-element "
        data-initial-lref="{LREF}">
    </select>
    <select
        name="${escape(NAME)}-district"
        id="${escape(HTML_ID)}-district"
        class="grouped-element # IF C_SELECT_TO_LIST #select-to-list # ENDIF #${escape(CSS_CLASS)}"
        data-initial-dref="{DREF}">
    </select>
    <input name="${escape(NAME)}-file" id="${escape(HTML_ID)}-file" type="hidden" value="{FILE}" />
</div>
<script>
    jQuery(document).ready(function() {
        const $countrySelect  = jQuery('#' + ${escapejs(HTML_ID)} + '-country');
        const $leagueSelect   = jQuery('#' + ${escapejs(HTML_ID)} + '-league');
        const $districtSelect = jQuery('#' + ${escapejs(HTML_ID)} + '-district');
        const $fileInput      = jQuery('#' + ${escapejs(HTML_ID)} + '-file');
        const initialCode = $countrySelect.data('initial-code') || $countrySelect.find('option[selected]').val();
        const initialLref = $leagueSelect.data('initial-lref') || $leagueSelect.find('option[selected]').val();
        const initialDref = $districtSelect.data('initial-dref') || $districtSelect.find('option[selected]').val();

        if (initialCode) {
            $countrySelect.val(initialCode);
        }

        // Load league list from file
        function loadLeagues(file) {
            $fileInput.val(file);
            if (!initialLref) {
                $leagueSelect.empty().append('<option value="">-- Aucune ligue sélectionnée --</option>');
            }

            jQuery.getJSON(file, function(data) {
                if (data.leagues && data.leagues.length > 0) {
                    jQuery.each(data.leagues, function(index, league) {
                        $leagueSelect.append(jQuery('<option>', {
                            value: league.lref,
                            text: league.league
                        }));
                    });
                    $leagueSelect.prop('disabled', false);

                    // If a league is allready set when the page loads, select it and load its districts
                    if (initialLref) {
                        $leagueSelect.val(initialLref);
                        loadDistricts(file, initialLref);
                    }
                }
            });
        }

        // Load district list from selected league
        function loadDistricts(file, lref) {
            if($leagueSelect.val() != '' && $leagueSelect.val() != null) {
                $districtSelect.empty().append('<option value="">-- Aucun district sélectionné --</option>');
            }

            jQuery.getJSON(file, function(data) {
                const selectedLeagueData = data.leagues.find(l => l.lref === lref);
                if (selectedLeagueData && selectedLeagueData.districts && selectedLeagueData.districts.length > 0) {
                    jQuery.each(selectedLeagueData.districts, function(index, district) {
                        $districtSelect.append(jQuery('<option>', {
                            value: district.dref,
                            text: district.district
                        }));
                    });
                    $districtSelect.prop('disabled', false);

                    // If a district is allready set when the page loads, select it
                    if (initialDref) {
                        $districtSelect.val(initialDref);
                    }
                }
            });
        }

        // Au chargement de la page, charger les ligues pour le pays initial
        // If a country is allready set when the page loads, load its leagues
        const initialFile = $countrySelect.find('option:selected').data('file');
        if (initialFile) {
            loadLeagues(initialFile);
        }

        // When a country is selected
        $countrySelect.change(function() {
            const file = jQuery(this).find('option:selected').data('file');
            $leagueSelect.empty().append('<option value="">-- Aucune ligue sélectionnée --</option>');
            $districtSelect.empty().append('<option value="">-- Aucune ligue sélectionnée --</option>');
            loadLeagues(file);
        });

        // When a league is selected
        $leagueSelect.change(function() {
            const file = $countrySelect.find('option:selected').data('file');
            const selectedLeague = jQuery(this).val();
            if (selectedLeague) {
                loadDistricts(file, selectedLeague);
            } else {
                $districtSelect.empty().append('<option value="">-- Aucun district sélectionné --</option>');
            }
        });
    });
</script>

