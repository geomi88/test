@extends('layout.admin.menu')
@section('content')
@section('title', 'Architects')

        <div class="adminPageHolder adminAddPropertyHolder">

				<div class="mainBoxHolder">
					<div class="row">
						<div class="col-6 text-capitalize">
							<h2 class="mt-2">Architects</h2>
						</div>
						<div class="col-6 text-right">
							<a class="addPropertyBtn" href="<?php echo url('/'); ?>/admin/add-architect">
								<figure>
									<img class="mr-1" src="{{URL::asset('admin')}}/images/iconPlus.png"> add new architect
								</figure>
							</a>
						</div>
					</div>
					<hr>

					<table class="table text-capitalize tableStyleHolder mt-3" >
						<tr>
							<th>Sl.No</th>
							<th>name</th>
							<th>email</th>
							<th>phone</th>
							<th>address</th>

							<th>actions</th>
						</tr>
                        <tbody id="property-list">
						<?php $n = $architects->perPage() * ($architects->currentPage() - 1);?>
							@foreach ($architects as $architect)
							<tr>
								<td>{{++$n}}</td>
								<td>{{$architect->name_en}}</td>
								<td>{{$architect->email}}</td>
								<td>{{$architect->phone}}</td>
								<td>{{$architect->address_en}}</td>

								<td>

        <a class="actnIcons" href="{{ URL::to('admin/architect-view', ['id' => Crypt::encrypt($architect->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconView.png"></figure>
        </a>
        <a class="actnIcons" onclick="return confirm('Are You sure?')" href="{{ URL::to('admin/architect-remove', ['id' => Crypt::encrypt($architect->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconDel.png"></figure>
        </a>
        <a class="actnIcons" href="{{ URL::to('admin/architect-edit', ['id' => Crypt::encrypt($architect->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconEdit.png"></figure>
        </a>
    </td>
</tr>
@endforeach
<?php if ($architects->lastPage() > 1) {?>
<tr>
    <td colspan='8'>

    <div class="mt-1 clearfix">
        {!! $architects->render() !!}
    </div>
    </td>
</tr>

<?php }?>
						</tbody>
					</table>

				</div>
			</div>

@endsection
