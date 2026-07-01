<?php 
function sidebar(){
?>
 <div class="d-flex flex-column flex-shrink-0 sidebar">

        <ul class="nav nav-pills flex-column mb-auto sidebar-nav">
          <li class="nav-item ">
            <a href="/retailer-account/" class="nav-link">
              <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 64 64">
<path d="M 32 3 L 1 28 L 1.4921875 28.654297 C 2.8591875 30.477297 5.4694688 30.791703 7.2304688 29.345703 L 32 9 L 56.769531 29.345703 C 58.530531 30.791703 61.140812 30.477297 62.507812 28.654297 L 63 28 L 54 20.742188 L 54 8 L 45 8 L 45 13.484375 L 32 3 z M 32 13 L 8 32 L 8 56 L 56 56 L 56 35 L 32 13 z M 26 34 L 38 34 L 38 52 L 26 52 L 26 34 z"></path>
</svg>
              Dashboard
            </a>
          </li>    
          <li class="nav-item ">
            <a href="https://elitewarrantyprogram.idestpro.com/register-new-warranty/" class="nav-link">
              <img src="https://elitewarrantyprogram.idestpro.com/wp-content/uploads/2025/08/009-registry.png" alt="" style=" width:34px;height:34px; "></img>
              
              Register New Warranty
            </a>
          </li>
          <li>
            <a href="https://elitewarrantyprogram.idestpro.com/my-warranties/" class="nav-link link-dark">
              
              <img src="https://elitewarrantyprogram.idestpro.com/wp-content/uploads/2025/08/003-warranty-1.png" alt="" style=" width:27px;height:27px; "></img>
            
              My Warranties
            </a>
          </li>
          <li>
            <a href="/payment-history/" class="nav-link link-dark">

              <img src="https://elitewarrantyprogram.idestpro.com/wp-content/uploads/2025/08/004-payment-method.png" alt=""></img>
             
              Payment History
            </a>
          </li>
          <li>
            <a href="https://elitewarrantyprogram.idestpro.com/profile-settings/" class="nav-link link-dark">
              <img src="https://elitewarrantyprogram.idestpro.com/wp-content/uploads/2025/08/006-setting.png" alt=""></img>
              Profile Settings
            </a>
          </li>
          <li>
            <a href="/support/" class="nav-link link-dark">
             <img src="https://elitewarrantyprogram.idestpro.com/wp-content/uploads/2025/08/007-customer-support.png" alt=""></img>
              Support
            </a>
          </li>
          <li>
            <a href="<?php echo esc_url( wp_logout_url( home_url('/retailer-login/') ) ); ?>" class="nav-link link-dark">
              <img src="https://elitewarrantyprogram.idestpro.com/wp-content/uploads/2025/08/turn-off.png" alt=""></img>
              Logout </a>
            </a>
          </li>
        </ul>
      </div>
      <?php }?>