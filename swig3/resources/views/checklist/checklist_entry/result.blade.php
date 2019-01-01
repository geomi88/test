<input type="hidden" id="maincategory" value="{{$main_category}}">
@foreach ($checkpoints as $checkpoint)
<?php if (key_exists($checkpoint->id, $arrentry)) { ?>
    <div class="question clschckpoints" id="{{$checkpoint->id}}">
        <div class="topSection">
            <div class="leftSide">Q</div>
            <div class="rightSide">{{$checkpoint->checkpoint}}<br>{{$checkpoint->alias}}</div>
        </div>
        <div class="btmSection">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="radioRender selected">
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input type="radio" value="Good" <?php if($arrentry[$checkpoint->id]['rating']=="Good"){ echo "checked";}?> name="rating_{{$checkpoint->id}}">
                                <span></span>
                                <em>Good</em>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="commonCheckHolder radioRender">
                        <label>
                            <input type="radio" value="Average" <?php if($arrentry[$checkpoint->id]['rating']=="Average"){ echo "checked";}?> name="rating_{{$checkpoint->id}}">
                            <span></span>
                            <em>Average</em>
                        </label>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="commonCheckHolder radioRender">
                        <label>
                            <input type="radio" value="Bad" <?php if($arrentry[$checkpoint->id]['rating']=="Bad"){ echo "checked";}?> name="rating_{{$checkpoint->id}}">
                            <span></span>
                            <em>Bad</em>
                        </label>
                    </div>
                </div>
            </div>
            <textarea id="txtcomment_{{$checkpoint->id}}"><?php echo $arrentry[$checkpoint->id]['comments'];?></textarea>

        </div>
    </div>
<?php } else { ?>
    <div class="question clschckpoints" id="{{$checkpoint->id}}">
        <div class="topSection">
            <div class="leftSide">Q</div>
            <div class="rightSide">{{$checkpoint->checkpoint}}<br>{{$checkpoint->alias}}</div>
        </div>
        <div class="btmSection">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="radioRender selected">
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input type="radio" value="Good" name="rating_{{$checkpoint->id}}">
                                <span></span>
                                <em>Good</em>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="commonCheckHolder radioRender">
                        <label>
                            <input type="radio" value="Average" name="rating_{{$checkpoint->id}}">
                            <span></span>
                            <em>Average</em>
                        </label>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="commonCheckHolder radioRender">
                        <label>
                            <input type="radio" value="Bad" name="rating_{{$checkpoint->id}}">
                            <span></span>
                            <em>Bad</em>
                        </label>
                    </div>
                </div>
            </div>
            <textarea id="txtcomment_{{$checkpoint->id}}"></textarea>

        </div>
    </div>
<?php } ?>
@endforeach


