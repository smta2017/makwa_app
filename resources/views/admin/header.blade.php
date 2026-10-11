  <!-- header area start -->
  <div class="header-area">
      <div class="row align-items-center">
          <!-- nav and search button -->
          <div class="col-md-12 col-sm-8 clearfix">
              <div class="nav-btn pull-left">
                  <span></span>
                  <span></span>
                  <span></span>
              </div>
              <div class="search-box pull-right">
                <a href="index.html"><img src="{{ asset ('imgs/makwa_dark.png')}}" width="40" alt="logo"></a>
              </div>
          </div>

          <!-- profile info & task notification -->
          
      </div>
  </div>
  <!-- header area end -->
  <!-- page title area start -->
  <div class="page-title-area">
      <div class="row align-items-center">
          <div class="col-sm-6">
              <div class="breadcrumbs-area clearfix">
                  <h4 class="page-title pull-left">Dashboard</h4>
              </div>
          </div>
          
          <div class="col-sm-6 clearfix">
              <div class="user-profile pull-right" style="background: #fff; border:2px solid blue">
              <form action="/logout/@if(Auth::guard('vendor')->check())vendor @elseif(Auth::guard('admin')->check())admin @endif" method="POST">
                          @csrf
                          <button type="submit"  class="btn btn-info">Log Out</button>
                      </form>
                  <img class="avatar user-thumb" src="{{ asset ('assets/images/author/avatar.png')}}" alt="avatar">
                  <h4 class="user-name dropdown-toggle" style="color: black;" data-toggle="dropdown">{{Auth::user()->name}} <i class="fa fa-angle-down"></i></h4>
                  <div class="dropdown-menu">
                      <a class="dropdown-item" href="/change-password">Change Password</a>
                      <form action="/logout/@if(Auth::guard('vendor')->check())vendor @elseif(Auth::guard('admin')->check())admin @endif" method="POST">
                          @csrf
                          <button type="submit" style="color:#8a8a8a;font-weight: bold;" class="dropdown-item">Log Out</button>
                      </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- page title area end -->