<?php
$plugin_url = plugin_dir_url(__DIR__);
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_warranty'])) {
    global $wpdb;
    $table = 'seller_purchaser_info';
    $massage= [];
    $error=[];

    $phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
    $uploaded_image_urls = [];
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    if (!empty($_FILES['damage_photos']['name'][0])) {
        $file_count = count($_FILES['damage_photos']['name']);

        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['damage_photos']['error'][$i] === 0) {
                $file = [
                    'name'     => $_FILES['damage_photos']['name'][$i],
                    'type'     => $_FILES['damage_photos']['type'][$i],
                    'tmp_name' => $_FILES['damage_photos']['tmp_name'][$i],
                    'error'    => $_FILES['damage_photos']['error'][$i],
                    'size'     => $_FILES['damage_photos']['size'][$i],
                ];
                $_FILES['single_image'] = $file;
                $attachment_id = media_handle_upload('single_image', 0);

                if (!is_wp_error($attachment_id)) {
                    $image_url = wp_get_attachment_url($attachment_id);
                    $uploaded_image_urls[] = esc_url_raw($image_url);
                }
            }
        }
    }

    $images_serialized = maybe_serialize($uploaded_image_urls);
    
         if(!isset($_POST['_warranty_nonce']) || !wp_verify_nonce($_POST['_warranty_nonce'],'add_seller_warranty')){
             $error[]='invalid submision';
         }
    else{
    $inserted = $wpdb->insert($table, [
        'plan_type'              => sanitize_text_field($_POST['plan_type']),
        'first_name'             => sanitize_text_field($_POST['customer_first_name']),
        'last_name'              => sanitize_text_field($_POST['customer_last_name']),
        'address_line1'          => sanitize_text_field($_POST['address_line1']),
        'address_line2'          => sanitize_text_field($_POST['address_line2']),
        'city'                   => sanitize_text_field($_POST['city']),
        'state'                  => sanitize_text_field($_POST['state']),
        'zip'                    => sanitize_text_field($_POST['zip']),
        'phone'                  => $phone,
        'email'                  => sanitize_email($_POST['customer_email']),
        'plan_number'            => sanitize_text_field($_POST['plan_number']),
        'fabricator'             => sanitize_text_field($_POST['customer_fabricator']),
        'countertop_type'        => sanitize_text_field($_POST['countertop_type']),
        'room'                   => sanitize_text_field($_POST['room']),
        'problem'                => sanitize_text_field($_POST['problem']),
        'chip_at_sink'           => sanitize_text_field($_POST['chip_at_sink']),
        'description'            => sanitize_textarea_field($_POST['description']),
        'damage_during_delivery' => sanitize_text_field($_POST['damage_during_delivery']),
        'install_date'           => sanitize_text_field($_POST['install_date']),
        'damage_date'            => sanitize_text_field($_POST['damage_date']),
        'attempt_clean'          => sanitize_text_field($_POST['attempt_clean']),
        'damage_photos'          => $images_serialized,
        'submitted_at'           => current_time('mysql'),
    ]);

    // Show feedback
    if (!$inserted) {
        $error[] = 'DB Error' . $wpdb->last_error;
    } else {
       $massage[]="Warranty Added";
    }
    }
    
}

?>

