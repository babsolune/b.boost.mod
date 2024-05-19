# IF C_MATCHES #
    <!--<div class="cell-flex cell-columns-2">-->
        <table class="table bordered-table cell-2-3">
            <colgroup>
                <col class="col-05" />
                <col class="col-05" />
                <col class="col-40" />
                <col class="col-05" />
                <col class="col-05" />
                <col class="col-40" />
            </colgroup>
            <thead>
                <tr>
                    <th>Terrain</th>
                    <th>Date</th>
                    <th>équipe 1</th>
                    <th colspan="2">score</th>
                    <th>équipe 2</th>
                </tr>
            </thead>
            <tbody>
                # START matches #
                    <tr# IF matches.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                        <td>{matches.PLAYGROUND}</td>
                        <td># IF C_ONE_DAY #{matches.MATCH_DATE_HOUR_MINUTE}# ELSE #{matches.MATCH_DATE_DAY_MONTH_YEAR_HOUR_MINUTE}# ENDIF #</td>
                        <td class="align-right">{matches.HOME_TEAM}</td>
                        <td>{matches.HOME_SCORE}</td>
                        <td>{matches.VISIT_SCORE}</td>
                        <td class="align-left">{matches.VISIT_TEAM}</td>
                    </tr>
                # END matches #
            </tbody>
        </table>
    <!--</div>-->
# ENDIF #
<script>
    jQuery('col[class*="col-"]').each(function() {
        let sizeClass = jQuery(this).attr('class'),
            size = sizeClass.split('-');
        jQuery(this).css('width', size.pop() + '%')
    })
</script>