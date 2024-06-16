# INCLUDE MENU #
<article>
    <header><h2>{@football.days.results}</h2></header>
    <div class="content">
        # IF C_HAS_MATCHES #
            <div class="tabs-container">
                <nav id="days" class="tabs-nav roundmenu">
                    <ul>
                        # START days #
                            <li><a class="small roundmenu-title# IF days.C_FIRST_DAY # active-tab# ENDIF #" href="#" data-tabs="" data-target="day-{days.DAY}">{days.DAY}</a></li>
                        # END days #
                    </ul>
                </nav>
                # START days #
                    <div id="day-{days.DAY}" class="tabs tabs-animation# IF days.C_FIRST_DAY # first-tab active-panel# ENDIF #">
                        <div class="content-panel">
                            <table class="width-pc-70 table bordered-table m-a">
                                <colgroup class="hidden-small-screens">
                                    # IF NOT C_ONE_DAY #
                                        <col class="width-pc-10" />
                                    # ENDIF #
                                    <col class="width-pc-4" />
                                    <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                                    <col class="width-pc-8" />
                                    <col class="width-pc-8" />
                                    <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                                </colgroup>
                                <thead>
                                    <tr>
                                        # IF NOT C_ONE_DAY #<th>{@football.th.date}</th># ENDIF #
                                        <th aria-label="{@football.th.hourly}"><i class="far fa-clock"></i></th>
                                        <th>{@football.th.team} 1</th>
                                        <th colspan="2">{@football.th.score}</th>
                                        <th>{@football.th.team} 2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    # START days.matches #
                                        <tr# IF days.matches.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                            # IF NOT C_ONE_DAY #<td><span class="">{days.matches.MATCH_DATE_SHORT}</span></td># ENDIF #
                                            <td>{days.matches.MATCH_DATE_HOUR_MINUTE}</td>
                                            <td class="# IF days.matches.C_HOME_FAV #text-strong# ENDIF #">
                                                <div class="flex-team flex-right">
                                                    <span><a href="{days.matches.U_HOME_CALENDAR}" class="offload">{days.matches.HOME_TEAM}</a></span>
                                                    <img src="{PATH_TO_ROOT}/{days.matches.HOME_LOGO}" alt="{days.matches.HOME_TEAM}">
                                                </div>
                                            </td>
                                            <td>{days.matches.HOME_SCORE}</td>
                                            <td>{days.matches.AWAY_SCORE}</td>
                                            <td class="# IF days.matches.C_AWAY_FAV #text-strong# ENDIF #">
                                                <div class="flex-team flex-left">
                                                    <img src="{PATH_TO_ROOT}/{days.matches.AWAY_LOGO}" alt="{days.matches.AWAY_TEAM}">
                                                    <span><a href="{days.matches.U_AWAY_CALENDAR}" class="offload">{days.matches.AWAY_TEAM}</a></span>
                                                </div>
                                            </td>
                                        </tr>
                                    # END days.matches #
                                </tbody>
                            </table>
                        </div>
                    </div>
                # END days #
            </div>
        # ELSE #
            <div class="message-helper bgc notice">{@football.message.no.matches}</div>
        # ENDIF #
    </div>
</article>
<script src="{PATH_TO_ROOT}/football/templates/js/football.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/football/templates/js/football.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>