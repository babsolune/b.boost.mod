# IF C_HAS_MATCHES #
    <table class="table bordered-table">
        <caption>{@football.menu.compet.group}</caption>
        <colgroup class="hidden-small-screens">
            # IF C_PLAYGROUNDS #<col class="width-pc-05" /># ENDIF #
            <col class="width-pc-# IF C_ONE_DAY #05# ELSE #13# ENDIF #" />
            <col class="width-pc-# IF C_ONE_DAY #33# ELSE #29# ENDIF #" />
            <col class="width-pc-05" />
            <col class="width-pc-05" />
            <col class="width-pc-# IF C_ONE_DAY #33# ELSE #29# ENDIF #" />
            <col class="width-pc-06" />
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
                    <td class="align-right home-{groups.ID}# IF groups.C_HOME_FAV # text-strong# ENDIF #">{groups.HOME_TEAM}</td>
                    <td>{groups.HOME_SCORE}</td>
                    <td>{groups.AWAY_SCORE}</td>
                    <td class="align-left away-{groups.ID}# IF groups.C_AWAY_FAV # text-strong# ENDIF #">{groups.AWAY_TEAM}</td>
                    <td>{groups.ID}</td>
                </tr>
            # END groups #
        </tbody>
    </table>
    <table class="table bordered-table">
        <caption>{@football.menu.compet.bracket}</caption>
        <colgroup class="hidden-small-screens">
            # IF C_PLAYGROUNDS #<col class="width-pc-05" /># ENDIF #
            <col class="# IF C_ONE_DAY #width-pc-05# ELSE #width-pc-13# ENDIF #" />
            <col class="# IF C_ONE_DAY #width-pc-33# ELSE ## ENDIF #" />
            <col class="width-pc-05" />
            <col class="width-pc-05" />
            <col class="# IF C_ONE_DAY #width-pc-33# ELSE ## ENDIF #" />
            <col class="width-pc-06" />
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
            # START bracket #
                <tr# IF bracket.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                    # IF C_PLAYGROUNDS #<td>{bracket.PLAYGROUND}</td># ENDIF #
                    <td># IF C_ONE_DAY #{bracket.MATCH_DATE_HOUR_MINUTE}# ELSE #{bracket.MATCH_DATE_SHORT} {bracket.MATCH_DATE_HOUR_MINUTE}# ENDIF #</td>
                    <td class="align-right home-{bracket.ID}# IF bracket.C_HOME_FAV # text-strong# ENDIF #">{bracket.HOME_TEAM}</td>
                    <td># IF bracket.C_HAS_PEN #<span class="small">({bracket.HOME_PEN})</span> # ENDIF #{bracket.HOME_SCORE}</td>
                    <td>{bracket.AWAY_SCORE}# IF bracket.C_HAS_PEN # <span class="small">({bracket.AWAY_PEN})</span># ENDIF #</td>
                    <td class="align-left away-{bracket.ID}# IF bracket.C_AWAY_FAV # text-strong# ENDIF #">{bracket.AWAY_TEAM}</td>
                    <td>{bracket.ID}</td>
                </tr>
            # END groups #
        </tbody>
    </table>
# ELSE #
    yenapadmatch
# ENDIF #
<script src="{PATH_TO_ROOT}/football/templates/js/football.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/football/templates/js/football.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>