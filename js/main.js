// Side bar view popular post
var tabs =  $(function () {
    "use strict";
    $("nav ul li").on("click", function () {
        
        // Get The ID Of a When I Clicked
        var myID = $(this).attr("id");
        
        // Remove Class Inactive When I clicked And Add It In Siblings In Ul
        $(this).addClass("inactive").siblings().removeClass("inactive");
        
        // Hide The Div When i Clicked
        $(".popular-list").hide();
        
        // When Clicked In Li Get Div Same ID
        
        $("#" + myID + "-content").fadeIn(500);
    });
});

// DROP DOWN NOTIFY
$(document).ready(function(){
  $(".notification_icon .fa-bell").click(function(){
    $(".notify_dropdown").toggleClass("active");
  })
  $(".badge").click(function(){
    $(".notify_dropdown").toggleClass("active");
  })
});

// Edit profile

$('.account_edit').click(function(){
  $('.account_edit').hide();
  $('.account_password').hide();
  $('.up_image').show();
  $('input').attr("readonly", false); 
  $('textarea').attr("readonly", false); 
  $('.btn.sm').show();
  $('.btn.sm.danger').show();
});

if($('.error').length){
  $('.account_edit').hide();
  $('.account_password').hide();
  $('.up_image').show();
  $('input').attr("readonly", false); 
  $('textarea').attr("readonly", false); 
  $('.btn.sm').show();
  $('.btn.sm.danger').show();
}

const validateEmail = (email) => {
  return email.match(
    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  );
};

const validate = () => {
  const $result = $('#result');
  const email = $('#email').val();
  $result.text('');

  if(validateEmail(email)){
    $result.text('email: ' + email + ' hợp lệ.');
    $result.css('color', 'green');
  } else{
    $result.text('email: ' + email + ' không hợp lệ.');
    $result.css('color', 'red');
  }
  return false;
}

$('#email').on('input', validate);

// update img 
var loadFile = function(event) {
	var image = document.getElementById('output');
	image.src = URL.createObjectURL(event.target.files[0]);
};
// get file name from input file to input hidden
var getFileName = function(event) {
  const files = event.target.files;
  const fileName = files[0].name;
  document.getElementById('imageValue').value = fileName;
}




// open profile button
function openProfile() {
    const openProfButton = document.querySelector('.nav__profile ul');
    openProfButton.classList.toggle('open');
}

$(".toggle-password").click(function() {

  $(this).toggleClass("fa-eye fa-eye-slash");
  var input = $($(this).attr("toggle"));
  if (input.attr("type") == "password") {
    input.attr("type", "text");
  } else {
    input.attr("type", "password");
  }
});
$(".search__bar-header").submit(function(e) {
  if ($.trim($("#postSearch").val()) === "") {
    toastr.error('Bạn vui lòng không để trống ô tìm kiếm',{
      tapToDismiss: true,
      toastClass: 'toast',
  
      showMethod: 'fadeIn', 
  
      showDuration: 300,
  
      showEasing: 'swing',
  
      // hide animation
      hideMethod: 'fadeOut',
    
      // duration of animation
      hideDuration: 1000,
    
      // easing function
      hideEasing: 'swing',
    
      // timeout in ms
      extendedTimeOut: 1000,
    
      // you can customize icons here
      iconClasses: {
        error: 'toast-warning',
        info: 'toast-info',
        success: 'toast-success',
        warning: 'toast-warning'
      },
      iconClass: 'toast-warning',
    
      // toast-top-center, toast-bottom-center, toast-top-full-width
      // toast-bottom-full-width, toast-top-left, toast-bottom-right
      // toast-bottom-left, toast-top-right
      positionClass: 'toast-top-right',
    
      // set timeOut and extendedTimeOut to 0 to make it sticky
      timeOut: 5000, 
    
      // title class
      titleClass: 'toast-title',
    
      // message class
      messageClass: 'toast-message',
    
      // allows HTML content in the toast?
      escapeHtml: false,
    
      // target container
      target: 'body',
    
      // close button
      closeHtml: '<button type="button">&times;</button>',
    
      // place the newest toast on the top
      newestOnTop: true,
    
      // revent duplicate toasts
      preventDuplicates: true,
    
      // shows progress bar
      progressBar: true
      
    })
    e.preventDefault();
  }
});


const navItems = document.querySelector('.nav__items');
const openNavBtn = document.querySelector('#open__nav-btn');
const closeNavBtn = document.querySelector('#close__nav-btn');
function openNav() {
  navItems.style.display = 'flex';
  closeNavBtn.style.display = 'inline-block';
  openNavBtn.style.display = 'none';
}
function closeNav() {
  navItems.style.display = 'none';
  closeNavBtn.style.display = 'none';
  openNavBtn.style.display = 'inline-block';
}
document.querySelector('#open__nav-btn').addEventListener('click', openNav);
document.querySelector('#close__nav-btn').addEventListener('click', closeNav);
// opens nav dropdown


