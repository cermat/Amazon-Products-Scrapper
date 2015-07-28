<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Awoke Scrapper Test</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script>
	$(document).ready(function(){
		$("#search").click(function(event){
			event.preventDefault();
			$("#products").html(' Loading products ... ');
			var search_word = $("#search_word").val();
			// Call the Ajax function
			$.ajax({
				url: 'awoke_scrapper.php',
				data:{
					'search_word':search_word,
				},
				success: function (data) {
					if(data){
						$("#products").html(data);
					}
				},
				error: function(errorThrown){
					//alert('error');
					console.log(errorThrown);
				}
			});	
		
		});	
	});

</script>
</head>
<body>
	<form action="" method="post">
		<input type="text" value="" name="search_word" id="search_word">
		<button name="search" id="search"> Search </button>
	</form>
	<br />
	<div id="products"> Please enter any keyword to search the products</div>
</body>
</html>