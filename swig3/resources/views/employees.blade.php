@foreach ($employees as $employee)
    <li><a href=''> <span class="pull-right"></span>{{$employee->username}}</a></li>
@endforeach
