<script>

	var ScmFormFieldGameEvents = function() {
		this.integer   = {FIELDS_NUMBER};
		this.id_input  = ${escapejs(ID)};
		this.max_input = {MAX_INPUT};
	};
	ScmFormFieldGameEvents.prototype = {
		add_field : function (field_id, id_input) {
            if (this.integer <= this.max_input)
            {
				var id = id_input + '_' + this.integer;

				jQuery('<div/>', {'id' : id, 'class' : 'grouped-inputs'}).appendTo('#' + field_id);

				jQuery('<input/> ', {type : 'text', id : 'field_player_' + id, 'class' : 'grouped-element', name : 'field_player_' + id, placeholder : '{@scm.game.event.player}'}).appendTo('#' + id);
				jQuery('#' + id).append(' ');

				jQuery('<input/> ', {type : 'number', id : 'field_time_' + id, 'class' : 'grouped-element', name : 'field_time_' + id, pattern : '#[A-Fa-f0-9]', placeholder : '{@scm.game.event.minute}'}).appendTo('#' + id);
				jQuery('#' + id).append(' ');

				jQuery('<a/> ', {href : 'javascript:ScmFormFieldGameEvents.delete_field("'+ id_input +'", '+ this.integer +');', 'class' : 'grouped-element bgc-full error', 'aria-label' : '{@common.delete}'}).html('<i class="far fa-trash-alt" aria-hidden="true"></i>').appendTo('#' + id);

				this.integer++;
			}
			if (this.integer == this.max_input) {
				jQuery('#add-' + id_input).hide();
			}
		},
		delete_field : function (input,id) {
			var id = input + '_' + id;
			jQuery('#' + id).remove();
			this.integer--;
			jQuery('#add-' + input).show();
		}
	};

	var ScmFormFieldGameEvents = new ScmFormFieldGameEvents();
</script>

<div id="input_fields_${escape(ID)}">
	# START fieldelements #
		<div id="${escape(ID)}_{fieldelements.ID}" class="grouped-inputs">
			<input class="grouped-element" type="text" name="field_player_${escape(ID)}_{fieldelements.ID}" id="field_player_${escape(ID)}_{fieldelements.ID}" value="{fieldelements.PLAYER}" placeholder="{@scm.game.event.player}">
			<input class="grouped-element" type="number" name="field_time_${escape(ID)}_{fieldelements.ID}" id="field_time_${escape(ID)}_{fieldelements.ID}" value="{fieldelements.TIME}" placeholder="{@scm.game.event.minute}">
			<a class="grouped-element bgc-full error" href="javascript:ScmFormFieldGameEvents.delete_field({fieldelements.ID});" data-confirmation="delete-element" aria-label="{@common.delete}"><i class="far fa-trash-alt"></i></a>
		</div>
	# END fieldelements #
</div>
<a href="javascript:ScmFormFieldGameEvents.add_field('input_fields_${escape(ID)}', '${escape(ID)}');" id="add-${escape(ID)}" class="field-source-more-value" aria-label="{@common.add}"><i class="fa fa-plus"></i></a>
