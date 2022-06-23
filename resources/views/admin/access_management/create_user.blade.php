@section('title') 
Create User
@endsection
@extends('admin.layouts.main')
@section('style')
<!-- Select2 CSS -->
<link href="{{ admin_url.'assets/plugins/select2/select2.min.css' }}" rel="stylesheet" type="text/css" />
@endsection 
@section('rightbar-content')
<!-- Start XP Breadcrumbbar -->                    
<div class="xp-breadcrumbbar text-center">
    <h4 class="page-title">Create User</h4>  
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Account</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create User</li>
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
                    <h5 class="card-title text-black">Create User</h5>
                </div>
                <div class="card-body">
					@if(session()->has('UpdateProfile'))
					<div class="xp-alert">
						<div class="alert alert-success" role="alert">
						{{trans('auth.UpdateProfile')}}
						</div>								 
					</div>
					@endif
                    <form method="post" action="{{url('submit_user')}}" >
					@csrf
						<div class="form-row">
							<div class="form-group col-md-4">
							  <label for="inputEmail4">Name</label>
							  <input type="text" class="form-control" name="Username" id="Username" placeholder="Name" value="">
							  <div class="invalid-data invalid-Username"></div>
							</div>
							<div class="form-group col-md-4">
							  <label for="inputPassword4">Email</label>
							  <input type="email" class="form-control" name="Email" id="Email" placeholder="Email" value="">
							  <div class="invalid-data invalid-Email"></div>
							</div>
							<div class="form-group col-md-4">
								<label for="inputState">Role</label>
								<select name="Role" class="form-control" id="Role" readonly>
									<option value="">Select Role</option>
									@foreach($RoleList as $RList)
									<option value="{{$RList->id}}" >{{$RList->name}}</option>
									@endforeach
								</select>
								<div class="invalid-data invalid-Role"></div>
							</div>
						</div>
						<div class="row">
							
							<div class="col-md-4">
								<div class="form-group">
									<label for="Password">Password</label>
									<input type="password" class="form-control" name="Password" id="Password" placeholder="Password" >
									<div class="invalid-data invalid-Password"></div>
								</div>
							</div>
							<div class="col-md-4">
								 <div class="form-group">
									<label for="ConfirmPassword">Conform Password</label>
									<input type="password" class="form-control" name="ConfirmPassword" id="ConfirmPassword" placeholder="Conform Password" >
									<div class="invalid-data invalid-ConfirmPassword"></div>
								</div>
							</div>
							<div class="col-md-4 ">
								 <div class="form-group">
									<label for="MobileNo">Mobile No</label>
									<input type="MobileNo" class="form-control" name="MobileNo" id="MobileNo" placeholder="Mobile No" value="{{$User->mobile_no}}">
									<div class="invalid-data invalid-MobileNo"></div>
								</div>
							</div>
							
						</div>
						<div class="form-row ">
							<div class="col-md-4 ">
								 <div class="form-group">
									<label for="UserAddress">Address</label>
									<textarea class="form-control" name="UserAddress" id="UserAddress" placeholder="Address" >{{$User->user_address}}</textarea>
									<div class="invalid-data invalid-UserAddress"></div>
								</div>
							</div>
							<div class="col-md-4" >
								<div class="form-group" >
									<div class="form-check" >
									  <input class="form-check-input" type="checkbox" id="Status" name="Status" value="1" checked>
									  <label class="form-check-label" for="gridCheck">
										Active
									  </label>
									</div>
								</div>
							</div>
							
						</div>
						
					  <div class="form-group col-md-12 selectPermissionBox" style="display:none;" >
							  <h4>Choose Permission </h4>
							</div>
							<div class="form-group col-md-12 selectPermissionBox"  style="display:none; border: 1px solid #ddd; padding: 10px; box-shadow: 5px 10px 18px #888888;">
							  @foreach($Permissions as $key => $val)
								<div class="row" style="margin-bottom:10px;">
									<div class="col-md-12">
										<h5 style="border-bottom:1px solid #ddd;">{{$val['menu_name']}}</h5>
										<div class="row">
										@foreach($val['sub_permissions'] as $keys => $vals)
											
												<div class="form-group col-md-3">
													<div class="form-check">
													  <input class="form-check-input" type="checkbox"  onchange="return getPermissionValue();" name="SubPermission[]" value="{{$vals['id']}}" {{($vals['key_name']=='view-press-release-management-menu' || $vals['key_name']=='view-press-release-management-list' || $vals['key_name']=='edit-press-release-management' || $vals['key_name']=='view-report-management-menu' || $vals['key_name']=='view-report-management-list' || $vals['key_name']=='edit-report-management')?'checked':''}}>
													  <label class="form-check-label" for="gridCheck">{{$vals['display_name']}}</label>
													</div>
												
												</div>
										
										@endforeach
										</div>
									</div>
								</div>
								
							  @endforeach
							</div>
							
					  <input type="hidden" id="sub_permission_change" name="sub_permission_change" value="0">
					  <input type="hidden" id="hid_id" name="hid_id" value="{{$User->id}}">
                      <button type="submit" onclick="return validateData();" class="btn btn-primary">Submit</button>
					  <a href="{{url('/user-management')}}" class="btn btn-secondary">Back</a>
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
<!-- Select2 JS -->
<script src="{{ admin_url.'assets/plugins/select2/select2.min.js' }}"></script>
<script src="{{ admin_url.'assets/js/init/form-select-init.js' }}"></script>
<script>
function getPermissionValue(){
	$('#sub_permission_change').val(1);
}

function validateData(){
	var flag = false;
	$('.invalid-data').html('');
	var Username = $('#Username').val();
	var Email = $('#Email').val();
	var Role = $('#Role').val();
	
	if(Role==''){
		$('.invalid-Role').html("{{ trans('auth.RoleError') }}");
		$('#Role').focus();
		flag = true;
	}
	
	if(Email.length!=''){
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		if (reg.test(Email) == false) 
		{
		   $('.invalid-Email').html("{{ trans('auth.EmailError') }}");
			$('#Email').focus();
			flag = true;
		}
	}
	if(Email.length==''){
		$('.invalid-Email').html("{{ trans('auth.EmailError') }}");
		$('#Email').focus();
		flag = true;
	}
	if(Username.length==''){
		$('.invalid-Username').html("{{ trans('auth.NameError') }}");
		$('#Username').focus();
		flag = true;
	}
	
	if(flag)
		return false;
}
</script>
@endsection 