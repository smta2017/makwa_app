<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content={{csrf_token()}}>

    <title>Makwa - Reset Password</title>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
    
</head>
<body class="hold-transition register-page">
    @if (isset($success))
   <div class="alert alert-info">{{ $success }}</div>
@endif
  <div class="register-box">
    <div class="register-logo">
      
    </div>
    <div class="row align-items-center mt-5">
        <div class="offset-md-3 col-md-6">
            <div class="card">
        <div class="card-header">Makwa - Reset Password</div>
      <div class="card-body register-card-body">
        <p class="login-box-msg">{{ __('Reset Password') }}</p>

        <form action="{{route('resetPassword')}}" method="get" id="resetForm">
          {{ csrf_field() }}
          
          <input type="hidden" name="email" value="{{$email}}">
          <div class="input-group mb-3">
            <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>
            <div class="input-group-append">
              <span class="fa fa-lock input-group-text"></span>
            </div>
          </div>
          <div class="input-group mb-3">
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirmation Password" required>
            <div class="input-group-append">
              <span class="fa fa-lock input-group-text"></span>
            </div>
          </div>
          <div class="input-group-append mb-5 pull-right">
              
              <span class="invalid-feedback" style="display:none" role="alert">
                     <strong>Password does not match</strong>
              </span> 
            </div>
          <div class="row">
            <div class="col-md-12">
              <button type="submit" class="btn btn-success btn-block pull-right btn-flat reset-button">Reset Password</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        
      </div>
      <!-- /.form-box -->
    </div>
        </div>
    </div>
    <!-- /.card -->
  </div>
  <script src="{{asset('js/app.js')}}"></script>
  <script>
    $(document).ready(function(){
       $('.reset-button').click(function(){
            if($('#password').val() != $('#password-confirm').val()){
                $('.invalid-feedback').show()
            }else{
                $('.invalid-feedback').hide()
                $('#resetForm').submit()
            }   
       });
        
    });
  </script>
  </body>
  
  </html>