jQuery(document).ready(function ($) {
  $("#country-select").select2({
    placeholder: "-Country-",
    minimumInputLength: 0, 
    ajax: {
      url: country_ajax_obj.ajax_url,
      type: "POST",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          action: "search_country",
          nonce: country_ajax_obj.nonce,
          q: params.term || "", 
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
    },
  });

  $("#country-select").on("select2:open", function () {
    if (!$(this).data("loaded")) {
      $(this).data("loaded", true);
      $.ajax({
        url: country_ajax_obj.ajax_url,
        type: "POST",
        dataType: "json",
        data: {
          action: "search_country",
          nonce: country_ajax_obj.nonce,
          q: "", 
          limit: 5, 
        },
        success: function (data) {
          let newOptions = data.map(function (item) {
            return new Option(item.text, item.id, false, false);
          });
          $("#country-select").append(newOptions).trigger("change");
        },
      });
    }
  });
});

jQuery(document).ready(function($) {
  $('.warranty_id').on('submit', function(e) {
    e.preventDefault();

    const form = this;
    const planValue = $('#plan_number').val().trim();
    const errorDiv = $('#plan-error');
    $.post(country_ajax_obj.ajax_url, {
      action: 'validate_plan_number',
      plan_number: planValue,
      nonce: country_ajax_obj.plan_nonce
    }, function(response) {
      if (response.success) {
        errorDiv.hide();
        if (form.checkValidity()) {
          form.submit();
        } else {
          form.reportValidity();
        }

      } else {
        errorDiv.show();
        $('html, body').animate({
            scrollTop: $('#plan__wrapper').offset().top - 100
        }, 500);
      }
    });
  });
});


jQuery(document).ready(function($) {
    var initStart = moment().subtract(6, 'days');
    var initEnd = moment();
    $('#warranty-date-filter').daterangepicker({
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: initStart,
        endDate: initEnd,
        locale: {
           format: 'MMM D, YYYY'
        }
    }, function(start, end) {
        loadWarrantyStats(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
    });
    loadWarrantyStats(initStart.format('YYYY-MM-DD'), initEnd.format('YYYY-MM-DD'));

    function loadWarrantyStats(start, end) {
        $.ajax({
            url: country_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'get_warranty_stats',
                start_date: start,
                end_date: end,
                nonce: country_ajax_obj.warranty_nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#total_warranties').text(response.data.count);
                } else {
                    const errorMsg = (response.data && response.data.message) ? response.data.message : "Unknown error fetching data";
                    console.log(errorMsg);
                    alert(errorMsg);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error:", textStatus, errorThrown);
                console.log("Response:", jqXHR.responseText);
                alert("Server error occurred.");
            }
        });
    }
});

