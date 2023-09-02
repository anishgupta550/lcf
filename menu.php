			<br />
			<nav class="navbar navbar-inverse">
				<div class="container-fluid">
					<div class="navbar-header">
						<a class="navbar-brand" href="index.php">Webslesson</a>
					</div>
					<ul class="nav navbar-nav navbar-right">
						<li>
							<input type="text" name="search_user" id="search_user" class="form-control input-sm" placeholder="Search User" autocomplete="off" style="margin-top: 10px; width: 400px; margin-right:180px;" />
						</li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="view_notification">Notification
							<?php
							$total_notification = Count_notification($connect, $_SESSION["user_id"]);

							if($total_notification > 0)
							{
								echo '<span class="label label-danger" id="total_notification">'.$total_notification.'</span>';

							}


							?>
								<span class="caret"></span></a>
								<ul class="dropdown-menu">
								<?php
								echo Load_notification($connect, $_SESSION["user_id"]);

								?>

								</ul>
							</a>
						</li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $_SESSION['username']; ?>
							<span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="profile.php">Profile</a></li>
								<li><a href="logout.php">Logout</a></li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>

			<script type="text/javascript">

				$(document).ready(function(){
					$('#search_user').typeahead({
						source:function(query, result)
						{
							$('.typeahead').css('position', 'absolute');
							$('.typeahead').css('top', '45px');
							var action = 'search_user';
							$.ajax({
								url:"action.php",
								method:"POST",
								data:{query:query, action:action},
								dataType:"json",
								success:function(data)
								{
									result($.map(data, function(item){
										return item;
									}));
								}
							})
						}
					});

					$(document).on('click', '.typeahead li', function(){
						var search_query = $(this).text();
						window.location.href="wall.php?data="+search_query;
					});
				});

			</script>