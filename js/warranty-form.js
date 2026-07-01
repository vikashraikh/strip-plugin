
(function () {
  "use strict";

  const forms = document.querySelectorAll(".needs-validation");

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
  $(".select-with-other").on("change", function () {
    let wrapper = $(this).next(".other-wrapper");
    if ($(this).val() === "Other") {
      wrapper.show();
    } else {
      wrapper.hide();
      wrapper.find("input").val(""); 
    }
  });
})();

$(function () {
  $("#customer_phone").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
  });
  const $emailInput = $("#customer_email");

  $emailInput.on("input", function () {
    const email = $(this).val();

    if (email.includes(".")) {
      $emailInput.removeClass("is-invalid is-valid");
      $emailInput.siblings(".invalid-feedback").hide();
    } else {
      emailInput.addClass("is-invalid");
      $emailInput.siblings(".invalid-feedback").show();
    }
  });

  var drop = $("#imageUpload");
  drop
    .on("dragenter", function (e) {
      $(".drop").css({
        border: "4px dashed #09f",
        background: "rgba(0, 153, 255, .05)",
      });
      $(".cont").css({
        color: "#09f",
      });
    })
    .on("dragleave dragend mouseout drop", function (e) {
      $(".drop").css({
        border: "3px dashed #DADFE3",
        background: "transparent",
      });
      $(".cont").css({
        color: "#8E99A5",
      });
    });
     
});

document.addEventListener("DOMContentLoaded", function () {
  const fileInputContainer = document.getElementById("file-inputs");
  const previewContainer = document.getElementById("list");

  fileInputContainer.addEventListener("change", function (e) {
    const input = e.target;
    const currentImages =
      previewContainer.querySelectorAll(".preview-item").length;
    const filesToAdd = Array.from(input.files);
    if (currentImages + filesToAdd.length > 5) {
      alert("You can only upload a maximum of 5 images.");
      input.value = "";
      return;
    }

    filesToAdd.forEach((file) => {
      const reader = new FileReader();
      reader.onload = function (event) {
        const wrapper = document.createElement("div");
        wrapper.classList.add("preview-item");
        wrapper.style.display = "inline-block";
        wrapper.style.margin = "5px";
        wrapper.style.position = "relative";

        const img = document.createElement("img");
        img.src = event.target.result;
        img.style.height = "80px";

        const removeBtn = document.createElement("button");
        removeBtn.type = "button";
        removeBtn.textContent = "×";
        removeBtn.classList.add('image-remove-btn')
        removeBtn.addEventListener("click", function () {
          wrapper.remove();
          input.remove();
        });
        wrapper.appendChild(img);
        wrapper.appendChild(removeBtn);
        previewContainer.appendChild(wrapper);
      };
      reader.readAsDataURL(file);
    });
    if (currentImages + filesToAdd.length < 5) {
      const newInput = document.createElement("input");
      newInput.type = "file";
      newInput.name = "damage_photos[]";
      newInput.accept = ".jpg,.jpeg,.png";
      newInput.required = false;
      newInput.className = "form-control";
      fileInputContainer.appendChild(newInput);

      input.style.display = "none";
    }
  });
});














