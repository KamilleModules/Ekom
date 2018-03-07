
<div class="block block-poll">
    <div class="block-title">
        <strong><span>Community Poll</span></strong>
    </div>
    <form id="pollForm"
          action="http://ultimo.infortis-themes.com/demo/default/poll/vote/add/poll_id/1/"
          method="post" onsubmit="return validatePollAnswerIsSelected();">
        <div class="block-content">
            <p class="block-subtitle">What is your Magento version</p>
            <ul id="poll-answers">
                <li>
                    <input type="radio" name="vote" class="radio poll_vote" id="vote_1"
                           value="1"/>
                    <span class="label"><label for="vote_1">1.7.x</label></span>
                </li>
                <li>
                    <input type="radio" name="vote" class="radio poll_vote" id="vote_2"
                           value="2"/>
                    <span class="label"><label for="vote_2">1.6.x</label></span>
                </li>
                <li>
                    <input type="radio" name="vote" class="radio poll_vote" id="vote_3"
                           value="3"/>
                    <span class="label"><label for="vote_3">1.5.x</label></span>
                </li>
                <li>
                    <input type="radio" name="vote" class="radio poll_vote" id="vote_4"
                           value="4"/>
                    <span class="label"><label for="vote_4">1.4.x</label></span>
                </li>
                <li>
                    <input type="radio" name="vote" class="radio poll_vote" id="vote_5"
                           value="5"/>
                    <span class="label"><label for="vote_5">1.3.x</label></span>
                </li>
            </ul>

            <div class="actions">
                <button type="submit" title="Vote" class="button">
                    <span><span>Vote</span></span></button>
            </div>
        </div>
    </form>
</div>