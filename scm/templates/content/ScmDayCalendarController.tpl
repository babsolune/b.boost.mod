<section id="module-scm" class="several-items modal-container">
    # INCLUDE MENU #
    <article>
        <header><h2 class="align-center">{@scm.days.results} {@scm.day} {DAY}</h2></header>
        <div class="content">
            # IF C_HAS_GAMES #
                <div class="md-width-pc-70 m-a"># INCLUDE DAY_GAMES #</div>
            # ELSE #
                <div class="message-helper bgc notice">{@scm.message.no.games}</div>
            # ENDIF #
        </div>
    </article>
    <footer></footer>
</section>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight.js"></script>