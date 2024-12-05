<section id="module-scm" class="several-items">
    # INCLUDE MENU #
    <article>
        <header>
            <h2>{@scm.days.ranking}</h2>
            <div class="flex-between">
                <nav class="roundmenu">
                    <ul>
                        <li><a href="{U_GENERAL}" class="roundmenu-title">{@scm.days.ranking.general}</a></li>
                        <li><a href="{U_HOME}" class="roundmenu-title">{@scm.days.ranking.home}</a></li>
                        <li><a href="{U_AWAY}" class="roundmenu-title">{@scm.days.ranking.away}</a></li>
                        <li><a href="{U_ATTACK}" class="roundmenu-title">{@scm.days.ranking.attack}</a></li>
                        <li><a href="{U_DEFENSE}" class="roundmenu-title">{@scm.days.ranking.defense}</a></li>
                    </ul>
                </nav>
                <nav class="roundmenu">
                    <ul>
                        <li><a href="{U_GENERAL_DAYS}" class="roundmenu-title">{@scm.days.ranking.days}</a> </li>
                    </ul>
                </nav>
            </div>
            # IF C_GENERAL_DAYS #
                <div>
                    <nav class="roundmenu general-days">
                        <ul>
                            # START days #
                                <li><a href="{days.U_DAY}" class="roundmenu-title# IF NOT days.C_DAY_PLAYED # not-played bgc error# ENDIF #">{days.DAY}</a></li>
                            # END days #
                        </ul>
                    </nav>
                </div>
            # ENDIF #
        </header>
        <div class="content">
            # IF C_HAS_GAMES #
                <div class="scm-table">
                    <div class="scm-line scm-head">
                        <div class="scm-line-group width-pc-50">
                            <div class="scm-cell width-pc-8" aria-label="{@scm.th.rank}">{@scm.th.rank.short}</div>
                            <div class="scm-cell cell-left width-pc-92">{@scm.th.team}</div>
                        </div>
                        <div class="scm-line-group width-pc-50">
                            <div class="scm-cell width-pc-9" aria-label="{@scm.th.points}">{@scm.th.points.short}</div>
                            <div class="scm-cell width-pc-9" aria-label="{@scm.th.played}">{@scm.th.played.short}</div>
                            <div class="scm-cell width-pc-9" aria-label="{@scm.th.win}">{@scm.th.win.short}</div>
                            <div class="scm-cell width-pc-9" aria-label="{@scm.th.draw}">{@scm.th.draw.short}</div>
                            <div class="scm-cell width-pc-9" aria-label="{@scm.th.loss}">{@scm.th.loss.short}</div>
                            <div class="scm-cell width-pc-9" aria-label="{@scm.th.goals.for}">{@scm.th.goals.for.short}</div>
                            <div class="scm-cell width-pc-9" aria-label="{@scm.th.goals.against}">{@scm.th.goals.against.short}</div>
                            <div class="scm-cell width-pc-9" aria-label="{@scm.th.goal.average}">{@scm.th.goal.average.short}</div>
                            # IF C_BONUS_SINGLE #<div class="scm-cell width-pc-9" aria-label="{@scm.th.off.bonus}">{@scm.th.off.bonus.short}</div># ENDIF #
                            # IF C_BONUS_DOUBLE #
                                <div class="scm-cell width-pc-9" aria-label="{@scm.th.off.bonus}">{@scm.th.off.bonus.short}</div>
                                <div class="scm-cell width-pc-9" aria-label="{@scm.th.def.bonus}">{@scm.th.def.bonus.short}</div>
                            # ENDIF #
                        </div>
                    </div>
                    <div class="scm-group">
                        # START ranks #
                            <div class="scm-line ranking-color team-{ranks.TEAM_ID}# IF ranks.C_FAV # fav-team# ENDIF #" style="background-color: {ranks.RANK_COLOR}">
                                <div class="scm-line-group width-pc-50">
                                    <div class="scm-cell width-pc-8">{ranks.RANK}</div>
                                    <div class="scm-cell scm-name cell-left width-pc-92">
                                        # IF ranks.C_HAS_TEAM_LOGO #<img src="{ranks.TEAM_LOGO}" alt="{ranks.TEAM_NAME}"># ENDIF #
                                        <span>
                                            <a href="{ranks.U_TEAM_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{ranks.TEAM_NAME}</a>
                                            # IF ranks.C_FORFEIT #<span class="smaller text-italic warning">({@scm.params.status.forfeit})</span># ENDIF #
                                        </span>
                                    </div>
                                </div>
                                <div class="scm-line-group width-pc-50">
                                    <div class="scm-cell width-pc-10">{ranks.POINTS}</div>
                                    <div class="scm-cell width-pc-10">{ranks.PLAYED}</div>
                                    <div class="scm-cell width-pc-10">{ranks.WIN}</div>
                                    <div class="scm-cell width-pc-10">{ranks.DRAW}</div>
                                    <div class="scm-cell width-pc-10">{ranks.LOSS}</div>
                                    <div class="scm-cell width-pc-10">{ranks.GOALS_FOR}</div>
                                    <div class="scm-cell width-pc-10">{ranks.GOALS_AGAINST}</div>
                                    <div class="scm-cell width-pc-10">{ranks.GOAL_AVERAGE}</div>
                                    # IF C_BONUS_SINGLE #<div class="scm-cell width-pc-10">{ranks.OFF_BONUS}</div># ENDIF #
                                    # IF C_BONUS_DOUBLE #
                                        <div class="scm-cell width-pc-10">{ranks.OFF_BONUS}</div>
                                        <div class="scm-cell width-pc-10">{ranks.DEF_BONUS}</div>
                                    # ENDIF #
                                </div>
                            </div>
                        # END ranks #
                    </div>
                </div>

                <div class="cell-flex cell-columns-2 modal-container">
                    # IF C_EVENT_STARTING #
                        <div></div>
                    # ELSE #
                        <div class="responsive-table">
                            <table class="">
                                <caption>{@scm.day} {LAST_DAY}</caption>
                                <colgroup class="hidden-small-screens">
                                    <col class="width-pc-05" />
                                    <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                                    <col class="width-pc-8" />
                                    <col class="width-pc-8" />
                                    <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                                    <col class="width-pc-8" />
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                                        <th>{@scm.th.home.team}</th>
                                        <th colspan="2">{@scm.th.score}</th>
                                        <th>{@scm.th.away.team}</th>
                                        <th aria-label="{@scm.th.details}"><i class="fa fa-align-left"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    # START prev_dates #
                                        <tr>
                                            <td colspan="6">{prev_dates.DATE}</td>
                                        </tr>
                                        # START prev_dates.prev_days #
                                            <tr# IF prev_dates.prev_days.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                                <td class="small">{prev_dates.prev_days.GAME_DATE_HOUR_MINUTE}</td>
                                                <td class="align-right home-{prev_dates.prev_days.HOME_ID}# IF prev_dates.prev_days.C_HOME_FAV # text-strong# ENDIF #">
                                                    <div class="flex-team flex-right">
                                                        <span>
                                                            <a
                                                                href="{prev_dates.prev_days.U_HOME_CALENDAR}"
                                                                aria-label="{@scm.see.club.calendar}"
                                                                class="offload# IF prev_dates.prev_days.HOME_FORFEIT # warning# ENDIF ## IF prev_dates.prev_days.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #"
                                                            >{prev_dates.prev_days.HOME_TEAM}</a>
                                                        </span>
                                                        # IF prev_dates.prev_days.C_HAS_HOME_LOGO #<img src="{prev_dates.prev_days.HOME_LOGO}" alt="{prev_dates.prev_days.HOME_TEAM}"># ENDIF #
                                                    </div>
                                                </td>
                                                # IF prev_dates.prev_days.C_STATUS #
                                                    <td colspan="2">{prev_dates.prev_days.STATUS}</td>
                                                # ELSE #
                                                    <td>{prev_dates.prev_days.HOME_SCORE}</td>
                                                    <td>{prev_dates.prev_days.AWAY_SCORE}</td>
                                                # ENDIF #
                                                <td class="align-left away-{prev_dates.prev_days.AWAY_ID}# IF prev_dates.prev_days.C_AWAY_FAV # text-strong# ENDIF #">
                                                    <div class="flex-team flex-left">
                                                        # IF prev_dates.prev_days.C_HAS_AWAY_LOGO #<img src="{prev_dates.prev_days.AWAY_LOGO}" alt="{prev_dates.prev_days.AWAY_TEAM}"># ENDIF #
                                                        <span>
                                                            <a
                                                                href="{prev_dates.prev_days.U_AWAY_CALENDAR}"
                                                                aria-label="{@scm.see.club.calendar}"
                                                                class="offload# IF prev_dates.prev_days.AWAY_FORFEIT # warning# ENDIF ## IF prev_dates.prev_days.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #"
                                                            >{prev_dates.prev_days.AWAY_TEAM}</a>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span data-modal="" data-target="target-panel-{prev_dates.prev_days.GAME_ID}">
                                                        # IF prev_dates.prev_days.C_HAS_DETAILS #
                                                            # IF prev_dates.prev_days.C_VIDEO #
                                                                <i class="far fa-circle-play"></i>
                                                            # ELSE #
                                                                <i class="far fa-file-lines"></i>
                                                            # ENDIF #
                                                        # ENDIF #
                                                    </span>
                                                    <div id="target-panel-{prev_dates.prev_days.GAME_ID}" class="modal modal-animation">
                                                        <div class="close-modal" aria-label="{@common.close}"></div>
                                                        <div class="content-panel">
                                                            <div class="align-right"><a href="#" class="error big hide-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></a></div>
                                                            <div class="cell-flex cell-columns-2 cell-tile">
                                                                <div class="home-team cell">
                                                                    <div class="cell-header">
                                                                        <div class="cell-name">
                                                                            <a href="{prev_dates.prev_days.U_HOME_CLUB}" class="offload">{prev_dates.prev_days.HOME_TEAM}</a>
                                                                        </div>
                                                                        # IF prev_dates.prev_days.C_HAS_HOME_LOGO #<img class="smaller width-px-25" src="{prev_dates.prev_days.HOME_LOGO}" alt="{prev_dates.prev_days.HOME_TEAM}"># ENDIF #
                                                                    </div>
                                                                    <div class="cell-score bigger align-center">
                                                                        {prev_dates.prev_days.HOME_SCORE}
                                                                    </div>
                                                                    <div class="cell-details">{@scm.game.event.goals}</div>
                                                                    # START prev_dates.prev_days.home_goals #
                                                                        <div class="cell-infos">
                                                                            <span>{prev_dates.prev_days.home_goals.PLAYER}</span>
                                                                            <span>{prev_dates.prev_days.home_goals.TIME}'</span>
                                                                        </div>
                                                                    # END prev_dates.prev_days.home_goals #
                                                                    <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                                    # START prev_dates.prev_days.home_yellow #
                                                                        <div class="cell-infos">
                                                                            <span>{prev_dates.prev_days.home_yellow.PLAYER}</span>
                                                                            <span>{prev_dates.prev_days.home_yellow.TIME}'</span>
                                                                        </div>
                                                                    # END prev_dates.prev_days.home_yellow #
                                                                    <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                                    # START prev_dates.prev_days.home_red #
                                                                        <div class="cell-infos">
                                                                            <span>{prev_dates.prev_days.home_red.PLAYER}</span>
                                                                            <span>{prev_dates.prev_days.home_red.TIME}'</span>
                                                                        </div>
                                                                    # END prev_dates.prev_days.home_red #
                                                                </div>
                                                                <div class="away-team cell">
                                                                    <div class="cell-header">
                                                                        <div class="cell-name">
                                                                            <a href="{prev_dates.prev_days.U_AWAY_CLUB}" class="offload">{prev_dates.prev_days.AWAY_TEAM}</a>
                                                                        </div>
                                                                        # IF prev_dates.prev_days.C_HAS_AWAY_LOGO #<img class="smaller width-px-25" src="{prev_dates.prev_days.AWAY_LOGO}" alt="{prev_dates.prev_days.AWAY_TEAM}"># ENDIF #
                                                                    </div>
                                                                    <div class="cell-score bigger align-center">
                                                                        {prev_dates.prev_days.AWAY_SCORE}
                                                                    </div>
                                                                    <div class="cell-details">{@scm.game.event.goals}</div>
                                                                    # START prev_dates.prev_days.away_goals #
                                                                        <div class="cell-infos">
                                                                            <span>{prev_dates.prev_days.away_goals.PLAYER}</span>
                                                                            <span>{prev_dates.prev_days.away_goals.TIME}'</span>
                                                                        </div>
                                                                    # END prev_dates.prev_days.away_goals #
                                                                    <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                                    # START prev_dates.prev_days.away_yellow #
                                                                        <div class="cell-infos">
                                                                            <span>{prev_dates.prev_days.away_yellow.PLAYER}</span>
                                                                            <span>{prev_dates.prev_days.away_yellow.TIME}'</span>
                                                                        </div>
                                                                    # END prev_dates.prev_days.away_yellow #
                                                                    <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                                    # START prev_dates.prev_days.away_red #
                                                                        <div class="cell-infos">
                                                                            <span>{prev_dates.prev_days.away_red.PLAYER}</span>
                                                                            <span>{prev_dates.prev_days.away_red.TIME}'</span>
                                                                        </div>
                                                                    # END prev_dates.prev_days.away_red #
                                                                </div>
                                                            </div>
                                                            # IF prev_dates.prev_days.C_VIDEO #
                                                                <a href="{prev_dates.prev_days.U_VIDEO}" class="button d-block align-center" target="blank" rel="noopener noreferer">
                                                                    <i class="far fa-circle-play"></i> {@scm.watch.video}
                                                                </a>
                                                            # ENDIF #
                                                            # IF prev_dates.prev_days.SUMMARY #
                                                                {prev_dates.prev_days.SUMMARY}
                                                            # ENDIF #
                                                            # IF prev_dates.prev_days.STADIUM #
                                                                <div class="width-pc-50 m-a">{prev_dates.prev_days.STADIUM}</div>
                                                            # ENDIF #
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        # END prev_dates.prev_days #
                                    # END prev_dates #
                                </tbody>
                            </table>
                        </div>
                    # ENDIF #
                    # IF C_EVENT_ENDING #
                        <div></div>
                    # ELSE #
                        <div class="responsive-table">
                            <table class="">
                                <caption>{@scm.day} {NEXT_DAY}</caption>
                                <colgroup class="hidden-small-screens">
                                    <col class="width-pc-05" />
                                    <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                                    <col class="width-pc-08" />
                                    <col class="width-pc-08" />
                                    <col class="width-pc-# IF C_ONE_DAY #40# ELSE #35# ENDIF #" />
                                    <col class="width-pc-08" />
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                                        <th>{@scm.th.home.team}</th>
                                        <th colspan="2">{@scm.th.score}</th>
                                        <th>{@scm.th.away.team}</th>
                                        <th aria-label="{@scm.th.details}"><i class="fa fa-align-left"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    # START next_dates #
                                        <tr>
                                            <td colspan="6">{next_dates.DATE}</td>
                                        </tr>
                                        # START next_dates.next_days #
                                            <tr# IF next_dates.next_days.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                                <td class="small">{next_dates.next_days.GAME_DATE_HOUR_MINUTE}</td>
                                                <td class="align-right home-{next_dates.next_days.HOME_ID}# IF next_dates.next_days.C_HOME_FAV # text-strong# ENDIF #">
                                                    <div class="flex-team flex-right">
                                                        <span>
                                                            <a
                                                                href="{next_dates.next_days.U_HOME_CALENDAR}"
                                                                aria-label="{@scm.see.club.calendar}"
                                                                class="offload# IF next_dates.next_days.HOME_FORFEIT # warning# ENDIF ## IF next_dates.next_days.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #"
                                                            >{next_dates.next_days.HOME_TEAM}</a>
                                                        </span>
                                                        # IF next_dates.next_days.C_HAS_HOME_LOGO #<img src="{next_dates.next_days.HOME_LOGO}" alt="{next_dates.next_days.HOME_TEAM}"># ENDIF #
                                                    </div>
                                                </td>
                                                # IF next_dates.next_days.C_STATUS #
                                                    <td colspan="2">{next_dates.next_days.STATUS}</td>
                                                # ELSE #
                                                    <td>{next_dates.next_days.HOME_SCORE}</td>
                                                    <td>{next_dates.next_days.AWAY_SCORE}</td>
                                                # ENDIF #
                                                <td class="align-left away-{next_dates.next_days.AWAY_ID}# IF next_dates.next_days.C_AWAY_FAV # text-strong# ENDIF #">
                                                    <div class="flex-team flex-LEFT">
                                                        # IF next_dates.next_days.C_HAS_AWAY_LOGO #<img src="{next_dates.next_days.AWAY_LOGO}" alt="{next_dates.next_days.AWAY_TEAM}"># ENDIF #
                                                        <span>
                                                            <a
                                                                href="{next_dates.next_days.U_AWAY_CALENDAR}"
                                                                aria-label="{@scm.see.club.calendar}"
                                                                class="offload# IF next_dates.next_days.AWAY_FORFEIT # warning# ENDIF ## IF next_dates.next_days.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #"
                                                            >{next_dates.next_days.AWAY_TEAM}</a>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span data-modal="" data-target="target-panel-{next_dates.next_days.GAME_ID}">
                                                        # IF next_dates.next_days.C_HAS_DETAILS #
                                                            # IF next_dates.next_days.C_VIDEO #
                                                                <i class="far fa-circle-play"></i>
                                                            # ELSE #
                                                                <i class="far fa-file-lines"></i>
                                                            # ENDIF #
                                                        # ENDIF #
                                                    </span>
                                                    <div id="target-panel-{next_dates.next_days.GAME_ID}" class="modal modal-animation">
                                                        <div class="close-modal" aria-label="{@common.close}"></div>
                                                        <div class="content-panel">
                                                            <div class="align-right"><a href="#" class="error big hide-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></a></div>
                                                            <div class="cell-flex cell-columns-2 cell-tile">
                                                                <div class="home-team cell">
                                                                    <div class="cell-header">
                                                                        <div class="cell-name">
                                                                            <a href="{next_dates.next_days.U_HOME_CLUB}" class="offload# IF next_dates.next_days.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #">{next_dates.next_days.HOME_TEAM}</a>
                                                                        </div>
                                                                        # IF next_dates.next_days.C_HAS_HOME_LOGO #<img class="smaller width-px-25" src="{next_dates.next_days.HOME_LOGO}" alt="{next_dates.next_days.HOME_TEAM}"># ENDIF #
                                                                    </div>
                                                                    <div class="cell-score bigger align-center">
                                                                        {next_dates.next_days.HOME_SCORE}
                                                                    </div>
                                                                    <div class="cell-details">{@scm.game.event.goals}</div>
                                                                    # START next_dates.next_days.home_goals #
                                                                        <div class="cell-infos">
                                                                            <span>{next_dates.next_days.home_goals.PLAYER}</span>
                                                                            <span>{next_dates.next_days.home_goals.TIME}'</span>
                                                                        </div>
                                                                    # END next_dates.next_days.home_goals #
                                                                    <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                                    # START next_dates.next_days.home_yellow #
                                                                        <div class="cell-infos">
                                                                            <span>{next_dates.next_days.home_yellow.PLAYER}</span>
                                                                            <span>{next_dates.next_days.home_yellow.TIME}'</span>
                                                                        </div>
                                                                    # END next_dates.next_days.home_yellow #
                                                                    <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                                    # START next_dates.next_days.home_red #
                                                                        <div class="cell-infos">
                                                                            <span>{next_dates.next_days.home_red.PLAYER}</span>
                                                                            <span>{next_dates.next_days.home_red.TIME}'</span>
                                                                        </div>
                                                                    # END next_dates.next_days.home_red #
                                                                </div>
                                                                <div class="away-team cell">
                                                                    <div class="cell-header">
                                                                        <div class="cell-name">
                                                                            <a href="{next_dates.next_days.U_AWAY_CLUB}" class="offload# IF next_dates.next_days.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #">{next_dates.next_days.AWAY_TEAM}</a>
                                                                        </div>
                                                                        # IF next_dates.next_days.C_HAS_AWAY_LOGO #<img class="smaller width-px-25" src="{next_dates.next_days.AWAY_LOGO}" alt="{next_dates.next_days.AWAY_TEAM}"># ENDIF #
                                                                    </div>
                                                                    <div class="cell-score bigger align-center">
                                                                        {next_dates.next_days.AWAY_SCORE}
                                                                    </div>
                                                                    <div class="cell-details">{@scm.game.event.goals}</div>
                                                                    # START next_dates.next_days.away_goals #
                                                                        <div class="cell-infos">
                                                                            <span>{next_dates.next_days.away_goals.PLAYER}</span>
                                                                            <span>{next_dates.next_days.away_goals.TIME}'</span>
                                                                        </div>
                                                                    # END next_dates.next_days.away_goals #
                                                                    <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                                    # START next_dates.next_days.away_yellow #
                                                                        <div class="cell-infos">
                                                                            <span>{next_dates.next_days.away_yellow.PLAYER}</span>
                                                                            <span>{next_dates.next_days.away_yellow.TIME}'</span>
                                                                        </div>
                                                                    # END next_dates.next_days.away_yellow #
                                                                    <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                                    # START next_dates.next_days.away_red #
                                                                        <div class="cell-infos">
                                                                            <span>{next_dates.next_days.away_red.PLAYER}</span>
                                                                            <span>{next_dates.next_days.away_red.TIME}'</span>
                                                                        </div>
                                                                    # END next_dates.next_days.away_red #
                                                                </div>
                                                            </div>
                                                            # IF next_dates.next_days.C_VIDEO #
                                                                <a href="{next_dates.next_days.U_VIDEO}" class="button d-block align-center" target="blank" rel="noopener noreferer">
                                                                    <i class="far fa-circle-play"></i> {@scm.watch.video}
                                                                </a>
                                                            # ENDIF #
                                                            # IF next_dates.next_days.SUMMARY #
                                                                {next_dates.next_days.SUMMARY}
                                                            # ENDIF #
                                                            # IF next_dates.next_days.STADIUM #
                                                                <div class="width-pc-50 m-a">{next_dates.next_days.STADIUM}</div>
                                                            # ENDIF #
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        # END next_dates.next_days #
                                    # END next_dates #
                                </tbody>
                            </table>
                        </div>
                    # ENDIF #
                </div>
            # ELSE #
                <div class="message-helper bgc notice">{@scm.message.no.games}</div>
            # ENDIF #
        </div>
    </article>
    <footer></footer>
</section>

<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>