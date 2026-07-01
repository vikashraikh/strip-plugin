
(function () {
  "use strict";

  const forms = document.querySelectorAll(".form-validation");

  Array.from(forms).forEach(function (form) {
    form.addEventListener(
      "submit",
      function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();

          const firstInvalid = form.querySelector(":invalid");
          if (firstInvalid) {
            window.scrollTo({
              top: firstInvalid.offsetTop - 100,
              behavior: "smooth",
            });
          }
        }

        form.classList.add("was-validated");
      },
      false
    );
  });
   
 
    var currentPage = window.location.href;

    $('.sidebar-nav li a').each(function () {
        if (currentPage.indexOf(this.href) !== -1) {
            $('.sidebar-nav li a').removeClass('active'); 
            $(this).addClass('active'); 
        }
    });
    
    
  $("form input[type='tel']").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, ""); 
  });
  

  jQuery(".select-with-other").on("change", function () { 
    let wrapper = jQuery(this).next(".other-wrapper");
    if (jQuery(this).val() === "Other") {
      wrapper.show();
    } else {
      wrapper.hide();
      wrapper.find("input").val(""); 
    }
  });
  
  $(".toggle-password").click(function() {
    $(this).toggleClass("fa-eye fa-eye-slash");
    const input = $(this).parent().find("input");
    const type = input.attr("type") === "password" ? "text" : "password";
    input.attr("type", type);
  });

})();



const canvas = document.getElementById('signature-pad');
const ctx = canvas.getContext('2d');
canvas.width = canvas.offsetWidth;
canvas.height = 200;

let drawing = false;
let isSignatureDrawn = false; 

function getPosition(e) {
    const rect = canvas.getBoundingClientRect();
    let x, y;
    if (e.touches && e.touches.length > 0) {
        x = e.touches[0].clientX - rect.left;
        y = e.touches[0].clientY - rect.top;
    } else {
        x = e.clientX - rect.left;
        y = e.clientY - rect.top;
    }
    return { x, y };
}

function startDraw(e) {
    drawing = true;
    ctx.beginPath();
    const pos = getPosition(e);
    ctx.moveTo(pos.x, pos.y);
    e.preventDefault();
}

function draw(e) {
    if (!drawing) return;
    const pos = getPosition(e);
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#000';
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
    isSignatureDrawn = true; // Mark as drawn
    e.preventDefault();
}

function endDraw() {
    drawing = false;
}

canvas.addEventListener('mousedown', startDraw);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', endDraw);
canvas.addEventListener('mouseout', endDraw);

canvas.addEventListener('touchstart', startDraw);
canvas.addEventListener('touchmove', draw);
canvas.addEventListener('touchend', endDraw);

function clearSignature(event) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    isSignatureDrawn = false; 
    document.getElementById('signature_data').value = ""; 
    if (event) event.preventDefault();
}

function saveSignature() {
    if (isSignatureDrawn) {
        const dataURL = canvas.toDataURL('image/png');
        document.getElementById('signature_data').value = dataURL;
    }

}


