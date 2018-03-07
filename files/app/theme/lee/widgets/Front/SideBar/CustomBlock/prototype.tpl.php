<div class="block">
    <div class="block-title">
        <strong><span>Custom Block (top)</span></strong>
    </div>
    <div class="block-content sample-block">
        <p>Custom CMS block displayed at the top of the left sidebar. Put your own
            content here.</p>
    </div>
</div>
<script type="text/javascript">
    //<![CDATA[
    function validatePollAnswerIsSelected() {
        var options = $$('input.poll_vote');
        for (i in options) {
            if (options[i].checked == true) {
                return true;
            }
        }
        return false;
    }
    //]]>
</script>