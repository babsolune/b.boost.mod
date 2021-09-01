
<div id="input_fields_${escape(ID)}">
# START fieldelements #
	<div class="map-location_${escape(ID)}_{fieldelements.ID}">
		<input id="geocomplete_${escape(ID)}_{fieldelements.ID}" value="{fieldelements.STREET_NUMBER} {fieldelements.ROUTE} {fieldelements.POSTAL_CODE} {fieldelements.CITY}" class="gmap-autocomplete" placeholder="{@clubs.labels.enter.address}" type="text" required="required"/>
	 	<div class="location-datas_${escape(ID)}_{fieldelements.ID}">
			<input data-location="street_number" placeholder="{@clubs.labels.street.number}" value="{fieldelements.STREET_NUMBER}" name="field_street_number_${escape(ID)}_{fieldelements.ID}" class="gmap-short" id="field_street_number_${escape(ID)}_{fieldelements.ID}" type="text"/>
	 	    <input data-location="route" placeholder="{@clubs.labels.street.address}" value="{fieldelements.ROUTE}" name="field_route_${escape(ID)}_{fieldelements.ID}" class="gmap-large" id="field_route_${escape(ID)}_{fieldelements.ID}" type="text"/>
			<input data-location="postal_code" placeholder="{@clubs.labels.postal.code}" value="{fieldelements.POSTAL_CODE}" name="field_postal_code_${escape(ID)}_{fieldelements.ID}" class="gmap-short" id="field_postal_code_${escape(ID)}_{fieldelements.ID}" type="text"/>
			<input data-location="locality" placeholder="{@clubs.labels.city}" value="{fieldelements.CITY}" name="field_city_${escape(ID)}_{fieldelements.ID}" class="gmap-large" id="field_city_${escape(ID)}_{fieldelements.ID}" type="text"/>
	 	</div>
	</div>

    <script>
	    $(function(){
	        $("#geocomplete_${escape(ID)}_{fieldelements.ID}").geocomplete({
	            details: ".map-location_${escape(ID)}_{fieldelements.ID}",
		        detailsAttribute: "data-location",
	            types: ["geocode", "establishment"],
	        });
	    });
    </script>

# END fieldelements #
</div>
