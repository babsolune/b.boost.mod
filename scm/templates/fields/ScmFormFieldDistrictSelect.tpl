<!-- === ScmFormFieldSelectDistrict.tpl === -->
<div class="grouped-inputs">
    <select
        name="${escape(NAME)}-country"
        id="${escape(HTML_ID)}-country"
        class="grouped-element # IF C_SELECT_TO_LIST #select-to-list # ENDIF #${escape(CSS_CLASS)}"
        # IF C_DISABLED # disabled="disabled" # ENDIF # >
        # START options #
            # INCLUDE options.OPTION #
        # END options #
    </select>
    <select
        name="${escape(NAME)}-league"
        id="${escape(HTML_ID)}-league"
        class="grouped-element # IF C_SELECT_TO_LIST #select-to-list # ENDIF #${escape(CSS_CLASS)}">
    </select>
    <select
        name="${escape(NAME)}-district"
        id="${escape(HTML_ID)}-district"
        class="grouped-element # IF C_SELECT_TO_LIST #select-to-list # ENDIF #${escape(CSS_CLASS)}">
    </select>
</div>
<script>
    jQuery(document).ready(function() {
        // Initialize dependent selectors
        jQuery('#' + ${escapejs(HTML_ID)} + '-league').empty().append('<option value="none"></option>');
        jQuery('#' + ${escapejs(HTML_ID)} + '-district').empty().append('<option value="none"></option>');
        // When a country is selected
        jQuery('#' + ${escapejs(HTML_ID)} + '-country').change(function() {
            const file = jQuery(this).find('option:selected').data('file');

            // Reset the following selectors
            jQuery('#' + ${escapejs(HTML_ID)} + '-league').empty().append('<option value="none">-- Sélectionnez une ligue --</option>');
            jQuery('#' + ${escapejs(HTML_ID)} + '-district').empty().append('<option value="none"></option>');

            // Load leagues from the selected json file
            const url = new URL(file, window.location.href).href;
            console.log(url);
            jQuery.getJSON(url, function(data) {
                if (data.leagues && data.leagues.length > 0) {
                    jQuery.each(data.leagues, function(index, league) {
                        jQuery('#' + ${escapejs(HTML_ID)} + '-league').append(jQuery('<option>', {
                            value: league.lref,
                            text: league.league
                        }));
                    });
                    jQuery('#' + ${escapejs(HTML_ID)} + '-league').prop('disabled', false);
                }
            });
        });

        // When a league is selected
        jQuery('#' + ${escapejs(HTML_ID)} + '-league').change(function() {
            const file = jQuery('#' + ${escapejs(HTML_ID)} + '-country').find('option:selected').data('file');
            const selectedLeague = jQuery(this).val();

            // Réinitialiser le sélecteur de district
            jQuery('#' + ${escapejs(HTML_ID)} + '-district').empty().append('<option value="none">-- Sélectionnez un district --</option>');

            if (!selectedLeague) return;

            // Charger les districts depuis le fichier JSON
            jQuery.getJSON(file, function(data) {
                const selectedLeagueData = data.leagues.find(l => l.lref === selectedLeague);
                if (selectedLeagueData && selectedLeagueData.districts && selectedLeagueData.districts.length > 0) {
                    jQuery.each(selectedLeagueData.districts, function(index, district) {
                        jQuery('#' + ${escapejs(HTML_ID)} + '-district').append(jQuery('<option>', {
                            value: district.dref,
                            text: district.district
                        }));
                    });
                    jQuery('#' + ${escapejs(HTML_ID)} + '-district').prop('disabled', false);
                }
            });
        });
    });
</script>

