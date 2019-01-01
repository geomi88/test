@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Update <span>Unit</span></h1>
    </header>	

    <script>
        $(document).ready(function ()
        {
            var unit_type = $('#unisess').val();
            if (unit_type == 'SIMPLE')
            {

                $('.simpleunitsection').show();
                $('.compoundunitsection').hide();
            }
            else
            {

                $('.simpleunitsection').hide();
                $('.compoundunitsection').show();
            }

            //////////////////////simple unit///////////////////////////
            $("#sunitsinsertion").validate({
                errorElement: "span",
                errorClass: "commonError",
                highlight: function (element, errorClass) {
                    $(element).addClass('valErrorV1');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass("valErrorV1");
                },
                rules: {
                    name: {
                        required: true,
                        remote:
                                {
                                    url: "../checkunits",
                                    type: "post",
                                    data:
                                            {
                                                name: function () {
                                                    return $("#name").val();
                                                },
                                                formal_name: function () {
                                                    return $("#formal_name").val();
                                                },
                                                decimal_value: function () {
                                                    return $("#decimal_value").val();
                                                },
                                                unit: function () {
                                                    return $("#unit").val();
                                                },
                                                cid: function () {
                                                    return $("#cid").val();
                                                }
                                            },
                                    dataFilter: function (data)
                                    {
                                        var json = JSON.parse(data);
                                        if (json.msg == "true") {
                                            //return "\"" + "That Company name is taken" + "\"";
                                            $('.sunitName').addClass('ajaxLoaderV1');
                                            $('.sunitName').removeClass('validV1');
                                            $('.sunitName').addClass('errorV1');
                                            // valid="false"
                                        }
                                        else
                                        {
                                            $('.sunitName').addClass('ajaxLoaderV1');
                                            $('.sunitName').removeClass('errorV1');
                                            $('.sunitName').addClass('validV1');
                                            //valid="true";
                                            return true;
                                        }
                                    }
                                }

                    },
                    formal_name: {
                        required: true
                    },
                    decimal_value:
                            {
                                required: true
                            }


                },
//                submitHandler: function () {
//                    form.submit();
//                },
                messages: {
                    name: "Enter Unit name",
                    formal_name: "Enter Formal Name",
                    decimal_value: "Enter Decimal Value",
                }
            });
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /////////////////////////////////compound unit////////////////////////////////////////////////////////////////////////////
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $("#cunitsinsertion").validate({
                errorElement: "span",
                errorClass: "commonError",
                highlight: function (element, errorClass) {
                    $(element).addClass('valErrorV1');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass("valErrorV1");
                },
                rules: {name: {required: true, },
                    from: {required: true},
                    to: {required: true},
                    conversion_value: {required: true}

                },
//                submitHandler: function () {
//                    form.submit();
//                },
                messages: {
                    conversion_value: "Enter Conversion Value",
                    from: "Select From Unit",
                    to: "Select To Unit",
                }
            });
        });
    </script>

    @foreach ($all_units as $all_unit)
    @endforeach
    <div class="fieldGroup" id="fieldSet1">

        <input type="hidden" name="unisess" id="unisess" value="{{$data['sess']}}">
        <div class="custRow">
            <!--                <div class="custCol-4">
                                <div class="inputHolder bgSelect">
                                    <label>Choose Type</label>
                                    <select class="commoSelect" name="unit_type" id="unit_type">
                                        <option <?php echo ($data['sess'] == "SIMPLE") ? "selected" : "" ?> value='SIMPLE'>SIMPLE</option>
                                        <option <?php echo ($data['sess'] == "COMPOUND") ? "selected" : "" ?> value='COMPOUND'>COMPOUND</option>
                                    </select>
                                </div>
                            </div>-->
            <div class="simpleunitsection">
                <form action="{{ action('Masterresources\UnitsController@supdate') }}" method="post" id="sunitsinsertion">
                    <div class="custCol-4">
                        <div class="inputHolder sunitName">
                            <label>Name</label>
                            <input type="hidden" name="cid" id="cid" value="{{ $all_unit->id }}">
                            <input type="hidden" name="unit" id="unit" value="SIMPLE">
                            <input type="text" name="name" id="name" value='{{ $all_unit->name }}' onpaste="return false;" autocomplete="off" placeholder="Enter Name">
                            <span class="commonError"></span>
                        </div>
                    </div>

                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Formal Name</label>
                            <input type="text" name="formal_name" id="formal_name" value='{{ $all_unit->formal_name }}'>
                        </div>
                    </div>

                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Decimal Value</label>
                            <input type="text" name="decimal_value" id="decimal_value" value='{{ $all_unit->decimal_value }}'>
                        </div>
                    </div>

                    <div class="custRow">
                        <div class="custCol-4">
                            <input type="submit" value="Update" class="commonBtn bgGreen addBtn addunit">
                        </div>
                    </div>
                </form>
            </div>
            <div class="compoundunitsection">
                <form action="{{ action('Masterresources\UnitsController@cupdate') }}" method="post" id="cunitsinsertion">
                    @foreach ($coumpound_units as $coumpound_unit) 
                    <div class="inputHolder bgSelect">
                        <div class="custCol-4">
                            <label>Choose unit</label>
                            <input type="hidden" name="ccid" id="ccid" value="{{ $coumpound_unit->id }}">                           
                            <input type="hidden" name="unit" id="unit" value="COMPOUND">
                            <select class="commoSelect" name="from" id="from">
                                <option value=''>Select Unit</option>
                                @foreach ($simple_units as $simple_unit)
                                <option <?php echo ($coumpound_unit->from == $simple_unit->id) ? "selected" : "" ?>  value='{{ $simple_unit->id }}'>{{ $simple_unit->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="inputHolder bgSelect">
                        <div class="custCol-4">
                            <label>Choose Unit</label>
                            <select class="commoSelect" name="to" id="to">
                                <option value=''>Select Unit</option>
                                @foreach ($simple_units as $simple_unit)
                                <option <?php echo ($coumpound_unit->to == $simple_unit->id) ? "selected" : "" ?>  value='{{ $simple_unit->id }}'>{{ $simple_unit->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                 

                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Conversion Value</label>
                            <input type="text" name="conversion_value" value='{{ $coumpound_unit->conversion_value }}' id="conversion_value" placeholder="Enter Conversion Value">
                            <span class="commonError"></span>
                        </div>                         
                    </div>
                    @endforeach       
                    <div class="custRow">
                        <div class="custCol-4">
                            <input type="submit" value="Update" class="commonBtn bgGreen addBtn addunit">
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div>

</div>

@endsection
