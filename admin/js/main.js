// Search Admin
$(document).ready(function(){
    $('#record-search').keyup(function() {    
        const textValue = $('#record-search').val();
        const count = textValue.length;
        if(count > 0){
          $('#maxRows').val(5000).change();
          var value = $(this).val().toLowerCase();
          $('#tbody tr').filter(function() {
              $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
          });
        }else if (count == 0){
          $('#maxRows').val(5).change();
        }
    });
});


// Pagination Admin
getPagination('#table-id');

function getPagination(table) {
  var lastPage = 1;
  $('#maxRows').on('change', function(evt) {
      //$('.table-paginationprev').html('');						// reset table-pagination
     lastPage = 1;
      $('.table-pagination')
        .find('li')
        .slice(1, -1)
        .remove();
      var trnum = 0; // reset tr counter
      var maxRows = parseInt($(this).val()); // get Max Rows from select option

      if (maxRows == 5000) {
        $('.table-pagination').hide();
      } else {
        $('.table-pagination').show();
      }

      var totalRows = $(table + ' tbody tr').length; // numbers of rows
      $(table + ' tr:gt(0)').each(function() {
        // each TR in  table and not the header
        trnum++; // Start Counter
        if (trnum > maxRows) {
          // if tr number gt maxRows

          $(this).hide(); // fade it out
        }
        if (trnum <= maxRows) {
          $(this).show();
        } // else fade in Important in case if it ..
      }); //  was fade out to fade it in
      if (totalRows > maxRows) {
        // if tr total rows gt max rows option
        var pagenum = Math.ceil(totalRows / maxRows); // ceil total(rows/maxrows) to get ..
        //	numbers of pages
        for (var i = 1; i <= pagenum; ) {
          // for each page append table-pagination li
          $('.table-pagination #prev')
            .before(
              '<li data-page="' +
                i +
                '">\
								  <span>' +
                i++ +
                '<span class="sr-only">(current)</span></span>\
								</li>'
            )
            .show();
        } // end for i
      } // end if row count > max rows
      $('.table-pagination [data-page="1"]').addClass('active'); // add active class to the first li
      $('.table-pagination li').on('click', function(evt) {
        // on click each page
        evt.stopImmediatePropagation();
        evt.preventDefault();
        var pageNum = $(this).attr('data-page'); // get it's number

        var maxRows = parseInt($('#maxRows').val()); // get Max Rows from select option

        if (pageNum == 'prev') {
          if (lastPage == 1) {
            return;
          }
          pageNum = --lastPage;
        }
        if (pageNum == 'next') {
          if (lastPage == $('.table-pagination li').length - 2) {
            return;
          }
          pageNum = ++lastPage;
        }

        lastPage = pageNum;
        var trIndex = 0; // reset tr counter
        $('.table-pagination li').removeClass('active'); // remove active class from all li
        $('.table-pagination [data-page="' + lastPage + '"]').addClass('active'); // add active class to the clicked
        // $(this).addClass('active');					// add active class to the clicked
	  	limitPagging();
        $(table + ' tr:gt(0)').each(function() {
          // each tr in table not the header
          trIndex++; // tr index counter
          // if tr index gt maxRows*pageNum or lt maxRows*pageNum-maxRows fade if out
          if (
            trIndex > maxRows * pageNum ||
            trIndex <= maxRows * pageNum - maxRows
          ) {
            $(this).hide();
          } else {
            $(this).show();
          } //else fade in
        }); // end of for each tr in table
      }); // end of on click table-pagination list
	  limitPagging();
    })
    .val(5)
    .change();
}

function limitPagging(){
	// alert($('.table-pagination li').length)
	if($('.table-pagination li').length > 7 ){
			if( $('.table-pagination li.active').attr('data-page') <= 3 ){
			$('.table-pagination li:gt(5)').hide();
			$('.table-pagination li:lt(5)').show();
			$('.table-pagination [data-page="next"]').show();
		}if ($('.table-pagination li.active').attr('data-page') > 3){
			$('.table-pagination li:gt(0)').hide();
			$('.table-pagination [data-page="next"]').show();
			for( let i = ( parseInt($('.table-pagination li.active').attr('data-page'))  -2 )  ; i <= ( parseInt($('.table-pagination li.active').attr('data-page'))  + 2 ) ; i++ ){
				$('.table-pagination [data-page="'+i+'"]').show();
			}
		}
	}
}

// update image
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

// MODAL DELETE 
const modal = document.getElementById('modal');  

$("#cancelBtn").click(function(){
  modal.style.display = 'none'; 
});

// Account management
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

$(".toggle-password").click(function() {

  $(this).toggleClass("fa-eye fa-eye-slash");
  var input = $($(this).attr("toggle"));
  if (input.attr("type") == "password") {
    input.attr("type", "text");
  } else {
    input.attr("type", "password");
  }
});

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