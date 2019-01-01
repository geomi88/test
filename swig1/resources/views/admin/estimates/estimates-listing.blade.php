@extends('layout.admin.menu')
@section('content')
@section('title', 'Estimates')
			<div class="adminPageHolder adminAddPropertyHolder">
				<div class="mainBoxHolder">
					<div class="row">
						<div class="col-6 text-capitalize">
							<h2 class="mt-2">Estimates</h2>
						</div>
						<div class="col-6 text-right">
							<!-- <a class="addPropertyBtn" href="javascript:void(0);">
								<figure>
									<img class="mr-1" src="images/iconPlus.png"> add new Neighbourhood
								</figure>
							</a> -->
							<!-- <button type="button" class="fltrIconStyle">
								<figure><img src="images/filter.png"></figure>
							</button>
							<button type="button" class="fltrIconStyle">
								<figure><img src="images/sort.png"></figure>
							</button> -->
						</div>
					</div>
					<!-- <hr> -->
					<table class="table text-capitalize tableStyleHolder mt-3">
						<tr>
							<th>SI.No</th>
							
							<th>from</th>
							<th>district</th>
							<th>email</th>
							<th>Tele Phone</th>
							<th>actions</th>
						</tr>
						<?php $n = $estimates->perPage() * ($estimates->currentPage() - 1);?>
							@foreach ($estimates as $estimate)
						<tr>
							<td>{{++$n}}</td>
							<td>{{$estimate->first_name}} {{$estimate->last_name}}</td>
							<td>{{$estimate->district}}</td>
							<td>{{$estimate->email}}</td>
							<td>{{$estimate->tele_phone}}</td>
							<td>
								<a class="actnIcons" href="{{ URL::to('admin/estimate-detail-view', ['id' => Crypt::encrypt($estimate->id)]) }}">
									<figure><img src="images/iconView.png"></figure>
								</a>
								<a class="actnIcons" href="{{ URL::to('admin/estimate-remove', ['id' => Crypt::encrypt($estimate->id)]) }}">
									<figure><img src="images/iconDel.png"></figure>
								</a>

							</td>
						</tr>
						@endforeach
						<?php if ($estimates->lastPage() > 1) {?>
						<tr>
							<td colspan='8'>

							<div class="mt-1 clearfix">
								{!! $estimates->render() !!}
							</div>
							</td>
						</tr>

						<?php }?>	
					</table>
					
				</div>
			</div>
@endsection
