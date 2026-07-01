# IF C_HAS_GAMES #
    <button class="button modal-button --infos-groups">{@scm.groups.composition}</button>
    <div class="modal" id="infos-groups">
        <div class="modal-overlay close-modal" aria-label="{@common.close}"></div>
        <div class="modal-content">
            <span class="error big hide-modal close-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></span>
            <div class="cell-flex cell-columns-4 cell-tile">
                # START team_groups #
                    <div class="cell">
                        <header class="cell-header">
                            <h5 class="cell-name">{@scm.group} {team_groups.GROUP}</h5>
                            <a href="{team_groups.U_GROUP}" class="offload"aria-label="{@scm.group.results}">
                                <i class="far fa-fw fa-calendar-days" aria-hidden="true"></i>
                            </a>
                        </header>
                        <div class="cell-body">
                            # START team_groups.teams #
                                <div class="cell-infos">
                                    <span>
                                        <img class="cell-icon" src="{team_groups.teams.TEAM_LOGO}" alt="{team_groups.teams.TEAM_NAME}">
                                        {team_groups.teams.TEAM_NAME}
                                    </span>
                                    <span class="controls">
                                        <a href="{team_groups.teams.U_TEAM_CALENDAR}" class="offload cssmenu-title" aria-label="{@scm.club.see.calendar}">
                                            <i class="far fa-fw fa-calendar-days" aria-hidden="true"></i>
                                        </a>
                                        <a href="{team_groups.teams.U_CLUB}" class="offload cssmenu-title" aria-label="{@scm.club.see.infos}">
                                            <i class="fa fa-fw fa-address-card" aria-hidden="true"></i>
                                        </a>
                                    </span>
                                </div>
                            # END team_groups.teams #
                        </div>
                    </div>
                # END team_groups #
            </div>
        </div>
    </div>
    <article class="games">
        <header class="article-header flex-between flex-between-large">
            <h3>{@scm.calendar}</h3>
            # IF C_ONE_DAY #<p>{ONE_DAY_DATE}</p># ENDIF #
            <button id="next-game" class="button default"><i class="fa fa-circle-arrow-down"></i> {@scm.next.games}</button>
        </header>
        <p><h3>{@scm.games.groups.stage}</h3></p>
        # START matchdays #
            <details open>
                # IF C_HAT_RANKING #
                    <summary class="bgc-sub"><a href="{matchdays.U_MATCHDAY}">{@scm.day} {matchdays.MATCHDAY}</a></summary>
                    <div class="">
                        <div class="content"># INCLUDE matchdays.MATCHDAYS_LIST #</div>
                    </div>
                # ELSE #
                    <summary class="bgc-sub">{@scm.round} {matchdays.MATCHDAY}</summary>
                    # IF C_ROUND_RANKING #
                        <div class="content"># INCLUDE matchdays.ROUND_RANKING_LIST #</div>
                    # ELSE #
                        <div class="content"># INCLUDE matchdays.ROUNDS_LIST #</div>
                    # ENDIF #
                # ENDIF #
            </details>
        # END matchdays #
        <p><h3>{@scm.games.brackets.stage}</h3></p>
        # START matchrounds #
            <details open>
                <summary class="bgc-sub">{matchrounds.L_MATCHROUND}</summary>
                # IF C_ROUND_RANKING #
                    <div class="content"># INCLUDE matchrounds.ROUND_RANKING_LIST #</div>
                # ELSE #
                    <div class="content"># INCLUDE matchrounds.ROUNDS_LIST #</div>
                # ENDIF #
            </details>
        # END matchrounds #
    </article>
# ELSE #
    <div class="message-helper bgc notice">{@scm.message.no.games}</div>
# ENDIF #