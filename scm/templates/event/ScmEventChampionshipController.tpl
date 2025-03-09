<div class="cell-flex cell-columns-2">
    <div class="cell days-calendar">
        <div id="event-home" class="tabs-container">
            <nav class="tabs-nav">
                <ul class="cell-header tabs-items">
                    # IF C_EVENT_STARTING #
                        <li class="tab-item --prev-panel-{EVENT_ID} bgc notice">{@scm.event.pending}</li>
                    # ELSE #
                        <li class="tab-item --prev-panel-{EVENT_ID}"> {@scm.day} {PREV_DAY}</li>
                    # ENDIF #
                    # IF NOT C_EVENT_STARTING #
                        <li>
                            # IF C_EVENT_ENDING #
                                <li class="tab-item --next-panel-{EVENT_ID} bgc notice">{@scm.event.ended.event}</li>
                            # ELSE #
                                <li class="tab-item --next-panel-{EVENT_ID}">{@scm.next.day}<!--{@scm.day} {NEXT_DAY}--></li>
                            # ENDIF #
                        </li>
                    # ENDIF #
                    <li class="tab-item --clubs-panel-{EVENT_ID}">{@scm.clubs.list}</li>
                </ul>
            </nav>
            <div class="tabs-wrapper no-style">
                <div id="prev-panel-{EVENT_ID}" class="tab-content">
                    # IF C_EVENT_STARTING #
                        <span class="message-helper bgc notice m-t">{L_STARTING_DATE}</span>
                    # ELSE #
                        # INCLUDE PREV_GAMES #
                    # ENDIF #
                </div>
                # IF NOT C_EVENT_STARTING #
                    <div id="next-panel-{EVENT_ID}" class="tab-content">
                        # IF C_EVENT_ENDING #
                            <span class="message-helper bgc notice">{@scm.event.ended.event}</span>
                        # ELSE #
                            # INCLUDE NEXT_GAMES #
                        # ENDIF #
                    </div>
                # ENDIF #
                <div id="clubs-panel-{EVENT_ID}" class="tab-content ">
                    <div class="cell-list">
                        <ul>
                            # START clubs_list #
                                <li class="flex-team">
                                    <img src="{clubs_list.CLUB_LOGO}" alt="{clubs_list.CLUB_SHORT_NAME}">
                                    <span>
                                        <a href="{clubs_list.U_CLUB}" class="offload d-block" aria-label="{@scm.club.see.infos}">
                                            {clubs_list.CLUB_SHORT_NAME}
                                        </a>
                                    </span>
                                </li>
                            # END clubs_list #
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="cell days-ranking">
        <div class="scm-table">
            <header class="cell-header flex-between">
                <h3 class="cell-name">{@scm.ranking}</h3>
                # IF C_CACHE_FILE #
                    <a href="{U_CACHE_FILE}" class="offload small text-italic"><i class="fa fa-code"></i>. json</a>
                # ENDIF #
            </header>
            <div class="scm-line scm-head">
                <div class="scm-line-group sm-width-pc-100">
                    <div class="scm-cell sm-width-pc-10" aria-label="{@scm.th.rank}">{@scm.th.rank.short}</div>
                    <div class="scm-cell cell-left sm-width-pc-60">{@scm.th.team}</div>
                    <div class="scm-cell sm-width-pc-10" aria-label="{@scm.th.points}">{@scm.th.points.short}</div>
                    <div class="scm-cell sm-width-pc-10" aria-label="{@scm.th.played}">{@scm.th.played.short}</div>
                    <div class="scm-cell sm-width-pc-10" aria-label="{@scm.th.goal.average}">{@scm.th.goal.average.short}</div>
                </div>
            </div>
            <div class="scm-body">
                # START ranks #
                    <div class="scm-line ranking-color team-{ranks.TEAM_ID}# IF ranks.C_FAV # fav-team# ENDIF #" style="background-color: {ranks.RANK_COLOR}">
                        <div class="scm-line-group sm-width-pc-100">
                            <div class="scm-cell sm-width-pc-10">{ranks.RANK}</div>
                            <div class="scm-cell scm-name cell-left sm-width-pc-60 flex-between">
                                <div class="flex-between">
                                    # IF ranks.C_HAS_TEAM_LOGO #<img src="{ranks.TEAM_LOGO}" alt="{ranks.TEAM_NAME}"># ENDIF #
                                    <span><a href="{ranks.U_TEAM_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload">{ranks.TEAM_NAME}</a></span>
                                </div>
                                # IF ranks.C_FORFEIT #<span class="smaller text-italic warning">({@scm.params.status.forfeit})</span># ENDIF #
                            </div>
                            <div class="scm-cell sm-width-pc-10">{ranks.POINTS}</div>
                            <div class="scm-cell sm-width-pc-10">{ranks.PLAYED}</div>
                            <div class="scm-cell sm-width-pc-10">{ranks.GOAL_AVERAGE}</div>
                        </div>
                    </div>
                # END ranks #
            </div>
        </div>
    </div>
</div>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight.js"></script>