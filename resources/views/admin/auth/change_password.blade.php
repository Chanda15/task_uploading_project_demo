@section('title') 
Change Password
@endsection
@extends('admin.layouts.main')
@section('style')

@endsection 
@section('rightbar-content')
<!-- Start XP Breadcrumbbar -->                    
<div class="xp-breadcrumbbar text-center">
    <h4 class="page-title">Change Password</h4>  
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Account</a></li>
        <li class="breadcrumb-item active" aria-current="page">Change Password</li>
      </ol>            
</div>
<!-- End XP Breadcrumbbar -->
<!-- Start XP Contentbar -->    
<div class="xp-contentbar">
    <!-- Start XP Row -->
    <div class="row">
        <!-- Start XP Col -->
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header bg-white">
                    <h5 class="card-title text-black">Profile</h5>
                    <h6 class="card-subtitle">To update profile details.</h6>
                </div>
                <div class="card-body">
					@if(session()->has('ChangePassword'))
					<div class="xp-alert">
						<div class="alert alert-success" role="alert">
						{{trans('auth.ChangePassword')}}
						</div>								 
					</div>
					@endif
                    <form method="post" action="{{url('submit_change_profile')}}" >
					@csrf
						<div class="form-row">
							<div class="form-group col-md-6">
							  <label for="inputEmail4">Password</label>
							 <input type="password" class="form-control" name="Password" id="Password" placeholder="Password" >
												<div class="invalid-data invalid-Password"></div>
							</div>
							<div class="form-group col-md-6">
							  <label for="inputPassword4">Conform Password</label>
							  <input type="password" class="form-control" name="ConfirmPassword" id="ConfirmPassword" placeholder="Conform Password" >
								<div class="invalid-data invalid-ConfirmPassword"></div>
							</div>
						</div>
						<input type="hidden" class="form-control" name="Email" id="Email" value="{{$User->email_id}}">
                      <button type="submit" onclick="return validateData();" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- End XP Col -->
    </div>    
    <!-- End Row -->
</div>
<!-- End XP Contentbar -->
@endsection
@section('script')
<script>
function validateData(){
	var flag = false;
	$('.invalid-data').html('');
	var Password = $('#Password').val();
	var ConfirmPassword = $('#ConfirmPassword').val();
	if(Password!=ConfirmPassword){
		$('.invalid-ConfirmPassword').html("{{ trans('auth.ConfirmPasswordError') }}");
		$('#ConfirmPassword').focus();
		flag = true;
	}
	if(ConfirmPassword.length==''){
		$('.invalid-ConfirmPassword').html("{{ trans('auth.ConfirmPasswordError') }}");
		$('#ConfirmPassword').focus();
		flag = true;
	}
	if(Password.length==''){
		$('.invalid-Password').html("{{ trans('auth.PasswordError') }}");
		$('#Password').focus();
		flag = true;
	}
	
	if(flag)
		return false;
}
</script>
@endsection 