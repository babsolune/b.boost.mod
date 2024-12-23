<section id="module-scm" class="single-item">
    <header class="section-header row-header">
        # IF C_HAS_LOGO #<img class="club-logo" src="{U_LOGO}" alt="{FULL_NAME}"># ENDIF #
        <div class="club-name">
            <h1># IF C_HAS_FULL_NAME #{FULL_NAME}# ELSE #{NAME}# ENDIF #</h1>
            <div class="flex-between">
                <div>
                    # IF C_HAS_FLAG #<img class="club-logo" src="{U_FLAG}" alt="{NAME}"># ENDIF #
                    <span class="big"># IF C_HAS_NAME #{NAME}# ENDIF #</span>
                </div>
                # IF C_CONTROLS #
                    <div class="controls align-right">
                        <a class="offload" href="{U_EDIT}" aria-label="{@common.edit}"><i class="far fa-fw fa-edit" aria-hidden="true"></i></a>
                        <a href="{U_DELETE}" aria-label="{@common.delete}" data-confirmation="delete-element"><i class="far fa-fw fa-trash-alt" aria-hidden="true"></i></a>
                    </div>
                # ENDIF #
            </div>
        </div>
    </header>
    <div class="sub-section">
        <div class="content-container">
            <article itemscope="itemscope" itemtype="https://schema.org/CreativeWork" id="club-item-{ID}" class="club-item# IF C_NEW_CONTENT # new-content# ENDIF #">
                <div class="content">
                    <div class="cell-flex cell-columns-2 cell-tile">
                        <div class="cell cell-1-3">
                            <div class="cell-content cell-infos">
                                <span>{@scm.club.website} : </span>
                                <span>
                                    # IF C_HAS_WEBSITE #
                                        <a href="{U_CLUB_WEBSITE}" target="_blank" rel="noopener noreferer">{@scm.club.see.website}</a>
                                    # ELSE #
                                        {@scm.not.specified}
                                    # ENDIF #
                                </span>
                            </div>
                            <div class="cell-content cell-infos">
                                <span>{@scm.club.email} : </span>
                                <span># IF C_HAS_EMAIL #{EMAIL}# ELSE #{@scm.not.specified}# ENDIF #</span>
                            </div>
                            <div class="cell-content cell-infos">
                                <span>{@scm.club.phone} : </span>
                                <span># IF C_HAS_EMAIL #{PHONE}# ELSE #{@scm.not.specified}# ENDIF #</span>
                            </div>
                            <div class="cell-content cell-infos">
                                <span>{@scm.club.colors} : </span>
                                <div>
                                    # IF C_COLORS #
                                        # START colors #
                                            <span class="club-colors" aria-label="{colors.NAME}" style="background-color: {colors.COLOR}"></span>
                                        # END colors #
                                    # ELSE #
                                        {@scm.not.specified}
                                    # ENDIF #
                                </div>
                            </div>
                        </div>
                        <div class="cell-2-3">
                            # IF C_DISPLAY_MAP #
                                {LOCATION_MAP}
                            # ELSE #
                                {LOCATION}
                            # ENDIF #
                        </div>
                    </div>

                    <h2>{@scm.club.event.list}</h2>
                    <div class="cell-flex cell-columns-2">
                        # START seasons #
                            <div class="cell-list club-events">
                                <ul>
                                    <li><h5 class="text-strong">{seasons.SEASON_NAME}</h5></li>
                                    <ul>
                                        # START seasons.categories #
                                            <li><h6 class="text-strong">{seasons.categories.CATEGORY_NAME}</h6></li>
                                            <ul>
                                                # START seasons.categories.events #
                                                    # IF seasons.categories.events.C_VISIBLE #
                                                        <li>
                                                            {seasons.categories.events.CLUB_NAME} : 
                                                            # IF seasons.categories.events.C_IS_SUB #
                                                                <span><a href="{seasons.categories.events.U_MASTER_EVENT}" class="offload">{seasons.categories.events.MASTER_DIVISION} {seasons.categories.events.MASTER_SEASON}</a> - </span>
                                                            # ENDIF #
                                                            <a href="{seasons.categories.events.U_EVENT}" class="offload"># IF seasons.categories.events.C_IS_SUB #{seasons.categories.events.DIVISION_NAME}# IF seasons.categories.events.C_HAS_POOL # {seasons.categories.events.POOL}# ENDIF ## ELSE #{seasons.categories.events.TITLE}# ENDIF #</a>
                                                        </li>
                                                    # ENDIF #
                                                # END seasons.categories.events #
                                            </ul>
                                        # END seasons.categories #
                                    </ul>
                                </ul>
                            </div>
                        # END seasons #
                    </div>
                </div>
            </article>
        </div>
    </div>
    <footer></footer>
</section>