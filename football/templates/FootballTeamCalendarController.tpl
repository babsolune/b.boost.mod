# INCLUDE MENU #
<article>
    <header><h2><span class="small">{@football.team.results} :</span> {TEAM_NAME}</h2></header>
    <div class="content">
        # IF C_HAS_MATCHES #
            <table class="table bordered-table width-pc-70 m-a">
                <colgroup class="hidden-small-screens">
                    # IF NOT C_ONE_DAY #<col class="width-pc-10" /># ENDIF #
                    <col class="width-pc-4" />
                    <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                    <col class="width-pc-8" />
                    <col class="width-pc-8" />
                    <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                </colgroup>
                <thead>
                    <tr>
                        # IF NOT C_ONE_DAY #<th>{@football.th.date}</th># ENDIF #
                        <th>{@football.th.hourly}</th>
                        <th>{@football.th.team} 1</th>
                        <th colspan="2">{@football.th.score}</th>
                        <th>{@football.th.team} 2</th>
                    </tr>
                </thead>
                <tbody>
                    # START matches #
                        <tr# IF matches.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                            # IF NOT C_ONE_DAY #<td>{matches.MATCH_DATE_SHORT}</td># ENDIF #
                            <td>{matches.MATCH_DATE_HOUR_MINUTE}</td>
                            <td class="# IF matches.C_HOME_WIN # text-strong# ENDIF #">
                                <div class="flex-team flex-right">
                                    <span><a href="{matches.U_HOME_CALENDAR}" class="offload">{matches.HOME_TEAM}</a></span>
                                    <img src="{PATH_TO_ROOT}/{matches.HOME_LOGO}" alt="{matches.HOME_TEAM}">
                                </div>
                            </td>
                            <td>{matches.HOME_SCORE}</td>
                            <td>{matches.AWAY_SCORE}</td>
                            <td class="# IF matches.C_AWAY_WIN # text-strong# ENDIF #">
                                <div class="flex-team flex-left">
                                    <img src="{PATH_TO_ROOT}/{matches.AWAY_LOGO}" alt="{matches.AWAY_TEAM}">
                                    <span><a href="{matches.U_AWAY_CALENDAR}" class="offload">{matches.AWAY_TEAM}</a></span>
                                </div>
                            </td>
                        </tr>
                    # END matches #
                </tbody>
            </table>
        # ELSE #
            yenapadmatch
        # ENDIF #
    </div>
</article>

<script src="{PATH_TO_ROOT}/football/templates/js/football.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/football/templates/js/football.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>