<div class="main container">
    <div class="inner-container">
        <div class="preface"></div>
        <div id="page-columns" class="columns">
            <div class="column-main">
                <div class="account-create">
                    <div class="page-title">
                        <h1>Success</h1>
                    </div>
                    <p>
                        Your account has been successfully created!
                        <?php if ("mailConfirmation" === $v['type']): ?>
                            A confirmation email has been sent to {email}.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="postscript"></div>
    </div>
</div>