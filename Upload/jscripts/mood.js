var Mood = {
	init: function()
	{
		$(document).ready(function(){
		});
	},

	submitMood: function(uid)
	{
		// Get form, serialize it and send it
		var datastring = $(".moodclass_"+uid).serialize();

		$.ajax({
			type: "POST",
			url: "mood.php",
			data: datastring,
			dataType: "html",
			success: function(data) {
				// Replace modal HTML
				$('.modal_'+uid).fadeOut('slow', function() {
					$('.modal_'+uid).html(data);
					$('.modal_'+uid).fadeIn('slow');
				});
			},
			error: function(){
				  alert(lang.unknown_error);
			}
		});

		return false;
	}
};

Mood.init();