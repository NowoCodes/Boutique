		</div>
	</div>
	<footer class="text-center" id="footer">
		&copy; Copyright 2013 - <?php echo date('Y'); ?> Shaunta's Boutique
	</footer>
	
	<script>
		function updateSizes() {
			var sizeString = '';
			for (var i = 1;i <= 12;i++) {
				if (jQuery('#size' + i).val() != '') {
					sizeString += jQuery('#size' + i).val() + ':' + jQuery('#qty' + i).val() + ',';
				}
			}
			jQuery('#sizes').val(sizeString);
		}
		
		function get_child_options(selected) {
			if (typeof selected === 'undefined') {
				var selected = '';
			}
			var parentID = jQuery('#parent').val();
			jQuery.ajax({
				url: '/Projects/InProgress/Boutique/admin/parsers/child_categories.php',
				type: 'POST',
				data: {parentID : parentID, selected: selected},
				success: function(data){
					jQuery('#child').html(data);
				},
				error: function(){alert("Something went wrong with the child options.")},
			});
		}
		jQuery('select[name = "parent"]').change(function(){
			get_child_options();
		});
	</script>
	<script>
		// Add the following code if you want the name of the file appear on select
		$(".custom-file-input").on("change", function() {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
		});
	</script>
	
	<script>
		window.setTimeout(function () {
			$(".alert-success").fadeTo(500, 0).slideUp(500, function () {
				$(this).remove();
			});
		}, 5000);
	</script>
	</body>
</html>