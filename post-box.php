<div class="post-box" id="post-box">
    <form method="POST" action="tuossog-api-json.php" id="timelineForm">
        <textarea placeholder="Share your interest here" name="post" id="postText" class="animateInput"></textarea>
        <div class="button"><span class="icon-globe" id="community-select-list"></span>	
            <select data-placeholder="Select Community" class="chzn-select" multiple name="comid[]"> 
                <option></option>
                <?php
                $userCommunity->setIsTimeline(TRUE);
                $comm = $userCommunity->userComm(0, 10000);
                if ($comm['status']) {
                    foreach ($comm['community_list'] as $com) {
                        if ($com['enableMemberPost'] == 1 || $com['creator_id'] == $_COOKIE['user_auth'])
                            echo "<option value='$com[id]'>$com[name]</option>";
                    }
                }
                ?>
            </select>
        </div>
        <input type="submit" class="submit button float-right" value="Post" id="postBtn">
        <input type="hidden" id="hiddenComm">
        <input type="file" onchange="$('#filesSelected').html(this.files.length + (this.files.length > 1 ? ' files selected' : ' file selected'))" name="photo[]" multiple style="position: absolute;left: -9999px;" id="uploadInput"/>
        <div class="button hint hint--left  float-right" data-hint="Upload image" id="uploadImagePost"><span class="icon-16-camera"></span></div><div id="filesSelected" class="float-right" style="font-size: 12px; color: #99c53d"></div>
        <div class="progress" style="display:none"><div class="bar"></div ><div class="percent">0%</div></div><div id="status"></div>
    </form>
    <div class="clear"></div>
</div>