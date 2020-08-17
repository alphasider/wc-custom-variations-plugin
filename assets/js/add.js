jQuery(function ($) {

  $('.toggles .toggle_title').on('click', function (i) {
    var active_toggle = $(this).parents('.toggles').find('.toggle_wrap.current_toggle .toggle'),
      toggle = $(this).parents('.toggle_wrap'),
      acc = ($(this).parents('.toggles').hasClass('toggles_mode_accordion')) ? true : false,
      dropDown = toggle.find('.toggle');
    if (toggle.hasClass('current_toggle')) {
      dropDown.slideUp('fast', function () {
        toggle.removeClass('current_toggle');
      });
    } else {
      if (acc) {
        active_toggle.slideUp('fast', function () {
          active_toggle.parents('.toggle_wrap').removeClass('current_toggle');
        });
      }
      dropDown.slideDown('fast', function () {
        toggle.addClass('current_toggle');
      });
    }
    i.preventDefault();
    setTimeout(function () {
      jQuery('body').trigger('debouncedresize');
    }, 300);
  });

  var mon;
  var del;

  // $('.last-attr ._size').click(function () {
  //     $(this).parent().find('.bed-image').removeClass('active');
  //     $(this).find('.bed-image').addClass('active');
  //
  //     var atr = $(this).find('.text-block-2').attr('data-atr');
  // });

  // Choose parachute frequency
  $('._month').click(function () {
    $('._month').find('.f').removeClass('active');
    $(this).find('.f').addClass('active');

    var atr = $(this).find('.but').attr('data-atr');

  })

  // Delivery
  $('.day').click(function () {
    $('.day').removeClass('active');
    $(this).addClass('active');

    var atr = $(this).find('.text-block-3').attr('data-atr');

  })

  let prod_count = 0;
  $('.bed-row.add').click(function () {
    // adding extra attr size row
    prod_count++;
    console.log('added');
    let htmlCode = $('.bed-row.last-attr').html();
    let new_row = '';
    new_row += '<div class="bed-row last-attr">';
    new_row += htmlCode.replace(new RegExp('bed-image active', 'g'), 'bed-image');
    new_row += '</div>';

    $('.bed-row.last-attr').removeClass('last-attr').after(new_row);
    if (prod_count == 2) {
      $(this).addClass('d-none');
    }
  })


  $('.add-attr_res ._size').click(function () {
    $('.add-attr_res ._size').find('.bed-image').removeClass('active');
    $(this).find('.bed-image').addClass('active');

    let size = $(this).find('.text-block-2').attr('data-atr');
    let id = $('.build-section').attr('data-product-id');
  });

  // Choose bed size
  $('.bed_attr').click(function (event) {
    let current = event.target,
      classes = event.target.classList;

    if (classes.contains("bed-image")) {
      current.parentNode.parentNode.classList.add('remove_active');
      $('.remove_active').find($('.active')).removeClass('active');
      $('.remove_active').removeClass('remove_active');

      classes.add('active');
    } else if (classes.contains("image-4")) {
      current.parentNode.parentNode.parentNode.classList.add('remove_active');
      $('.remove_active').find($('.active')).removeClass('active');
      $('.remove_active').removeClass('remove_active');

      current.parentNode.classList.add('active');
    }
  });

  // Add to cart button
  $('.add-prods-to-cart').click(function (e) {
    console.log('add to cart');
    let sizes = [];
    let delivery = $('.date .active > div').attr('data-atr');
    let month = $('._month .active .but').attr('data-atr');
    let id = $(this).attr('data-prod-id');

    $('.bed_attr .bed-image.active').each(function () {
      sizes.push($(this).next().attr('data-atr'));
    });

    let is_valid = true;

    if (delivery == undefined || month == undefined || sizes == undefined) {
      is_valid = false;
    }


    if (is_valid) {
      console.log('Valid');
      let ajax_obj = {
        action: 'filter_post',
        pa_size: sizes.join('%%'),
        pa_delivery: delivery,
        pa_month: month,
        prod_id: id
      };
      console.log(ajax_obj);

      $.post(global_obj.ajaxurl, ajax_obj).done(function (response) {
        let data = JSON.parse(response);
        console.log(data);
        if (data.result === 1) {
          console.log('Ajax success');
          // alert('Products added to basket');
          window.location.href = "http://parachute.1devserver.co.uk/cart";
        } else {
          console.log('Ajax fail');
          alert('Sorry, this product is unavailable. Please choose a different combination.');
        }
      });
    } else {
      alert('Please, choose all product attributes');
    }
  });

});