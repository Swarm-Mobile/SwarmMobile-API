Dashboard Layout
<?= $this->fetch('content') ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript">
	$.ajax(
			{
				url: '<?= Router::url('/api/dashboard/test') ?>',
				type: 'GET',
				data: {
					access_token: '<?= $access_token ?>'
				},
				success: function(data) {
					console.log(data);
				}
			}
	);

</script>