<div class="container mt-5 mb-5">
    <?php
        foreach ($massage as $mass) :?> 
      <div class="alert alert-success"> <?= esc_html($mass) ?> </div>
      <?php endforeach ;
        foreach ($error as $err) :
        ?> 
      <div class="alert alert-danger"> <?= esc_html( $err) ?> </div>
      <?php endforeach; ?>
  <h1 class="text-center mb-5">Protection Plan Claim Form</h1>
  <form method="post" id="warranty-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>"
    enctype="multipart/form-data" class="needs-validation" novalidate>
       <?php wp_nonce_field('add_seller_warranty', '_warranty_nonce'); ?> 
    <div class="row">
      <div class="col-12 valid  mb-3">
        <label for="plan_type" class="form-label">Select your Protection Plan type.<span
            class="text-danger">*</span></label>
        <select class="form-select" id="plan_type" name="plan_type" required>
          <option value="">-Select-</option>
          <option value="Countertop Protection Plan">Countertop Protection Plan</option>
          <option value="Cabinet Protection Plan">Cabinet Protection Plan</option>
        </select>
        <div class="invalid-feedback">Please Select your Protection Plan type</div>
      </div>
      <div class="col-12 mb-12"> <label for="customer_first_name" class="form-label">Name.<span
            class="text-danger">*</span></label></div>
      <div class="col-6 mb-6">
        <input class="form-control" type="text" id="customer_first_name" name="customer_first_name" required />
        <small class="form-text text-muted">First</small>
        <div class="invalid-feedback">Please Enter Your First Name</div>
      </div>
      <div class="col-6 mb-6">
        <input class="form-control" type="text" id="customer_last_name" name="customer_last_name" required />
        <small class="form-text text-muted">Last</small>
        <div class="invalid-feedback">Please Enter Your First Name</div>
      </div>
      <div class="col-12 mt-3">
        <label class="form-label" for="address_line1">Address <span class="text-danger">*</span></label>
        <input class="form-control" type="text" id="address_line1" name="address_line1" required />
        <small class="form-text text-muted"> Street Address</small>
        <div class="invalid-feedback">Please Enter Valid Address </div>
        <input class="form-control mt-3" type="text" id="address_line2" name="address_line2" />
        <small class="form-text text-muted"> Address Line 2</small>
      </div>
      <div class="row m-0 p-0">
        <div class="col-md-6 mb-2">
          <input class="form-control mt-3" type="text" id="city" name="city" required />
          <small class="form-text text-muted"> City</small>
          <div class="invalid-feedback">Please Enter City</div>
        </div>
        <div class="col-md-6 mb-2">
          <input class="form-control mt-3" type="text" id="state" name="state" required />
          <small class="form-text text-muted"> State/Region/Province</small>
          <div class="invalid-feedback">Please Enter State</div>
        </div>
        <div class="col-md-6 mb-2">
          <input class="form-control mt-3" type="text" id="zip" name="zip" required />
          <small class="form-text text-muted"> Postal / Zip Code</small>
          <div class="invalid-feedback">Please Enter Zip Code</div>
        </div>
      </div>
      <!-- Phone -->
      <div class="mb-3">
        <label for="customer_phone" class="form-label mt-2">Best Contact Phone <span
            class="text-danger">*</span></label>
        <input class="form-control" type="tel" name="customer_phone" id="customer_phone" required maxlength="10" />
        <div class="invalid-feedback">Please Enter Valid Contact Number</div>
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label class="form-label" for="customer_email">Email <span class="text-danger">*</span></label>
        <input class="form-control" type="email" id="customer_email" name="customer_email" required />
        <div class="invalid-feedback">Please enter a valid Email</div>
      </div>

      <!-- Protection Plan Number -->
      <div class="mb-3">
        <label class="form-label" for="plan_number">What is your Protection Plan Number? <span
            class="text-danger">*</span></label>
        <input class="form-control" type="text" id="plan_number" name="plan_number" required />
        <small class="form-text text-muted">If you do not know your Protection Plan Number, please use your phone
          number.</small>
        <div class="invalid-feedback">Please enter a Protection Plan Number</div>
      </div>

      <!-- Fabricator -->
      <div class="mb-3">
        <label for="customer_fabricator" class="form-label">Who was your countertop fabricator/installer? <span
            class="text-danger">*</span></label>
        <input class="form-control" type="text" id="customer_fabricator" name="customer_fabricator" required />
        <div class="invalid-feedback">Please enter a countertop fabricator</div>
      </div>

      <!-- Type of Countertop -->
      <div class="mb-3">
        <label for="countertop_type" class="form-label">What type of countertop do you have? <span
            class="text-danger">*</span></label>
        <select class="form-select" id="countertop_type" name="countertop_type" required>
          <option value="">-Select-</option>
          <option value="Granite">Granite</option>
          <option value="Quartz">Quartz</option>
          <option value="Quartzite">Quartzite</option>
          <option value="Solid Surface">Solid Surface</option>
          <option value="Don't Know">Don't Know</option>
          <option value="Other"> Other</option>
        </select>
        <div class="invalid-feedback">Please Select countertop type</div>
      </div>

      <!-- Room -->
      <div class="mb-3">
        <label class="form-label">In which room is the damaged countertop? <span class="text-danger">*</span></label>
        <select class="form-select" id="room" name="room" required>
          <option value="">-Select-</option>
          <option value="Kitchen">Kitchen</option>
          <option value="Bathroom">Bathroom</option>
          <option value="Other"> Other</option>
        </select>
        <div class="invalid-feedback">Please Select which room is the damaged</div>
      </div>

      <!-- Problem -->
      <div class="mb-3">
        <label class="form-label">Tell us what the problem is with your countertop <span
            class="text-danger">*</span></label>
        <select class="form-select" id="problem" name="problem" required>
          <option value="">-Select-</option>
          <option value="Stain - Food & Beverage">Stain - Food & Beverage</option>
          <option value="Stain - Oil based or Non Household">Stain - Oil based or Non Household</option>
          <option value="Damage - Chip">Damage - Chip</option>
          <option value="Damage - Scratch(es)">Damage - Scratch(es)</option>
          <option value="Damage - Crack">Damage - Crack</option>
          <option value="Damage - Pitting">Damage - Pitting</option>
          <option value="Damage - Dulling of Surface/Etching">Damage - Dulling of Surface/Etching</option>
          <option value="Damage - Caulking">Damage - Caulking</option>
          <option value="Damage - Hard Water Mark or Deposit">Damage - Hard Water Mark or Deposit</option>
          <option value="Other"> Other</option>
        </select>
        <div class="invalid-feedback">Please Select problem</div>
      </div>

      <!-- Chip at sink? -->
      <div class="mb-3">
        <label class="form-label">If you chipped your countertop, did it occur at the sink?</label>
        <div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="chip_at_sink" value="yes">
            <label class="form-check-label" for="chipYes">Yes</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="chip_at_sink" value="no">
            <label class="form-check-label" for="chipNo">No</label>
          </div>
        </div>
      </div>
      <!-- What happened -->
      <div class="mb-3">
        <label class="form-label">Tell us what happened and how it happened. <span class="text-danger">*</span></label>
        <textarea class="form-control" name="description" rows="3" required></textarea>
        <div class="invalid-feedback">This field is required</div>
      </div>

      <!-- Damage during installation/delivery -->
      <div class="mb-3">
        <label class="form-label">Did the damage occur during installation or delivery? <span
            class="text-danger">*</span></label>
        <div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="damage_during_delivery" value="yes" required>
            <label class="form-check-label" for="deliveryYes">Yes</label>

          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="damage_during_delivery" value="no">
            <label class="form-check-label" for="deliveryNo">No</label>
          </div>
        </div>
      </div>

      <!-- Installation Date -->
      <div class="mb-3">
        <label class="form-label">Countertop Installation Date? <span class="text-danger">*</span></label>
        <input type="date" name="install_date" class="form-control" required />
        <div class="invalid-feedback">Please enter a Countertop Installation Date</div>
      </div>

      <!-- Damage Date -->
      <div class="mb-3">
        <label class="form-label">Date the Stain or Damage Occurred? <span class="text-danger">*</span></label>
        <input type="date" class="form-control" name="damage_date" required />
        <small class="form-text text-muted">For your claim to be accepted, the damage must be reported within 30 days of
          occurring.</small>
        <div class="invalid-feedback">Please enter a Damage Occurred</div>
      </div>

      <!-- Attempt to clean or repair -->
      <div class="mb-3">
        <label class="form-label">Did you attempt to clean or repair it? <span class="text-danger">*</span></label>
        <div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="attempt_clean" value="yes" required>
            <label class="form-check-label" for="cleanYes">Yes</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="attempt_clean" value="no">
            <label class="form-check-label" for="cleanNo">No</label>
          </div>
        </div>
      </div>
      <!-- Photos Required Notice -->
      <div class="my-2 ">
        <h5 class="text-danger fw-bold text-center">PHOTOS REQUIRED</h5>
        <p class="text-center">
          <strong>In order for us to process your claim, we require the following images:</strong>
        </p>
        <ul class="text-center list-unstyled">
          <li>- Images that capture the entire surface area of your countertop(s), ideally taken from <strong>6–10 feet
              away</strong></li>
          <li>- Images that capture each damaged area, ideally taken from <strong>2 feet away</strong></li>
        </ul>
        <p class="text-center mb-1">You can upload a maximum of 5 images</p>
        <h6 class="fw-bold text-center mt-3">Here are some Examples:</h6>
        <div class="ex-images">
          <div>
            <img src="<?php echo $plugin_url . 'images/GG1.jpg' ?>">
          </div>
          <div>
            <img src="<?php echo $plugin_url . 'images/Example_Photo_2.jpg' ?>">
          </div>
          <div>
            <img src="<?php echo $plugin_url . 'images/Example_Photo_3.jpg' ?>">
          </div>
          <div>
            <img src="<?php echo $plugin_url . 'images/Example_Photo_5.jpg' ?>">
          </div>
          <div>
            <img src="<?php echo $plugin_url . 'images/GG2.jpg' ?>">
          </div>
          <div>
            <img src="<?php echo $plugin_url . 'images/Example_Photo_4.jpg' ?>">
          </div>
        </div>
      </div>
      <!-- Upload Section -->
      <div class="mt-2  text-center">
        <h5 class="fw-bold text-uppercase">Upload Images Here</h5>
        <p class="fw-semibold text-uppercase">Click submit button below when completed</p>

        <!-- File Upload -->
        <div id="imageUpload_wrapper" class="mb-3 text-start">
          <label for="imageUpload" class="form-label fw-bold">Image Upload <span class="text-danger">*</span></label>
          <div class="wrapper">
            <div class="drop" id="drop-area">
              <div class="cont">
                <img src="<?php echo $plugin_url . 'images/10254947.png' ?>" width="57">
                <div class="desc">
                  your files to Assets, or
                </div>
                <div class="browse" id="browse-trigger">
                  click here to browse
                </div>
              </div>
              <output id="list"></output>
              <div id="file-inputs">
              <input class="form-control" type="file" id="imageUpload" name="damage_photos[]" multiple
                accept=".jpg,.jpeg,.png" required>
                </div>
            </div>
          </div>
          <small class="form-text text-muted">Please take a look at the examples of images below before uploading your
            images.</small>
          <div class="invalid-feedback">This field is required</div>
          <ul id="fileNamesList"></ul>
        </div>
        <button type="submit" name="add_warranty" class="btn btn-primary mt-3">Submit</button>

        <!-- Thank You Note -->
        <div class="mt-4">
          <p class="mb-1">Thank you for your inquiry and business.</p>
          <p>Please allow us 24–48 hours for us to respond.</p>
        </div>
      </div>
    </div>
  </form>
</div>


