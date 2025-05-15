@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/css/pages/teams.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/tagify/tagify.js')}}"></script>
<script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('res/js/form-wizard-numbered.js')}}"></script>
<script src="{{asset('res/js/forms-file-upload.js')}}"></script>
<script src="{{asset('res/js/forms-selects.js')}}"></script>
<script src="{{asset('res/js/forms-tagify.js')}}"></script>
<script src="{{asset('res/js/forms-typeahead.js')}}"></script>
<script src="{{asset('res/js/forms-pickers.js')}}"></script>
<script src="{{asset('res/js/forms-extras.js')}}"></script>
<script src="{{asset('res/js/forms-tagify.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Edit the Team Account') }}</span>
</h4>

<form action="{{ route('users.update',$user) }}" method="POST">
@csrf
@method('PATCH')
<div class="row">
	<div class="col-md-12 mb-4">
		<div class="card">
			<div class="card-body">
				<div class="mb-3">
				  <label for="username" class="form-label">{{ __('Username') }}</label>
				  <input type="text" autocomplete="false" class="form-control" id="username" name="username" value="{{ $user->username }}" placeholder="admin" />
                    @error('username')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
				</div>
				<div class="mb-2">
				  <label for="password" class="form-label">{{ __('Password (fill in if you need change)') }}</label>
				  <input class="form-control" autocomplete="false" id="password" type="password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
			</div>
		</div>
	</div>
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-body">
				<div class="row d-flex w-100 align-self-center">
					<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
						<div class="row align-self-center h-100">
							<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
								<div class="d-flex justify-content-center mb-4">
								  <div class="settings_icon bg-label-primary">
									  <i class="bx bxs-hand"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Grant All Privileges to this Team Account?') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This account will have access to every element of the dashboard.') }}"></i>
								</h4>
								<div class="mb-3 col-md-10">
								<p class="card-text">{{ __('This account will have access to every element of the dashboard (super user).') }}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
						<label class="switch switch-square" for="is_admin">
						  <input type="checkbox" class="switch-input" id="is_admin" {{ $userRules['isAdmin'] ? 'checked' : ''}} onclick="makeAdmin(event)" />
						  <span class="switch-toggle-slider">
							<span class="switch-on"></span>
							<span class="switch-off"></span>
						  </span>
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-body mt-2" style="padding: 0;">
				<div class="table-responsive text-nowrap mb-2">
				  <table class="table table-striped">
					<thead>
					  <tr>
						<th class="text-nowrap">{{ __('Type') }}</th>
						<th class="text-nowrap text-center">👀 {{ __('View') }}</th>
						<th class="text-nowrap text-center">📋 {{ __('Create/Edit') }}</th>
						<th class="text-nowrap text-center">🗑️ {{ __('Delete') }}</th>
					  </tr>
					</thead>
					<tbody>
					@for($i = 0; $i < count($rules); $i++)
					  <tr>
						<td>{{$rules[$i][0]}}</td>
						<td>
							<div class="d-flex justify-content-center">
								<label class="orange-checkbox-container">
								  <input type="checkbox" class="ruleCheckbox" name="{{$rules[$i][1]}}[read]" {{ ($userRules['isAdmin'] || (isset($userRules[$rules[$i][1]]) && $userRules[$rules[$i][1]]['read'])) ? 'checked' : '' }}>
								  <span class="checkmark"></span>
								</label>
							</div>
						</td>
						<td>
							<div class="d-flex justify-content-center">
								<label class="orange-checkbox-container">
								  <input type="checkbox" class="ruleCheckbox" name="{{$rules[$i][1]}}[write]" {{ ($userRules['isAdmin'] || (isset($userRules[$rules[$i][1]]) && $userRules[$rules[$i][1]]['write'])) ? 'checked' : ''}}>
								  <span class="checkmark"></span>
								</label>
							</div>
						</td>
						<td>
							<div class="d-flex justify-content-center">
								<label class="orange-checkbox-container">
								  <input type="checkbox" class="ruleCheckbox" name="{{$rules[$i][1]}}[del]" {{ ($userRules['isAdmin'] || (isset($userRules[$rules[$i][1]]) && $userRules[$rules[$i][1]]['del'])) ? 'checked' : ''}}>
								  <span class="checkmark"></span>
								</label>
							</div>
						</td>
					  </tr>
					@endfor
					</tbody>
				  </table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row mb-4">
	<div class="d-grid gap-2 col-lg-12 mx-auto">
       <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ __('Update an Account') }}</button>
    </div>
</div>
</form>

<script type="text/javascript">
  const ruleCheckboxes = document.querySelectorAll(".ruleCheckbox");
  const adminCheckbox = document.querySelector("#is_admin");
  function makeAdmin(e){
    if (e.target.checked){
      for (var i = 0; i < ruleCheckboxes.length; i++) {
        ruleCheckboxes[i].checked = true;
      }
    } else {
        for (var i = 0; i < ruleCheckboxes.length; i++) {
            ruleCheckboxes[i].checked = false;
        }
    }
  }

  for (var i = 0; i < ruleCheckboxes.length; i++) {
    ruleCheckboxes[i].addEventListener("change", function (el) {
      if (!el.target.checked) adminCheckbox.checked = false;
    });
  }
</script>
@endsection
