<center> <h1> Congrats! You have logged in successfully </h1>
<br/>
<table class="details bordered" align="center" style="width: 400px; margin: 0 auto; text-align: center">
	<tr>
		<td colspan="2"> <h5> Your Details - </h5> </td>
	</tr>
	<tr>
		<td> Name: </td>
		<td> <?php echo $userdata['name']; ?> </td>
	</tr>
	<tr>
		<td> Username: </td>
		<td> <?php echo $userdata['username']; ?> </td>
	</tr>
	<tr>
		<td> Email: </td>
		<td> <?php echo $userdata['email']; ?> </td>
	</tr>
	<tr>
		<td> Location: </td>
		<td> <?php echo $userdata['location']; ?> </td>
	</tr>
	<tr>
		<td> Gender: </td>
		<td> <?php echo $userdata['gender']; ?> </td>
	</tr>
	
</table>

</center>