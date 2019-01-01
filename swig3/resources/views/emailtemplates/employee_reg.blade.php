<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>MTG</title>
        <style type="text/css">

            p {
                margin: 0px;
            }

            img {
                outline: none;
                border: none;
            }

            .newSite, .dashBoard {
                height: 94px;
                width: 30%;
            }

            .newSite a, .dashBoard a {
                color: #fff;
                font-size: 24px;
                text-decoration: none;
                padding: 15px;
                display: block;
            }

        </style>
    </head>

    <body style="margin:0; padding:0;">
        <table cellpadding="0" cellspacing="0" border="0" align="center" style="border-top: 5px solid #680115; background: #851e32;  max-width:674px; margin: 0px; color:#454446; font-size:18px; font-family:Arial, Helvetica, sans-serif;padding-bottom: 12px;">

            <tr>
                <td style="padding:32px 0; text-align: center;"><img src="{{ URL::asset('images/pageLogo.png') }}" alt="Logo" style="max-width: 84%;"/></td>
            </tr>

            <tr>
                <td style="padding: 0 25px;">
                    <table max-width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fff; display: block; min-height: 408px;">
                        <tr>
                            <td style="padding:31px 0 0 41px; font-weight: bold;"><p>Hi {{$employee_name}},</p></td>
                        </tr>
                        <tr>
                            <td style="padding:31px 0 0 41px; font-weight: bold;">
                            Welcome to Moroccon Taste
			    </td>
			</tr>
			<tr>
                            <td style="padding:31px 0 0 41px; font-weight: bold;">
                            Congratulations, you’ve successfully registered to our organization.
                            Please check your login credentials
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:18px 41px 0 41px;font-size: 16px; line-height: 25px;">
                                <p>Login Details</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:21px 41px 0; font-size: 15px;">
                                <p><span style="font-weight:bold;">User Name:</span> {{$username}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:14px 41px; font-size: 15px;">
                                <p><span style="font-weight:bold;">Password:</span> {{$password}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:31px 0 0 41px; font-weight: bold;">
                            <p><span style="font-weight:bold;">Login url:</span> {{$web_url}} ( Click this link & get started!!! )</p>
			    </td>
		        </tr>	
			<tr>
                            <td style="padding:31px 0 0 41px; font-weight: bold;">
                            We are here to help. If you have any queries, just contact support@mtg.com
			</td>
		        </tr>	
			<tr>
                            <td style="padding:31px 0 0 41px; font-weight: bold;">
                            Thank You    
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>


        </table>

    </body>
</html>
