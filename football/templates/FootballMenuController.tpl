<header class="section-header">
    <div class="align-right">{HEADER_TYPE} - {HEADER_CATEGORY}</div>
    <h1>{HEADER_NAME}</h1>
</header>
<div class="compet-menu flex-between controls">
    <nav class="cssmenu cssmenu-horizontal">
        <ul>
            <li><a href="{U_CALENDAR}" class="offload cssmenu-title" aria-label="{@football.menu.compet.calendar}"><i class="far fa-fw fa-calendar-days"></i></a></li>
            # IF C_TOURNAMENT #
                <li><a href="{U_GROUPS_STAGE}" class="offload cssmenu-title" aria-label="{@football.menu.compet.group}"><i class="fa fa-fw fa-list" aria-hidden="true"></i></a></li>
                <li><a href="{U_FINALS_STAGE}" class="offload cssmenu-title" aria-label="{@football.menu.compet.bracket}"><i class="fa fa-fw fa-sitemap fa-rotate-270" aria-hidden="true"></i></a></li>
            # ELSE #
                # IF C_CHAMPIONSHIP #
                    <li><a href="{U_RESULTS}" class="offload cssmenu-title" aria-label="{@football.menu.compet.results}"><i class="fa fa-fw fa-" aria-hidden="true"></i></a></li>
                    <li><a href="{U_RANKING}" class="offload cssmenu-title" aria-label="{@football.menu.compet.ranking}"><i class="fa fa-fw fa-" aria-hidden="true"></i></a></li>
                # ENDIF #
            # ENDIF #
        </ul>
    </nav>
    # IF C_CONTROLS #
        <nav class="cssmenu cssmenu-horizontal">
            <ul>
                <li><a href="{U_SETUP_TEAMS}" class="offload cssmenu-title" aria-label="{@football.menu.teams}"><i class="fa fa-fw fa-people-group" aria-hidden="true"></i></a></li>
                # IF C_HAS_TEAMS #<li><a href="{U_SETUP_PARAMS}" class="offload cssmenu-title" aria-label="{@football.menu.params}"><i class="fa fa-fw fa-cogs" aria-hidden="true"></i></a></li># ENDIF #

                # IF C_TOURNAMENT #
                    # IF C_HAS_GROUP_PARAMS #
                        <li><a href="{U_SETUP_GROUPS}" class="offload cssmenu-title" aria-label="{@football.menu.groups}"><i class="fa fa-fw fa-people-roof" aria-hidden="true"></i></a></li>
                        # IF C_HAS_MATCHES #
                            <li><a href="{U_SETUP_MATCHES}" class="offload cssmenu-title" aria-label="{@football.menu.groups.matches}"><i class="fa fa-fw fa-list" aria-hidden="true"></i></a></li>
                            <li><a href="{U_SETUP_BRACKET}" class="offload cssmenu-title" aria-label="{@football.menu.bracket.matches}"><i class="fa fa-fw fa-sitemap fa-rotate-270" aria-hidden="true"></i></a></li>
                        # ENDIF #
                    # ENDIF #
                # ELSE #
                    # IF C_HAS_TEAMS #
                        <li><a href="{U_SETUP_MATCHES}" class="offload cssmenu-title" aria-label="{@football.menu.matches}"><i class="fa fa-fw fa-people-arrows" aria-hidden="true"></i></a></li>
                    # ENDIF #
                # ENDIF #
            </ul>
        </nav>
    # ENDIF #
</div>
