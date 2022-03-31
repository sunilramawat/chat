<html>
	<head>
		<title>Gh0st | Email</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
		<style type="text/css">
		*{
			margin: 0;
			padding: 0;
			outline: 0;
			box-sizing: border-box;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
		}
		body{
			font-family: 'Roboto', sans-serif;
			color: #000000;
		}
		@media only screen and (max-width: 767px) {
			table{
				width: 100%;
			}
		}
		</style>
	</head>
	<body style="-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;font-family: 'Roboto', sans-serif;height: 100%;margin: 0;padding: 0;width: 100%;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;border-collapse: collapse; background-image: url({{URL('public/admin/dist/img/bg-business.png')}});background-repeat: no-repeat;background-size: cover;">
			<tbody>
				<tr>
					<td align="center" style="padding: 15px 15px 15px 15px;  -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
						<table cellpadding="0" cellspacing="0" width="600" style="-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-radius: 0 0 10px 10px;">
							<tbody>
								<tr>
									<td align="center" width="100%"  style="-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;border-radius: 0;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;padding: 0 0 20px;">
										<img border="0" width="197" alt="Gh0st Logo" src="{{URL('public/admin/dist/img/logo.png')}}" style="-ms-interpolation-mode: bicubic;border: 0;height: auto;line-height: 100%;outline: none;text-decoration: none;" />
									</td>
								</tr>
								<tr>
									<td align="center" width="100%"  style="padding:0;  background-color: #EFEFEF; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;border-radius: 0;pointer-events: none;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;">
										<img border="0" width="100%" alt="Gh0st Email Banner" src="{{URL('public/admin/dist/img/email-image.png')}}" style="-ms-interpolation-mode: bicubic;border: 0;height: auto;line-height: 100%;outline: none;text-decoration: none;" />
									</td>
								</tr>
								<tr>
									<td align="left" style="padding: 20px; background-color: #FFFFFF; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
										<table bgcolor="#FFFFFF" width="100%" style="-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;border-collapse: collapse; text-align: center;">
											<tbody>
												<tr>
													<td style="-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-family: 'Roboto', sans-serif;padding: 0;">
														

														{!! stripslashes( $data['message']) !!}


													</td>
												</tr>
												<tr>
													<td style="-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-family: 'Roboto', sans-serif; padding:25px 0 12px">
														
													
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>