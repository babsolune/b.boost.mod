<section id="module-scm" class="several-items modal-container">
    # INCLUDE MENU #
    <article>
        <header><h2>{@scm.calendar}</h2></header>
        <div class="content">
            # IF C_HAS_GAMES #
                <div class="cell-flex cell-columns-2">
                    # START days #
                        <div class="cell">
                            <h3>{@scm.day} {days.DAY}</h3>
                            # INCLUDE days.DAYS_GAMES #
                        </div>
                    # END days #
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