@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                     <!-- Load Font Awesome Icon Library -->
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

                    <!-- Buttons to choose list or grid view -->
                    <button onclick="listView()"><i class="fa fa-bars"></i> List</button>
                    <button onclick="gridView()"><i class="fa fa-th-large"></i> Grid</button>

                    <div class="row">
                    <div class="column" style="background-color:#aaa;">
                        <h2>Column 1</h2>
                        <p>Some text..</p>
                    </div>
                    <div class="column" style="background-color:#bbb;">
                        <h2>Column 2</h2>
                        <p>Some text..</p>
                    </div>
                    </div>

                    <div class="row">
                    <div class="column" style="background-color:#ccc;">
                        <h2>Column 3</h2>
                        <p>Some text..</p>
                    </div>
                    <div class="column" style="background-color:#ddd;">
                        <h2>Column 4</h2>
                        <p>Some text..</p>
                    </div>
                    </div> 
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection
