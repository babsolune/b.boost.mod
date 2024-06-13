# IF C_HAS_MATCHES #
    <div class="cell-flex cell-columns-2">
        <div class="cell-1-3">
            <h5>{@football.menu.groups.rounds}</h5>
            # START team_groups #
                <table class="table bordered-table">
                    <thead>
                        <tr>
                            <th>{@football.group} {team_groups.GROUP}</th>
                        </tr>
                    </thead>
                    <tbody>
                        # START team_groups.teams #
                            <tr>
                                <td>
                                    <div class="flex-team">
                                        <img src="{PATH_TO_ROOT}/{team_groups.teams.TEAM_LOGO}" alt="{team_groups.teams.TEAM_NAME}">
                                        <span>{team_groups.teams.TEAM_NAME}</span>
                                    </div>
                                </td>
                            </tr>
                        # END team_groups.teams #
                    </tbody>
                </table>
            # END team_groups #
        </div>
        <div class="cell-2-3">
            <h5>{@football.menu.groups.rounds}</h5>
            <table class="table bordered-table">
                <colgroup class="hidden-small-screens">
                    <col class="width-pc-06" />
                    # IF NOT C_ONE_DAY #<col class="width-pc-10" /># ENDIF #
                    <col class="width-pc-05" />
                    # IF C_PLAYGROUNDS #<col class="width-pc-05" /># ENDIF #
                    <col class="width-pc-# IF C_ONE_DAY #33# ELSE #29# ENDIF #" />
                    <col class="width-pc-05" />
                    <col class="width-pc-05" />
                    <col class="width-pc-# IF C_ONE_DAY #33# ELSE #29# ENDIF #" />
                </colgroup>
                <thead>
                    <tr>
                        <th>id</th>
                        # IF NOT C_ONE_DAY #<th>{@football.th.date}</th># ENDIF #
                        <th>{@football.th.hourly}</th>
                        # IF C_PLAYGROUNDS #<th>{@football.th.playground}</th># ENDIF #
                        <th>{@football.th.team} 1</th>
                        <th colspan="2">{@football.th.score}</th>
                        <th>{@football.th.team} 2</th>
                    </tr>
                </thead>
                <tbody>
                    # START groups #
                        <tr# IF groups.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                            <td>{groups.MATCH_ID}</td>
                            # IF NOT C_ONE_DAY #<td>{groups.MATCH_DATE_SHORT}</td># ENDIF #
                            <td>{groups.MATCH_DATE_HOUR_MINUTE}</td>
                            # IF C_PLAYGROUNDS #<td>{groups.PLAYGROUND}</td># ENDIF #
                            <td class="align-right home-{groups.ID}# IF groups.C_HOME_FAV # text-strong# ENDIF #">
                                <div class="flex-team flex-right">
                                    <span>{groups.HOME_TEAM}</span>
                                    <img src="{PATH_TO_ROOT}/{groups.HOME_LOGO}" alt="{groups.HOME_TEAM}">
                                </div>
                            </td>
                            <td>{groups.HOME_SCORE}</td>
                            <td>{groups.AWAY_SCORE}</td>
                            <td class="align-left away-{groups.ID}# IF groups.C_AWAY_FAV # text-strong# ENDIF #">
                                <div class="flex-team flex-left">
                                    <img src="{PATH_TO_ROOT}/{groups.AWAY_LOGO}" alt="{groups.AWAY_TEAM}">
                                    <span>{groups.AWAY_TEAM}</span>
                                </div>
                            </td>
                        </tr>
                    # END groups #
                </tbody>
            </table>
        </div>
    </div>
    <table class="table bordered-table width-pc-70 m-a">
        <caption>{@football.menu.brackets.rounds}</caption>
        <colgroup class="hidden-small-screens">
            <col class="width-pc-06" />
            # IF NOT C_ONE_DAY #<col class="width-pc-10" /># ENDIF #
            <col class="width-pc-05" />
            # IF C_PLAYGROUNDS #<col class="width-pc-05" /># ENDIF #
            <col class="width-pc-# IF C_ONE_DAY #33# ELSE ## ENDIF #" />
            <col class="width-pc-05" />
            <col class="width-pc-05" />
            <col class="width-pc-# IF C_ONE_DAY #33# ELSE ## ENDIF #" />
        </colgroup>
        <thead>
            <tr>
                <th>id</th>
                # IF NOT C_ONE_DAY #<th>{@football.th.date}</th># ENDIF #
                <th>{@football.th.hourly}</th>
                # IF C_PLAYGROUNDS #<th>{@football.th.playground}</th># ENDIF #
                <th>{@football.th.team} 1</th>
                <th colspan="2">{@football.th.score}</th>
                <th>{@football.th.team} 2</th>
            </tr>
        </thead>
        <tbody>
            # START bracket #
                <tr# IF bracket.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                    <td>{bracket.MATCH_ID}</td>
                    # IF NOT C_ONE_DAY #<td>{bracket.MATCH_DATE_SHORT}</td># ENDIF #
                    <td>{bracket.MATCH_DATE_HOUR_MINUTE}</td>
                    # IF C_PLAYGROUNDS #<td>{bracket.PLAYGROUND}</td># ENDIF #
                    <td class="align-right home-{bracket.MATCH_ID}# IF bracket.C_HOME_FAV # text-strong# ENDIF #">
                        # IF bracket.HOME_ID #
                            <div class="flex-team flex-right">
                                <span>{bracket.HOME_TEAM}</span>
                                <img src="{PATH_TO_ROOT}/{bracket.HOME_LOGO}" alt="{bracket.HOME_TEAM}">
                            </div>
                        # ENDIF #
                    </td>
                    <td># IF bracket.C_HAS_PEN #<span class="small">({bracket.HOME_PEN})</span> # ENDIF #{bracket.HOME_SCORE}</td>
                    <td>{bracket.AWAY_SCORE}# IF bracket.C_HAS_PEN # <span class="small">({bracket.AWAY_PEN})</span># ENDIF #</td>
                    <td class="align-left away-{bracket.MATCH_ID}# IF bracket.C_AWAY_FAV # text-strong# ENDIF #">
                        # IF bracket.HOME_ID #
                            <div class="flex-team flex-left">
                                <img src="{PATH_TO_ROOT}/{bracket.AWAY_LOGO}" alt="{bracket.AWAY_TEAM}">
                                <span>{bracket.AWAY_TEAM}</span>
                            </div>
                        # ENDIF #
                    </td>
                </tr>
            # END groups #
        </tbody>
    </table>
# ELSE #
    yenapadmatch
# ENDIF #
<script src="{PATH_TO_ROOT}/football/templates/js/football.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/football/templates/js/football.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>