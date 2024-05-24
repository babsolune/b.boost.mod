# IF C_MATCHES #
    <table class="table bordered-table">
        <caption>{@football.menu.compet.group}</caption>
        <colgroup class="hidden-small-screens">
            # IF C_PLAYGROUNDS #<col class="width-05" /># ENDIF #
            <col class="width-# IF C_ONE_DAY #05# ELSE #13# ENDIF #" />
            <col class="width-# IF C_ONE_DAY #33# ELSE #29# ENDIF #" />
            <col class="width-05" />
            <col class="width-05" />
            <col class="width-# IF C_ONE_DAY #33# ELSE #29# ENDIF #" />
            <col class="width-06" />
        </colgroup>
        <thead>
            <tr>
                # IF C_PLAYGROUNDS #<th>{@football.th.playground}</th># ENDIF #
                <th># IF C_ONE_DAY #{@football.th.hourly}# ELSE #{@football.th.date}# ENDIF #</th>
                <th>{@football.th.team} 1</th>
                <th colspan="2">{@football.th.score}</th>
                <th>{@football.th.team} 2</th>
                <th>id</th>
            </tr>
        </thead>
        <tbody>
            # START groups #
                <tr# IF groups.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                    # IF C_PLAYGROUNDS #<td>{groups.PLAYGROUND}</td># ENDIF #
                    <td># IF C_ONE_DAY #{groups.MATCH_DATE_HOUR_MINUTE}# ELSE #{groups.MATCH_DATE_SHORT} {groups.MATCH_DATE_HOUR_MINUTE}# ENDIF #</td>
                    <td class="align-right home-{groups.ID}">{groups.HOME_TEAM}</td>
                    <td>{groups.HOME_SCORE}</td>
                    <td>{groups.AWAY_SCORE}</td>
                    <td class="align-left away-{groups.ID}">{groups.AWAY_TEAM}</td>
                    <td>{groups.ID}</td>
                </tr>
            # END groups #
        </tbody>
    </table>
    <table class="table bordered-table">
        <caption>{@football.menu.compet.bracket}</caption>
        <colgroup class="hidden-small-screens">
            # IF C_PLAYGROUNDS #<col class="width-05" /># ENDIF #
            <col class="# IF C_ONE_DAY #width-05# ELSE #width-13# ENDIF #" />
            <col class="# IF C_ONE_DAY #width-33# ELSE ## ENDIF #" />
            <col class="width-05" />
            <col class="width-05" />
            <col class="# IF C_ONE_DAY #width-33# ELSE ## ENDIF #" />
            <col class="width-06" />
        </colgroup>
        <thead>
            <tr>
                # IF C_PLAYGROUNDS #<th>{@football.th.playground}</th># ENDIF #
                <th># IF C_ONE_DAY #{@football.th.hourly}# ELSE #{@football.th.date}# ENDIF #</th>
                <th>{@football.th.team} 1</th>
                <th colspan="2">{@football.th.score}</th>
                <th>{@football.th.team} 2</th>
                <th>id</th>
            </tr>
        </thead>
        <tbody>
            # START finals #
                <tr# IF finals.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                    # IF C_PLAYGROUNDS #<td>{finals.PLAYGROUND}</td># ENDIF #
                    <td># IF C_ONE_DAY #{finals.MATCH_DATE_HOUR_MINUTE}# ELSE #{finals.MATCH_DATE_SHORT} {finals.MATCH_DATE_HOUR_MINUTE}# ENDIF #</td>
                    <td class="align-right home-{finals.ID}">{finals.HOME_TEAM}</td>
                    <td># IF finals.C_HAS_PEN #<span class="small">({finals.HOME_PEN})</span> # ENDIF #{finals.HOME_SCORE}</td>
                    <td>{finals.AWAY_SCORE}# IF finals.C_HAS_PEN # <span class="small">({finals.AWAY_PEN})</span># ENDIF #</td>
                    <td class="align-left away-{finals.ID}">{finals.AWAY_TEAM}</td>
                    <td>{finals.ID}</td>
                </tr>
            # END groups #
        </tbody>
    </table>
# ENDIF #
<script src="{PATH_TO_ROOT}/football/templates/js/football.table# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/football/templates/js/finals.matches.{TEAMS_NUMBER}.{TEAMS_PER_GROUP}# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>