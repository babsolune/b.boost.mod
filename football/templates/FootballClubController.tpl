<section id="module-football">
    <header class="section-header row-header">
        # IF C_HAS_LOGO #<img class="club-logo" src="{U_LOGO}" alt="{FULL_NAME}"># ENDIF #
        <div class="club-name">
            <h1># IF C_HAS_FULL_NAME #{FULL_NAME}# ELSE #{NAME}# ENDIF #</h1>
            <div class="flex-between">
                <span class="big"># IF C_HAS_NAME #{NAME}# ENDIF #</span>
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
                    <div class="cell-flex cell-columns-2">
                        <div class="cell">
                            <p>{@football.club.email} : # IF C_HAS_EMAIL #{EMAIL}# ELSE ## ENDIF #</p>
                            <p>{@football.club.phone} : # IF C_HAS_EMAIL #{PHONE}# ELSE ## ENDIF #</p>

                            # START compets #
                                # IF compets.C_VISIBLE #
                                    <article class="flex-team flex-left">
                                        <span><a href="{compets.U_COMPET}" class="offload">{compets.TITLE}</a></span>
                                    </article>
                                # ENDIF #
                            # END compets #
                        </div>
                        <div class="cell">
                            # IF C_LOCATION_MAP #
                                {LOCATION_MAP}
                            # ELSE #
                                {LOCATION}
                            # ENDIF #
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>