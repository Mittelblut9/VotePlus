{extends file="parent:frontend/detail/comment/entry.tpl"}

{block name='frontend_detail_comment_header'}
    <div class="entry--vote" data-url="{url controller='VotePlus'}">
        <div id="entry--upvote_{$vote.id}" class="entry--upvote_btn" data-voted="{if $vote.hasUpVoted == '1'}true{else}false{/if}" data-voteId="{$vote.id}" title="Ist dieser Kommentar hilfreich?"></div> <!-- entry--upvote_btn_active -->
        <div id="entry--downvote_{$vote.id}" class="entry--downvote_btn" data-voted="{if $vote.hasDownVoted == '1'}true{else}false{/if}" data-voteId="{$vote.id}" title="Ist dieser Kommentar nicht hilfreich?"></div> <!-- entry--downvote_btn_active -->
        <span id="entry--voteCount_{$vote.id}">{$vote.count}</span>
        <span>Votes</span>
    </div>
    {$smarty.block.parent}
{/block}