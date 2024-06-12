# INCLUDE MENU #
<article>
    <header>
        <h2>{@football.days.ranking}</h2>
        <div class="flex-between">
            <nav class="roundmenu">
                <ul>
                    <li><a href="{U_GENERAL}" class="roundmenu-title">{@football.days.ranking.general}</a></li>
                    <li><a href="{U_HOME}" class="roundmenu-title">{@football.days.ranking.home}</a></li>
                    <li><a href="{U_AWAY}" class="roundmenu-title">{@football.days.ranking.away}</a></li>
                    <li><a href="{U_ATTACK}" class="roundmenu-title">{@football.days.ranking.attack}</a></li>
                    <li><a href="{U_DEFENSE}" class="roundmenu-title">{@football.days.ranking.defense}</a></li>
                </ul>
            </nav>
            <nav class="roundmenu">
                <ul>
                    <li><a href="{U_GENERAL_DAYS}" class="roundmenu-title">{@football.days.ranking.days}</a> </li>
                </ul>
            </nav>
        </div>
        # IF C_GENERAL_DAYS #
            <div>
                <nav class="roundmenu general-days">
                    <ul>
                        # START days #
                            <li><a href="{days.U_DAY}" class="roundmenu-title">{days.DAY}</a></li>
                        # END days #
                    </ul>
                </nav>
            </div>
        # ENDIF #
    </header>
    <div class="content">
        # IF C_HAS_MATCHES #
            <table class="table bordered-table width-pc-70 m-a">
                <colgroup class="hidden-small-screens">
                    <col class="width-pc-05" />
                    <col class="width-pc-60" />
                    <col class="width-pc-05" />
                    <col class="width-pc-05" />
                    <col class="width-pc-05" />
                    <col class="width-pc-05" />
                    <col class="width-pc-05" />
                    <col class="width-pc-05" />
                    <col class="width-pc-05" />
                </colgroup>
                <thead>
                    <tr>
                        <th>{@football.th.rank}</th>
                        <th>{@football.th.team}</th>
                        <th>{@football.th.points}</th>
                        <th>{@football.th.played}</th>
                        <th>{@football.th.win}</th>
                        <th>{@football.th.draw}</th>
                        <th>{@football.th.loss}</th>
                        <th>{@football.th.goals.for}</th>
                        <th>{@football.th.goals.against}</th>
                        <th>{@football.th.goal.average}</th>
                    </tr>
                </thead>
                <tbody>
                    # START ranks #
                        <tr class="ranking-color# IF ranks.C_FAV # fav-team# ENDIF #" style="background-color: {ranks.RANK_COLOR}">
                            <td>{ranks.RANK}</td>
                            <td class="align-left">
                                <div class="flex-team flex-left">
                                    <img src="{PATH_TO_ROOT}/{ranks.TEAM_LOGO}" alt="{ranks.TEAM_NAME}">
                                    <span><a href="{ranks.U_TEAM_CALENDAR}" class="offload">{ranks.TEAM_NAME}</a></span>
                                </div>
                            </td>
                            <td>{ranks.POINTS}</td>
                            <td>{ranks.PLAYED}</td>
                            <td>{ranks.WIN}</td>
                            <td>{ranks.DRAW}</td>
                            <td>{ranks.LOSS}</td>
                            <td>{ranks.GOALS_FOR}</td>
                            <td>{ranks.GOALS_AGAINST}</td>
                            <td>{ranks.GOAL_AVERAGE}</td>
                        </tr>
                    # END ranks #
                </tbody>
            </table>

            <div class="cell-flex cell-columns-2">
                <div>
                    <table class="table bordered-table">
                        <caption>{@football.prev.day} ({LAST_DAY})</caption>
                        <colgroup class="hidden-small-screens">
                            <col class="width-pc-05" />
                            # IF NOT C_ONE_DAY #<col class="width-pc-10" /># ENDIF #
                            <col class="width-pc-05" />
                            <col class="width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                            <col class="width-pc-8" />
                            <col class="width-pc-8" />
                            <col class="width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th>id</th>
                                # IF NOT C_ONE_DAY #<th>{@football.th.date}</th># ENDIF #
                                <th>{@football.th.hourly}</th>
                                <th>{@football.th.team} 1</th>
                                <th colspan="2">{@football.th.score}</th>
                                <th>{@football.th.team} 2</th>
                            </tr>
                        </thead>
                        <tbody>
                            # START prev_days #
                                <tr# IF prev_days.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                    <td class="small">{prev_days.MATCH_ID}</td>
                                    # IF NOT C_ONE_DAY #<td class="small">{prev_days.MATCH_DATE_SHORT}</td># ENDIF #
                                    <td class="small">{prev_days.MATCH_DATE_HOUR_MINUTE}</td>
                                    <td class="align-right home-{prev_days.ID}# IF prev_days.C_HOME_FAV # text-strong# ENDIF #">
                                        <div class="flex-team flex-right">
                                            <span><a href="{prev_days.U_HOME_CALENDAR}" class="offload">{prev_days.HOME_TEAM}</a></span>
                                            <img src="{PATH_TO_ROOT}/{prev_days.HOME_LOGO}" alt="{prev_days.HOME_TEAM}">
                                        </div>
                                    </td>
                                    <td>{prev_days.HOME_SCORE}</td>
                                    <td>{prev_days.AWAY_SCORE}</td>
                                    <td class="align-left away-{prev_days.ID}# IF prev_days.C_AWAY_FAV # text-strong# ENDIF #">
                                        <div class="flex-team flex-left">
                                            <img src="{PATH_TO_ROOT}/{prev_days.AWAY_LOGO}" alt="{prev_days.AWAY_TEAM}">
                                            <span><a href="{prev_days.U_AWAY_CALENDAR}" class="offload">{prev_days.AWAY_TEAM}</a></span>
                                        </div>
                                    </td>
                                </tr>
                            # END prev_days #
                        </tbody>
                    </table>
                </div>
                <div>
                    <table class="table bordered-table">
                        <caption>{@football.next.day} ({NEXT_DAY})</caption>
                        <colgroup class="hidden-small-screens">
                            <col class="width-pc-05" />
                            # IF NOT C_ONE_DAY #<col class="width-pc-10" /># ENDIF #
                            <col class="width-pc-05" />
                            <col class="width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                            <col class="width-pc-08" />
                            <col class="width-pc-08" />
                            <col class="width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th>id</th>
                                # IF NOT C_ONE_DAY #<th>{@football.th.date}</th># ENDIF #
                                <th>{@football.th.hourly}</th>
                                <th>{@football.th.team} 1</th>
                                <th colspan="2">{@football.th.score}</th>
                                <th>{@football.th.team} 2</th>
                            </tr>
                        </thead>
                        <tbody>
                            # START next_days #
                                <tr# IF next_days.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                    <td class="small">{next_days.MATCH_ID}</td>
                                    # IF NOT C_ONE_DAY #<td class="small">{next_days.MATCH_DATE_SHORT}</td># ENDIF #
                                    <td class="small">{next_days.MATCH_DATE_HOUR_MINUTE}</td>
                                    <td class="align-right home-{next_days.ID}# IF next_days.C_HOME_FAV # text-strong# ENDIF #">
                                        <div class="flex-team flex-right">
                                            <span><a href="{next_days.U_HOME_CALENDAR}" class="offload">{next_days.HOME_TEAM}</a></span>
                                            <img src="{PATH_TO_ROOT}/{next_days.HOME_LOGO}" alt="{next_days.HOME_TEAM}">
                                        </div>
                                    </td>
                                    <td>{next_days.HOME_SCORE}</td>
                                    <td>{next_days.AWAY_SCORE}</td>
                                    <td class="align-left away-{next_days.ID}# IF next_days.C_AWAY_FAV # text-strong# ENDIF #">
                                        <div class="flex-team flex-LEFT">
                                            <img src="{PATH_TO_ROOT}/{next_days.AWAY_LOGO}" alt="{next_days.AWAY_TEAM}">
                                            <span><a href="{next_days.U_AWAY_CALENDAR}" class="offload">{next_days.AWAY_TEAM}</a></span>
                                        </div>
                                    </td>
                                </tr>
                            # END next_days #
                        </tbody>
                    </table>
                </div>
            </div>
        # ELSE #
            yenapadmatch
        # ENDIF #
    </div>
</article>

<script src="{PATH_TO_ROOT}/football/templates/js/football.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/football/templates/js/football.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>