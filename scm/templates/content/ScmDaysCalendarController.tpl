<section id="module-scm" class="several-items modal-container">
    # INCLUDE MENU #
    <article>
        <header><h2>{@scm.calendar}</h2></header>
        <div class="content">
            # IF C_HAS_GAMES #
                <div class="cell-flex cell-columns-2">
                    # START days #
                        <div class="cell">
                            <h3># IF C_ONE_DAY #{@scm.round}# ELSE #{@scm.day}# ENDIF # {days.DAY}</h3>
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
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight.js"></script>