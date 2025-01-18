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
        <div class="content modal-container cell-modal">
            # IF C_HAS_GAMES #
                <div class="scm-table">
                    <div class="scm-line scm-head">
                        <div class="scm-line-group sm-width-pc-100 md-width-pc-45">
                            <div class="scm-cell md-width-px-43" aria-label="{@scm.th.rank}">{@scm.th.rank.short}</div>
                            <div class="scm-cell cell-left md-width-pc-92">{@scm.th.team}</div>
                        </div>
                        <div class="scm-line-group sm-width-pc-100 md-width-pc-55">
                            <div class="scm-cell md-width-px-43" aria-label="{@scm.th.points}">{@scm.th.points.short}</div>
                            <div class="scm-cell md-width-px-43" aria-label="{@scm.th.played}">{@scm.th.played.short}</div>
                            <div class="scm-cell md-width-px-43" aria-label="{@scm.th.win}">{@scm.th.win.short}</div>
                            <div class="scm-cell md-width-px-43" aria-label="{@scm.th.draw}">{@scm.th.draw.short}</div>
                            <div class="scm-cell md-width-px-43" aria-label="{@scm.th.loss}">{@scm.th.loss.short}</div>
                            <div class="scm-cell md-width-px-43" aria-label="{@scm.th.goals.for}">{@scm.th.goals.for.short}</div>
                            <div class="scm-cell md-width-px-43" aria-label="{@scm.th.goals.against}">{@scm.th.goals.against.short}</div>
                            <div class="scm-cell md-width-px-43" aria-label="{@scm.th.goal.average}">{@scm.th.goal.average.short}</div>
                            # IF C_BONUS_SINGLE #<div class="scm-cell md-width-px-43" aria-label="{@scm.th.off.bonus}">{@scm.th.off.bonus.short}</div># ENDIF #
                            # IF C_BONUS_DOUBLE #
                                <div class="scm-cell md-width-px-43" aria-label="{@scm.th.off.bonus}">{@scm.th.off.bonus.short}</div>
                                <div class="scm-cell md-width-px-43" aria-label="{@scm.th.def.bonus}">{@scm.th.def.bonus.short}</div>
                            # ENDIF #
                            <div class="scm-cell md-width-px-100 hidden-small-screens" aria-label="{@scm.rank.form}"><i class="fa fa-chart-pie"></i></div>
                            # IF C_CHARTS #
                                <div class="scm-cell md-width-px-30"><i class="fa fa-chart-line" aria-hidden="true"></i></div>
                            # ENDIF #
                        </div>
                    </div>
                    <div class="scm-body">
                        # START ranks #
                            <div class="scm-line ranking-color team-{ranks.TEAM_ID}# IF ranks.C_FAV # fav-team# ENDIF #" style="background-color: {ranks.RANK_COLOR}">
                                <div class="scm-line-group sm-width-pc-100 md-width-pc-45">
                                    <div class="scm-cell md-width-px-43">{ranks.RANK}</div>
                                    <div class="scm-cell scm-name cell-left md-width-pc-92 flex-between">
                                        <div>
                                            # IF ranks.C_HAS_TEAM_LOGO #<img src="{ranks.TEAM_LOGO}" alt="{ranks.TEAM_NAME}"># ENDIF #
                                            <span>
                                                <a href="{ranks.U_TEAM_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload">{ranks.TEAM_NAME}</a>
                                                # IF ranks.C_FORFEIT #<span class="smaller text-italic warning">({@scm.params.status.forfeit})</span># ENDIF #
                                            </span>
                                        </div>
                                        # IF ranks.C_HAS_DIFF_RANK #
                                            # IF ranks.C_IS_POSITIVE #<span class="small success"><i class="fa fa-arrow-up-long" aria-hidden="true"></i> {ranks.DIFF_RANK}</span># ENDIF #
                                            # IF ranks.C_IS_NEGATIVE #<span class="small error"><i class="fa fa-arrow-down-long" aria-hidden="true"></i> {ranks.DIFF_RANK}</span># ENDIF #
                                        # ENDIF #
                                    </div>
                                </div>
                                <div class="scm-line-group sm-width-pc-100 md-width-pc-55">
                                    <div class="scm-cell md-width-px-43">{ranks.POINTS}</div>
                                    <div class="scm-cell md-width-px-43">{ranks.PLAYED}</div>
                                    <div class="scm-cell md-width-px-43">{ranks.WIN}</div>
                                    <div class="scm-cell md-width-px-43">{ranks.DRAW}</div>
                                    <div class="scm-cell md-width-px-43">{ranks.LOSS}</div>
                                    <div class="scm-cell md-width-px-43">{ranks.GOALS_FOR}</div>
                                    <div class="scm-cell md-width-px-43">{ranks.GOALS_AGAINST}</div>
                                    <div class="scm-cell md-width-px-43">{ranks.GOAL_AVERAGE}</div>
                                    # IF C_BONUS_SINGLE #<div class="scm-cell md-width-px-43">{ranks.OFF_BONUS}</div># ENDIF #
                                    # IF C_BONUS_DOUBLE #
                                        <div class="scm-cell md-width-px-43">{ranks.OFF_BONUS}</div>
                                        <div class="scm-cell md-width-px-43">{ranks.DEF_BONUS}</div>
                                    # ENDIF #
                                    <div class="scm-cell md-width-px-100 hidden-small-screens">
                                        # START ranks.form #
                                            <span
                                                class="smaller pinned bgc# IF NOT ranks.form.C_UNPLAYED #-full# ENDIF # {ranks.form.CLASS}"
                                                aria-label="# IF ranks.form.C_UNPLAYED ## IF ranks.form.C_DELAYED #{@scm.rank.health.delayed}# ELSE ## IF ranks.form.C_EXEMPT #{@scm.label.health.exempt}# ELSE #{@scm.rank.health.unplayed}# ENDIF ## ENDIF ## ELSE #{ranks.form.SCORE}# ENDIF #">
                                                # IF ranks.form.C_UNPLAYED #&nbsp;&nbsp;# ELSE #{ranks.form.L_PLAYED}# ENDIF #
                                            </span>
                                        # END ranks.form #
                                    </div>
                                    # IF C_CHARTS #
                                        <div class="scm-cell md-width-px-30">
                                            <a data-modal="" data-target="target-panel-chart-{ranks.RANK}" aria-label="{@scm.rank.chart}">
                                                <i class="fa fa-chart-line" aria-hidden="true"></i>
                                            </a>
                                            <div id="target-panel-chart-{ranks.RANK}" class="modal modal-animation">
                                                <div class="close-modal" aria-label="{@common.close}"></div>
                                                <div class="content-panel cell-chart">
                                                    <h3>{ranks.TEAM_NAME}</h3>
                                                    <canvas id="ranks-chart-{ranks.RANK}"></canvas>
                                                    <script>
                                                        let ctx_ranks_{ranks.RANK} = document.getElementById("ranks-chart-{ranks.RANK}").getContext('2d');
                                                        let data_ranks_{ranks.RANK} = {
                                                            labels: [
                                                                # START ranks.matchdays #"{ranks.matchdays.MATCHDAY}",# END ranks.matchdays #
                                                            ],
                                                            datasets: [
                                                                {
                                                                    label : "{ranks.TEAM_NAME}",
                                                                    labels: [ # START ranks.days # "{@scm.day} {ranks.days.DAY}",# END ranks.days # ],
                                                                    data: [ # START ranks.days # "{ranks.days.RANK}", # END ranks.days # ],
                                                                }
                                                            ]
                                                        };
                                                        const tooltip_rank_{ranks.RANK} = {
                                                            yAlign: 'bottom',
                                                            xAlign: 'center',
                                                            callbacks: {
                                                                title: function(context) {
                                                                    return ''
                                                                },
                                                                label: function(context) {
                                                                    return context.dataset.labels[context.dataIndex] + ' | {@scm.ranking} : ' + context.dataset.data[context.dataIndex]
                                                                }
                                                            }
                                                        };
                                                        const rankChart_{ranks.RANK} = new Chart(ctx_ranks_{ranks.RANK}, {
                                                            type: 'line',
                                                            data: data_ranks_{ranks.RANK},
                                                            options: {
                                                                responsive: true,
                                                                plugins: {
                                                                    legend: {
                                                                        display: false,
                                                                    },
                                                                    tooltip: tooltip_rank_{ranks.RANK},
                                                                },
                                                                scales: {
                                                                    x: {
                                                                        ticks: {
                                                                            stepSize: 5
                                                                        }
                                                                    },
                                                                    y: {
                                                                        grid: {
                                                                            color: (context) => {
                                                                                if (context.tick.value === {PROM_LINE})
                                                                                    return '{PROM_LINE_COLOR}';
                                                                                    // return 'rgba(0, 0, 0, 0.2)';
                                                                                if (context.tick.value === {PO_PROM_LINE})
                                                                                    return '{PO_PROM_LINE_COLOR}';
                                                                                if (context.tick.value === {PO_RELEG_LINE})
                                                                                    return '{PO_RELEG_LINE_COLOR}';
                                                                                if (context.tick.value === {RELEG_LINE})
                                                                                    return '{RELEG_LINE_COLOR}';
                                                                                return 'rgba(0, 0, 0, 0.05)';
                                                                            },
                                                                            lineWidth: 2
                                                                        },
                                                                        min: 1,
                                                                        max: {TEAMS_NUMBER},
                                                                        reverse: true,
                                                                        ticks: {
                                                                            stepSize: 1
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
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
                        <div class="cell">
                            <header class="cell-header"><h3 class="cell-name">{@scm.day} {PREV_DAY}</h3></header>
                            # INCLUDE PREV_GAMES #
                        </div>
                    # ENDIF #
                    # IF C_EVENT_ENDING #
                        <div><span class="message-helper bgc notice">{@scm.event.ended.event}</span></div>
                    # ELSE #
                        <div class="cell">
                            <header class="cell-header"><h3 class="cell-name">{@scm.day} {NEXT_DAY}</h3></header>
                            # INCLUDE NEXT_GAMES #
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