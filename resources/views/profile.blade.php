@extends('layouts.main')
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-menu icon-gradient bg-ripe-malin"> </i>
            </div>
            <div>
                {{ $maintitle }}
                <div class="page-title-subheading">This dashboard was created as an example of the flexibility that Architect offers.</div>
            </div>
        </div>
    </div>
</div>

<div id="profileVue">
    <div class="tabs-animation">
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card" style="min-height: 250px;">
                  @include('blocks.skeleton') 
                  <form action="profile/{{ $id }}" method="post" enctype='multipart/form-data'>
                    @csrf
                    <div class="g-3 col" style="max-width: 600px" id="idbio">
                      <div class="mt-3">
                        <label for="email" class="form-label">Email</label>
                        <input value="{{$biodata->email}}" disabled type="email" class="form-control" id="email">
                      </div>
                      <div class="mt-3">
                        <label for="username" class="form-label">Username</label>
                        <input value="{{$biodata->username}}" disabled type="username" class="form-control" id="username">
                      </div>
                      <div class="mt-3">
                        <label for="nama" class="form-label">Name</label>
                        <input value="{{$biodata->name}}" required type="name" class="form-control" name="name" id="name">
                      </div>
                      <div class="mt-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input value="{{$biodata->phone}}" required type="phone" class="form-control" name="phone" id="phone">
                      </div>
                      <div class="mt-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input value="{{$biodata->address}}" type="text" class="form-control" name="address" id="address">
                      </div>

                      <div class="mt-3">
                        <label for='password_edit'>Password Edit Access</label>
                        <input type='text' class='form-control' value="{{ $biodata->password_edit }}" name='password_edit' id='password_edit'>
                      </div>
                      <div class="mt-3">
                        <label for='password_edit'>Password Edit Timeout (in second)</label>
                        <input type='number' class='form-control' value="{{ $biodata->password_edit_timeout }}" name='password_edit_timeout' id='password_edit_timeout'>
                        <small>eg. 1 minute = 60 seconds.</small>
                      </div>
                      <div class="mt-3">
                        <label for='photos'>Photo <small>*only for change</small></label>
                        <input type='file' class='form-control' value="" name='photos' id='photos'>
                      </div>

                    </div>
                    <div class="g-3 col" style="max-width: 600px" id="idpass">
                      <div class="mt-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="password" pattern="^\S{6,}$"
                        onchange="this.setCustomValidity(this.validity.patternMismatch ?
                        'Must have at least 6 characters' : ''); 
                        if(this.checkValidity()) form.repassword.pattern = this.value;
                        ">
                      </div>
                      <div class="mt-3">
                        <label for="repassword" class="form-label">Re-Type</label>
                        <input type="password" class="form-control" name="repassword" id="repassword" pattern="^\S{6,}$"
                        onchange="this.setCustomValidity(this.validity.patternMismatch ?
                        'Please enter the same Password as above' : '');
                        ">                        
                        </div>                      
                    </div>
                    <div class="col mt-3 mb-3 g-3">
                      <button style="width: 150px" type="button" id="btnEdit" onclick="editBio()" class="btn btn-primary">Edit Profile</button>
                      <button style="width: 150px" type="button" id="btnChangePass" onclick="changePass()" class="mt-2 btn btn-primary">Change Password</button>
                      <button style="width: 100px" type="submit" id="btnSave" class="btn btn-primary">Save</button>
                      <button style="width: 100px" type="button" id="btnCancel" onclick="normalView()" class="mt-2 btn btn-primary">Cancel</button>
                    </div>

                  </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
      setTimeout(() => {
        $('input [type="password"]').val('');
      }, 500);
    })
</script>

<script>
  x = document.getElementById("idbio");
  y = document.getElementById("idpass");

  btnEdit = document.getElementById("btnEdit");
  btnPass = document.getElementById("btnChangePass");
  btnSave = document.getElementById("btnSave");
  btnCancel = document.getElementById("btnCancel");

  inputNama = document.getElementById("name");
  inputPhone = document.getElementById("phone");
  inputAddress = document.getElementById("address");

  function normalView() {
    x.style.display = "block"
    btnEdit.style.display = "block"
    btnPass.style.display = "block"
    y.style.display = "none"
    btnSave.style.display = "none"
    btnCancel.style.display = "none"

    inputNama.disabled = true
    inputPhone.disabled = true
    inputAddress.disabled = true
  }

  function changePass() {
    x.style.display = "none"
    btnEdit.style.display = "none"
    btnPass.style.display = "none"
    y.style.display = "block"
    btnSave.style.display = "block"
    btnCancel.style.display = "block"   
  }

  function editBio() {
    x.style.display = "block"
    btnEdit.style.display = "none"
    btnPass.style.display = "none"
    y.style.display = "none"
    btnSave.style.display = "block"
    btnCancel.style.display = "block"   
    
    inputNama.disabled = false
    inputPhone.disabled = false
    inputAddress.disabled = false
  }
  
  normalView()

</script>

@endsection
