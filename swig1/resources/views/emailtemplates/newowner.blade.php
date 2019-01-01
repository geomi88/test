<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Account Activation</title>
	</head>
	<body style="margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; font-size: 16px;">
		<table cellpadding="0" cellspacing="0" border="0"  style="  background: #073563; table-layout: fixed;  
		max-width:700px; width:100%; margin:0 auto;  color:#454446; font-size:16px; font-family:Arial, Helvetica, sans-serif; line-height:22px;">
			<tr>
				<td style="text-align: center; color: #fff; font-size: 24px; font-weight: normal; height: 56px;">Welcome to LuxEstate Brokers</td>
			</tr>
			<tr>
				<td style="padding: 0 12px;  color: #454446;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fff;">
						<tr style="text-align: center; height: 78px; background:#fff; ">
							<td style="border-bottom: 1px solid #073563;  padding: 22px 0 29px;"><a href="#" title="LuxEstate"><img src="{{ URL::asset('images/imgLogo.png')}}" width="20%" height="auto" alt="LuxEstate"/></a>
							</td>
						</tr>
						<tr>
							<td>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fff;padding:20px; margin: 0; font-size:16px; font-family:Arial, Helvetica, sans-serif; line-height:22px;">
									<tr>
										<td style="padding-top:30px; margin: 0px; font-weight: bold;font-size: 18px; ">
											Hi {{$name}}, 
										</td>			
									</tr>
									<tr>
										<td>
											<em style="text-align: left ;font-style: italic; display: block; padding-top: 20px; color: #073563;">Welcome to <strong>LuxEstate Brokers team</strong>.</em>
											<p>
												You have been added as an Owner with privilege to add and manage properties.
											</p>
											<p>
												Please login to access your portal.
											</p>
											<p>
												Owner URL: <a href="javascript:void(0)" style="color: #073563;text-decoration: none;">{{env('APP_URL')}}/owner/login</a>
					
											</p>
											<p>
												Username: <strong> {{$email}}</strong>
											</p>
											<p>
												Password: <strong> {{$newpassword}} </strong>	
											</p>
											<p>
												Thank you for your support and collaboration.
											</p>
											<p>
												Please keep in touch and let us know your feedback, if any
											</p>
										</td>
									</tr>
									<tr>
										<td style="padding-top:20px;">
											<em>Sincerely,</em>	
										</td>
									</tr>
							    	<tr>
										<td style="padding-top:0px;">
											<p>Support team</p>
										</td>
									</tr>
									<tr>
										<td>
											<em style="font-size: 18px; font-weight: bold; font-style: normal;
										 margin-top: 8px;">Lux Estate Brokers</em><br>
											<a href="javascript:void(0)" style="color:#073563;text-decoration: none;">support@luxestate.com </a>
										</td>
									</tr>
									<tr>
										<td style="padding-top:14px;">
											<a href="javascript:void(0);" title="LuxEstate"><img src="{{ URL::asset('images/imgLogoFooter.png')}}" alt="LuxEstate" width="15%" /></a>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="padding: 14px 12px; width: 100%; color: #454446;">
	</td>
			</tr>
			
		</table>
	</body>
</html>
