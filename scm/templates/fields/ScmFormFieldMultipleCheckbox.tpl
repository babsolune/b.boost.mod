# START choice #
	<div id="{choice.CHECKBOX_ID}" data-sort="{choice.SORT_ID}" class="# IF choice.C_GROUP_OPTION #checkbox-title # ENDIF #form-field-checkbox">
        # IF choice.C_GROUP_OPTION #
            <div>{choice.NAME}</div>
        # ELSE #
            <label class="checkbox" for="${escape(choice.HTML_ID)}">
                <input
                    type="checkbox"
                    name="${escape(choice.HTML_ID)}"
                    id="${escape(choice.HTML_ID)}"
                    # IF choice.C_CHECKED # checked="checked"# ENDIF # />
                <span>${escape(choice.NAME)}</span>
            </label>
        # ENDIF #
	</div>
# END choice #
