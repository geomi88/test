@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Upload<span> Documents</span></h1>
    </header>
    
<script>
    $(document).ready(function()
    {       
        $("#docsinsertion").validate({
                    errorElement: "span",
                    errorClass: "commonError",
                    highlight: function(element, errorClass){
                                $(element).addClass('valErrorV1');
                            },
                            unhighlight: function(element, errorClass, validClass){
                                $(element).removeClass('valErrorV1');
                            },

                    rules: {

                        name:{required: true },
                        document_type:{required: true},
                        docs:{required: true}                       
                        },
                        submitHandler: function() {  form.submit(); },
                        messages: {
                        name: "Enter File name",
                        document_type: "Select Document Type",
                        docs: "Choose File",
                         }
                    });
});
</script>
    <form action="{{ action('Masterresources\DocsController@store') }}" method="post" id="docsinsertion" enctype="multipart/form-data">
         {{ csrf_field() }}
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">                
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>File Name</label>
                        <input type="text" name="name" id="name" placeholder="Enter File Name" >
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" placeholder="Enter Alias Name">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custCol-4">
                <div class="inputHolder bgSelect">
                    <label>Document Type</label>
                    <select class="commoSelect" name='document_type'>
                        <option  value=""> Select Document Type </option>
                        <option value='PLANNING'>Planning</option>                            
                        <option value='RECIPE'>Standard Recipe</option>
                        <option value='KPI'>Kpi</option>
                        <option value='CHART'>Chart</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 uploadfile">
                    <span class="commonError"></span>
                <input type="file" class="docs" name="docs"  accept=".doc, .docx.,.ppt, .pptx,.pdf"/>                 
                </div>                
            </div>
            <div class="col-md-12">
                <input type="submit" class="commonBtn bgGreen  addBtn adddoc" Value="Upload">
                </div>
        </div>
    </form>
</div>
@endsection
