<script>

	var ScmFormFieldGameEvents = function() {
		this.integer   = {FIELDS_NUMBER};
		this.id_input  = ${escapejs(ID)};
		this.max_input = {MAX_INPUT};
	};

    ScmFormFieldGameEvents.prototype = {
		add_field : function (field_id, id, field_nb) {
            if (this.integer <= this.max_input)
            {
                var new_field = id + '_' + field_nb
				jQuery('<div/>', {'id' : new_field, 'class' : 'grouped-inputs'}).appendTo('#' + field_id);

				jQuery('<input/> ', {type : 'text', 'id' : 'field_player_' + new_field, 'class' : 'grouped-element', name : 'field_player_' + new_field, placeholder : '{@scm.game.event.player}'}).appendTo('#' + new_field);
				jQuery('#' + id).append(' ');

				jQuery('<input/> ', {type : 'number', 'id' : 'field_time_' + new_field, 'class' : 'grouped-element', name : 'field_time_' + new_field, pattern : '#[A-Fa-f0-9]', placeholder : '{@scm.game.event.minute}'}).appendTo('#' + new_field);
				jQuery('#' + id).append(' ');

				jQuery('<a/> ', {'href' : 'javascript:ScmFormFieldGameEvents.delete_field("' + id + '", "' + field_nb + '");', class : 'grouped-element item-delete bgc-full error', 'data-confirmation' : 'delete-element', 'aria-label' : ${escapejs(@common.delete)}}).html('<i class="fa fa-trash-alt" aria-hidden="true"></i>').appendTo('#' + new_field);

				this.integer++;
                field_nb++;
                jQuery('#add-' + id).attr('href', "javascript:ScmFormFieldGameEvents.add_field('input_fields_" + id + "', '" + id + "', '" + field_nb + "');");
			}
			if (this.integer == this.max_input) {
				jQuery('#add-' + id).hide();
			}
		},
		delete_field : function (id, field_nb) {
            var field = id + '_' + field_nb;
            jQuery('#' + field).remove();
			this.integer--;
            // jQuery('#add-' + id).attr('href', "javascript:ScmFormFieldGameEvents.add_field('input_fields_" + id + "', '" + id + "', '" + this.integer + "');");
			jQuery('#add-' + id).show();
		}
	};

	var ScmFormFieldGameEvents = new ScmFormFieldGameEvents();
</script>

<div id="input_fields_${escape(ID)}">
	# START fieldelements #
		<div id="${escape(ID)}_{fieldelements.ID}" class="grouped-inputs">
			<input class="grouped-element" type="text" name="field_player_${escape(ID)}_{fieldelements.ID}" id="field_player_${escape(ID)}_{fieldelements.ID}" value="{fieldelements.PLAYER}" placeholder="{@scm.game.event.player}">
			<input class="grouped-element" type="number" name="field_time_${escape(ID)}_{fieldelements.ID}" id="field_time_${escape(ID)}_{fieldelements.ID}" value="{fieldelements.TIME}" placeholder="{@scm.game.event.minute}">
			<a class="grouped-element bgc-full error" href="javascript:ScmFormFieldGameEvents.delete_field('${escape(ID)}', '{fieldelements.ID}');" data-confirmation="delete-element" aria-label="{@common.delete}"><i class="far fa-trash-alt"></i></a>
		</div>
	# END fieldelements #
</div>
<a href="javascript:ScmFormFieldGameEvents.add_field('input_fields_${escape(ID)}', '${escape(ID)}', '{FIELDS_NUMBER}');" id="add-${escape(ID)}" class="add-more-value" aria-label="{@common.add}"><i class="far fa-plus-square"></i></a>
