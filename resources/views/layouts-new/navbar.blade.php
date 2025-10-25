<!-- navigator -->
<nav class="navbar navbar-expand-lg bg-primary rounded m-0 mb-1" data-theme="dark" style="--bg-opacity: .25;">
	<div class="container-fluid">
		<a class="navbar-brand" href="@auth{{ url('/dashboard') }}@else{{ url('/') }}@endauth">
			<!-- {!! config('app.name') !!} -->
			<img src="{{ asset('images/logo.png') }}" class="img-fluid rounded" alt="Home" width="40%">
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarColor01">
			<ul class="navbar-nav me-auto">
				<li class="nav-item">
						<!-- <a class="nav-link" href="
							@auth
								{{ url('/dashboard') }}
							@else
								{{ url('/') }}
							@endauth
						">
							@auth
								Dashboard
							@else
								Home
							@endauth
							<span class="visually-hidden">(current)</span>
						</a> -->
				</li>


				@auth
					@include('layouts.navigation')
				@else
					<!-- nav for guest -->
				@endauth
			</ul>
			@if (Route::has('login'))
				@auth
					<div class="dropdown me-5">
						<a href="#" class="btn btn-sm btn-outline-secondary dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							@if(\Auth::user()->belongstostaff->unreadNotifications->count())<span class="badge text-bg-warning">{{\Auth::user()->belongstostaff->unreadNotifications->count()}}</span>@endif
              {{ Auth::user()->belongstostaff->name }}
            </a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="{{ route('profile.show', Auth::user()->belongstostaff->id) }}"><i class="fa-regular fa-user"></i> Profile</a></li>
							<li>
								<!-- notification -->
								@include('layouts.notification')
							</li>
							<li><a class="dropdown-item" href="{{ route('leave.index') }}"><i class="fa-solid fa-mug-hot"></i> Apply Leave</a></li>
							<li><a class="dropdown-item" href="{{ route('outstationattendance.index') }}"><i class="fa-solid fa-user-plus"></i> Outstation Attendance</a></li>
							<li><a class="dropdown-item" href="{{ route('appraisalmark.index') }}"><i class="fa-solid fa-list"></i> Appraisal</a></li>
							<li>
								<form method="POST" action="{{ route('logout') }}">
									@csrf
									<a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fas fa-light fa-right-from-bracket"></i> Log Out</a>
								</form>
							</li>
						</ul>
					</div>

				@else
					<a class="btn btn-sm btn-secondary {{ (Route::has('register'))?'me-1':'me-5' }}" href="{{ route('login') }}">Sign in</a>
					@if (Route::has('register'))
						<!-- <a href="{{ route('register') }}" class="btn btn-sm btn-secondary me-5">Sign up</a> -->
					@endif
				@endauth
			@endif
			<!-- <form class="d-flex">
				<input class="form-control me-sm-2" type="search" placeholder="Search">
				<button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
			</form> -->
		</div>
	</div>
</nav>
<!-- end navigation -->

<!-- 2nd nav -->
<!-- <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
	<div class="container-fluid">
		<a class="navbar-brand" href="#">Navbar</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarColor01">
			<ul class="navbar-nav me-auto">
				<li class="nav-item">
					<a class="nav-link active" href="#">Home
						<span class="visually-hidden">(current)</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#">Features</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#">Pricing</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#">About</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Dropdown</a>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="#">Action</a>
						<a class="dropdown-item" href="#">Another action</a>
						<a class="dropdown-item" href="#">Something else here</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#">Separated link</a>
					</div>
				</li>
			</ul>
			<form class="d-flex">
				<input class="form-control me-sm-2" type="search" placeholder="Search">
				<button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
			</form>
		</div>
	</div>
</nav> -->
<!-- 2nd nav end -->
