@if(\Auth::user()->belongstostaff->unreadNotifications->count())
	@foreach(\Auth::user()->belongstostaff->unreadNotifications as $v)
		<li>
			<a class="dropdown-item" href="{{ $v->data['link'] }}">
				<i class="fa-regular fa-comment"></i>
				{{ $v->data['data'] }}
			</a>
		</li>
	@endforeach
@endif
<a class="dropdown-item" href="#"><i class="fa-regular fa-comment"></i>Notifications (MarkAsRead in pdf-layout.blade)</a>
