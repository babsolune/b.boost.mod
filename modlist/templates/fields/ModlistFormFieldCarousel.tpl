<script>
<!--
var ModlistFormFieldCarousel = function(){
	this.integer = {NBR_FIELDS};
	this.id_input = ${escapejs(ID)};
	this.max_input = {MAX_INPUT};
};

ModlistFormFieldCarousel.prototype = {
	add_field : function () {
		if (this.integer <= this.max_input) {
			var id = this.id_input + '_' + this.integer;

			jQuery('<div/>', {'id' : id}).appendTo('#input_fields_' + this.id_input);

			jQuery('<input/> ', {type : 'text', id : 'field_name_' + id, name : 'field_name_' + id, placeholder : '{@modlist.form.image.description}'}).appendTo('#' + id);
			jQuery('#' + id).append(' ');

			jQuery('<input/> ', {type : 'text', id : 'field_value_' + id, name : 'field_value_' + id, class : 'slider-url', placeholder : '{@modlist.form.image.url}'}).appendTo('#' + id);
			jQuery('#' + id).append(' ');

			jQuery('<a/> ', {href : '', title : '${LangLoader::get_message('files_management', 'main')}', class : 'fa fa-cloud-upload fa-2x', onclick : "window.open('{PATH_TO_ROOT}/user/upload.php?popup=1&fd=field_url_" + id + "&parse=true&no_path=true', '', 'height=500,width=780,resizable=yes,scrollbars=yes');return false;"}).appendTo('#' + id);
			jQuery('#' + id).append(' ');

			jQuery('<a/> ', {href : 'javascript:ModlistFormFieldCarousel.delete_field('+ this.integer +');'}).html('<i class="fa fa-delete"></i>').appendTo('#' + id);

			this.integer++;
		}
		if (this.integer == this.max_input) {
			jQuery('#add-' + this.id_input).hide();
		}
	},
	delete_field : function (id) {
		var id = this.id_input + '_' + id;
		jQuery('#' + id).remove();
		this.integer--;
		jQuery('#add-' + this.id_input).show();
	}
};

var ModlistFormFieldCarousel = new ModlistFormFieldCarousel();
-->
</script>

<div id="input_fields_${escape(ID)}">
# START fieldelements #
		<div id="${escape(ID)}_{fieldelements.ID}">
			<input type="text" name="field_name_${escape(ID)}_{fieldelements.ID}" id="field_name_${escape(ID)}_{fieldelements.ID}" value="{fieldelements.NAME}" placeholder="{@modlist.form.image.description}"/>
			<input type="text" name="field_value_${escape(ID)}_{fieldelements.ID}" id="field_value_${escape(ID)}_{fieldelements.ID}" value="{fieldelements.VALUE}" placeholder="{@modlist.form.image.url}" class="slider-url"/>
			<a title="${LangLoader::get_message('files_management', 'main')}" href="" class="fa fa-cloud-upload fa-2x" onclick="window.open('{PATH_TO_ROOT}/user/upload.php?popup=1&fd=field_value_${escape(ID)}_{fieldelements.ID}&parse=true&no_path=true', '', 'height=500,width=780,resizable=yes,scrollbars=yes');return false;"></a>
			<a href="javascript:ModlistFormFieldCarousel.delete_field({fieldelements.ID});" data-confirmation="delete-element"><i class="fa fa-delete"></i></a>
		</div>
# END fieldelements #
</div>
<a href="javascript:ModlistFormFieldCarousel.add_field();" id="add-${escape(ID)}" class="field-source-more-value" title="${LangLoader::get_message('add', 'common')}"><i class="fa fa-plus"></i></a>
