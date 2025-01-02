<div>
    <h3>{@scm.clubs.list}</h3>
    <div class="columns-6">
        # START clubs_list #
            <a href="{clubs_list.U_CLUB}" class="offload align-center pinned link-color d-block" aria-label="{@scm.club.see.infos}">{clubs_list.CLUB_SHORT_NAME}</a>
        # END clubs_list #
    </div>
</div>

<div class="cell-flex cell-columns-2">
    <div class="cell days-calendar">
        <div class="tabs-container">
            <nav id="matchdays" class="tabs-nav">
                <ul class="cell-header">
                    <li>
                        # IF C_EVENT_STARTING #
                            <a href="#" data-tabs="" data-target="prev-panel-{EVENT_ID}" class="bgc notice active-tab">{L_STARTING_DATE}</a>
                        # ELSE #
                            <a href="#" data-tabs="" data-target="prev-panel-{EVENT_ID}" class="active-tab"> {@scm.day} {PREV_DAY}</a>
                        # ENDIF #
                    </li>
                    # IF NOT C_EVENT_STARTING #
                        <li>
                            # IF C_EVENT_ENDING #
                                <a class="bgc notice" href="#" data-tabs="" data-target="next-panel-{EVENT_ID}">{@scm.event.ended.event}</a>
                            # ELSE #
                                <a href="#" data-tabs="" data-target="next-panel-{EVENT_ID}">{@scm.day} {NEXT_DAY}</a>
                            # ENDIF #
                        </li>
                    # ENDIF #
                </ul>
            </nav>
            <div id="prev-panel-{EVENT_ID}" class="first-tab tabs tabs-animation">
                <div class="content-panel">
                    # IF C_EVENT_STARTING #
                        <div class="message-helper bgc notice m-t">{L_STARTING_DATE}</div>
                    # ELSE #
                        <div class="cell">
                            <header class="cell-header"><h3 class="cell-name">{@scm.day} {PREV_DAY}</h3></header>
                            # INCLUDE PREV_GAMES #
                        </div>
                    # ENDIF #
                </div>
            </div>
            <div id="next-panel-{EVENT_ID}" class="tabs tabs-animation">
                <div class="content-panel">
                    # IF C_EVENT_ENDING #
                        <div class="message-helper bgc notice">{@scm.event.ended.event}</div>
                    # ELSE #
                        <div class="cell">
                            <header class="cell-header"><h3 class="cell-name">{@scm.day} {NEXT_DAY}</h3></header>
                            # INCLUDE NEXT_GAMES #
                        </div>
                    # ENDIF #
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
                <div class="scm-line-group md-width-pc-70">
                    <div class="scm-cell md-width-pc-20" aria-label="{@scm.th.rank}">{@scm.th.rank.short}</div>
                    <div class="scm-cell cell-left md-width-pc-80">{@scm.th.team}</div>
                </div>
                <div class="scm-line-group md-width-pc-30">
                    <div class="scm-cell md-width-pc-30" aria-label="{@scm.th.points}">{@scm.th.points.short}</div>
                    <div class="scm-cell md-width-pc-30" aria-label="{@scm.th.played}">{@scm.th.played.short}</div>
                    <div class="scm-cell md-width-pc-30" aria-label="{@scm.th.goal.average}">{@scm.th.goal.average.short}</div>
                </div>
            </div>
            <div class="scm-body">
                # START ranks #
                    <div class="scm-line ranking-color team-{ranks.TEAM_ID}# IF ranks.C_FAV # fav-team# ENDIF #" style="background-color: {ranks.RANK_COLOR}">
                        <div class="scm-line-group md-width-pc-70">
                            <div class="scm-cell md-width-pc-20">{ranks.RANK}</div>
                            <div class="scm-cell scm-name cell-left md-width-pc-80">
                                # IF ranks.C_HAS_TEAM_LOGO #<img src="{ranks.TEAM_LOGO}" alt="{ranks.TEAM_NAME}"># ENDIF #
                                <span>
                                    <a href="{ranks.U_TEAM_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload">{ranks.TEAM_NAME}</a>
                                    # IF ranks.C_FORFEIT #<span class="smaller text-italic warning">({@scm.params.status.forfeit})</span># ENDIF #
                                </span>
                            </div>
                        </div>
                        <div class="scm-line-group md-width-pc-30">
                            <div class="scm-cell md-width-pc-30">{ranks.POINTS}</div>
                            <div class="scm-cell md-width-pc-30">{ranks.PLAYED}</div>
                            <div class="scm-cell md-width-pc-30">{ranks.GOAL_AVERAGE}</div>
                        </div>
                    </div>
                # END ranks #
            </div>
        </div>
    </div>
</div>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>