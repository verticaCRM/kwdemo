jQuery(document).ready(function($){
    // $() will work as an alias for jQuery() inside of this function
		$('body').tooltip({ selector: '[rel=tooltip]'});
		$(".topNavButton").click(function () {
			$('.topnav').slideToggle('slow', function() {
				$(".topNavButton").css({'display':'none'});
				$(".topNavButtonClose").css({'display':'block'});
	  		});
	  
		});    

		$(".topNavButtonClose").click(function () {
			$('.topnav').slideToggle('fast', function() {
				$(".topNavButtonClose").css({'display':'none'});
				$(".topNavButton").css({'display':'block'});
	  		});
		});
		
		$("#topLogin, .loginToSave").on('click', function(event){
			event.preventDefault();
			$('#loginActive').modal('show');
		});

		$(".loginToSave").on('click', function(event){
			$.data(document.body, 'adid', $(".loginToSave").attr('data-listing-id'));
		});
		

	 	function changeSliderSet(thisSet) {
			$(thisSet).fadeIn(350, function() {
				$(thisSet + ' .row h1').animate({
					left : '+=1200'
				}, {
					duration : 450,
					specialEasing : {
						left : 'easeOutCubic'
					}
				});
				$(thisSet + ' .row p').animate({
					left : '+=1200',
					top : '+=600px'
				}, {
					duration : 600,
					specialEasing : {
						left : 'easeOutCubic'
					}
				});
				checkSlide = thisSet.split('.panel');
				$.data(document.body, 'currentSlide', checkSlide[1]);
			});
		}

		function resetSlider(thisSet) {
			$(thisSet).fadeOut(250, function() {
				$(thisSet + ' .row h1').animate({
					left : '-=1200px'
				}, {
					duration : 200,
					specialEasing : {
						left : 'easeOutCubic'
					}
				});
				$(thisSet + ' .row p').animate({
					left : '-=1200px',
					top : '-=600px'
				}, {
					duration : 200,
					specialEasing : {
						left : 'easeOutCubic'
					}
				});
			});
		}

		function startSlider() {
			slideVal = $.data(document.body, 'currentSlide');
			/*
			if (slideVal == '3') {
				resetSlider('.panel3');
				changeSliderSet('.panel1');
			} else
			*/ 
			if (slideVal == '2') {
				resetSlider('.panel2');
				changeSliderSet('.panel1');
			} else if (slideVal == '1') {
				resetSlider('.panel1');
				changeSliderSet('.panel2');
			}
		}
		$(document).ready(function() {
			$.data(document.body, 'currentSlide', 1);
			changeSliderSet('.panel1');
			var slideStart = setInterval(function() {
				startSlider()
			}, 6500);
		});
	     


	function updatePageRef(){
		$('.issaved').removeClass('noshow');
		$('.notsaved').addClass('noshow');
		$.removeData(document.body, 'adid');
	}

	function loginDone(){
		$.post('_el/el-re-members-log.html', { loggedInCheck: true }, function(data) {
			$('.membersPanel').html(''+data+'');
			if( parseInt($.data(document.body, 'adid')) != NaN ){
				updatePageRef();
			}		
		});
	}

	$('#loginActive').on('show.bs.modal', function () {
		if( parseInt($.data(document.body, 'adid')) != NaN ){
			$('#addlisting').val(parseInt($.data(document.body, 'adid')));	
		}
	});

	$('#buttonHere').html('<button type="submit" class="btn btn-default btn-lg">Login Now</button>');

	 	
		$( '#loginNow' ).parsley( {
			 listeners: {
		
		        onFormSubmit: function ( isFormValid, event ) {
		            if ( isFormValid ) {
		            	event.preventDefault();
			          	$('.main-error').addClass('noshow');
			          	$('.user-error').addClass('noshow');
			          	$('#inputCheck').remove();

					    fmData = $('#loginNow').serialize();
						    $.ajax({
						      type: 'POST',
						      url: '/_el/el-login-modal.php',
						      data: fmData,
						      success: function(data){
						      	newResp = data.split('|');
						        if( newResp[0] === 'success' ){
						          	$('.alert-errorLogin').addClass('noshow');
						        	loginDone();
									if( typeof(updateResultsRef) == 'function' ){
										updateResultsRef();
									}
									$('#loginActive').modal('hide');
						        }else{
						          $('.alertContent').html(newResp[1]);
						          if( newResp[1] == 'Sorry, your username or password are incorrect, please try again.' ){
						          	$('.main-errorLogin').removeClass('noshow');
						          }else{
						          	$('.main-errorLogin').removeClass('noshow');
						          }
						          return false;
						        }
						      }
						    });
		            }
		    	}
		    }
		});
} );
}