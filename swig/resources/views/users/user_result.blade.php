<ul>
    <?php foreach ($users as $user) { ?>
        <input type="hidden" id="name_{{$user->id}}" value="{{$user->firstName}} {{$user->lastName}}">
        <li><input type="checkbox" id="{{$user->id}}" class="chkuser" <?php if(in_array($user->id, $arrid)){echo "checked";}?>> {{$user->firstName}} {{$user->lastName}}</li>
    <?php } ?>
</ul>
<ul class="pagination">
    {!! $users->render() !!}
</ul>