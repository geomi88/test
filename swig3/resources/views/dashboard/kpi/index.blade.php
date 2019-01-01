@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Current <span>Performance</span></h1>
    </header>

    <div class="currentPerformHandle">
        <div class="performList {{$trainingGrade['class']}}">
            <span>Learning</span>
            <div class="table">
                <div class="tableCell">{{$trainingGrade['grade']}}</div>
            </div>
        </div>
        
        <div class="performList {{$workGrade['class']}}">
            <span>Working Level</span>
            <div class="table">
                <div class="tableCell">{{$workGrade['grade']}}</div>
            </div>
        </div>
        
        <div class="performList {{$todoGrade['class']}}">
            <span>To Do Level</span>
            <div class="table">
                <div class="tableCell">{{$todoGrade['grade']}}</div>
            </div>
        </div>

        <div class="performanceLevel">
            <div class="greenHandle greenDark">
                <strong>A</strong>
                <div class="graphLineTop">
                    <em>100%</em>
                    <span>100%</span>
                </div>
                <div class="graphLine">
                    <em>90%</em>
                    <span>90%</span>
                </div>
                <p class="english">Exceptional</p>
                <p class="arabic">Exceptional</p>
            </div>
            <div class="lightGreenHandle greenLight">
                <strong>B</strong>
                <div class="graphLine">
                    <em>70%</em>
                    <span>70%</span>
                </div>
                <p class="english">Effective</p>
                <p class="arabic">Effective</p>
            </div>
            <div class="yellowHandle yellow">
                <strong>C</strong>
                <div class="graphLine">
                    <em>50%</em>
                    <span>50%</span>
                </div>
                <p class="english">Inconsistent</p>
                <p class="arabic">Inconsistent</p>
            </div>
            <div class="orangeHandle orange">
                <strong>D</strong>
                <div class="graphLine">
                    <em>40%</em>
                    <span>40%</span>
                </div>
                <p class="english">Unsatisfactory</p>
                <p class="arabic">Unsatisfactory</p>
            </div>
            <div class="redHandle red">
                <strong>E</strong>
                <div class="graphLine">
                    <em>0%</em>
                    <span>0%</span>
                </div>
                <p class="english">Not Acceptable</p>
                <p class="arabic">Not Acceptable</p>
            </div>
        </div>
    </div>
</div>
<div class="customClear"></div>
<script>
    $(document).ready(function () {

    });
</script>
@endsection