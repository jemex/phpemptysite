// Ripple Effect

(function($) {
    $(".ripple-effect").click(function(e){
        var rippler = $(this);
        if(rippler.find(".teffect").length === 0) {
            rippler.append("<span class='teffect'></span>");
        }
        var teffect = rippler.find(".teffect");
        teffect.removeClass("animate");
        if(!teffect.height() && !teffect.width())
        {
            var d = Math.max(rippler.outerWidth(), rippler.outerHeight());
            teffect.css({height: d, width: d});
        }
        var x = e.pageX - rippler.offset().left - teffect.width()/2;
        var y = e.pageY - rippler.offset().top - teffect.height()/2;
        teffect.css({
            top: y+'px',
            left:x+'px'
        }).addClass("animate");
    })
})(jQuery);


$( '#write_comment' )
.on('submit.before', function(e, event) {
    //alert( event.isDefaultPrevented() ); // false
 // console.log(event.isDefaultPrevented());
    event.preventDefault();
    //this.submit();
})
.submit(function(e) {
  $(this).trigger('submit.before', e);
  
  console.log(e.isDefaultPrevented());
  console.log('go');
});

$(document).ready(function() {
   var ro_input = function(input) {
         if(!$(input).hasClass("in_typing")) $(input).addClass("in_typing");
         if($(input).val().length > 0) $(input).attr('data-value', 'Y');
         else $(input).attr('data-value', '');
   };
   $(".ro-input-form .ro-input-text").each(function() {
     ro_input(this);
   });
   $(".ro-input-form .ro-input-text").on( "keydown keyup change input focus", function() {
     ro_input(this);  
   });
});




/*=========================dropdowm=========================*/
$(document).on('click', '.myyyy', function(){
    $(this).parent('#clickbtns').find('#listupdwn').slideToggle();
  });


/*===============table-filter=====================*/
  $(".search").keyup(function () {
    var searchTerm = $(".search").val();
    var listItem = $('.results tbody').children('tr');
    var searchSplit = searchTerm.replace(/ /g, "'):containsi('")
    
  $.extend($.expr[':'], {'containsi': function(elem, i, match, array){
        return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
  });
    
  $(".results tbody tr").not(":containsi('" + searchSplit + "')").each(function(e){
    $(this).attr('visible','false');
  });

  $(".results tbody tr:containsi('" + searchSplit + "')").each(function(e){
    $(this).attr('visible','true');
  });

  var jobCount = $('.results tbody tr[visible="true"]').length;
    $('.counter').text(jobCount + ' item');

  if(jobCount == '0') {$('.no-result').show();}
    else {$('.no-result').hide();}
      });

/*=======================sidebar==================*/
  $('body').on('click', '.bars_set', function(){
    $('.right_bar').toggleClass('mobile_right');
    $('.sidebar').toggleClass('sidebar23');
  });



/*====================load===================*/
$(".Completed").click(function(){
  $(".loding_content").load("account_histroy.html");
});
 



$(document).ready(function() {
   if(window.File && window.FileList && window.FileReader) {
       $("#files").on("change",function(e) {
           var files = e.target.files ,
           filesLength = files.length ;
               for (var i = 0; i < filesLength ; i++) {
                   var f = files[i]
                   var fileReader = new FileReader();
                   fileReader.onload = (function(e) {
                       var file = e.target;
                       $("<div class='s'><img class='imageThumb' src='"+e.target.result+"' title='"+file.name+ "'></img><span onclick=remove(this)><i class='fa fa-close'></i></span></div>").insertAfter(".upld_group");
                   });
                   fileReader.readAsDataURL(f);
               }
       });
   }
});
function remove(r) {
_this = jQuery(r);    
_this.closest(".s").remove();
}


function resetImage(input) {
        input.value = '';
        input.onchange();
      }
      function readImage(input) {
        var receiver = input.nextElementSibling.nextElementSibling;
        input.setAttribute('title', input.value.replace(/^.*[\\/]/, ''));
        if (input.files && input.files[0]) {
          var reader = new FileReader();
          reader.onload = function (e) {
          
            receiver.style.backgroundImage = 'url(' + e.target.result + ')';
          };
          
          reader.readAsDataURL(input.files[0]);
        }
        else receiver.style.backgroundImage = 'none';
      }
/*
      function resetImage(input) {
  input.value = '';
  input.onchange();
}
function readImage(input) {
  var receiver = input.nextElementSibling.nextElementSibling;
  input.setAttribute('title', input.value.replace(/^.*[\\/]/, ''));
  
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
    
      receiver.style.backgroundImage = 'url(' + e.target.result + ')';
    };
    
    $(".filetype").val(input.files[0].type);
    $(".filename").val(input.files[0].name);
    $(".filesize").val(input.files[0].size);
   
    reader.readAsDataURL(input.files[0]);
  }
  else receiver.style.backgroundImage = 'none';
}*/

$(".complete_view").click(function(){
    $.ajax({
        url:$("#site_url").val()+'adminmain/popupdata',
        type:"post",
        data:{
            game_id:$(this).attr("game_id"),
            bet_id:$(this).attr("bet_id")
        },
        success:function(res){
            $(".edit_time .modal-content .betdata").html(res);
            $(".bs-example-modal-lg").modal("show");
        }
    })
})

$(".withdrawl_payment").click(function(){
    
    $(".submitPayment .withdrwal_amount").val($(this).attr("amount"));
    $(".submitPayment .user_id").val($(this).attr("user_id"));
    $(".submitPayment .wallet_id").val($(this).attr("wallet_id"));
    $(".submitPayment").modal("show");
})