<div class="main container">
    <div class="inner-container">
        <div class="preface"></div>
        <div id="page-columns" class="columns">
            <div class="column-main">
                <div class="account-create">
                    <div class="page-title">
                        <h1>Error</h1>
                    </div>
                    <p>
                        <?php if ('exception' === $v['errorType']): ?>
                            Oops, an error occurred! A message has been sent to the webmaster.
                            <br>
                            Thank you for your patience.
                        <?php elseif ('duplicate' === $v['errorType']): ?>
                            This entry already exists in the database.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="postscript"></div>
    </div>
</div>